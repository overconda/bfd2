<?php
$qid = $_POST['qid'];
$title = htmlspecialchars($_POST['question'] , ENT_QUOTES);
$cateid = $_POST['cate_id'];

$route_name = htmlspecialchars($_POST['route_name'] , ENT_QUOTES);



include('../dbconnect.php');
include('functions.php');

$sql = "insert into sbfdm_route (route_title) values('$route_name') ";

$stmt = $dbh->prepare($sql);
$stmt->execute();


header("Location: route_list.php");

?>
