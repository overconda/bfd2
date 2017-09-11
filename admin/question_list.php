<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
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

  <script type="text/javascript">
    function deleteQuiz(id){
      var result = confirm("Are you sure to delete this quiz?");
      if (result) {
        window.location = "_delete_question.php?id=" + id;
      }
    }
  </script>

  <body>
<?php
$sql = "select quiz.qzid,qz_title, quiz_cate.name_th from quiz ";
$sql .= " inner join quiz_cate on quiz.qz_cate_id = quiz_cate.cate_id ";
$sql .= " order by qzid ";//desc";


$sql = "select * from sbfdm_quiz order by ID ";//desc";


try{
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

  $data = [];

  foreach ($result as $row) {
      $data[] = [
        /*
          'id' => $row['qzid'],
          'title' => $row['qz_title'],
          'cate_name' => $row['name_th']
          */
          'id' => $row['ID'],
          'title'=> $row['question'],
          'cate_id'=>$row['category']
      ];


  }


  $i=1;
  foreach ($data as $row) {

    $id=$row['id'];
    $title = $row['title'];
    switch ($row['cate_id']) {
      case '1': $cate_name = 'Singha Beer'; break;
      case '2': $cate_name = 'Place'; break;
      case '3': $cate_name = 'Misc.'; break;
      case '4': $cate_name = 'Sport'; break;
      case '5': $cate_name = 'Movies'; break;
      case '6': $cate_name = 'Music'; break;
      case '7': $cate_name = 'Food'; break;
    }
    $cate = $cate_name;
    $r = $i%2?'odd':'even';
$out .=<<<EOD
  <tr class='$r'>
    <td>$i . <a href='question_edit.php?id=$id' target=_blank>$title</a></td>
    <td>$cate</td>
    <td>[<a href='javascript:void(0);' onclick='deleteQuiz($id);'>x</a>]</td>
  </tr>
EOD;
  $i++;
  }

echo "<table align=center id='list'>";
echo $out;
echo "</table>";


}catch (PDOException $ev) {
  $e->ret = $ev->getMessage;
  echo $e->ret;
}


 ?>
 <a name="latest"></a>
 <hr>
 <form method=post action="_add_question.php">
 <table align=center width="60%">
   <tr>
    <td align="right">คำถาม</td>
    <td><input type=text name="question" class="form-control"></td>
   </tr>
   <tr>
      <td align="right">หมวด</td>
      <td>
        <select name="cate_id">
        <?php
        /*
$sql = "select * from quiz_cate";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );

foreach ($result as $row) {
  echo "<option value=" . $row['cate_id'] . ">" . $row['name_th'] . "</option>\n";
}
*/
  $dbh = null;
         ?>
          <option value="1">Singha Beer</option>
          <option value="2">Place</option>
          <option value="3">Misc.</option>
          <option value="4">Sport</option>
          <option value="5">Movies</option>
          <option value="6">Music</option>
          <option value="7">Food</option>
       </select>
      </td>
   </tr>
   <tr>
     <td align="right" valign="top">คำตอบ</td>
     <td>

  1.<input type='radio' name="correct_answer" value=1> <input type="text" class="form-control" name="a1"><br>
  2.<input type='radio' name="correct_answer" value=2> <input type="text" class="form-control" name="a2"><br>
  3.<input type='radio' name="correct_answer" value=3> <input type="text" class="form-control" name="a3"><br>
  4.<input type='radio' name="correct_answer" value=4> <input type="text" class="form-control" name="a4"></td>
   </tr>
   <tr>
      <td colspan=2 align="center">
        <button type="submit" class="btn btn-primary">Add Question</button>
      </td>
   </tr>
 </table>
 </form>
 <p>&nbsp;</p>
  </body>
</html>
