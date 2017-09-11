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
  </head>
  <body>
    <?php

$languate_id = 1; // thai

$sql = <<<EOD
  select base_main.base_id, base_main.route_id, base_text.base_name , route_text.route_name

  from base_main

  inner join base_text on base_main.base_id = base_text.base_id
  inner join route_text on base_main.route_id = route_text.route_id

  where base_text.language_id = $languate_id
  and route_text.route_lang_id = $languate_id

  order by base_main.base_id
EOD;
    try{
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

      $data = [];

      foreach ($result as $row) {
          $data[] = [
              'base_id' => $row['base_id'],
              'route_id' => $row['route_id'],
              'base_name' => $row['base_name'],
              'route_name' => $row['route_name']
          ];


      }


      $i=1;
      foreach ($data as $row) {

        $base_id=$row['base_id'];
        $route_id=$row['route_id'];
        $base_name = $row['base_name'];
        $route_name = $row['route_name'];
        $r = $i%2?'odd':'even';

$out .=<<<EOD
  <tr class='$r'>
    <td>$i . <a href='base_edit.php?base_id=$base_id' target=_blank>$base_name</a></td>
    <td>$route_name</td>
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

     <form method=post action="_add_route.php">
     <table align=center width="60%">
       <tr>
        <td align="right">Base Name</td>
        <td><input type=text name="base_name" class="form-control"></td>
       </tr>
       <tr>
          <td align="right">Route</td>
          <td>
            <select name="route_id">
            <?php
     $sql = "select route.route_id, route_text.route_name from route inner join route_text on route.route_id = route_text.route_id where route_lang_id=1";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

     foreach ($result as $row) {
      echo "<option value=" . $row['route_id'] . ">" . $row['route_name'] . "</option>\n";
     }

      $dbh = null;
             ?>
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



  </body>
</html>
