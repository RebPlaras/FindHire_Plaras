<?php
session_start();

if (isset($_POST['logout'])) {
    // Destroy the session and redirect to login page
    session_unset();
    session_destroy();
    header("Location: index.php");  // Redirect to login page
    exit();
}

?>