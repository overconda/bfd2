<?php

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Access-Control-Allow-Methodhs: GET, POST, PUT');

require_once 'database.php';

/**
 * SBF API Class
 *
 */
class COUNTDOWN_API {

  public $database;

  /**
   * Construction
   */
  public function __construct() {
      $this->database = new Database();
  }

  public function getStartCountdownTime($oauth_user_id){
    $ret="";
    $sql = "select * from sbfdm_user_incorrect where  oauth_user_id='{$oauth_user_id}' ";
    $result = $this->database->query($sql);

    if($result->num_rows){
      $row = $result->fetch_assoc();
      $ret = $row['mark_time'];
    }else{
      /// create the new one
      $now = date('Y-m-d H:i:s');

      $sql = "insert into sbfdm_user_incorrect (oauth_user_id, mark_time) values ( '{$oauth_user_id}', '{$now}')";
      $this->database->query($sql);

      $ret = $now;
    }
    $data= array('time'=>'');
    $data['time']=$ret;
    return $data;
  }

  public function deleteCountdownTime($oauth_user_id){
    $sql = "delete from sbfdm_user_incorrect where oauth_user_id='{$oauth_user_id}' ";
    $this->database->query($sql);
  }

  public function getBaseInfo($base_id){
    $data = array();
    $sql = "select sbfdm_route.route_title, sbfdm_route_base.base_title, sbfdm_route_base.base_no from sbfdm_route ";
    $sql .= "inner join sbfdm_route_base on sbfdm_route.ID = sbfdm_route_base.route_id ";
    $sql .= " where sbfdm_route_base.ID = $base_id";
    $result = $this->database->query($sql);
    if($result->num_rows){
      $row = $result->fetch_assoc();
      $data=$row;
    }
    return $data;
  }
}/// end class

/**
 * Main Program
 */
$countdown_api = new COUNTDOWN_API();

if ($_POST['method'] == "get_start_countdown_time") {
    $response = $countdown_api->getStartCountdownTime( $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "delete_countdown_time") {
    $response = $countdown_api->deleteCountdownTime( $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}else if ($_POST['method'] == "get_base_info") {
    $response = $countdown_api->getBaseInfo( $_POST['base_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}
 ?>
