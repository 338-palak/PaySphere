<?php

require_once "includes/db.php";

$error = "";
$success = "";

if (isset($_POST['register'])) {

    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];


    // Basic validation

    if (
        $full_name == "" ||
        $email == "" ||
        $phone == "" ||
        $password == ""
    ) {

        $error = "Please fill in all fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (!preg_match('/^[0-9]{10}$/', $phone)) {

        $error = "Phone number must contain exactly 10 digits.";

    } elseif (strlen($password) < 8) {

        $error = "Password must be at least 8 characters.";

    } else {

        $hashed_password =
            password_hash(
                $password,
                PASSWORD_DEFAULT
            );


        $sql = "INSERT INTO users
                (
                    full_name,
                    email,
                    phone,
                    password
                )
                VALUES (?, ?, ?, ?)";


        $stmt = mysqli_prepare(
            $conn,
            $sql
        );


        mysqli_stmt_bind_param(
            $stmt,
            "ssss",
            $full_name,
            $email,
            $phone,
            $hashed_password
        );


      if (mysqli_stmt_execute($stmt)) {

    header(
        "Location: login.php?registered=1"
    );

    exit();

} else {

            if (
                mysqli_stmt_errno($stmt) == 1062
            ) {

                $error =
                    "An account with this email or phone number already exists.";

            } else {

                $error =
                    "Registration failed. Please try again.";

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

    <title>Register | PaySphere</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css"
>

<link
    rel="stylesheet"
    href="css/app.css"
>

</head>

<body class="auth-body">

<div class="auth-page">


    <!-- =========================
         LEFT SIDE
    ========================== -->

    <section class="auth-brand-panel">

        <a
            href="index.php"
            class="auth-brand"
        >

            <i class="bi bi-wallet2"></i>

            PaySphere

        </a>


        <div class="auth-brand-content">

            <div class="auth-small-badge">

                <i class="bi bi-person-check"></i>

                Create your wallet

            </div>


            <h1>

                Your money.
                <span>Your PaySphere.</span>

            </h1>


            <p>

                Create your account and start managing
                transfers, wallet funds and transaction
                history from one place.

            </p>


            <div class="auth-features">


                <div>

                    <span>
                        <i class="bi bi-person-plus"></i>
                    </span>

                    <div>

                        <strong>
                            Simple account setup
                        </strong>

                        <small>
                            Create your wallet in just a few steps.
                        </small>

                    </div>

                </div>


                <div>

                    <span>
                        <i class="bi bi-send"></i>
                    </span>

                    <div>

                        <strong>
                            Send money easily
                        </strong>

                        <small>
                            Transfer money to registered users.
                        </small>

                    </div>

                </div>


                <div>

                    <span>
                        <i class="bi bi-shield-check"></i>
                    </span>

                    <div>

                        <strong>
                            Secure account
                        </strong>

                        <small>
                            Your password is securely hashed.
                        </small>

                    </div>

                </div>


            </div>

        </div>

    </section>



    <!-- =========================
         RIGHT SIDE
    ========================== -->

    <section class="auth-form-panel register-form-panel">

        <div class="auth-form-wrapper register-form-wrapper">


            <!-- MOBILE LOGO -->

            <a
                href="index.php"
                class="auth-mobile-brand"
            >

                <i class="bi bi-wallet2"></i>

                PaySphere

            </a>


            <!-- HEADING -->

            <div class="auth-heading register-heading">

                <div class="auth-login-icon">

                    <i class="bi bi-person-plus"></i>

                </div>

                <h2>
                    Create account
                </h2>

                <p>
                    Create your PaySphere wallet account.
                </p>

            </div>


            <!-- ERROR -->

            <?php if ($error != "") { ?>

                <div class="alert alert-danger auth-alert">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php } ?>


            <!-- SUCCESS -->

            <?php if ($success != "") { ?>

                <div class="alert alert-success auth-alert">

                    <i class="bi bi-check-circle me-2"></i>

                    <?php
                    echo htmlspecialchars($success);
                    ?>

                </div>

            <?php } ?>


            <form method="POST">


                <!-- FULL NAME -->

                <div class="mb-3">

                    <label class="form-label auth-label">
                        Full Name
                    </label>

                    <div class="auth-input-wrap">

                        <i class="bi bi-person"></i>

                        <input
                            type="text"
                            name="full_name"
                            class="form-control auth-input"
                            placeholder="Enter your full name"
                            value="<?php
                                echo htmlspecialchars(
                                    $_POST['full_name'] ?? ''
                                );
                            ?>"
                            required
                        >

                    </div>

                </div>


                <!-- EMAIL -->

                <div class="mb-3">

                    <label class="form-label auth-label">
                        Email Address
                    </label>

                    <div class="auth-input-wrap">

                        <i class="bi bi-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            class="form-control auth-input"
                            placeholder="name@example.com"
                            value="<?php
                                echo htmlspecialchars(
                                    $_POST['email'] ?? ''
                                );
                            ?>"
                            required
                        >

                    </div>

                </div>


                <!-- PHONE -->

                <div class="mb-3">

                    <label class="form-label auth-label">
                        Phone Number
                    </label>

                    <div class="auth-input-wrap">

                        <i class="bi bi-telephone"></i>

                        <input
                            type="text"
                            name="phone"
                            class="form-control auth-input"
                            placeholder="Enter your phone number"
                            maxlength="15"
                            value="<?php
                                echo htmlspecialchars(
                                    $_POST['phone'] ?? ''
                                );
                            ?>"
                            required
                        >

                    </div>

                </div>


                <!-- PASSWORD -->

                <div class="mb-3">

                    <label class="form-label auth-label">
                        Password
                    </label>

                    <div class="auth-input-wrap">

                        <i class="bi bi-lock"></i>

                        <input
                            type="password"
                            name="password"
                            id="registerPassword"
                            class="form-control auth-input auth-password-input"
                            placeholder="Minimum 8 characters"
                            minlength="8"
                            required
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="registerPasswordToggle"
                        >

                            <i
                                class="bi bi-eye"
                                id="registerPasswordIcon"
                            ></i>

                        </button>

                    </div>

                </div>


                <!-- PASSWORD INFO -->

                <div class="register-password-info">

                    <i class="bi bi-info-circle"></i>

                    Use at least 8 characters for your password.

                </div>


                <!-- REGISTER BUTTON -->

                <button
                    type="submit"
                    name="register"
                    class="btn btn-paysphere auth-submit"
                >

                    Create Account

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>


                <!-- LOGIN LINK -->

                <p class="auth-switch-text">

                    Already have an account?

                    <a href="login.php">
                        Login
                    </a>

                </p>


            </form>

        </div>

    </section>


</div>


<script>

    const registerPassword =
        document.getElementById(
            'registerPassword'
        );

    const registerPasswordToggle =
        document.getElementById(
            'registerPasswordToggle'
        );

    const registerPasswordIcon =
        document.getElementById(
            'registerPasswordIcon'
        );


    registerPasswordToggle.addEventListener(
        'click',
        function() {

            if (
                registerPassword.type === 'password'
            ) {

                registerPassword.type = 'text';

                registerPasswordIcon.classList.remove(
                    'bi-eye'
                );

                registerPasswordIcon.classList.add(
                    'bi-eye-slash'
                );

            } else {

                registerPassword.type = 'password';

                registerPasswordIcon.classList.remove(
                    'bi-eye-slash'
                );

                registerPasswordIcon.classList.add(
                    'bi-eye'
                );

            }

        }
    );

</script>

</body>
</html>