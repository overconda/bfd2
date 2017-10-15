<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}

$thread = $_POST['thread'];
$message = htmlspecialchars($_POST['replytext'], ENT_QUOTES);
$rcv_oauth_user_id = $_POST['rcv_oauth_user_id'];

$sql = "select msg_title from sbf_message where thread = '{$thread}' ";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
foreach ($result as $row) {
  $msg_title = $row['msg_title'];
}



$timezone  = 7; // GMT +7
$now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

$unique_id = generateRandomString(16);

$sql = "insert into sbf_message (unique_id, is_broadcast, oauth_user_id, rcv_oauth_user_id, direction, msg_title, thread, msg_html,date_create) ";
$sql .= " values('{$unique_id}', 0,'ADMIN', '{$rcv_oauth_user_id}', 1, '{$msg_title}', '{$thread}', '{$message}', '$now')";
$stmt = $dbh->prepare($sql);
$stmt->execute();

header('Location: message.php');

 ?>
