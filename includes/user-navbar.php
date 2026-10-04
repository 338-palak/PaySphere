
<!-- =========================
     PAYSPHERE NAVBAR
========================= -->
<?php

$current_page = basename($_SERVER['PHP_SELF']);

?>

<nav class="navbar navbar-expand-lg app-navbar sticky-top">

    <div class="app-container navbar-shell">


        <!-- BRAND -->

        <a
            href="dashboard.php"
            class="navbar-brand"
        >
            <i class="bi bi-wallet2 me-2"></i>
            PaySphere
        </a>


        <!-- MOBILE BUTTON -->

        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#mainNavbar"
            aria-controls="mainNavbar"
            aria-expanded="false"
            aria-label="Toggle navigation"
        >

            <i class="bi bi-list"></i>

        </button>


        <!-- COLLAPSIBLE AREA -->

        <div
            class="collapse navbar-collapse"
            id="mainNavbar"
        >


            <!-- LINKS -->

            <ul class="navbar-nav mx-auto">

                <li class="nav-item">

                  <a
    href="dashboard.php"
    class="nav-link <?php
        echo $current_page == 'dashboard.php'
            ? 'active'
            : '';
    ?>"
>
    Home
</a>

                </li>


                <li class="nav-item">

                   <a
    href="send-money.php"
    class="nav-link <?php
        echo $current_page == 'send-money.php'
            ? 'active'
            : '';
    ?>"
>
    Send Money
</a>

                </li>


                <li class="nav-item">

                   <a
    href="add-money.php"
    class="nav-link <?php
        echo $current_page == 'add-money.php'
            ? 'active'
            : '';
    ?>"
>
    Add Money
</a>

                </li>


                <li class="nav-item">

                  <a
    href="transactions.php"
    class="nav-link <?php
        echo $current_page == 'transactions.php'
            ? 'active'
            : '';
    ?>"
>
    Transactions
</a>

                </li>

            </ul>


            <!-- PROFILE -->

            <div class="dropdown profile-dropdown">

                <button
                    class="profile-nav-btn"
                    type="button"
                    data-bs-toggle="dropdown"
                    aria-expanded="false"
                >

                    <div class="profile-nav-avatar">

                        <?php
                        echo strtoupper(
                            substr(
                                $user['full_name'],
                                0,
                                1
                            )
                        );
                        ?>

                    </div>


                    <div class="profile-nav-info">

                        <span class="profile-nav-name">

                            <?php
                            echo htmlspecialchars(
                                $user['full_name']
                            );
                            ?>

                        </span>

                        <small>
                            My Account
                        </small>

                    </div>


                    <i class="bi bi-chevron-down ms-auto"></i>

                </button>


                <ul class="dropdown-menu dropdown-menu-end profile-menu">

                    <li class="px-3 py-3">

                        <div class="fw-semibold">
                            <?php echo htmlspecialchars($user['full_name']); ?>
                        </div>

                        <small class="text-secondary">
                            <?php echo htmlspecialchars($user['email']); ?>
                        </small>

                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                      <a
    href="dashboard.php?open=profile"
    class="dropdown-item"
>
    <i class="bi bi-person me-2"></i>
    My Profile
</a>

                    </li>


                    <li>

                     <a
    href="dashboard.php?open=edit-profile"
    class="dropdown-item"
>
    <i class="bi bi-pencil-square me-2"></i>
    Edit Profile
</a>

                    </li>


                    <li>

                      <a
    href="dashboard.php?open=change-password"
    class="dropdown-item"
>
    <i class="bi bi-shield-lock me-2"></i>
    Change Password
</a>

                    </li>


                    <li>
                        <hr class="dropdown-divider">
                    </li>


                    <li>

                        <a
                            href="../logout.php"
                            class="dropdown-item text-danger"
                        >
                            <i class="bi bi-box-arrow-right me-2"></i>
                            Logout
                        </a>

                    </li>

                </ul>

            </div>

        </div>

    </div>

</nav>

