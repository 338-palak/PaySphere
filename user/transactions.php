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


// -----------------------------------------
// SEARCH, FILTER AND PAGINATION
// -----------------------------------------

$search = trim($_GET['search'] ?? '');

$type_filter = $_GET['type'] ?? 'ALL';

$status_filter = $_GET['status'] ?? 'ALL';

$page = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$limit = 5;


// Allow only valid transaction types

$allowed_types = [
    'ALL',
    'TRANSFER',
    'ADD_MONEY'
];

if (!in_array($type_filter, $allowed_types)) {
    $type_filter = 'ALL';
}


// Allow only valid statuses

$allowed_statuses = [
    'ALL',
    'SUCCESS',
    'PENDING',
    'FAILED'
];

if (!in_array($status_filter, $allowed_statuses)) {
    $status_filter = 'ALL';
}


$search_like = "%" . $search . "%";


// -----------------------------------------
// COUNT TRANSACTIONS
// -----------------------------------------

$count_sql = "SELECT COUNT(*) AS total

              FROM transactions t

              JOIN users sender
                  ON t.sender_id = sender.id

              JOIN users receiver
                  ON t.receiver_id = receiver.id

              WHERE
                  (t.sender_id = ? OR t.receiver_id = ?)

              AND
                  (
                      ? = ''
                      OR t.reference_id LIKE ?
                      OR sender.full_name LIKE ?
                      OR receiver.full_name LIKE ?
                  )

              AND
                  (
                      ? = 'ALL'
                      OR t.type = ?
                  )

              AND
                  (
                      ? = 'ALL'
                      OR t.status = ?
                  )";


$count_stmt = mysqli_prepare(
    $conn,
    $count_sql
);


mysqli_stmt_bind_param(
    $count_stmt,
    "iissssssss",

    $user_id,
    $user_id,

    $search,
    $search_like,
    $search_like,
    $search_like,

    $type_filter,
    $type_filter,

    $status_filter,
    $status_filter
);


mysqli_stmt_execute($count_stmt);

$count_result =
    mysqli_stmt_get_result($count_stmt);

$count_row =
    mysqli_fetch_assoc($count_result);

$total_transactions =
    $count_row['total'];

$total_pages =
    max(
        1,
        ceil(
            $total_transactions / $limit
        )
    );


// Prevent invalid page numbers

if ($page > $total_pages) {
    $page = $total_pages;
}

$offset =
    ($page - 1) * $limit;


// -----------------------------------------
// GET TRANSACTIONS
// -----------------------------------------

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

        JOIN users sender
            ON t.sender_id = sender.id

        JOIN users receiver
            ON t.receiver_id = receiver.id

        WHERE
            (t.sender_id = ? OR t.receiver_id = ?)

        AND
            (
                ? = ''
                OR t.reference_id LIKE ?
                OR sender.full_name LIKE ?
                OR receiver.full_name LIKE ?
            )

        AND
            (
                ? = 'ALL'
                OR t.type = ?
            )

        AND
            (
                ? = 'ALL'
                OR t.status = ?
            )

        ORDER BY t.created_at DESC

        LIMIT ?
        OFFSET ?";


$stmt = mysqli_prepare(
    $conn,
    $sql
);


mysqli_stmt_bind_param(
    $stmt,
    "iissssssssii",

    $user_id,
    $user_id,

    $search,
    $search_like,
    $search_like,
    $search_like,

    $type_filter,
    $type_filter,

    $status_filter,
    $status_filter,

    $limit,
    $offset
);


mysqli_stmt_execute($stmt);

$transactions =
    mysqli_stmt_get_result($stmt);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Transactions | PaySphere</title>

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


<!-- MAIN CONTENT -->
<!-- =========================
     TRANSACTIONS PAGE
========================= -->

<main class="app-container transactions-page">

    <div class="send-page-header">

        <div class="transactions-page-icon">

            <i class="bi bi-clock-history"></i>

        </div>

        <div>

            <h1>
                Transactions
            </h1>

            <p>
                Search, filter and review your PaySphere activity.
            </p>

        </div>

    </div>

<!-- =========================
     SEARCH AND FILTER
========================= -->

<div class="app-card transaction-filter-card">

    <form
        method="GET"
        class="row g-3 align-items-end"
    >

        <!-- SEARCH -->

        <div class="col-lg-5">

            <label class="form-label send-label">
                Search
            </label>

            <div class="transaction-search">

                <i class="bi bi-search"></i>

                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Reference ID or user name"
                    value="<?php
                        echo htmlspecialchars($search);
                    ?>"
                >

            </div>

        </div>


        <!-- TYPE -->

        <div class="col-lg-2 col-md-4">

            <label class="form-label send-label">
                Type
            </label>

            <select
                name="type"
                class="form-select"
            >

                <option
                    value="ALL"
                    <?php
                    if ($type_filter == 'ALL') {
                        echo 'selected';
                    }
                    ?>
                >
                    All
                </option>

                <option
                    value="TRANSFER"
                    <?php
                    if ($type_filter == 'TRANSFER') {
                        echo 'selected';
                    }
                    ?>
                >
                    Transfer
                </option>

                <option
                    value="ADD_MONEY"
                    <?php
                    if ($type_filter == 'ADD_MONEY') {
                        echo 'selected';
                    }
                    ?>
                >
                    Add Money
                </option>

            </select>

        </div>


        <!-- STATUS -->

        <div class="col-lg-2 col-md-4">

            <label class="form-label send-label">
                Status
            </label>

            <select
                name="status"
                class="form-select"
            >

                <option
                    value="ALL"
                    <?php
                    if ($status_filter == 'ALL') {
                        echo 'selected';
                    }
                    ?>
                >
                    All
                </option>

                <option
                    value="SUCCESS"
                    <?php
                    if ($status_filter == 'SUCCESS') {
                        echo 'selected';
                    }
                    ?>
                >
                    Success
                </option>

                <option
                    value="PENDING"
                    <?php
                    if ($status_filter == 'PENDING') {
                        echo 'selected';
                    }
                    ?>
                >
                    Pending
                </option>

                <option
                    value="FAILED"
                    <?php
                    if ($status_filter == 'FAILED') {
                        echo 'selected';
                    }
                    ?>
                >
                    Failed
                </option>

            </select>

        </div>


        <!-- BUTTONS -->

        <div class="col-lg-3 col-md-4">

            <div class="d-flex gap-2">

                <button
                    type="submit"
                    class="btn btn-paysphere flex-grow-1"
                >

                    <i class="bi bi-funnel me-1"></i>

                    Apply

                </button>


                <a
                    href="transactions.php"
                    class="transaction-reset-btn"
                >

                    Reset

                </a>

            </div>

        </div>

    </form>

</div>


    <!-- TRANSACTIONS CARD -->

   <div class="app-card transaction-history-card">

    <div class="transaction-history-body">
          <div class="transaction-list-header">

    <div>

        <h5>
            Transaction History
        </h5>

        <p>
            Your latest wallet activity.
        </p>

    </div> 

   <span class="transaction-result-count">

        <?php
        echo $total_transactions;
        ?>

        result<?php
        echo $total_transactions == 1
            ? ''
            : 's';
        ?>

    </span>

</div>


            <?php if (mysqli_num_rows($transactions) > 0) { ?>


                <?php while ($transaction = mysqli_fetch_assoc($transactions)) { ?>


                  <?php

$is_add_money =
    ($transaction['type'] == 'ADD_MONEY');

$is_sender =
    ($transaction['sender_id'] == $user_id);


if ($is_add_money) {

    $detail_title = "Money Added";

    $detail_person_label = "Details";

    $detail_person = "Wallet Top Up";

    $detail_sign = "+";

} elseif ($is_sender) {

    $detail_title = "Money Sent";

    $detail_person_label = "To";

    $detail_person =
        $transaction['receiver_name'];

    $detail_sign = "-";

} else {

    $detail_title = "Money Received";

    $detail_person_label = "From";

    $detail_person =
        $transaction['sender_name'];

    $detail_sign = "+";
}

?>

<div class="transaction-history-row">

    <!-- LEFT SIDE -->

    <div class="d-flex align-items-center gap-3">

        <?php if ($is_add_money) { ?>

            <!-- MONEY ADDED -->

          <div class="transaction-type-icon transaction-added">

    <i class="bi bi-plus-lg"></i>

</div>

            <div>

              <h6 class="transaction-name">
    Money Added
</h6>

<p class="transaction-description">
    Added to your wallet
</p>

<small class="transaction-reference">
    Ref:
    <?php
    echo htmlspecialchars(
        $transaction['reference_id']
    );
    ?>
</small>
  

            </div>


        <?php } elseif ($is_sender) { ?>

            <!-- MONEY SENT -->

           <div class="transaction-type-icon transaction-sent">

    <i class="bi bi-arrow-up-right"></i>

</div>

            <div>

              <h6 class="transaction-name">
    Money Sent
</h6>

<p class="transaction-description">

    To
    <?php
    echo htmlspecialchars(
        $transaction['receiver_name']
    );
    ?>

</p>

<small class="transaction-reference">
    Ref:
    <?php
    echo htmlspecialchars(
        $transaction['reference_id']
    );
    ?>
</small>

             

            </div>


        <?php } else { ?>

            <!-- MONEY RECEIVED -->

            <div class="transaction-type-icon transaction-received">

    <i class="bi bi-arrow-down-left"></i>

</div>

            <div>

              <h6 class="transaction-name">
                    Money Received
                </h6>

               <p class="transaction-description">

                    From
                    <?php
                    echo htmlspecialchars(
                        $transaction['sender_name']
                    );
                    ?>

                </p>
                <small class="transaction-reference">
    Ref:
    <?php
    echo htmlspecialchars(
        $transaction['reference_id']
    );
    ?>
</small>

            </div>

        <?php } ?>

    </div>


    <!-- RIGHT SIDE -->

    <div class="text-end">

        <?php if ($is_add_money) { ?>

       <h5 class="transaction-amount transaction-positive">

                +
                ₹<?php
                echo number_format(
                    $transaction['amount'],
                    2
                );
                ?>

            </h5>


        <?php } elseif ($is_sender) { ?>

           <h5 class="transaction-amount transaction-negative">
                -
                ₹<?php
                echo number_format(
                    $transaction['amount'],
                    2
                );
                ?>

            </h5>


        <?php } else { ?>

          <h5 class="transaction-amount transaction-positive">

                +
                ₹<?php
                echo number_format(
                    $transaction['amount'],
                    2
                );
                ?>

            </h5>

        <?php } ?>


      <small class="transaction-date">

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
 class="transaction-details-btn view-transaction-btn"

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

  <i class="bi bi-eye me-1"></i>
Details
</button>

    </div>

</div>
            


                <?php } ?>


            <?php } else { ?>


                <!-- NO TRANSACTIONS -->

              <div class="transaction-empty-state">

    <div class="transaction-empty-icon">

        <i class="bi bi-receipt"></i>

    </div>

    <h5>
        No transactions found
    </h5>

    <p>
        Try changing your search or filters.
    </p>

</div>


        <?php } ?>


<!-- PAGINATION -->

<?php if ($total_pages > 1) { ?>

    <nav class="mt-4">

        <ul class="pagination justify-content-center mb-0">


            <!-- PREVIOUS -->

            <li class="page-item <?php
                if ($page <= 1) {
                    echo 'disabled';
                }
            ?>">

                <a
                    class="page-link"
                    href="?<?php

                    echo http_build_query([
                        'search' => $search,
                        'type' => $type_filter,
                        'status' => $status_filter,
                        'page' => $page - 1
                    ]);

                    ?>"
                >
                    Previous
                </a>

            </li>


            <!-- PAGE NUMBERS -->

            <?php
            for ($i = 1; $i <= $total_pages; $i++) {
            ?>

                <li class="page-item <?php
                    if ($i == $page) {
                        echo 'active';
                    }
                ?>">

                    <a
                        class="page-link"
                        href="?<?php

                        echo http_build_query([
                            'search' => $search,
                            'type' => $type_filter,
                            'status' => $status_filter,
                            'page' => $i
                        ]);

                        ?>"
                    >

                        <?php echo $i; ?>

                    </a>

                </li>

            <?php } ?>


            <!-- NEXT -->

            <li class="page-item <?php
                if ($page >= $total_pages) {
                    echo 'disabled';
                }
            ?>">

                <a
                    class="page-link"
                    href="?<?php

                    echo http_build_query([
                        'search' => $search,
                        'type' => $type_filter,
                        'status' => $status_filter,
                        'page' => $page + 1
                    ]);

                    ?>"
                >
                    Next
                </a>

            </li>


        </ul>

    </nav>

<?php } ?>


</div>

</div>


</main>
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


                <!-- STATUS AREA -->

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


                <!-- DETAILS -->

                <div class="border rounded-4 overflow-hidden">


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
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js">
</script>
<script>

document
    .querySelectorAll(
        '.view-transaction-btn'
    )
    .forEach(function(button) {

        button.addEventListener(
            'click',
            function() {


                // Transaction title

                document.getElementById(
                    'transactionTitle'
                ).textContent =
                    this.dataset.title;


                // Amount

                document.getElementById(
                    'transactionAmount'
                ).textContent =
                    this.dataset.sign +
                    " ₹" +
                    this.dataset.amount;


                // Status

                document.getElementById(
                    'transactionStatus'
                ).textContent =
                    this.dataset.status;


                // Reference ID

                document.getElementById(
                    'transactionReference'
                ).textContent =
                    this.dataset.reference;


                // Type

                document.getElementById(
                    'transactionType'
                ).textContent =
                    this.dataset.type;


                // To / From

                document.getElementById(
                    'transactionPersonLabel'
                ).textContent =
                    this.dataset.personLabel;


                document.getElementById(
                    'transactionPerson'
                ).textContent =
                    this.dataset.person;


                // Date

                document.getElementById(
                    'transactionDate'
                ).textContent =
                    this.dataset.date;

            }
        );

    });

</script>
</body>

</html>