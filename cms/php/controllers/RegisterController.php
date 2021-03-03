<?php


session_start();
// Include config file


if (!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true) {
    header("location: index.php");
    exit;
}

require(dirname(__DIR__) . "/bootstrap.php");
// Define variables and initialize with empty values
$username = $password = $confirm_password = "";
$username_err = $password_err = $confirm_password_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Validate username
    if (empty(htmlspecialchars(trim($_POST["username"])))) {
        $username_err = "Please enter a username.";
    } else {
        // Prepare a select statement
        $sql = "SELECT id FROM users WHERE username = :username";

        if ($stmt = $pdo->prepare($sql)) {
            // prepare valiues
            $test_username = htmlspecialchars(trim($_POST["username"]));
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':username', $test_username);
            // Attempt to execute the prepared statement

            if ($stmt->execute()) {
                /* store result */
                $result = $stmt->fetchAll();

                if (count($result) == 1) {
                    $username_err = "This username is already taken.";
                } else {
                    $username = $test_username;
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
    }

    // Validate password
    if (empty(htmlspecialchars(trim($_POST["password"])))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(htmlspecialchars(trim($_POST["password"]))) < 6) {
        $password_err = "Password must have atleast 6 characters.";
    } else {
        $password = htmlspecialchars(trim($_POST["password"]));
    }

    // Validate confirm password
    if (empty(htmlspecialchars(trim($_POST["confirm_password"])))) {
        $confirm_password_err = "Please confirm password.";
    } else {
        $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
        if (empty($password_err) && ($password != $confirm_password)) {
            $confirm_password_err = "Password did not match.";
        }
    }

    // Check input errors before inserting in database
    if (empty($username_err) && empty($password_err) && empty($confirm_password_err)) {

        // Prepare an insert statement
        $sql = "INSERT INTO users (username, password) VALUES (:username, :password)";

        if ($stmt = $pdo->prepare($sql)) {
            // Set parameters
            // $param_username = $username;
            $password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
            // Bind variables to the prepared statement as parameters
            $stmt -> bindParam(':username', $username);
            $stmt -> bindParam(':password', $password);


            // Attempt to execute the prepared statement
            if ($stmt->execute()) {
                // Redirect to login page
                header("location: index.php");
            } else {
                echo "Something went wrong. Please try again later.";
            }
        }
    }
}

?>