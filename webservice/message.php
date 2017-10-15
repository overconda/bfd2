<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methodhs: GET, POST, PUT');

require_once 'database.php';

/**
 * SBF API Class
 *
 */
class ROUTE_API {

    public $database;

    /**
     * Construction
     */
    public function __construct() {
        $this->database = new Database();
    }

    public function getNameKeypress($txt){
      $data=array();
      $sql = "select $sdbfm.oauth_user_id, sbfdm_oauth.user_name from sbfdm_oauth where username like '%{$txt}%'; ";
      $result = $this->database->query($sql);
      while($row = $result->fetch_assoc()){
        $data[$row['oauth_user_id']] = $row['user_name'];
      }

      return $data;
    }

    public function getInbox($oauth_user_id){
      $data=array();
      $sql = "select sbf_message.* /*,sbfdm_oauth.user_name*/ ";
      $sql .= " from sbf_message ";
      //$sql .= " inner join sbfdm_oauth on sbf_message.oauth_user_id = sbfdm_oauth.oauth_user_id ";
      $sql .= " where direction=1 and rcv_oauth_user_id='{$oauth_user_id}' ";
      $sql .= " order by date_create desc";
      $result = $this->database->query($sql);
      $i=0;
      while($row = $result->fetch_assoc()){
        $data[$i]['msg_id'] = $row['msg_id'];
        $data[$i]['unique_id'] = $row['unique_id'];
        $data[$i]['thread'] = $row['thread'];
        //$data[$i]['user_name'] = $row['user_name'];
        $data[$i]['oauth_user_id'] = $row['oauth_user_id'];
        $data[$i]['msg_title'] = $row['msg_title'];
        $data[$i]['date_create'] = $row['date_create'];
        $data[$i]['is_read'] = $row['is_read'];

        $i++;
      }
      //return $sql;
      return $data;
    }

    public function getMessage($unique_id){

      //// mark read
      $timezone  = 7; // GMT +7
      $now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

      $sql = "update sbf_message set is_read=1 , date_read='{$now}' where unique_id='{$unique_id}' ";
      $this->database->query($sql);

      /// get message
      $data = array();
      $sql = "select * from sbf_message where unique_id='{$unique_id}' ";
      $result = $this->database->query($sql);
      $row = $result->fetch_assoc();

      return $row;
    }



    public function sendMessage($subject, $message, $oauth_user_id){

      $thread = generateRandomString(32);
      $unique_id = generateRandomString(16);
      $message = str_replace('<script' , '[xxscriptxx]', $message); /// prevent intruder
      $message = str_replace('<style' , '[xxstylexx]', $message); /// prevent intruder
      $message = str_replace(' style=' , '[xxstyle=xx]', $message); /// prevent intruder
      $message = nl2p($message);
      $subject = htmlspecialchars($subject, ENT_QUOTES);
      $message = htmlspecialchars($message, ENT_QUOTES);
      //$now = date('Y-m-d H:i:s');
      $timezone  = 7; // GMT +7
      $now = gmdate("Y-m-d H:i:s", time() + 3600*($timezone/*+date("I")*/));

      $sql = "insert into sbf_message (unique_id, is_broadcast, oauth_user_id, direction, thread, msg_title, msg_html, is_read, date_create) ";
      $sql .= " values ('{$unique_id}', '0', '{$oauth_user_id}', 2 , '{$thread}', '$subject', '$message', 0, '{$now}')";

      $this->database->query($sql);

      return "success";

    }




}/// end class

/**
 * Main Program
 */

function generateRandomString($length = 16) {
   $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
   $charactersLength = strlen($characters);
   $randomString = '';
   for ($i = 0; $i < $length; $i++) {
       $randomString .= $characters[rand(0, $charactersLength - 1)];
   }
   return $randomString;
 }

function nl2p($string){
    $paragraphs = '';

    foreach (explode("\n", $string) as $line) {
        if (trim($line)) {
            $paragraphs .= '<p>' . $line . '</p>';
        }
    }
    return $paragraphs;
}

$route_api = new ROUTE_API();

if ($_POST['method'] == "get_name_keypress") {
    $response = $route_api->getNameKeypress($_POST['txt']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "send_message") {
    $response = $route_api->sendMessage($_POST['subject'], $_POST['message'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "get_inbox") {
    $response = $route_api->getInbox( $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "get_message") {
    $response = $route_api->getMessage( $_POST['unique_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}
