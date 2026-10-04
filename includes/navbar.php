
<nav class="navbar navbar-expand-lg bg-white shadow-sm py-3 sticky-top">
    <div class="container">

        <!-- BRAND -->
        <a class="navbar-brand fw-bold fs-1 text-primary"
           href="index.php">
            PaySphere
        </a>

        <!-- MOBILE MENU -->
        <button
            class="navbar-toggler"
            type="button"
            data-bs-toggle="collapse"
            data-bs-target="#menu"
            aria-controls="menu"
            aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- NAVBAR CONTENT -->
        <div class="collapse navbar-collapse" id="menu">

            <ul class="navbar-nav mx-auto gap-lg-3">

                <li class="nav-item">
                    <a class="nav-link active fw-semibold"
                       href="index.php">
                        Home
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold"
                       href="index.php#features">
                        Features
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold"
                       href="index.php#about">
                        About
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link fw-semibold"
                       href="index.php#contact">
                        Contact
                    </a>
                </li>

            </ul>

            <!-- AUTH BUTTONS -->
            <div class="d-flex flex-column flex-lg-row gap-2 gap-lg-3 mt-3 mt-lg-0">

                <a href="login.php"
                   class="btn btn-outline-primary px-4">
                    Login
                </a>

                <a href="register.php"
                   class="btn btn-primary px-4">
                    Register
                </a>

            </div>

        </div>

    </div>
</nav>
