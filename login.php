<?php

session_start();

require_once "includes/db.php";

if (isset($_POST['login'])) {

    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Find user by email
    $sql = "SELECT * FROM users WHERE email = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        // Check password
        if (password_verify($password, $user['password'])) {
            session_regenerate_id(true);

            // Store user information in session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];

            // Go to dashboard
            header("Location: user/dashboard.php");
            exit();

        } else {

            $error = "Invalid password.";

        }

    } else {

        $error = "No account found with this email.";

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | PaySphere</title>

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
    href="css/app.css"
>

</head>

<body class="auth-body">

<!-- =========================
     LOGIN PAGE
========================= -->

<div class="auth-page">

    <!-- LEFT SIDE -->

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

                <i class="bi bi-shield-check"></i>

                Secure Digital Wallet

            </div>


            <h1>

                Manage your money
                <span>smarter.</span>

            </h1>


            <p>

                Send money, add funds and track
                every transaction from one simple
                PaySphere account.

            </p>


            <div class="auth-features">

                <div>

                    <span>
                        <i class="bi bi-send"></i>
                    </span>

                    <div>

                        <strong>
                            Fast transfers
                        </strong>

                        <small>
                            Send money to registered PaySphere users.
                        </small>

                    </div>

                </div>


                <div>

                    <span>
                        <i class="bi bi-shield-lock"></i>
                    </span>

                    <div>

                        <strong>
                            Secure account
                        </strong>

                        <small>
                            Passwords are securely hashed.
                        </small>

                    </div>

                </div>


                <div>

                    <span>
                        <i class="bi bi-clock-history"></i>
                    </span>

                    <div>

                        <strong>
                            Transaction history
                        </strong>

                        <small>
                            Track your wallet activity anytime.
                        </small>

                    </div>

                </div>

            </div>

        </div>

    </section>


    <!-- RIGHT SIDE -->

    <section class="auth-form-panel">

        <div class="auth-form-wrapper">

            <!-- MOBILE LOGO -->

            <a
                href="index.php"
                class="auth-mobile-brand"
            >

                <i class="bi bi-wallet2"></i>

                PaySphere

            </a>


            <div class="auth-heading">

                <div class="auth-login-icon">

                    <i class="bi bi-person"></i>

                </div>

                <h2>
                    Welcome back
                </h2>

                <p>
                    Sign in to continue to your PaySphere wallet.
                </p>

            </div>


            <!-- ERROR -->
             <?php if (isset($_GET['registered'])) { ?>

    <div class="alert alert-success auth-alert">

        <i class="bi bi-check-circle me-2"></i>

        Account created successfully!
        Please login.

    </div>

<?php } ?>

            <?php if (isset($error)) { ?>

                <div class="alert alert-danger auth-alert">

                    <i class="bi bi-exclamation-circle me-2"></i>

                    <?php
                    echo htmlspecialchars($error);
                    ?>

                </div>

            <?php } ?>


            <!-- LOGIN FORM -->

            <form method="POST">


                <!-- EMAIL -->

                <div class="mb-4">

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
                            id="loginPassword"
                            class="form-control auth-input auth-password-input"
                            placeholder="Enter your password"
                            required
                        >


                        <button
                            type="button"
                            class="password-toggle"
                            id="passwordToggle"
                        >

                            <i
                                class="bi bi-eye"
                                id="passwordToggleIcon"
                            ></i>

                        </button>

                    </div>

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    name="login"
                    class="btn btn-paysphere auth-submit"
                >

                    Login

                    <i class="bi bi-arrow-right ms-2"></i>

                </button>


                <!-- REGISTER -->

                <p class="auth-switch-text">

                    Don't have an account?

                    <a href="register.php">
                        Create account
                    </a>

                </p>


            </form>

        </div>

    </section>

</div>
<script>

    const passwordInput =
        document.getElementById(
            'loginPassword'
        );

    const passwordToggle =
        document.getElementById(
            'passwordToggle'
        );

    const passwordToggleIcon =
        document.getElementById(
            'passwordToggleIcon'
        );


    passwordToggle.addEventListener(
        'click',
        function() {

            if (
                passwordInput.type === 'password'
            ) {

                passwordInput.type = 'text';

                passwordToggleIcon.classList.remove(
                    'bi-eye'
                );

                passwordToggleIcon.classList.add(
                    'bi-eye-slash'
                );

            } else {

                passwordInput.type = 'password';

                passwordToggleIcon.classList.remove(
                    'bi-eye-slash'
                );

                passwordToggleIcon.classList.add(
                    'bi-eye'
                );

            }

        }
    );

</script>

</body>

</html>