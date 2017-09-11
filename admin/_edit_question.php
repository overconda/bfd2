<?php
$qid = $_POST['qid'];
$title = htmlspecialchars($_POST['question'] , ENT_QUOTES);
$cateid = $_POST['cate_id'];

$a1 = htmlspecialchars($_POST['a1'] , ENT_QUOTES);
$a2 = htmlspecialchars($_POST['a2'] , ENT_QUOTES);
$a3 = htmlspecialchars($_POST['a3'] , ENT_QUOTES);
$a4 = htmlspecialchars($_POST['a4'] , ENT_QUOTES);


$correct = $_POST['correct_answer'];

include('../dbconnect.php');
include('functions.php');

$sess = generateRandomString(32);
$now = date('Y-m-d H:i:s');

//$sql = "update quiz set qz_title = '$title', qz_cate_id=$cateid , udate='$now' where qzid=$qid";

$sql = "update sbfdm_quiz set question = '{$title}' , category=$cateid ,  correct_answer=$correct , answer_1='$a1' , answer_2='$a2' , answer_3='$a3' , answer_4='$a4' where ID = $qid ";

$stmt = $dbh->prepare($sql);
$stmt->execute();
/*
$c1=0;
$c2=0;
$c3=0;
$c4=0;
switch ($correct) {
  case '1': $c1=1; break;
  case '2': $c2=1; break;
  case '3': $c3=1; break;
  case '4': $c4=1; break;

}


//// clear all answer with this qid
$sql = "delete from quiz_answer where qzid=$qid";
$stmt = $dbh->prepare($sql);
$stmt->execute();


///// ANSWER insert
if(trim($a1)!=""){
  $sql = "insert into quiz_answer (qzid,ans_text,is_correct,cdate,udate) ";
  $sql .= " values($qid , '$a1' , $c1, '$now', '$now')";

  //echo $sql; exit;
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
}

if(trim($a2)!=""){
  $sql = "insert into quiz_answer (qzid,ans_text,is_correct,cdate,udate) ";
  $sql .= " values($qid , '$a2' , $c2, '$now', '$now')";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();
}

if(trim($a3)!=""){
  $sql = "insert into quiz_answer (qzid,ans_text,is_correct,cdate,udate) ";
  $sql .= " values($qid , '$a3' , $c3, '$now', '$now')";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();
}

if(trim($a4)!=""){
  $sql = "insert into quiz_answer (qzid,ans_text,is_correct,cdate,udate) ";
  $sql .= " values($qid , '$a4' , $c4, '$now', '$now')";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();
}
*/

 ?>
 <html>
<script>
 window.opener.location.reload();
 window.close();
</script>
 </html>
