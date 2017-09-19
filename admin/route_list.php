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
      #route_name{
        width:300px;
      }
    </style>
  </head>
  <body>
    <form method="post" action="_add_route.php">
      <center>
        Route Name : <input type="text" name="route_name" id="route_name" class="form-control">
        <br><button type="submit">Add New</button>
        <br><Br>
      </center>
    </form>
    <?php
    $sql = "select quiz.qzid,qz_title, quiz_cate.name_th from quiz ";
    $sql .= " inner join quiz_cate on quiz.qz_cate_id = quiz_cate.cate_id ";
    $sql .= " order by qzid ";//desc";


    $sql = "select * from sbfdm_route order by ID ";//desc";


    try{
      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

      $data = [];

      $i=0;
      foreach ($result as $row) {
          $data[] = [
            /*
              'id' => $row['qzid'],
              'title' => $row['qz_title'],
              'cate_name' => $row['name_th']
              */
              'id' => $row['ID'],
              'title'=> $row['route_title']
          ];


      }


      $i=1;
      foreach ($data as $row) {
        $r = $i%2?'odd':'even';
        $id=$row['id'];
        $title = $row['title'];

$out .=<<<EOD
<tr class='$r'>
  <td>$i . <a href='route_detail.php?id=$id' target=_blank>$title</a></td>
  <td>[<a href='javascript:void(0);' onclick='deleteQuiz($id);'></a>x</a>]</td>
</tr>
EOD;
      $i++;
      }
    }catch (PDOException $ev) {
      $e->ret = $ev->getMessage;
      echo $e->ret;
    }

    echo "<table align=center id='list'>";
    echo $out;
    echo "</table>";
    ?>
  </body>
</html>
