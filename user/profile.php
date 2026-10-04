<?php

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

require_once("../includes/db.php");

$user_id = $_SESSION['user_id'];


// Get user details

$sql = "SELECT id, full_name, email, phone, balance
        FROM users
        WHERE id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

$profile_success = "";
$profile_error = "";

if (isset($_POST['update_profile'])) {

    $full_name = trim($_POST['full_name']);
    $phone = trim($_POST['phone']);

    if ($full_name == "") {

        $profile_error = "Full name cannot be empty.";

    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

        $profile_error = "Enter a valid phone number.";

    } else {

        // Check if phone already belongs to another user

        $check_sql = "SELECT id
                      FROM users
                      WHERE phone = ?
                      AND id != ?";

        $check_stmt = mysqli_prepare($conn, $check_sql);

        mysqli_stmt_bind_param(
            $check_stmt,
            "si",
            $phone,
            $user_id
        );

        mysqli_stmt_execute($check_stmt);

        $check_result = mysqli_stmt_get_result($check_stmt);


        if (mysqli_num_rows($check_result) > 0) {

            $profile_error =
                "This phone number is already registered.";

        } else {

            $update_sql = "UPDATE users
                           SET full_name = ?, phone = ?
                           WHERE id = ?";

            $update_stmt =
                mysqli_prepare($conn, $update_sql);

            mysqli_stmt_bind_param(
                $update_stmt,
                "ssi",
                $full_name,
                $phone,
                $user_id
            );

            if (mysqli_stmt_execute($update_stmt)) {

                $_SESSION['user_name'] = $full_name;

                $user['full_name'] = $full_name;
                $user['phone'] = $phone;

                $profile_success =
                    "Profile updated successfully.";

            } else {

                $profile_error =
                    "Unable to update profile.";

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

    <title>My Profile | PaySphere</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >

    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
    >

    <style>

        body {
            background: #f5f7fb;
            color: #172033;
        }

        .navbar {
            height: 76px;
        }

        .brand {
            font-size: 28px;
            font-weight: 700;
            color: #2563eb;
            text-decoration: none;
        }

        .profile-wrapper {
            max-width: 550px;
            margin: 30px auto;
        }

        .profile-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            border: 1px solid #e9edf5;
            box-shadow: 0 8px 30px rgba(30, 41, 59, 0.07);
        }

        /* PROFILE HEADER */

        .profile-header {
            padding: 25px 30px;
            background: linear-gradient(
                135deg,
                #2563eb,
                #4f46e5
            );
            color: white;
        }

        .avatar {
            width: 82px;
            height: 82px;
            border-radius: 50%;
            background: rgba(255,255,255,0.18);
            border: 2px solid rgba(255,255,255,0.5);

            display: flex;
            align-items: center;
            justify-content: center;

            font-size: 38px;
        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .profile-email {
            opacity: 0.85;
        }

        /* CONTENT */

        .profile-content {
            padding: 25px 30px;
        }

        .section-title {
            font-size: 15px;
            font-weight: 700;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            margin-bottom: 18px;
        }

        /* BALANCE */

        .balance-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 15px;
            padding: 22px;
            margin-bottom: 35px;
        }

        .balance-label {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 5px;
        }

        .balance {
            color: #15803d;
            font-size: 32px;
            font-weight: 700;
        }

        .wallet-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            background: #dcfce7;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #16a34a;
            font-size: 24px;
        }

        /* ACCOUNT INFO */

        .info-row {
            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 18px 0;

            border-bottom: 1px solid #edf0f5;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .info-icon {
            width: 42px;
            height: 42px;

            border-radius: 11px;

            background: #eff6ff;

            display: flex;
            align-items: center;
            justify-content: center;

            color: #2563eb;
            font-size: 19px;
        }

        .info-label {
            font-size: 13px;
            color: #64748b;
            margin-bottom: 3px;
        }

        .info-value {
            font-size: 16px;
            font-weight: 600;
            color: #172033;
        }

        /* BUTTONS */

        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        .btn-edit {
            background: #2563eb;
            color: white;
            border: none;
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 600;
        }

        .btn-edit:hover {
            background: #1d4ed8;
            color: white;
        }

        .btn-logout {
            padding: 11px 20px;
            border-radius: 10px;
            font-weight: 600;
        }

        @media (max-width: 576px) {

            .profile-wrapper {
                margin: 25px 15px;
            }

            .profile-header {
                padding: 25px;
            }

            .profile-content {
                padding: 25px;
            }

            .profile-name {
                font-size: 24px;
            }

            .info-row {
                align-items: flex-start;
            }

            .info-value {
                max-width: 190px;
                text-align: right;
                word-break: break-word;
            }

            .action-buttons {
                flex-direction: column;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar bg-white border-bottom">

    <div class="container">

        <a
            href="dashboard.php"
            class="brand"
        >
            PaySphere
        </a>

        <a
            href="dashboard.php"
            class="btn btn-outline-primary"
        >

            <i class="bi bi-arrow-left me-2"></i>

            Dashboard

        </a>

    </div>

</nav>


<!-- PROFILE -->

<div class="container">

    <div class="profile-wrapper">

        <div class="profile-card">


            <!-- PROFILE HEADER -->

            <div class="profile-header">

                <div class="d-flex align-items-center gap-4">

                    <div class="avatar">

                        <i class="bi bi-person"></i>

                    </div>

                    <div>

                        <div class="profile-name">

                            <?php
                            echo htmlspecialchars(
                                $user['full_name']
                            );
                            ?>

                        </div>

                        <div class="profile-email">

                            <?php
                            echo htmlspecialchars(
                                $user['email']
                            );
                            ?>

                        </div>

                    </div>

                </div>

            </div>


            <!-- CONTENT -->

            <div class="profile-content">


                <!-- WALLET BALANCE -->

                <div class="section-title">

                    Wallet

                </div>


                <div class="balance-card">

                    <div class="d-flex justify-content-between align-items-center">

                        <div>

                            <div class="balance-label">

                                Available Balance

                            </div>

                            <div class="balance">

                                ₹<?php
                                echo number_format(
                                    $user['balance'],
                                    2
                                );
                                ?>

                            </div>

                        </div>


                        <div class="wallet-icon">

                            <i class="bi bi-wallet2"></i>

                        </div>

                    </div>

                </div>


                <!-- ACCOUNT INFORMATION -->

                <div class="section-title">

                    Account Information

                </div>


                <!-- NAME -->

                <div class="info-row">

                    <div class="info-left">

                        <div class="info-icon">

                            <i class="bi bi-person"></i>

                        </div>

                        <div>

                            <div class="info-label">
                                Full Name
                            </div>

                            <div class="info-value">

                                <?php
                                echo htmlspecialchars(
                                    $user['full_name']
                                );
                                ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- EMAIL -->

                <div class="info-row">

                    <div class="info-left">

                        <div class="info-icon">

                            <i class="bi bi-envelope"></i>

                        </div>

                        <div>

                            <div class="info-label">
                                Email Address
                            </div>

                            <div class="info-value">

                                <?php
                                echo htmlspecialchars(
                                    $user['email']
                                );
                                ?>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- PHONE -->

<div class="info-row">

    <div class="info-left">

        <div class="info-icon">

            <i class="bi bi-telephone"></i>

        </div>

        <div>

            <div class="info-label">
                Phone Number
            </div>

            <div class="info-value">

                <?php
                echo htmlspecialchars(
                    $user['phone']
                );
                ?>

            </div>

        </div>

    </div>

</div>


                <!-- USER ID -->

                <div class="info-row">

                    <div class="info-left">

                        <div class="info-icon">

                            <i class="bi bi-fingerprint"></i>

                        </div>

                        <div>

                            <div class="info-label">
                                User ID
                            </div>

                            <div class="info-value">

                                #<?php
                                echo $user['id'];
                                ?>

                            </div>

                        </div>

                    </div>

                </div>


                <!-- ACTIONS -->

                <div class="action-buttons">

                   <button
    class="btn btn-edit"
    type="button"
    data-bs-toggle="modal"
    data-bs-target="#editProfileModal"
>

    <i class="bi bi-pencil me-2"></i>

    Edit Profile

</button>


                    <a
                        href="../logout.php"
                        class="btn btn-outline-danger btn-logout"
                    >

                        <i class="bi bi-box-arrow-right me-2"></i>

                        Logout

                    </a>

                </div>


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

            <div class="modal-header border-0">

                <div>

                    <h5 class="modal-title fw-bold">
                        Edit Profile
                    </h5>

                    <small class="text-secondary">
                        Update your account information
                    </small>

                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body px-4 pb-4">


                <?php if ($profile_success != "") { ?>

                    <div class="alert alert-success">

                        <?php
                        echo htmlspecialchars(
                            $profile_success
                        );
                        ?>

                    </div>

                <?php } ?>


                <?php if ($profile_error != "") { ?>

                    <div class="alert alert-danger">

                        <?php
                        echo htmlspecialchars(
                            $profile_error
                        );
                        ?>

                    </div>

                <?php } ?>


                <form method="POST">


                    <!-- FULL NAME -->

                    <div class="mb-3">

                        <label class="form-label fw-semibold">
                            Full Name
                        </label>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control"
                            value="<?php
                            echo htmlspecialchars(
                                $user['full_name']
                            );
                            ?>"
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
                            class="form-control bg-light"
                            value="<?php
                            echo htmlspecialchars(
                                $user['email']
                            );
                            ?>"
                            readonly
                        >

                        <small class="text-secondary">
                            Email cannot be changed.
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
                            class="form-control"
                            value="<?php
                            echo htmlspecialchars(
                                $user['phone']
                            );
                            ?>"
                            maxlength="15"
                            required
                        >

                    </div>


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
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>
</body>

</html>