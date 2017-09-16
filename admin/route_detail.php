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
     <style type="text/css">
     .w100{
       width: 100%;
     }
     tr.head td{
       text-align: center;
       background-color: #dddddd;
     }
     </style>
   </head>
   <body>
     <form method="post" action="_edit_route.php">
     <?php
$id=$_GET['id'];
$sql = "select * from sbfdm_route where ID = $id";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );

$title = $result[0]['route_title'];
$svg = $result[0]['svg'];
//$complete_point = $result[0]['complete_point'];
$extra_point = $result[0]['extra_point'];


      ?>
      <table border="0" align="center" width="70%">
        <tr>
            <td align="right">Route Name</td>
            <td><input type="text" name="route_title" value="<?echo $title?>" class="w100"></td>
        </tr>
        <tr>
            <td valign="top" align="right">svg</td>
            <td><textarea name="svg" cols="60" rows="20" class="w100"><?echo $svg;?></textarea></td>
        </tr>
      </table>

      <table border="0" align="center" >
        <tr class="head">
          <td colspan=2>Base Name</td>
          <td>Order</td>
          <td>Transform</td>
          <td>Y</td>
        </tr>
        <?php
$sql = "select * from sbfdm_route_base where route_id = $id order by base_no";
//$sql = "select * from sbfdm_route where ID = $id";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
$data = [];


foreach ($result as $row) {
    $data[] = [
        'base_id' => $row['ID'],
        'base_no'=> $row['base_no'],
        'base_title' => $row['base_title'],
        'coordinate_y' => $row['coordinate_y'],
        'coordinate_transform' => $row['coordinate_transform']
    ];
}

$i=0;
$subData = $data;
$out ="";
$numRow = sizeof($data);
foreach ($data as $row ) {
  //$r = $i%2?'odd':'even';
  $base_id=$row['base_id'];
  $base_no = $row['base_no'];
  $base_title = $row['base_title'];
  $coordinate_y = $row['coordinate_y'];
  $coordinate_transform = $row['coordinate_transform'];

  $k=$i+1;

  $out .= "\n<input type='hidden' name='base_id[{$i}]' value='$base_id'>";
  $out .= "\n<tr>";
  $out .= "\n\t<td align='left'>Base {$k}. $base_title<td>";
  $out .= "\n\t\t<td><select name='order[{$i}]'>";
  for($k=0;$k<$numRow; $k++){
    $x = $k+1;
    $sel = $x==($base_no)? " selected": "";
    $out .= "\n\t<option value='$x' $sel>$x</option>";
  }
  /*
  foreach ($subData as $subRow) {
    $sel = $k==$subRow['base_no']? " selected": "";
    $out .= "\n\t\t<option value='" . $subRow['base_id'] . "' $sel>" . $subRow['base_title'] . "</option>";
  }
  */
  $out .= "\n\t\t</select>";
  $out .= "\n\t</td>";
  $out .= "\n\t<td><input type=text name='tf[{$i}]' value='{$coordinate_transform}'></td>";
  $out .= "\n\t<td><input type=text name='y[{$i}]' value='{$coordinate_y}'></td>";
  $out .= "\n</tr>";
  $i++;
}

echo $out;




         ?>
      </table>
      <input type="hidden" name="svg">
      <p align=center><button type=submit> Save </button></p>
    </form>
   </body>
 </html>
