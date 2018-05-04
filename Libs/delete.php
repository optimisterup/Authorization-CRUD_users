<?php
session_start();
if(!isset($_SESSION['login']) || empty($_SESSION['login'])){
    header("location: /Libs/authorization.php");
    exit;
}
if (isset($_POST["delete"]) && !empty(trim(($_POST["delete"])))){
    require_once 'Db.php';
    $db=new DB();
    $result=$db->delete($_POST["delete"]);
    var_dump($result);
        if($result){
            // Redirect to login page
            header("location: /index.php");
        }else{
            echo "Something went wrong. Please try again later.";
        }
}
