<?php
session_start();
if(!isset($_SESSION['login']) || empty($_SESSION['login'])){
    header("location: /Libs/authorization.php");
    exit;
}
require_once 'config.php';

if ($_POST["delete"]){
    $sql="DELETE FROM users WHERE `id`=:id;";
    if($stmt = $pdo->prepare($sql)){
        $id = trim($_POST["delete"]);
        $stmt->bindParam(':id', $id, PDO::PARAM_STR);
        if($stmt->execute()){
            // Redirect to login page
            header("location: /index.php");
        } else{
            echo "Something went wrong. Please try again later.";
        }
    }
}
