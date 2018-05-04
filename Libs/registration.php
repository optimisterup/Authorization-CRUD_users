<?php
session_start();
if(!isset($_SESSION['login']) || empty($_SESSION['login'])){
    header("location: /Site/Libs/authorization.php");
    exit;
}
// Include config file
require_once 'config.php';

// Define variables and initialize with empty values
$login = $password = $confirm_password = "";
$login_err = $password_err = $confirm_password_err = "";
$surname =$firstname = $sex = $birthday = $admin = "";
$surname_err =$firstname_err = $sex_err = $birthday_err = $admin_err = "";

if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    $sql = "SELECT * FROM users WHERE `id`=:id;";
//        if($stmt = $pdo->prepare($sql)){
    $id = trim($_GET["id"]);
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_STR);
    $stmt->execute();
    $download = $stmt->fetchAll();
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
if($_SERVER["REQUEST_METHOD"] == "POST"){
    // Validate login
    if (empty(trim($_POST['id']))) {
        if (empty(trim($_POST["login"]))) {
            $login_err = "Please enter a login.";
        } else {
            // Prepare a select statement
            $sql = "SELECT id FROM users WHERE login = :login";

            if ($stmt = $pdo->prepare($sql)) {
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(':login', $param_login, PDO::PARAM_STR);

                // Set parameters
                $param_login = trim($_POST["login"]);

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                    if ($stmt->rowCount() > 0) {
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
    } elseif(strlen(trim($_POST['password'])) < 2){
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
            // Prepare an insert statement
            if (!empty(trim($_POST['id']))){
                $sql = "UPDATE `users` SET `password`=:password, `firstname`=:firstname, `surname`=:surname, `sex`=:sex, `birthday`=:birthday, `admin`=:admin  WHERE `id`=:id;";
            }else {
                $sql = "INSERT INTO users (login, password, firstname, surname, sex, birthday, admin ) 
VALUES (:login, :password, :firstname, :surname, :sex, :birthday, :admin)";
            }

            if ($stmt = $pdo->prepare($sql)) {
                // Set parameters
                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                $param_firstname = $firstname;
                $param_surname = $surname;
                $param_sex = $sex;
                $param_birthday = $birthday;
                $param_admin = $admin;
                // Bind variables to the prepared statement as parameters
                $stmt->bindParam(':password', $param_password, PDO::PARAM_STR);
                $stmt->bindParam(':firstname', $param_firstname, PDO::PARAM_STR);
                $stmt->bindParam(':surname', $param_surname, PDO::PARAM_STR);
                $stmt->bindParam(':sex', $param_sex, PDO::PARAM_STR);
                $stmt->bindParam(':birthday', $param_birthday, PDO::PARAM_STR);
                $stmt->bindParam(':admin', $param_admin, PDO::PARAM_STR);

                if (!empty(trim($_POST['id']))){
                    $param_id=$_POST['id'];
                    $stmt->bindParam(':id', $param_id, PDO::PARAM_STR);

                }else {
                    $param_login = $login;
                    $stmt->bindParam(':login', $param_login, PDO::PARAM_STR);
                }

                // Attempt to execute the prepared statement
                if ($stmt->execute()) {
                     //Redirect to login page
                    header("location: /Site/index.php");
                } else {
                    echo "Something went wrong. Please try again later.";
                }
            }

            // Close statement
            unset($stmt);
        }

        // Close connection
        unset($pdo);
}
require_once "view/registration.html";
?>

