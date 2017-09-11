<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}

$qid = $_REQUEST['id'];


$title = "";
$cateid=0;

$sql = "select * from sbfdm_quiz where ID = $qid";
try{
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $title = $result[0]['question'];
  $cateid = $result[0]['category'];

}catch (PDOException $ev) {
  $e->ret = $ev->getMessage;
  //echo $e->ret;
}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title></title>
  </head>
  <style>
    .odd{ background-color: #f9f5d9; }
    .even { background-color: #e0dbba;}
    #list tr:hover {background-color: #b6d3b3;}
    a{
      color: #0;
      text-decoration: none;
    }
    td{
      padding: 4px;
    }

  </style>

  <body>

 <a name="latest"></a>
 <hr>
 <form method=post action="_edit_question.php">
 <table align=center width="60%">
   <tr>
    <td align="right">คำถาม</td>
    <td><input type=text name="question" class="form-control" value="<?php echo $title;?>"></td>
   </tr>
   <tr>
      <td align="right">หมวด</td>
      <td>
        <select name="cate_id">
        <?php
        $cate = array(
          1=>'Singha Beer',
          2=>'Place',
          3=>'Misc.',
          4=>'Sport',
          5=>'Movies',
          6=>'Music',
          7=>'Food '
        );
        /*
$sql = "select * from quiz_cate";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );

foreach ($result as $row) {
  $sel = "";
  if($cate==$row['cate_id']){
    $sel = "selected";
  }

  echo "<option value=" . $row['cate_id'] . " $sel>" . $row['name_th'] . "</option>\n";
}
*/
      foreach ($cate as $key => $value) {
        $sel = "";
        if($cateid==$key){
          $sel = "selected";
        }

        echo "<option value='{$key}' {$sel}>$value</option>";
      }

         ?>
       </select>
      </td>
   </tr>
   <tr>
     <td align="right" valign="top">คำตอบ</td>
     <td>

<?php
/*
$sql = "select * from quiz_answer where qzid=$qid";

try{
  $i=1;



  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );


  //foreach ($result as $row) {
  for($i=0; $i<4 ; $i++){
    $row = $result[$i];
    $x = $i+1;
    $ck='';
    if($row['is_correct']=='1'){
      $ck = ' checked';
    }
    echo "  $x.<input type='radio' name='correct_answer' value=$x $ck> <input type='text' class='form-control' name='a$x' value='" . $row['ans_text'] . "'><br>";
  }

}catch (PDOException $ev) {
  $e->ret = $ev->getMessage;
  //echo $e->ret;
}
*/
$correct = $result[0]['correct_answer'];
for($i=1 ; $i<=4; $i++){
  $ck= '';
  if($i==$correct){
    $ck = ' checked';
  }

  echo "  $i.<input type='radio' name='correct_answer' value=$i $ck> <input type='text' class='form-control' name='a$i' value='" . $result[0]['answer_' . $i] . "'><br>";
}

 ?>


<!--

  2.<input type='radio' name="correct_answer" value=2> <input type="text" class="form-control" name="a2"><br>
  3.<input type='radio' name="correct_answer" value=3> <input type="text" class="form-control" name="a3"><br>
  4.<input type='radio' name="correct_answer" value=4> <input type="text" class="form-control" name="a4"></td>
-->
   </tr>
   <tr>
      <td colspan=2 align="center">
        <input type="hidden" name="qid" value="<?php echo $qid;?>">
        <button type="submit" class="btn btn-primary">Save Question</button>
      </td>
   </tr>
 </table>
 </form>
 <p>&nbsp;</p>
  </body>
</html>

<?php
  $dbh = null;
 ?>
