<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}

$id=$_POST['id'];
$title= mysql_real_escape_string(htmlspecialchars($_POST['title'], ENT_QUOTES));
$svg = mysql_real_escape_string(htmlspecialchars($_POST['svg'], ENT_QUOTES));

$sql = "update sbfdm_route set route_title='$title', svg='$svg' where ID=$id ";
$stmt = $dbh->prepare($sql);
$stmt->execute();



$sql = "update sbfdm_route set route_title='$title' , svg='$svg' where ID=$id";
$stmt = $dbh->prepare($sql);
$stmt->execute();

//// cleart route_base order
$sql = "update sbfdm_route_base set base_no=0 where route_id = $id";
$stmt = $dbh->prepare($sql);
$stmt->execute();


$len = sizeof($_POST['base_id']);
for($i=0; $i<$len; $i++){
  $base_id = $_POST['base_id'][$i];
  $y = $_POST['y'][$i];
  $transform = $_POST['tf'][$i];
  $ordering = $_POST['order'][$i];

  $sql = "update sbfdm_route_base set base_no=$ordering, coordinate_y='$y', coordinate_transform='$transform' where ID=$base_id ";

  echo "<br><br>$sql";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();
}


 ?>
 <html>
<script>
// window.opener.location.reload();
// window.close();
//window.location.reload(history.back());
</script>
<pre>
<?php


//htmlentities(print_r($_POST));

 ?>
 </pre>
 </html>
