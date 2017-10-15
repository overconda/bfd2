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
    <title>Message</title>
    <style>
    th{
      background-color: #B2B2B2;
      color: #FFFFFF;
    }
    </style>
  </head>
  <body>
<table align=center width="80%">
  <tr>
    <th colspan=2>Subject</th>
    <th>From</th>
    <th>Date</th>
  </tr>

<?php
$sql = "select sbf_message.*,sbfdm_oauth.user_name ";
$sql .= " from sbf_message ";
$sql .= " inner join sbfdm_oauth on sbf_message.oauth_user_id = sbfdm_oauth.oauth_user_id ";
$sql .= " where direction=2";
$sql .= " order by date_create desc";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
foreach ($result as $row) {
  $id = $row['msg_id'];
  $unique_id = $row['unique_id'];  
  $thread = $row['thread'];
  $subject = $row['msg_title'];
  $by = $row['user_name'];
  $date = $row['date_create'];

  echo "<tr>";
  echo "<td><input type='checkbox' name='ck[{$thread}]' /></td>";
  echo "<td><a href='message_detail.php?thread={$thread}&unique_id={$unique_id}' target='_blank'>{$subject}</a></td>";
  echo "<td>$by</td>";
  echo "<td>$date</td>";

  echo "</tr>";
}



 ?>

</table>
  </body>
</html>
