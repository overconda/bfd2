<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}


$base_id = $_GET['base_id'];

$sql = "select * from sbfdm_route_base where ID=$base_id";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
$baseRow = $result[0];
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

     <a name="latest"></a>

     <form method=post action="_edit_base.php" enctype="multipart/form-data">
     <table align=center width="60%">
       <tr>
        <td align="right">Base Name</td>
        <td><input type=text name="base_name" class="form-control" value="<?php echo $baseRow['base_title']?>"></td>
       </tr>
       <tr>
          <td align="right">Route</td>
          <td>
            <select name="route_id">
            <?php
     $sql = "select ID,route_title from sbfdm_route order by ID ";
     $stmt = $dbh->prepare($sql);
     $stmt->execute();
     $result = $stmt->fetchAll( PDO::FETCH_ASSOC );

     foreach ($result as $row) {
       $sel = $row['ID'] == $baseRow['route_id'] ? " selected" : "";
      echo "<option value=" . $row['ID'] . " $sel>" . $row['route_title'] . "</option>\n";
     }

      $dbh = null;
             ?>
           </select>
          </td>
       </tr>

       <tr>
          <td>Latitude</td>
          <td><input type="text" name="lat" value="<?php echo $baseRow['base_latitude']?>">
          </td>
       </tr>

      <tr>
         <td>Longitude</td>
         <td><input type="text" name="lon" value="<?php echo $baseRow['base_longitude']?>">
         </td>
      </tr>
      <tr>
          <td>Base Excerpt</td>
          <td><textarea name="excerpt"><?php echo $baseRow['base_excerpt']?></textarea></td>
      </tr>
      <tr>
          <td>Base Description</td>
          <td><textarea name="desc"><?php echo $baseRow['base_desc']?></textarea></td>
      </tr>

      <tr>
        <td valign="top" align="right">Image</td>
        <td>
          <?php
          $image = $baseRow['base_image'];
          if(trim($image)!=""){
            echo "<img src='../{$image}'>";
            echo "<br>";
          }

           ?>
           <input type="file" name="BaseImage">
        </td>
      </tr>

        <tr>
          <td></td>
           <td align="center">
             <input type="hidden" name="base_id" value="<?php echo $base_id;?>">
             <button type="submit" class="btn btn-primary">Save Base</button>
           </td>
        </tr>
     </table>
     </form>



  </body>
</html>
