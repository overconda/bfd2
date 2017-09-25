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

    /**
     * Get bases and user info with route
     * @param type $route_id
     * @param type $oauth_user_id
     * @return type
     */
    public function getRouteSvg($route_id) {
        $data = array("route" => array(), "bases" => array());

        // get route info
        $result = $this->database->query("SELECT * FROM sbfdm_route WHERE ID={$route_id}");
        if ($result->num_rows) {
            $row = $result->fetch_assoc();
            $data["route"] = $row;
        }


        return $data;
    }


    public function getRouteBasesUserUnlocked($route_id, $oauth_user_id){
      $bases = array();

      $sql = "select sbfdm_route_base.ID as base_id, sbfdm_route_base.route_id, sbfdm_route_base.base_no, base_title from sbfdm_route_base where route_id={$route_id}";
      $result = $this->database->query($sql);
      if($result->num_rows){
        while($row = $result->fetch_assoc()){

          $bases[$row['base_id']]['base_id'] = $row['base_id'];
          $bases[$row['base_id']]['base_no'] = $row['base_no'];
          $bases[$row['base_id']]['base_title'] = $row['base_title'];
        }
      }

      foreach ($bases as $base_id => $value) {
        $sql = "select count(*) as cc from sbfdm_user_base where base_id = {$base_id} and unlocked_status='true' and oauth_user_id like '{$oauth_user_id}' ";
        $result = $this->database->query($sql);
        $row = $result->fetch_assoc();
        $bases[$base_id]['unlocked'] = $row['cc'];
      }
      return $bases;
    }


    public function getAllRoutes($orderby , $order ){
      $data = array();
      $ORDER = " order by $orderby $order";

      $sql = "select * from sbfdm_route where active=1 $ORDER";
      $result = $this->database->query($sql);
        while($row = $result->fetch_assoc()){
          $data['route'][]=$row;
        }
      return $data;
    }


    function generateRandomString($length = 16) {
			$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
			$charactersLength = strlen($characters);
			$randomString = '';
			for ($i = 0; $i < $length; $i++) {
					$randomString .= $characters[rand(0, $charactersLength - 1)];
			}
			return $randomString;
  	}

}/// end class

/**
 * Main Program
 */
$route_api = new ROUTE_API();

if ($_POST['method'] == "get_route_svg") {
    $response = $route_api->getRouteSvg($_POST['route_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_all_routes") {
    $response = $route_api->getAllRoutes($_POST['orderby'], $_POST['order']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_route_bases_user_unlocked") {
    $response = $route_api->getRouteBasesUserUnlocked($_POST['route_id'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}/*else if ($_POST['method'] == "get_base_user") {
    $response = $route_api->getBaseUser($_POST['base_id'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_nbase_user") {
    $response = $route_api->getNBaseUser($_POST['base_ids'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}*/
