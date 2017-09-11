<?php
$qid = $_REQUEST['id'];

include('../dbconnect.php');
include('functions.php');

//$sql = "delete from quiz where qzid=$qid";
$sql = "delete from sbfdm_quiz where ID=$qid";

$stmt = $dbh->prepare($sql);
$stmt->execute();


//// clear all answer with this qid
/*
$sql = "delete from quiz_answer where qzid=$qid";
$stmt = $dbh->prepare($sql);
$stmt->execute();
*/

header("Location: question_list.php#latest");


 ?>
