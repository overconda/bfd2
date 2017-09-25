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
    <title>Message Admin</title>
    <style type="text/css">
    .head td{
      text-align: center;
      color: #FFFFFF;
      background-color: #8e2076;
    }
    table , table td{
      border: 1px solid #631853;
    }
    body{
      font-family: verdana, sans-serif;
    }
    </style>
  </head>
  <body>
<table align="center" width="70%" border=1 cellpadding="4" cellspacing=0>
  <tr class="head">
    <td>Num</td>
    <td>From</td>
    <td>Title</td>
    <td>Status</td>
    <td>Date</td>
  </tr>
<?php

$sql = "select * from sbf_message order by msg_id desc";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );

$data = [];

$i=0;
foreach ($result as $row) {
}

$dbh=null;

 ?>
 </table>
  </body>
</html>
