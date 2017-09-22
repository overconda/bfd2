<?php


$now = date('Y-m-d H:i:s');
$time="2017-09-19 13:12:50";
$ret =  isMoreThanHour($time, $now);
echo "<br><br>";
if($ret){echo "TRUE";}else{echo "FALSE";}

function isMoreThanHour($dateStart, $dateStop){
    $hour = 3600;
    $d1 = strtotime($dateStart);
    $d2 = strtotime($dateStop);
    $diff = $d2 - $d1;
    if($diff>$hour){
        return true;
    }else{
        return false;
    }
}
 ?>
