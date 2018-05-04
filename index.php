<?php
session_start();
if (!isset($_SESSION['login']) || empty($_SESSION['login'])) {
    header("location: /Libs/authorization.php");
    exit;
}
// Include config file
require_once 'Libs/config.php';

//$route=new Route();
//$route->add('/');
//$route->add('/index');
//$route->add('/contact');



//How many records per page
$rpp=5;
//Check for set page
isset($_GET['page']) ? $page=$_GET['page'] : $page=0;
//Check for page 1
if ($page>1){
    $start=($page*$rpp)-$rpp;
}else{
    $start=0;
}
$sql = "SELECT * FROM users";
$stmt = $pdo->query($sql);
//Get total records
$numRows=$stmt->rowCount();
//Get total number of pages
$totalPages=ceil($numRows/$rpp);
//$download = $stmt->fetchAll();
$resultSet=$pdo->query("SELECT * FROM users LIMIT $start, $rpp");
$download = $resultSet->fetchAll();

require_once 'Libs/view/index.html';

?>

