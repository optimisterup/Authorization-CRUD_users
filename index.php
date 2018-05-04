<?php
session_start();
if (!isset($_SESSION['login']) || empty($_SESSION['login'])) {
    header("location: /Libs/authorization.php");
    exit;
}
// Include config file
require_once 'Libs/Db.php';
$db= new DB;
$totalPages=$db->pagination($_GET['page']);
$download=$db->list();
require_once 'Libs/view/index.html';
?>

