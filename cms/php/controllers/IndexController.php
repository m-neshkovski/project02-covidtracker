<?php
// Initialize the session
session_start();
 
// Check if the user is already logged in, if yes then redirect him to welcome page
if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: welcome.php");
    exit;
}
 
// Include config file
require(dirname(__DIR__) . "/bootstrap.php");
 
// Define variables and initialize with empty values
$username = $password = "";
$username_err = $password_err = "";
 
// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
 
    // Check if username is empty
    if(empty(htmlspecialchars(trim($_POST["username"])))){
        $username_err = "Please enter username.";
    }else{
        $username = htmlspecialchars(trim($_POST["username"]));
    }
    
    // Check if password is empty
    if(empty(htmlspecialchars(trim($_POST["password"])))){
        $password_err = "Please enter your password.";
    } else{
        $password = htmlspecialchars(trim($_POST["password"]));
    }
    
    // Validate credentials
    if(empty($username_err) && empty($password_err)){
        // Prepare a select statement
        $sql = "SELECT id, username, password FROM users WHERE username = :username";
        
        if($stmt = $pdo->prepare($sql)){
            // Bind variables to the prepared statement as parameters
            $stmt->bindParam(':username', $username);
            // Attempt to execute the prepared statement
            if($stmt->execute()){
                // Store result
                $result = $stmt->fetchAll();
                
                // Check if username exists (result os not null), if yes then verify password
                if(count($result) == 1){               
                        // verify password
                        if(password_verify($password, $result[0]['password'])){
                            // Password is correct, so start a new session
                            session_start();
                            
                            // Store data in session variables
                            $_SESSION["loggedin"] = true;
                            $_SESSION["id"] = $result[0]['id'];
                            $_SESSION["username"] = $username;                            
                            
                            // Redirect user to welcome page
                            header("location: welcome.php");
                        } else{
                            // Display an error message if password is not valid
                            $password_err = "The password you entered was not valid.";
                        }
                    // }
                } else{
                    // Display an error message if username doesn't exist
                    $username_err = "No account found with that username.";
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }

        }
    }
    
}

?>