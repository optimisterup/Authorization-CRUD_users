<?php
// Include config file
require_once 'Db.php';

// Define variables and initialize with empty values
$login = $password = "";
$login_err = $password_err = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (empty(trim($_POST["login"]))) {
        $login_err = 'Please enter login.';
    } else {
        $login = trim($_POST["login"]);
    }
    if (empty(trim($_POST['password']))) {
        $password_err = 'Please enter your password.';
    } else {
        $password = trim($_POST['password']);
    }
    if (empty($login_err) && empty($password_err)) {
        $db = new DB();
        $result = $db->authorization($login, $password);
        if ($result !== "wrong") {
            if ($result !== "no account") {
                if ($result !== "no access") {
                    if ($result !== "no valid") {
                        /* Password is correct, so start a new session and save the login to the session */
                        session_start();
                        $_SESSION['login'] = $login;
                        header("location: /index.php");
                    } else {
                        // Display an error message if password is not valid
                        $password_err = 'The password you entered was not valid.';
                    }
                } else {
                    $password_err = 'You do not have access rights';
                }
            } else {
                // Display an error message if login doesn't exist
                $login_err = 'No account found with that login.';
            }
        } else {
            echo "Oops! Something went wrong. Please try again later.";
        }
    }
}
require_once 'view/authorization.html';
?>
