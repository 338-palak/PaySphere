<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "paysphere"
);

if (!$conn) {
    die("Database Connection Failed");
}

?>