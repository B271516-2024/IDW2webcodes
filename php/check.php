<?php

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in by checking the session
if (!isset($_SESSION['userid'])) {
    // If the user is not logged in, redirect them to the login page
    header("Location: ../html/index.html");
    exit();
}
?>
