<?php

require_once("../includes/auth.php");
require_once("../includes/db.php");
$user_id = $_SESSION['user_id'];

$user_sql =
    "SELECT id, full_name, email
     FROM users
     WHERE id = ?";

$user_stmt =
    mysqli_prepare(
        $conn,
        $user_sql
    );

mysqli_stmt_bind_param(
    $user_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute(
    $user_stmt
);

$user_result =
    mysqli_stmt_get_result(
        $user_stmt
    );

$user =
    mysqli_fetch_assoc(
        $user_result
    );

$error = "";
$success = "";
if (isset($_SESSION['success_message'])) {

    $success =
        $_SESSION['success_message'];

    unset(
        $_SESSION['success_message']
    );
}
if (isset($_POST['send_money'])) {
    if (
    !isset($_POST['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {

    $error = "Invalid request. Please refresh the page.";

} else {

    $sender_id = $_SESSION['user_id'];

    $receiver_email = trim($_POST['receiver_email']);

    $amount = $_POST['amount'];


    // --------------------------------
    // 1. Validate amount
    // --------------------------------

if (!is_numeric($amount) || $amount <= 0) {

    $error = "Amount must be greater than zero.";

} elseif ($amount > 100000) {

    $error = "You can send a maximum of ₹1,00,000 per transaction.";

} else {

    $amount = (float)$amount;


        // --------------------------------
        // 2. Find receiver
        // --------------------------------

        $sql = "SELECT id, full_name
                FROM users
                WHERE email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $receiver_email
        );

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);


        if (mysqli_num_rows($result) == 0) {

            $error = "Receiver not found.";

        } else {

            $receiver = mysqli_fetch_assoc($result);

            $receiver_id = $receiver['id'];


            // --------------------------------
            // 3. Prevent sending to yourself
            // --------------------------------

            if ($sender_id == $receiver_id) {

                $error = "You cannot send money to yourself.";

            } else {


                // --------------------------------
                // 4. Start database transaction
                // --------------------------------

                mysqli_begin_transaction($conn);

                try {


                    // --------------------------------
                    // 5. Lock sender balance
                    // --------------------------------

                    $sql = "SELECT balance
                            FROM users
                            WHERE id = ?
                            FOR UPDATE";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "i",
                        $sender_id
                    );

                    mysqli_stmt_execute($stmt);

                    $result = mysqli_stmt_get_result($stmt);

                    $sender = mysqli_fetch_assoc($result);

                    $sender_balance = (float)$sender['balance'];


                    // --------------------------------
                    // 6. Check sufficient balance
                    // --------------------------------

                    if ($amount > $sender_balance) {

                        throw new Exception(
                            "Insufficient balance."
                        );
                    }


                    // --------------------------------
                    // 7. Deduct money from sender
                    // --------------------------------

                    $sql = "UPDATE users
                            SET balance = balance - ?
                            WHERE id = ?";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "di",
                        $amount,
                        $sender_id
                    );

                    if (!mysqli_stmt_execute($stmt)) {

                        throw new Exception(
                            "Could not deduct sender balance."
                        );
                    }


                    // --------------------------------
                    // 8. Add money to receiver
                    // --------------------------------

                    $sql = "UPDATE users
                            SET balance = balance + ?
                            WHERE id = ?";

                    $stmt = mysqli_prepare($conn, $sql);

                    mysqli_stmt_bind_param(
                        $stmt,
                        "di",
                        $amount,
                        $receiver_id
                    );

                    if (!mysqli_stmt_execute($stmt)) {

                        throw new Exception(
                            "Could not update receiver balance."
                        );
                    }


                    // --------------------------------
                    // 9. Save transaction
                    // --------------------------------

                 // Generate unique reference ID

$reference_id =
    "PS" .
    date("YmdHis") .
    strtoupper(
        bin2hex(
            random_bytes(3)
        )
    );


// Transaction information

$type = "TRANSFER";

$status = "SUCCESS";


// Save transaction in database

$sql = "INSERT INTO transactions
        (
            reference_id,
            sender_id,
            receiver_id,
            amount,
            type,
            status
        )
        VALUES (?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "siidss",
    $reference_id,
    $sender_id,
    $receiver_id,
    $amount,
    $type,
    $status
);


if (!mysqli_stmt_execute($stmt)) {

    throw new Exception(
        "Could not save transaction."
    );
}


                    // --------------------------------
                    // 10. Everything succeeded
                    // --------------------------------

                   mysqli_commit($conn);

$_SESSION['success_message'] =
    "₹" .
    number_format($amount, 2) .
    " sent successfully!";

header("Location: send-money.php");
exit();

                } catch (Exception $e) {

                    // --------------------------------
                    // Something failed
                    // --------------------------------

                    mysqli_rollback($conn);

                    $error = $e->getMessage();
                }
            }
        }
    }
}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Send Money | PaySphere</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >
    <link
    rel="stylesheet"
    href="../css/app.css"
>

</head>

<body >


<?php
require_once("../includes/user-navbar.php");
?>


<!-- SEND MONEY FORM -->

<!-- =========================
     SEND MONEY PAGE
========================= -->

<main class="app-container send-page">

    <div class="send-page-header">

        <div class="send-page-icon">
            <i class="bi bi-send"></i>
        </div>

        <div>

            <h1>
                Send Money
            </h1>

            <p>
                Transfer money securely to another
                PaySphere user.
            </p>

        </div>

    </div>


    <div class="row g-4">

        <!-- LEFT: FORM -->

        <div class="col-lg-7">

            <div class="app-card send-money-card">

                <div class="send-card-heading">

                    <div>

                        <h5>
                            Transfer Details
                        </h5>

                        <p>
                            Enter the receiver and amount
                            you want to send.
                        </p>

                    </div>

                    <div class="secure-badge">

                        <i class="bi bi-shield-check"></i>

                        Secure

                    </div>

                </div>


                <?php if ($error != "") { ?>

                    <div class="alert alert-danger send-alert">

                        <i class="bi bi-exclamation-circle me-2"></i>

                        <?php
                        echo htmlspecialchars($error);
                        ?>

                    </div>

                <?php } ?>


                <?php if ($success != "") { ?>

                    <div class="alert alert-success send-alert">

                        <i class="bi bi-check-circle me-2"></i>

                        <?php
                        echo htmlspecialchars($success);
                        ?>

                    </div>

                <?php } ?>


                <form method="POST">
                    <input
    type="hidden"
    name="csrf_token"
    value="<?php
        echo htmlspecialchars(
            $_SESSION['csrf_token']
        );
    ?>"
>


                    <!-- RECEIVER EMAIL -->

                    <div class="mb-4">

                        <label class="form-label send-label">

                            Receiver's Email

                        </label>


                        <div class="send-input-wrap">

                            <i class="bi bi-envelope"></i>

                            <input
                                type="email"
                                name="receiver_email"
                                class="form-control send-input"
                                placeholder="name@example.com"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST['receiver_email'] ?? ''
                                    );
                                ?>"
                                required
                            >

                        </div>


                        <small class="send-helper">

                            Enter the email linked to the
                            receiver's PaySphere account.

                        </small>

                    </div>


                    <!-- AMOUNT -->

                    <div class="mb-4">

                        <label class="form-label send-label">

                            Amount

                        </label>


                        <div class="send-input-wrap amount-input">

                            <span class="currency-symbol">
                                ₹
                            </span>

                           <input
    type="number"
    name="amount"
    class="form-control send-input"
    placeholder="0.00"
    min="1"
    max="100000"
    step="0.01"
                                value="<?php
                                    echo htmlspecialchars(
                                        $_POST['amount'] ?? ''
                                    );
                                ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- INFO -->

                    <div class="send-info-box">

                        <i class="bi bi-info-circle"></i>

                        <p>

                            Transfers are processed instantly
                            between registered PaySphere users.

                        </p>

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        name="send_money"
                        class="btn btn-paysphere send-submit-btn"
                    >

                        <i class="bi bi-send me-2"></i>

                        Send Money

                    </button>

                </form>

            </div>

        </div>


        <!-- RIGHT: INFORMATION -->

        <div class="col-lg-5">

            <div class="app-card transfer-side-card">

                <div class="transfer-side-icon">

                    <i class="bi bi-arrow-left-right"></i>

                </div>


                <h5>
                    Safe transfers
                </h5>


                <p>

                    Your transfer is processed using a
                    secure database transaction.

                </p>


                <div class="transfer-feature">

                    <div class="transfer-feature-icon">

                        <i class="bi bi-shield-lock"></i>

                    </div>

                    <div>

                        <strong>
                            Secure processing
                        </strong>

                        <span>
                            Balance updates happen safely
                            as one transaction.
                        </span>

                    </div>

                </div>


                <div class="transfer-feature">

                    <div class="transfer-feature-icon">

                        <i class="bi bi-receipt"></i>

                    </div>

                    <div>

                        <strong>
                            Transaction record
                        </strong>

                        <span>
                            Every successful transfer gets
                            its own reference ID.
                        </span>

                    </div>

                </div>


                <div class="transfer-feature">

                    <div class="transfer-feature-icon">

                        <i class="bi bi-clock-history"></i>

                    </div>

                    <div>

                        <strong>
                            Track anytime
                        </strong>

                        <span>
                            View your transfer later from
                            transaction history.
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>
</body>

</html>