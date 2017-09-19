<?php

$base_name = htmlspecialchars($_POST['base_name'] , ENT_QUOTES);
$base_excerpt = htmlspecialchars($_POST['excerpt'] , ENT_QUOTES);
$base_desc = htmlspecialchars($_POST['desc'] , ENT_QUOTES);
$route_id = $_POST['route_id'];
$lat = $_POST['lat'];
$lon = $_POST['lon'];



include('../dbconnect.php');
include('functions.php');


$sql = "insert into sbfdm_route_base (route_id, base_title, base_excerpt, base_description, base_latitude, base_longitude ) values($route_id, '{$base_name}', '{$base_excerpt}', '{$base_description}', '$lat', '$lon') ";


//echo "$sql";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$sql = "select ID from sbfdm_route_base order by ID desc limit 1";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
$base_id = $result[0]['ID'];

if($_FILES){
  if($_FILES['BaseImage']['size']>0){
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
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  }
}



header("Location: base_list.php");


 ?>
