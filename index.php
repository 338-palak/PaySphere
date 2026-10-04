
<?php
include "includes/header.php";
include "includes/navbar.php";
?>

<!-- HERO SECTION -->
<section class="py-4 py-md-5 bg-light">

    <div class="container">

      <div class="row align-items-center g-3 g-lg-5">

            <!-- LEFT SIDE -->
            <div class="col-12 col-lg-6">

                <span class="badge bg-primary mb-3 px-3 py-2">
                    Digital Wallet Simulation
                </span>

              <h1 class="fw-bold lh-sm mb-3"
    style="font-size:clamp(2rem, 5vw, 4.5rem);">

                    Move Money.
                    <br>

                    <span class="text-primary">
                        Move Forward.
                    </span>

                </h1>

                <p class="lead text-secondary mt-3 mb-4">
                    A simple way to manage your digital wallet,
                    send money to registered users, and track
                    your transactions.
                </p>

                <!-- BUTTONS -->
               <div class="d-grid d-sm-flex gap-2 gap-md-3">

                    <a href="register.php"
                       class="btn btn-primary  px-4 py-2 fw-semibold">
                        Get Started
                    </a>

                    <a href="#features"
                       class="btn btn-outline-primary  px-4 py-2 fw-semibold">
                        Learn More
                    </a>

                </div>

            </div>

            <!-- RIGHT SIDE -->
            <div class="col-12 col-lg-6">

                <div class="card shadow-lg border-0 rounded-4 p-3 p-sm-4 mx-auto"
                     style="width:100%;max-width:370px;">

                    <div class="card bg-primary text-white border-0 rounded-4 p-3 p-sm-4 text-center">

                        <small>Illustrative Wallet Balance</small>

                        <h2 class="fw-bold mt-3 mb-2">
                            ₹24,580
                        </h2>

                        <small>Demo preview</small>

                    </div>

                    <div class="row row-cols-3 mt-4 text-center g-2">

                        <div class="col">
                            <i class="bi bi-send fs-3 text-primary"></i>
                            <p class="mt-2 mb-0 small">Send</p>
                        </div>

                        <div class="col">
                            <i class="bi bi-wallet2 fs-3 text-primary"></i>
                            <p class="mt-2 mb-0 small">Wallet</p>
                        </div>

                        <div class="col">
                            <i class="bi bi-clock-history fs-3 text-primary"></i>
                            <p class="mt-2 mb-0 small">History</p>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- FEATURES SECTION -->
<section id="features" class="py-5">

    <div class="container">

        <div class="text-center mb-4 mb-md-5">

            <h2 class="fw-bold fs-2">
                Why Choose PaySphere?
            </h2>

            <p class="text-secondary">
                Explore the features of your digital wallet.
            </p>

        </div>

        <div class="row g-4">

            <div class="col-12 col-md-4">

                <div class="card h-100 shadow-sm border-0 text-center p-4 rounded-4">

                    <div class="display-5 mb-3">💸</div>

                    <h4 class="fw-semibold">Wallet Transfers</h4>

                    <p class="text-secondary mb-0">
                        Transfer wallet balance between registered
                        PaySphere users.
                    </p>

                </div>

            </div>

            <div class="col-12 col-md-4">

                <div class="card h-100 shadow-sm border-0 text-center p-4 rounded-4">

                    <div class="display-5 mb-3">🔒</div>

                    <h4 class="fw-semibold">Account Security</h4>

                    <p class="text-secondary mb-0">
                        Password hashing, session authentication,
                        and protected form submissions.
                    </p>

                </div>

            </div>

            <div class="col-12 col-md-4">

                <div class="card h-100 shadow-sm border-0 text-center p-4 rounded-4">

                    <div class="display-5 mb-3">📊</div>

                    <h4 class="fw-semibold">Transaction History</h4>

                    <p class="text-secondary mb-0">
                        Search, filter, and review your
                        wallet transactions.
                    </p>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ABOUT SECTION -->
<section id="about" class="py-5 bg-white">

    <div class="container">

        <div class="row align-items-center g-4 g-lg-5">

            <!-- IMAGE -->
            <div class="col-12 col-lg-6 text-center">

                <img
                    src="assets/about.png"
                    class="img-fluid rounded-4 shadow"
                    style="max-width:100%;width:400px;"
                    alt="About PaySphere"
                >

            </div>

            <!-- CONTENT -->
            <div class="col-12 col-lg-6">

                <h2 class="fs-2 fw-bold mb-3">
                    About PaySphere
                </h2>

                <p class="lead text-secondary">
                    PaySphere is a digital wallet simulation
                    that allows registered users to manage
                    wallet balances and transfer funds.
                </p>

                <p class="text-secondary">
                    Built using PHP, MySQL, and Bootstrap,
                    PaySphere demonstrates authentication,
                    database transactions, and responsive
                    full-stack development.
                </p>

                <a href="register.php"
                   class="btn btn-primary mt-2 px-4">
                    Get Started
                </a>

            </div>

        </div>

    </div>

</section>

<!-- CONTACT SECTION -->
<section id="contact" class="py-5 bg-light">

    <div class="container">

        <div class="text-center mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-2 mb-3">
                Get In Touch
            </span>

            <h2 class="fw-bold display-6">
                Contact Us
            </h2>

            <p class="text-secondary">
                Have a question about PaySphere?
                We'd love to hear from you.
            </p>
        </div>

        <div class="row justify-content-center g-4">

            <!-- CONTACT INFORMATION -->
            <div class="col-12 col-lg-5">

                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">

                        <div class="bg-primary-subtle text-primary rounded-3 d-inline-flex align-items-center justify-content-center mb-4"
                             style="width:55px;height:55px;">
                            <i class="bi bi-chat-dots fs-3"></i>
                        </div>

                        <h4 class="fw-bold mb-3">
                            Let's Connect
                        </h4>

                        <p class="text-secondary mb-4">
                            PaySphere is a digital wallet
                            simulation developed as a
                            full-stack portfolio project.
                            You can contact the developer
                            through the links below.
                        </p>


<!-- EMAIL -->
<div class="d-flex align-items-center gap-3 mb-3">

    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary flex-shrink-0"
         style="width:48px;height:48px;">

        <i class="bi bi-envelope fs-5"></i>

    </div>

    <div class="text-start">
        <small class="text-secondary d-block">
            Email
        </small>

        <a href="mailto:palakmehra969@gmail.com"
           class="fw-semibold text-primary text-decoration-none">
            YOUR_EMAIL
        </a>
    </div>

</div>

<!-- GITHUB -->
<div class="d-flex align-items-center gap-3 mb-4">

    <div class="d-flex align-items-center justify-content-center rounded-3 bg-primary-subtle text-primary flex-shrink-0"
         style="width:48px;height:48px;">

        <i class="bi bi-github fs-5"></i>

    </div>

    <div class="text-start">
        <small class="text-secondary d-block">
            GitHub
        </small>

        <a href="https://github.com/338-palak"
           target="_blank"
           rel="noopener noreferrer"
           class="fw-semibold text-primary text-decoration-none">
            View GitHub Profile
        </a>
    </div>

</div>

                            </div>
                        </div>

                        <div class="alert alert-primary border-0 rounded-3 mb-0">
                            <i class="bi bi-info-circle me-2"></i>
                            Educational project — no real
                            financial transactions are processed.
                        </div>

                    </div>
                </div>

            </div>

            <!-- CONTACT ACTIONS -->
            <div class="col-12 col-lg-5">

                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4 p-md-5">

                        <h4 class="fw-bold mb-3">
                            Have a Question?
                        </h4>

                        <p class="text-secondary mb-4">
                            Want to know more about how
                            PaySphere works or its features?
                            Explore the project or get in
                            touch with its developer.
                        </p>

                        <div class="d-grid gap-3">

                            <a href="mailto:palakmehra969@gmail.com"
                               class="btn btn-primary py-3">
                                <i class="bi bi-envelope me-2"></i>
                                Send an Email
                            </a>

                            <a href="#features"
                               class="btn btn-outline-primary py-3">
                                <i class="bi bi-grid me-2"></i>
                                Explore Features
                            </a>

                        </div>

                        <hr class="my-4">

                        <p class="small text-secondary mb-0">
                            This is a demonstration application.
                            Please do not share sensitive banking
                            information or passwords.
                        </p>

                    </div>
                </div>

            </div>

        </div>

    </div>

</section>

<?php
include "includes/footer.php";
?>
