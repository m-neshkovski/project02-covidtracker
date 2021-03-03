<?php

// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: index.php");
    exit;
}
 
// Include config file
require(dirname(__DIR__) . "/bootstrap.php");
 
// Define variables and initialize with empty values
$new_password = $confirm_password = "";
$new_password_err = $confirm_password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Validate new password
    if(empty(htmlspecialchars(trim($_POST["new_password"])))){
        $new_password_err = "Please enter the new password.";     
    } elseif(strlen(htmlspecialchars(trim($_POST["new_password"]))) < 6){
        $new_password_err = "Password must have atleast 6 characters.";
    } else{
        $new_password = htmlspecialchars(trim($_POST["new_password"]));
    }
    
    // Validate confirm password
    if(empty(htmlspecialchars(trim($_POST["confirm_password"])))){
        $confirm_password_err = "Please confirm the password.";
    } else{
        $confirm_password = htmlspecialchars(trim($_POST["confirm_password"]));
        if(empty($new_password_err) && ($new_password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
        
    // Check input errors before updating the database
    if(empty($new_password_err) && empty($confirm_password_err)){
        // Prepare an update statement
        $sql = "UPDATE users SET password = :password WHERE id = :id LIMIT 1";
        
        if($stmt = $pdo->prepare($sql)){
            
            // Set parameters
            $param_password = password_hash($new_password, PASSWORD_DEFAULT);
            $param_id = $_SESSION["id"];
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':password', $param_password);
            $stmt->bindParam(':id', $param_id);
            
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Password updated successfully. Destroy the session, and redirect to login page
                session_destroy();
                header("location: index.php");
                exit();
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

        }
    }

}

?>