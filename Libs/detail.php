<?php
session_start();
if(!isset($_SESSION['login']) || empty($_SESSION['login'])){
    header("location: /Libs/authorization.php");
    exit;
}
if(isset($_GET["id"]) && !empty(trim($_GET["id"]))) {
    // Include config file
    require_once 'Db.php';
    $db=new DB;
    $download=$db->detail($_GET["id"]);
    $password = "";
    $login = $download[0]["login"];
    $firstname = $download[0]["firstname"];
    $surname = $download[0]["surname"];
    $sex = $download[0]["sex"];
    $birthday = $download[0]["birthday"];
    $admin = $admin[0]["admin"];
    require_once "view/detail.html";
}
