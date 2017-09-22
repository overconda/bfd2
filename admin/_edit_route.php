<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}

$id=$_POST['id'];
$title= htmlspecialchars($_POST['route_title'], ENT_QUOTES);
$svg = htmlspecialchars($_POST['svg'], ENT_QUOTES);

//$title = $_POST['route_title'];

/*
echo "<pre>";
print_r($_POST);
print_r($_FILES);
echo "</pre>";

echo $sql;
exit;
*/
$stmt = $dbh->prepare($sql);
$stmt->execute();



$sql = "update sbfdm_route set route_title='$title' , svg='$svg' ,active=1 where ID=$id";
$stmt = $dbh->prepare($sql);
$stmt->execute();

//// cleart route_base order
$sql = "update sbfdm_route_base set base_no=0 where route_id = $id";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$basesHTML = "";
$len = sizeof($_POST['base_id']);
for($i=0; $i<$len; $i++){
  $sub_base_id = $_POST['base_id'][$i];
  $y = $_POST['y'][$i];
  $transform = $_POST['tf'][$i];
  $ordering = $_POST['order'][$i];

  $sql = "update sbfdm_route_base set base_no=$ordering, coordinate_y='$y', coordinate_transform='$transform' where ID=$sub_base_id ";

  //echo "<br><br>$sql";

  $stmt = $dbh->prepare($sql);
  $stmt->execute();

$eachBaseHTML = <<<EOS
<g class="base-info-{$ordering} base-marker" transform="{$transform}">
  <text x="-10" y="{$y}" font-size="19" fill="#fff">{$ordering}</text>
  <g class="group-unactive">
    <circle fill="#ffd503" stroke="#fff" stroke-width="5" cx="0" cy="0" r="6"/>
  </g>
</g>

EOS;


  $basesHTML .= $eachBaseHTML;
}

$h = htmlspecialchars($basesHTML, ENT_QUOTES);



$sql = "update sbfdm_route set bases_html='$h' where ID=$id";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$sql = "update sbfdm_route set route_title='$title', svg='$svg' where ID=$id ";
$stmt = $dbh->prepare($sql);
$stmt->execute();

$imageFilename = "";
$ext="";
if($_FILES){
  if($_FILES['RouteImage']['size']>0){
    $now=date('YmdHis');
    $fileid = sprintf("%05d", $id);
    $imageFileType = pathinfo(basename($_FILES["RouteImage"]["name"],PATHINFO_EXTENSION));

    switch($imageFileType){
      case "image/jpeg" :
      case "image/jpg" : $ext = ".jpg"; break;
      case "image/png" : $ext = ".png"; break;
      default: $ext=".jpg";
    }

    $imageFilenameMove = "../images_route/" . $fileid . $ext;
    $imageFilename = "images_route/" . $fileid . $ext;
    move_uploaded_file($_FILES["RouteImage"]["tmp_name"], $imageFilenameMove) or die('cant move');

    $sql = "update sbfdm_route set image='$imageFilename' where ID=$id ";
    $stmt = $dbh->prepare($sql);
    $stmt->execute();
  }
}



 ?>
 <html>
<script>
// window.opener.location.reload();
// window.close();
window.location.reload(history.back());
</script>
<pre>
<?php


//htmlentities(print_r($_POST));

 ?>
 </pre>
 </html>
