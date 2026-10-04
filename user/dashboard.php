<?php

require_once("../includes/auth.php");
require_once("../includes/db.php");



$user_id = $_SESSION['user_id'];

$sql = "SELECT id, full_name, email, phone, balance
        FROM users
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

$balance = $user['balance'];
$profile_success = "";
$profile_error = "";

if (isset($_POST['update_profile'])) {
       if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {

        $profile_error =
            "Invalid request. Please refresh the page.";

    } else {

    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    if ($full_name == "") {

        $profile_error = "Full name cannot be empty.";

    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

        $profile_error = "Please enter a valid phone number.";

    } else {
$check_phone_sql =
    "SELECT id
     FROM users
     WHERE phone = ?
     AND id != ?";

$check_phone_stmt =
    mysqli_prepare(
        $conn,
        $check_phone_sql
    );

mysqli_stmt_bind_param(
    $check_phone_stmt,
    "si",
    $phone,
    $user_id
);

mysqli_stmt_execute(
    $check_phone_stmt
);

$check_phone_result =
    mysqli_stmt_get_result(
        $check_phone_stmt
    );

if (
    mysqli_num_rows(
        $check_phone_result
    ) > 0
) {

    $profile_error =
        "This phone number is already linked to another account.";

} else {

    // your existing UPDATE code goes here

        $sql = "UPDATE users
                SET full_name = ?, phone = ?
                WHERE id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssi",
            $full_name,
            $phone,
            $user_id
        );

        if (mysqli_stmt_execute($stmt)) {

            $_SESSION['user_name'] = $full_name;

            $user['full_name'] = $full_name;
            $user['phone'] = $phone;

            $profile_success = "Profile updated successfully.";

        } else {

            $profile_error = "Unable to update profile.";

        }
    }
}
}
}

// CHANGE PASSWORD

$password_success = "";
$password_error = "";

if (isset($_POST['change_password'])) {
      if (
        !isset($_POST['csrf_token']) ||
        !hash_equals(
            $_SESSION['csrf_token'],
            $_POST['csrf_token']
        )
    ) {

        $password_error =
            "Invalid request. Please refresh the page.";

    } else {

    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];


    // Get current password hash

    $password_sql = "SELECT password
                     FROM users
                     WHERE id = ?";

    $password_stmt = mysqli_prepare(
        $conn,
        $password_sql
    );

    mysqli_stmt_bind_param(
        $password_stmt,
        "i",
        $user_id
    );

    mysqli_stmt_execute($password_stmt);

    $password_result =
        mysqli_stmt_get_result($password_stmt);

    $password_user =
        mysqli_fetch_assoc($password_result);


    // Check current password

    if (
        !password_verify(
            $current_password,
            $password_user['password']
        )
    ) {

        $password_error =
            "Current password is incorrect.";

    }


    // Check password length

    elseif (strlen($new_password) < 8) {

        $password_error =
            "New password must be at least 8 characters.";

    }


    // Check confirm password

    elseif ($new_password !== $confirm_password) {

        $password_error =
            "New passwords do not match.";

    }


    // New password should not be same as old

    elseif (
        password_verify(
            $new_password,
            $password_user['password']
        )
    ) {

        $password_error =
            "New password must be different from current password.";

    }


    else {

        // Hash new password

        $hashed_password =
            password_hash(
                $new_password,
                PASSWORD_DEFAULT
            );


        // Update database

        $update_password_sql =
            "UPDATE users
             SET password = ?
             WHERE id = ?";

        $update_password_stmt =
            mysqli_prepare(
                $conn,
                $update_password_sql
            );

        mysqli_stmt_bind_param(
            $update_password_stmt,
            "si",
            $hashed_password,
            $user_id
        );


        if (
            mysqli_stmt_execute(
                $update_password_stmt
            )
        ) {

            $password_success =
                "Password changed successfully.";

        } else {

            $password_error =
                "Unable to change password.";
        }
    }
}
}
// Get recent transactions
$sql = "SELECT 
            t.id,
            t.reference_id,
            t.sender_id,
            t.receiver_id,
            t.amount,
            t.type,
            t.status,
            t.created_at,
            sender.full_name AS sender_name,
            receiver.full_name AS receiver_name
        FROM transactions t
        JOIN users sender ON t.sender_id = sender.id
        JOIN users receiver ON t.receiver_id = receiver.id
        WHERE t.sender_id = ? OR t.receiver_id = ?
        ORDER BY t.created_at DESC
        LIMIT 5";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $user_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$transactions = mysqli_stmt_get_result($stmt);


?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>PaySphere Dashboard</title>

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

<body class="bg-light">
<?php
require_once("../includes/user-navbar.php");
?>
<!-- DASHBOARD -->

<div class="app-container py-5">

  <!-- =========================
     DASHBOARD HERO
========================= -->

<section class="wallet-hero">

    <div class="row align-items-center g-4">


        <!-- LEFT SIDE -->

        <div class="col-lg-7">

            <p class="hero-small-text">
                Welcome back 👋
            </p>

            <h1 class="hero-title">

                Manage your money with
                <span>PaySphere</span>

            </h1>

            <p class="hero-description">

                Send, add and track your money securely
                from one simple dashboard.

            </p>


            <!-- BALANCE -->

            <div class="hero-balance">

                <div>

                    <small>
                        Available Balance
                    </small>

                    <h2>

                        ₹<?php
                        echo number_format(
                            $balance,
                            2
                        );
                        ?>

                    </h2>

                </div>

                <div class="hero-wallet-icon">

                    <i class="bi bi-wallet2"></i>

                </div>

            </div>


            <!-- BUTTONS -->

            <div class="d-flex flex-wrap gap-2 mt-4">

                <a
                    href="send-money.php"
                    class="btn btn-paysphere"
                >

                    <i class="bi bi-send me-2"></i>

                    Send Money

                </a>


                <a
                    href="add-money.php"
                    class="btn btn-paysphere-outline"
                >

                    <i class="bi bi-plus-circle me-2"></i>

                    Add Money

                </a>

            </div>

        </div>


       <!-- RIGHT SIDE -->

<div class="col-lg-5">

    <div class="hero-summary-card">

        <div class="d-flex justify-content-between align-items-start mb-4">

            <div>

                <small class="summary-label">
                    PaySphere Wallet
                </small>

                <h5 class="fw-bold mt-1 mb-0">
                    Your money, simplified.
                </h5>

            </div>

            <div class="summary-logo">

                <i class="bi bi-wallet2"></i>

            </div>

        </div>


        <div class="summary-balance">

            <small>
                Current Balance
            </small>

            <h2>

                ₹<?php
                echo number_format(
                    $balance,
                    2
                );
                ?>

            </h2>

        </div>


        <div class="summary-actions">

            <a href="send-money.php">

                <span>
                    <i class="bi bi-send"></i>
                </span>

                Send

            </a>


            <a href="add-money.php">

                <span>
                    <i class="bi bi-plus"></i>
                </span>

                Add

            </a>


            <a href="transactions.php">

                <span>
                    <i class="bi bi-clock-history"></i>
                </span>

                History

            </a>

        </div>

    </div>

</div>
       

</section>


<!-- =========================
     QUICK ACTIONS
========================= -->

<section class="mt-4">

    <div class="row g-3">


        <!-- SEND MONEY -->

        <div class="col-md-4">

            <a
                href="send-money.php"
                class="quick-action-card"
            >

                <div class="quick-action-icon purple">

                    <i class="bi bi-send"></i>

                </div>


                <div>

                    <h6>
                        Send Money
                    </h6>

                    <p>
                        Transfer money securely
                    </p>

                </div>


                <i class="bi bi-chevron-right action-arrow"></i>

            </a>

        </div>


        <!-- ADD MONEY -->

        <div class="col-md-4">

            <a
                href="add-money.php"
                class="quick-action-card"
            >

                <div class="quick-action-icon green">

                    <i class="bi bi-plus-circle"></i>

                </div>


                <div>

                    <h6>
                        Add Money
                    </h6>

                    <p>
                        Top up your wallet
                    </p>

                </div>


                <i class="bi bi-chevron-right action-arrow"></i>

            </a>

        </div>


        <!-- TRANSACTIONS -->

        <div class="col-md-4">

            <a
                href="transactions.php"
                class="quick-action-card"
            >

                <div class="quick-action-icon orange">

                    <i class="bi bi-clock-history"></i>

                </div>


                <div>

                    <h6>
                        Transactions
                    </h6>

                    <p>
                        View payment history
                    </p>

                </div>


                <i class="bi bi-chevron-right action-arrow"></i>

            </a>

        </div>


    </div>

</section>
 <!-- RECENT TRANSACTIONS -->

<div class="card border-0 shadow-sm mt-5">

    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <h4 class="fw-bold mb-0">
                Recent Transactions
            </h4>

        </div>


       <?php if (mysqli_num_rows($transactions) > 0) { ?>

    <?php while ($transaction = mysqli_fetch_assoc($transactions)) { ?>

        <?php

        $is_add_money = ($transaction['type'] == 'ADD_MONEY');

        $is_sender = ($transaction['sender_id'] == $user_id);
        if ($is_add_money) {

    $detail_title = "Money Added";
    $detail_person_label = "Details";
    $detail_person = "Wallet Top Up";
    $detail_sign = "+";

} elseif ($is_sender) {

    $detail_title = "Money Sent";
    $detail_person_label = "To";
    $detail_person = $transaction['receiver_name'];
    $detail_sign = "-";

} else {

    $detail_title = "Money Received";
    $detail_person_label = "From";
    $detail_person = $transaction['sender_name'];
    $detail_sign = "+";
}

        ?>


        <div class="d-flex justify-content-between align-items-center border-bottom py-3">


            <!-- LEFT SIDE -->

            <div class="d-flex align-items-center gap-3">


                <?php if ($is_add_money) { ?>

                    <!-- MONEY ADDED -->

                    <div class="fs-3 text-success">

                        <i class="bi bi-plus-circle"></i>

                    </div>


                    <div>

                      <h6 class="recent-transaction-title">
    Money Added
</h6>

<p class="recent-transaction-desc">
    Added to your wallet
</p>

<div class="recent-transaction-meta">

    <span>
        Ref:
        <?php
        echo htmlspecialchars(
            $transaction['reference_id']
        );
        ?>
    </span>

    <span class="recent-transaction-dot">•</span>

    <span>
        <?php
        echo htmlspecialchars(
            $transaction['type']
        );
        ?>
    </span>

    <span class="recent-transaction-dot">•</span>

    <span class="recent-status-success">
        <?php
        echo htmlspecialchars(
            $transaction['status']
        );
        ?>
    </span>

</div>

                    </div>


                <?php } elseif ($is_sender) { ?>

                    <!-- MONEY SENT -->

                    <div class="fs-3 text-danger">

                        <i class="bi bi-arrow-up-circle"></i>

                    </div>


                    <div>

                 <h6 class="recent-transaction-title">
    Money Sent
</h6>

<p class="recent-transaction-desc">
    To
    <?php
    echo htmlspecialchars(
        $transaction['receiver_name']
    );
    ?>
</p>

<div class="recent-transaction-meta">

    <span>
        Ref:
        <?php
        echo htmlspecialchars(
            $transaction['reference_id']
        );
        ?>
    </span>

    <span class="recent-transaction-dot">•</span>

    <span>
        <?php
        echo htmlspecialchars(
            $transaction['type']
        );
        ?>
    </span>

    <span class="recent-transaction-dot">•</span>

    <span class="recent-status-success">
        <?php
        echo htmlspecialchars(
            $transaction['status']
        );
        ?>
    </span>

</div> 
         

                    </div>


                <?php } else { ?>

                    <!-- MONEY RECEIVED -->

                    <div class="fs-3 text-success">

                        <i class="bi bi-arrow-down-circle"></i>

                    </div>


                    <div>

<h6 class="recent-transaction-title">
    Money Received
</h6>

<p class="recent-transaction-desc">
    From
    <?php
    echo htmlspecialchars(
        $transaction['sender_name']
    );
    ?>
</p>

<div class="recent-transaction-meta">

    <span>
        Ref:
        <?php
        echo htmlspecialchars(
            $transaction['reference_id']
        );
        ?>
    </span>

    <span class="recent-transaction-dot">•</span>

    <span>
        <?php
        echo htmlspecialchars(
            $transaction['type']
        );
        ?>
    </span>

    <span class="recent-transaction-dot">•</span>

    <span class="recent-status-success">
        <?php
        echo htmlspecialchars(
            $transaction['status']
        );
        ?>
    </span>

</div>

                    </div>

                <?php } ?>


            </div>


            <!-- RIGHT SIDE -->

            <div class="text-end">


                <?php if ($is_add_money) { ?>

                    <!-- ADD MONEY AMOUNT -->

                 <h6 class="recent-transaction-amount text-success">
                        +

                        ₹<?php

                        echo number_format(
                            $transaction['amount'],
                            2
                        );

                        ?>

                    </h6>


                <?php } elseif ($is_sender) { ?>

                    <!-- SENT AMOUNT -->

                  <h6 class="recent-transaction-amount text-danger">

                        -

                        ₹<?php

                        echo number_format(
                            $transaction['amount'],
                            2
                        );

                        ?>

                    </h6>


                <?php } else { ?>

                    <!-- RECEIVED AMOUNT -->

                   <h6 class="recent-transaction-amount text-success">

                        +

                        ₹<?php

                        echo number_format(
                            $transaction['amount'],
                            2
                        );

                        ?>

                    </h6>

                <?php } ?>


                <!-- DATE -->

               <small class="recent-transaction-date">

                    <?php

                    echo date(
                        "d M Y, h:i A",
                        strtotime(
                            $transaction['created_at']
                        )
                    );

                    ?>

                </small>
                <br>

<button
    type="button"
   class="recent-view-btn view-transaction-btn"
    data-bs-toggle="modal"
    data-bs-target="#transactionDetailsModal"

    data-title="<?php
        echo htmlspecialchars(
            $detail_title,
            ENT_QUOTES
        );
    ?>"

    data-reference="<?php
        echo htmlspecialchars(
            $transaction['reference_id'],
            ENT_QUOTES
        );
    ?>"

    data-type="<?php
        echo htmlspecialchars(
            $transaction['type'],
            ENT_QUOTES
        );
    ?>"

    data-status="<?php
        echo htmlspecialchars(
            $transaction['status'],
            ENT_QUOTES
        );
    ?>"

    data-amount="<?php
        echo number_format(
            $transaction['amount'],
            2
        );
    ?>"

    data-sign="<?php
        echo $detail_sign;
    ?>"

    data-person-label="<?php
        echo htmlspecialchars(
            $detail_person_label,
            ENT_QUOTES
        );
    ?>"

    data-person="<?php
        echo htmlspecialchars(
            $detail_person,
            ENT_QUOTES
        );
    ?>"

    data-date="<?php
        echo date(
            'd M Y, h:i A',
            strtotime(
                $transaction['created_at']
            )
        );
    ?>"
>

    View Details

</button>


            </div>


        </div>


    <?php } ?>


<?php } else { ?>


    <!-- NO TRANSACTIONS -->

    <div class="text-center py-5">

        <i class="bi bi-receipt fs-1 text-secondary"></i>

        <p class="text-secondary mt-3 mb-0">

            Your transactions will appear here.

        </p>

    </div>


<?php } ?>

                       

                          
</div>

        </div>

    </div>

</div>
<!-- TRANSACTION DETAILS MODAL -->

<div
    class="modal fade"
    id="transactionDetailsModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4 shadow">


            <!-- HEADER -->

            <div class="modal-header border-0">

                <h5 class="modal-title fw-bold">
                    Transaction Details
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body px-4 pb-4">


                <div class="text-center mb-4">

                    <div
                        class="mx-auto rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center mb-3"
                        style="width:70px;height:70px;font-size:32px;"
                    >

                        <i class="bi bi-check-circle-fill"></i>

                    </div>


                    <h5
                        class="fw-bold"
                        id="transactionTitle"
                    >
                    </h5>


                    <h2
                        class="fw-bold mt-2"
                        id="transactionAmount"
                    >
                    </h2>


                    <span
                        class="badge text-bg-success"
                        id="transactionStatus"
                    >
                    </span>

                </div>


                <div class="border rounded-4 overflow-hidden">


                    <!-- REFERENCE ID -->

                    <div class="p-3 border-bottom">

                        <small class="text-secondary">
                            Reference ID
                        </small>

                        <div
                            class="fw-semibold text-break"
                            id="transactionReference"
                        >
                        </div>

                    </div>


                    <!-- TYPE -->

                    <div class="p-3 border-bottom">

                        <small class="text-secondary">
                            Transaction Type
                        </small>

                        <div
                            class="fw-semibold"
                            id="transactionType"
                        >
                        </div>

                    </div>


                    <!-- TO / FROM -->

                    <div class="p-3 border-bottom">

                        <small
                            class="text-secondary"
                            id="transactionPersonLabel"
                        >
                        </small>

                        <div
                            class="fw-semibold"
                            id="transactionPerson"
                        >
                        </div>

                    </div>


                    <!-- DATE -->

                    <div class="p-3">

                        <small class="text-secondary">
                            Date & Time
                        </small>

                        <div
                            class="fw-semibold"
                            id="transactionDate"
                        >
                        </div>

                    </div>


                </div>


                <button
                    type="button"
                    class="btn btn-light border w-100 mt-4"
                    data-bs-dismiss="modal"
                >
                    Close
                </button>


            </div>

        </div>

    </div>

</div>
<!-- PROFILE MODAL -->

<div
    class="modal fade"
    id="profileModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4 shadow">


            <!-- HEADER -->

            <div class="modal-header border-0 pb-0">

                <h5 class="modal-title fw-bold">
                    My Profile
                </h5>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body p-4">


                <!-- USER -->

                <div class="text-center mb-4">

                    <div
                        class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-primary text-white mb-3"
                        style="width:75px;height:75px;font-size:30px;"
                    >

                        <i class="bi bi-person-fill"></i>

                    </div>


                    <h4 class="fw-bold mb-1">

                        <?php echo htmlspecialchars($user['full_name']); ?>

                    </h4>


                    <p class="text-secondary mb-0">

                        <?php echo htmlspecialchars($user['email']); ?>

                    </p>

                </div>


                <!-- BALANCE -->

                <div class="bg-light rounded-4 p-3 mb-3">

                    <small class="text-secondary">
                        Available Balance
                    </small>

                    <h3 class="fw-bold text-success mb-0 mt-1">

                        ₹<?php echo number_format($user['balance'], 2); ?>

                    </h3>

                </div>


                <!-- ACCOUNT INFO -->

                <div class="border rounded-4 overflow-hidden">


                    <div class="p-3 border-bottom">

                        <small class="text-secondary">
                            User ID
                        </small>

                        <div class="fw-semibold">
                            #<?php echo $user['id']; ?>
                        </div>

                    </div>


                    <div class="p-3">

                        <small class="text-secondary">
                            Email Address
                        </small>

                        <div class="fw-semibold">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </div>

                    </div>
                    <div class="p-3 border-top">

    <small class="text-secondary">
        Phone Number
    </small>

    <div class="fw-semibold">
        <?php echo htmlspecialchars($user['phone']); ?>
    </div>

</div>


                </div>


                <!-- EDIT BUTTON -->
                 <button
    type="button"
    class="dropdown-item rounded-3 py-2"
    data-bs-toggle="modal"
    data-bs-target="#editProfileModal"
>

    <i class="bi bi-pencil-square me-2"></i>

    Edit Profile

</button>

             


            </div>

        </div>

    </div>

</div>
<!-- EDIT PROFILE MODAL -->

<div
    class="modal fade"
    id="editProfileModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4 shadow">


            <!-- HEADER -->

            <div class="modal-header border-0">

                <div>

                    <h5 class="modal-title fw-bold">
                        Edit Profile
                    </h5>

                    <small class="text-secondary">
                        Update your personal information
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body px-4 pb-4">


                <?php if ($profile_success != "") { ?>

                    <div class="alert alert-success">

                        <?php echo $profile_success; ?>

                    </div>

                <?php } ?>


                <?php if ($profile_error != "") { ?>

                    <div class="alert alert-danger">

                        <?php echo $profile_error; ?>

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


                    <!-- FULL NAME -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control form-control-lg"
                            value="<?php echo htmlspecialchars($user['full_name']); ?>"
                            required
                        >

                    </div>


                    <!-- EMAIL -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Email Address
                        </label>

                        <input
                            type="email"
                            class="form-control form-control-lg bg-light"
                            value="<?php echo htmlspecialchars($user['email']); ?>"
                            readonly
                        >

                        <small class="text-secondary">
                            Email cannot be changed right now.
                        </small>

                    </div>


                    <!-- PHONE -->

                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Phone Number
                        </label>

                        <input
                            type="text"
                            name="phone"
                            class="form-control form-control-lg"
                            value="<?php echo htmlspecialchars($user['phone']); ?>"
                            maxlength="15"
                            required
                        >

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        name="update_profile"
                        class="btn btn-primary w-100 py-2 fw-semibold"
                    >

                        <i class="bi bi-check-circle me-2"></i>

                        Save Changes

                    </button>


                </form>

            </div>

        </div>

    </div>

</div>
<!-- CHANGE PASSWORD MODAL -->

<div
    class="modal fade"
    id="changePasswordModal"
    tabindex="-1"
>

    <div class="modal-dialog modal-dialog-centered">

        <div class="modal-content border-0 rounded-4 shadow">


            <!-- HEADER -->

            <div class="modal-header border-0">

                <div>

                    <h5 class="modal-title fw-bold">
                        Change Password
                    </h5>

                    <small class="text-secondary">
                        Keep your PaySphere account secure
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <!-- BODY -->

            <div class="modal-body px-4 pb-4">


                <?php if ($password_success != "") { ?>

                    <div class="alert alert-success">

                        <i class="bi bi-check-circle me-2"></i>

                        <?php
                        echo htmlspecialchars(
                            $password_success
                        );
                        ?>

                    </div>

                <?php } ?>


                <?php if ($password_error != "") { ?>

                    <div class="alert alert-danger">

                        <i class="bi bi-exclamation-circle me-2"></i>

                        <?php
                        echo htmlspecialchars(
                            $password_error
                        );
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


                    <!-- CURRENT PASSWORD -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Current Password
                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="form-control"
                            autocomplete="current-password"
                            required
                        >

                    </div>


                    <!-- NEW PASSWORD -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            New Password
                        </label>

                        <input
                            type="password"
                            name="new_password"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                        <small class="text-secondary">
                            Minimum 8 characters.
                        </small>

                    </div>


                    <!-- CONFIRM PASSWORD -->

                    <div class="mb-4">

                        <label class="form-label fw-semibold">
                            Confirm New Password
                        </label>

                        <input
                            type="password"
                            name="confirm_password"
                            class="form-control"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        name="change_password"
                        class="btn btn-primary w-100 py-2 fw-semibold"
                    >

                        <i class="bi bi-shield-lock me-2"></i>

                        Update Password

                    </button>


                </form>

            </div>

        </div>

    </div>

</div>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>
<?php if (isset($_GET['open'])) { ?>

<script>

document.addEventListener(
    'DOMContentLoaded',
    function() {

        const openModal =
            "<?php echo htmlspecialchars($_GET['open']); ?>";

        let modalId = "";

        if (openModal === "profile") {
            modalId = "profileModal";
        }

        if (openModal === "edit-profile") {
            modalId = "editProfileModal";
        }

        if (openModal === "change-password") {
            modalId = "changePasswordModal";
        }

        if (modalId !== "") {

            const modalElement =
                document.getElementById(modalId);

            if (modalElement) {

                const modal =
                    new bootstrap.Modal(
                        modalElement
                    );

                modal.show();
            }
        }
    }
);

</script>

<?php } ?>
<script>
document
    .querySelectorAll(
        '.view-transaction-btn'
    )
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {


                document.getElementById(
                    'transactionTitle'
                ).textContent =
                    this.dataset.title;


                document.getElementById(
                    'transactionAmount'
                ).textContent =
                    this.dataset.sign +
                    " ₹" +
                    this.dataset.amount;


                document.getElementById(
                    'transactionStatus'
                ).textContent =
                    this.dataset.status;


                document.getElementById(
                    'transactionReference'
                ).textContent =
                    this.dataset.reference;


                document.getElementById(
                    'transactionType'
                ).textContent =
                    this.dataset.type;


                document.getElementById(
                    'transactionPersonLabel'
                ).textContent =
                    this.dataset.personLabel;


                document.getElementById(
                    'transactionPerson'
                ).textContent =
                    this.dataset.person;


                document.getElementById(
                    'transactionDate'
                ).textContent =
                    this.dataset.date;

            }
        );

    });

</script>

<?php
if (
    $profile_error != "" ||
    $profile_success != ""
) {
?>

<script>

    const profileModalElement =
        document.getElementById(
            'editProfileModal'
        );

    const profileModal =
        new bootstrap.Modal(
            profileModalElement
        );

    profileModal.show();


    <?php if ($profile_success != "") { ?>

        setTimeout(function () {

            profileModal.hide();

        }, 1500);

    <?php } ?>

</script>

<?php
}
?>


<?php
if (
    $password_error != "" ||
    $password_success != ""
) {
?>

<script>

    const passwordModalElement =
        document.getElementById(
            'changePasswordModal'
        );

    const passwordModal =
        new bootstrap.Modal(
            passwordModalElement
        );

    passwordModal.show();


    <?php if ($password_success != "") { ?>

        setTimeout(function () {

            passwordModal.hide();

        }, 1500);

    <?php } ?>

</script>

<?php
}
?>
</body>

</html>