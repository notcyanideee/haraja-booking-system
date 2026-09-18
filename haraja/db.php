<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$conn = mysqli_connect("localhost", "root", "", "haraja");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if (!$conn) {
    die("Connection Failed");
}
