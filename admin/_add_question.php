<?php
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

/*
$sql = "insert into quiz (qz_title, qz_cate_id, qz_lang_id, qz_locale_id, sess , cdate, udate) ";
$sql .= " values('$title', $cateid, 1, 1 , '$sess', '$now','$now') ";
*/

$sql = "insert into sbfdm_quiz (category, question, answer_1, answer_2, answer_3, answer_4, correct_answer) ";
$sql .= " values($cateid , '{$title}', '{$a1}', '{$a2}', '{$a3}', '{$a4}', $correct) ";
$stmt = $dbh->prepare($sql);
$stmt->execute();

//echo $sql; exit;
/*
try{
  $stmt = $dbh->prepare($sql);
  $stmt->execute();


  //// get qid
  $sql = "select qzid from quiz where sess='$sess' ";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $qid = "";

  foreach ($result as $row) {
      $qid = $row['qzid'];
  }

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

  header("Location: question_list.php#latest");

}catch (PDOException $ev) {
  $e->ret = $ev->getMessage;
  echo $e->ret;
}
*/
header("Location: question_list.php#latest");
 ?>
