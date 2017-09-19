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

  <script
  src="https://code.jquery.com/jquery-3.2.1.min.js"
  integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
  crossorigin="anonymous"></script>
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
  tr td:nth-child(1){
    text-align: right;
    vertical-align: top;
  }
  textarea{
    width: 500px;
    height: 120px;
  }

</style>
  </head>
  <body>

<p align="center">
  <a href="base_new.php">Add New Base</a>
</p>

    <table border=1 align=center>
      <tr bgcolor='#cccccc'>
        <td>ID</td>
        <td>Base Name</td>
        <td>Route Name</td>
      </tr>
    <?php

$sql = "select sbfdm_route_base.*, sbfdm_route.route_title from sbfdm_route_base ";
$sql .= " inner join sbfdm_route on sbfdm_route.ID = sbfdm_route_base.route_id  ";
$sql .= " order by ID desc";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
foreach ($result as $row) {
  $base_id = $row['ID'];
  $base_title = $row['base_title'];
  $route_title = $row['route_title'];
  echo "<tr>";
  echo "<td>{$base_id}</td>";
  echo "<td><a href='base_detail.php?base_id={$base_id}' target='_blank'>{$base_title}</a></td>";
  echo "<td>{$route_title}</td>";
  echo "</tr>";
}


     ?>
   </table>

  </body>
</html>
