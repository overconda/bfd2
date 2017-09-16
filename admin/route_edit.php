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
    <style type="text/css">
      table td{
        padding: 4px;
      }
      textarea.form-control{
        height:260px;
      }
    </style>
  </head>
  <body>
    <?php
    $sql = "select * from sbfdm_route where ID = " . $_GET['id'] . " order by ID";


      $stmt = $dbh->prepare($sql);
      $stmt->execute();
      $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

      $data = [];

      foreach ($result as $row) {
        $data[] = [
            'id' => $row['ID'],
            'title'=> $row['route_title'],
            'svg'=>$row['svg']
        ];
      }
      ?>



    <center><h1>Route Edit</h1></center>
<form method="post" action="_edit_route.php">
  <table border=0 cellpadding=4 cellspacing=4 align=center width="70%">
    <tr>
        <td align="right">Route Name:</td>
        <td><input type=text name="route_name" value="<?php echo $data[0]['title']; ?>" class="form-control">
    </tr>
    <tr>
      <td align="right" valign="top">SVG</td>
      <td><textarea name="svb" class="form-control" height="8"><?php echo $data[0]['svg']; ?></textarea></td>
    </tr>
    <input type="hidden" name="id" value="<?php echo $_GET['id'];?>">
  </table>
  <center><button type=submit class="btb btn-primary">Save</button>
</form>
  </body>
</html>
