<?php

// DB connection
// require_once('./config.php');
require(dirname(__DIR__) . "/bootstrap.php");

// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

?>