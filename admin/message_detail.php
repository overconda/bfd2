<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

if($_SESSION['is_admin'] != 'finderAdmiN'){
  header("Location: index.php");
}

$unique_id = $_GET['unique_id'];
$thread = $_GET['thread'];

$data=array();
$rcv_oauth_user_id = "";

$timezone  = 7; // GMT +7
$now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

/// check if read , dont update read
$sql = "select is_read,oauth_user_id from sbf_message where unique_id='{$unique_id}' ";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
$is_read = $result[0]['is_read']+0;
$rcv_oauth_user_id = $result[0]['oauth_user_id'];
if($is_read != 1){
  $sql = "update sbf_message set is_read=1, date_read='{$now}' where unique_id='{$unique_id}' ";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
}



$sql = "select sbf_message.*,sbfdm_oauth.user_name from sbf_message ";
$sql .= " inner join sbfdm_oauth on sbf_message.oauth_user_id = sbfdm_oauth.oauth_user_id";
$sql .= " where binary thread='{$thread}' ";
$stmt = $dbh->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll( PDO::FETCH_ASSOC );
foreach ($result as $row) {
  $data['subject'] = $row['msg_title'];
  $data['msg'] = $row['msg_html'];
  $data['by'] = $row['user_name'];
  $data['date'] = $row['date_create'];

}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title><?php echo $data['subject'];?></title>
    <style type="text/css">
    table{
      min-width: 400px;
    }
    table td{
      border: solid #c0c0c0 1px;
    }
    textarea{
      width:100%;
      min-height: 200px;
    }
    .mid{
      margin: auto;
      max-width: 60%;
    }
    </style>
  </head>
  <body>

    <table align=center  cellspacing=0 cellpadding=4>
      <tr>
        <td align="right">
          Subject:
        </td>
        <td>
          <?php echo $data['subject'] ?>
        </td>
      </tr>
      <tr>
        <td align="right">
          By:
        </td>
        <td>
          <?php echo $data['by'] . ' - ' . $data['date'] ?>
        </td>
      </tr>
      <tr>
        <td align="right" valign="top">Msg</td>
        <td align="lwft">
          <?php
          $html = htmlspecialchars_decode($data['msg']);
          echo $html;
           ?>
        </td>
      </tr>
    </table>

    <p>
    </p>
    <form method=post action="_reply_message.php">
      <div class="mid">
        <textarea name="replytext" placeholder="Reply text"></textarea>
      </div>
        <div class="mid">
          <button type=submit>Reply</button>
        </div>

      <input type="hidden" name="thread" value="<?php echo $thread;?>" />
      <input type="hidden" name="rcv_oauth_user_id" value="<?php echo $rcv_oauth_user_id;?>" />
    </form>


  </body>
</html>
