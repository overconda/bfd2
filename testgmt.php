<?php
/*
$date = new DateTime();
$dt_bkk = new DateTimeZone('Asia/Bangkok');

$dt = new DateTime($date, $dt_bkk);
$dt->setTimeZon

echo "<br>" . $date->format('Y-m-d H:i:s');
echo "<br>" . $dateGmt;//->format('Y-m-d H:i:s');;
*/

$timezone  = 7; // GMT +7

//echo "<br>" . date("M d Y H:i:s");
//echo "<br>" . gmdate("M d Y H:i:s", time() + 3600*($timezone/*+date("I")*/));

$time = new DateTime();
//echo "<br>" . $time->format('Y-m-d H:i:s');


$mytime = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

echo "<br>" . $mytime;

$minutes_add = '+3 minutes';
//$mytime->add(new DateInterval('PT' . $minutes_add . 'M'));

$mynewtime = date('Y-m-d H:i:s', strtotime($minutes_add, strtotime($mytime)));

echo "<br>" . $mynewtime;
 ?>
