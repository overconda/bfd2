<?php
require_once 'database.php';

$db = new Database();

$bases = array();

$sql = "select sbfdm_route_base.ID as base_id, sbfdm_route_base.route_id, sbfdm_route_base.base_no, base_title from sbfdm_route_base where route_id=3";
$result = $db->query($sql);
if($result->num_rows){
  while($row = $result->fetch_assoc()){

    $bases[$row['base_id']]['base_id'] = $row['base_id'];
    $bases[$row['base_id']]['base_no'] = $row['base_no'];
    $bases[$row['base_id']]['base_title'] = $row['base_title'];
  }
}

echo "<pre>";
print_r($bases);
echo "</pre>";

foreach ($bases as $base_id => $value) {
  $sql = "select count(*) as cc from sbfdm_user_base where base_id = {$base_id} and unlocked_status='true' and oauth_user_id like 'tw_15776186' ";
  $result = $db->query($sql);
  $row = $result->fetch_assoc();
  $bases[$base_id]['unlocked'] = $row['cc'];
}

echo "<br> ================= <br>";
echo "<pre>";
print_r($bases);
echo "</pre>";
 ?>
