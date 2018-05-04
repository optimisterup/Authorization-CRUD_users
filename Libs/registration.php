<?php
session_start();
if(!isset($_SESSION['login']) || empty($_SESSION['login'])){
    header("location: /Site/Libs/authorization.php");
    exit;
}
// Include config file
require_once 'Db.php';
$db=new DB();
// Define variables and initialize with empty values
$login = $password = $confirm_password = "";
$login_err = $password_err = $confirm_password_err = "";
$surname =$firstname = $sex = $birthday = $admin = "";
$surname_err =$firstname_err = $sex_err = $birthday_err = $admin_err = "";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $download =$db->getElementById($_GET["id"]);
    $password = "";
    $id=$download[0]["id"];
    $login = $download[0]["login"];
    $firstname = $download[0]["firstname"];
    $surname = $download[0]["surname"];
    $sex = $download[0]["sex"];
    $birthday = $download[0]["birthday"];
    $admin = $download[0]["admin"];
//    var_dump($_GET['id']);
    require_once "view/registration.html";
}

// Processing form data when form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate login
    if (empty(trim($_POST['id']))) {
        if (empty(trim($_POST["login"]))) {
            $login_err = "Please enter a login.";
        } else {
            $result = $db->isTheLoginBusy($_POST["login"]);
            if ($result !== "wrong") {
                if ($result === "busy") {
                    $login_err = "This login is already taken.";
                } else {
                    $login = trim($_POST["login"]);
                }
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        // Close statement
        unset($stmt);
    }
    // Validate password
    if (empty(trim($_POST['password']))) {
        $password_err = "Please enter a password.";
    } elseif (strlen(trim($_POST['password'])) < 6) {
        $password_err = "Password must have at least 6 characters.";
    } else {
        $password = trim($_POST['password']);
    }

    // Validate confirm password
    if (empty(trim($_POST["confirm_password"]))) {
        $confirm_password_err = 'Please confirm password.';
    } else {
        $confirm_password = trim($_POST['confirm_password']);
        if ($password != $confirm_password) {
            $confirm_password_err = 'Password did not match.';
        }
    }

    // Validate surname
    if (empty(trim($_POST['surname']))) {
        $password_err = "Please enter a surname.";
    } elseif (strlen(trim($_POST['password'])) < 2) {
        $password_err = "Surname must have atleast 2 characters.";
    } else {
        $surname = trim($_POST['surname']);
        $firstname = trim($_POST['firstname']);
        $sex = trim($_POST['sex']);
        $birthday = trim($_POST['birthday']);
        $admin = trim($_POST['admin']);
    }
    // Check input errors before inserting in database
    if (empty($login_err) && empty($password_err) && empty($confirm_password_err)) {
        //Prepare an insert statement
        if (!empty(trim($_POST['id']))) {
            $update = $db->updateUser($_POST['id'], $password, $firstname, $surname, $sex, $birthday, $admin);
        } else {
            $add = $db->addUser($login, $password, $firstname, $surname, $sex, $birthday, $admin);
        }
        // Attempt to execute the prepared statement
        if ($update === true || $add === true) {
            //Redirect to login page
            header("location: /index.php");
        } else {
            echo "Something went wrong. Please try again later.";
        }
    }
}
    require_once "view/registration.html";
?>

