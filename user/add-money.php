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

$success = "";
$error = "";
if (isset($_SESSION['success_message'])) {

    $success =
        $_SESSION['success_message'];

    unset(
        $_SESSION['success_message']
    );
}

if (isset($_POST['add_money'])) {
    if (
    !isset($_POST['csrf_token']) ||
    !hash_equals(
        $_SESSION['csrf_token'],
        $_POST['csrf_token']
    )
) {

    $error =
        "Invalid request. Please refresh the page.";

} else {

    $amount = $_POST['amount'];


    // Check amount

   if (!is_numeric($amount) || $amount <= 0) {

    $error = "Please enter a valid amount.";

} elseif ($amount > 100000) {

    $error =
        "You can add a maximum of ₹1,00,000 at a time.";

} else {


        // Start database transaction

        mysqli_begin_transaction($conn);


        try {


            // Add money to user's balance

            $sql = "UPDATE users
                    SET balance = balance + ?
                    WHERE id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "di",
                $amount,
                $user_id
            );

            if (!mysqli_stmt_execute($stmt)) {

                throw new Exception("Balance update failed.");

            }


            // Create transaction record

           // Generate unique transaction reference

$reference_id =
    "PS" .
    date("YmdHis") .
    strtoupper(
        bin2hex(
            random_bytes(3)
        )
    );

$type = "ADD_MONEY";
$status = "SUCCESS";


$transaction_sql =
    "INSERT INTO transactions
    (
        reference_id,
        sender_id,
        receiver_id,
        amount,
        type,
        status
    )
    VALUES (?, ?, ?, ?, ?, ?)";

$transaction_stmt =
    mysqli_prepare(
        $conn,
        $transaction_sql
    );

mysqli_stmt_bind_param(
    $transaction_stmt,
    "siidss",
    $reference_id,
    $user_id,
    $user_id,
    $amount,
    $type,
    $status
);

if (
    !mysqli_stmt_execute(
        $transaction_stmt
    )
) {

    throw new Exception(
        "Transaction record failed."
    );
}

            // Everything worked
mysqli_commit($conn);

$_SESSION['success_message'] =
    "₹" .
    number_format($amount, 2) .
    " added successfully!";

header("Location: add-money.php");
exit();


        } catch (Exception $e) {


            // Something went wrong

            mysqli_rollback($conn);

            $error =
                "Unable to add money. Please try again.";

        }

    }

}
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Add Money | PaySphere</title>

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

<body>



<?php
require_once("../includes/user-navbar.php");
?>

<!-- =========================
     ADD MONEY PAGE
========================= -->

<main class="app-container add-page">

    <div class="send-page-header">

        <div class="add-page-icon">
            <i class="bi bi-plus-circle"></i>
        </div>

        <div>

            <h1>
                Add Money
            </h1>

            <p>
                Add funds to your PaySphere wallet.
            </p>

        </div>

    </div>


    <div class="row g-4">

        <!-- LEFT: FORM -->

        <div class="col-lg-7">

            <div class="app-card add-money-card">

                <div class="send-card-heading">

                    <div>

                        <h5>
                            Add Funds
                        </h5>

                        <p>
                            Enter the amount you want to add
                            to your wallet.
                        </p>

                    </div>

                    <div class="secure-badge">

                        <i class="bi bi-shield-check"></i>

                        Secure

                    </div>

                </div>


                <?php if ($success != "") { ?>

                    <div class="alert alert-success send-alert">

                        <i class="bi bi-check-circle me-2"></i>

                        <?php
                        echo htmlspecialchars($success);
                        ?>

                    </div>

                <?php } ?>


                <?php if ($error != "") { ?>

                    <div class="alert alert-danger send-alert">

                        <i class="bi bi-exclamation-circle me-2"></i>

                        <?php
                        echo htmlspecialchars($error);
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


                        <small class="send-helper">

                            Enter the amount you want to add
                            to your PaySphere wallet.

                        </small>

                    </div>


                    <!-- QUICK AMOUNTS -->

                    <div class="mb-4">

                        <label class="form-label send-label">
                            Quick Amount
                        </label>

                        <div class="quick-amounts">

                            <button
                                type="button"
                                class="quick-amount-btn"
                                data-amount="100"
                            >
                                ₹100
                            </button>

                            <button
                                type="button"
                                class="quick-amount-btn"
                                data-amount="500"
                            >
                                ₹500
                            </button>

                            <button
                                type="button"
                                class="quick-amount-btn"
                                data-amount="1000"
                            >
                                ₹1,000
                            </button>

                            <button
                                type="button"
                                class="quick-amount-btn"
                                data-amount="2000"
                            >
                                ₹2,000
                            </button>

                        </div>

                    </div>


                    <!-- INFO -->

                    <div class="add-info-box">

                        <i class="bi bi-info-circle"></i>

                        <p>
                            This is a simulated wallet top-up
                            for your PaySphere project.
                        </p>

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        name="add_money"
                        class="btn btn-paysphere add-submit-btn"
                    >

                        <i class="bi bi-plus-circle me-2"></i>

                        Add Money

                    </button>

                </form>

            </div>

        </div>


        <!-- RIGHT: INFO -->

        <div class="col-lg-5">

            <div class="app-card add-side-card">

                <div class="add-side-icon">

                    <i class="bi bi-wallet2"></i>

                </div>


                <h5>
                    Wallet top-up
                </h5>


                <p>
                    Add funds to your PaySphere wallet
                    and use them for transfers.
                </p>


                <div class="transfer-feature">

                    <div class="transfer-feature-icon">

                        <i class="bi bi-lightning-charge"></i>

                    </div>

                    <div>

                        <strong>
                            Instant balance update
                        </strong>

                        <span>
                            Your balance is updated immediately
                            after a successful top-up.
                        </span>

                    </div>

                </div>


                <div class="transfer-feature">

                    <div class="transfer-feature-icon">

                        <i class="bi bi-receipt"></i>

                    </div>

                    <div>

                        <strong>
                            Reference ID
                        </strong>

                        <span>
                            Every top-up creates its own
                            transaction reference.
                        </span>

                    </div>

                </div>


                <div class="transfer-feature">

                    <div class="transfer-feature-icon">

                        <i class="bi bi-clock-history"></i>

                    </div>

                    <div>

                        <strong>
                            Recorded in history
                        </strong>

                        <span>
                            You can view your top-up later
                            from transaction history.
                        </span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</main>
<script>

    const amountInput =
        document.querySelector(
            'input[name="amount"]'
        );

    document
        .querySelectorAll('.quick-amount-btn')
        .forEach(function(button) {

            button.addEventListener(
                'click',
                function() {

                    amountInput.value =
                        this.dataset.amount;

                }
            );

        });

</script>
</body>

</html>