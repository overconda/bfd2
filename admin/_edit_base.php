<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}

$base_id = $_POST['base_id'];
$base_title = $_POST['base_name'];
$route_id = $_POST['route_id'];
$lat = $_POST['lat'];
$lon = $_POST['lon'];
$excerpt = $_POST['excerpt'];
$desc = $_POST['desc'];

$sql = "update sbfdm_route_base set ";
$sql .= " base_title = '$base_title' ,";
$sql .= " route_id = '$route_id' ,";
$sql .= " base_latitude = '$lat' ,";
$sql .= " base_longitude = '$lon' ,";
$sql .= " base_excerpt = '$excerpt' ,";
$sql .= " base_description = '$desc' ";
$sql .= " where ID= $base_id";

//echo $sql;
$stmt = $dbh->prepare($sql);
$stmt->execute();


if($_FILES){
  //echo "<br>FIles";
  if($_FILES['BaseImage']['size']>0){
    //echo "<br>FIle size is " . $_FILES['BaseImage']['size'];
    $now=date('YmdHis');
    $fileid = sprintf("%05d", $base_id);
    $imageFileType = pathinfo(basename($_FILES["BaseImage"]["name"],PATHINFO_EXTENSION));

    switch($imageFileType){
      case "image/jpeg" :
      case "image/jpg" : $ext = ".jpg"; break;
      case "image/png" : $ext = ".png"; break;
      default: $ext=".jpg";
    }

    $imageFilenameMove = "../images_base/" . $fileid . $ext;
    $imageFilename = "images_base/" . $fileid . $ext;
    move_uploaded_file($_FILES["BaseImage"]["tmp_name"], $imageFilenameMove) or die('cant move');

    $sql = "update sbfdm_route_base set base_image='$imageFilename' where ID=$base_id ";
    //echo "<br>" . $sql;
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  }
}


?>

<script>alert('Done');window.location='base_detail.php?base_id=<?php echo $base_id;?>'</script>
