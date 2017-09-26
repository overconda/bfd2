<?php
require_once 'database.php';

class ROUTECK_API{

  public $database;

  public function __construct() {
      $this->database = new Database();
  }

  public function findUserRoutes($oauth_user_id){
    $data = array();
    $sql = "select distinct sbfdm_route.ID as route_id, sbfdm_route.route_title ";
    $sql .= " from sbfdm_route ";
    $sql .= " inner join sbfdm_user_base on sbfdm_route.ID = sbfdm_user_base.route_id";
    $sql .= " where sbfdm_user_base.unlocked_status='true' and sbfdm_user_base.oauth_user_id = '{$oauth_user_id}' ";

    $result = $this->database->query($sql);
    while($row = $result->fetch_assoc()){
      $route_id = $row['route_id'];
      $data[$route_id]['route_id'] = $row['route_id'];
      $data[$route_id]['route_title'] = $row['route_title'];
    }
    return $data;
  }

  public function findRoutesBasesNum($data){
    if($data){
      foreach($data as $route){
        $route_id = $route['route_id'];
        $sql = "select count(*) as cc from sbfdm_route_base where route_id = $route_id";
        $result = $this->database->query($sql);
        $row = $result->fetch_assoc();
        $data[$route_id]['bases_num'] = $row['cc'];
      }
    }

    return $data;
  }

  public function findUsersRouteBasesNum($data, $oauth_user_id){
    if($data){
      foreach($data as $route){
        $route_id = $route['route_id'];
        $sql = "select count(*) as cc from sbfdm_user_base where route_id = {$route_id} and unlocked_status='true' and oauth_user_id='{$oauth_user_id}' ";
        $result = $this->database->query($sql);
        $row = $result->fetch_assoc();
        $data[$route_id]['user_bases_num'] = $row['cc'];
      }
    }
    return $data;
  }


  public function checkUserRouteStatus($oauth_user_id){
    $routeComplete = array();
    $routeToGo = array(); // participate but not complete
    $data = array();

    $data = $this->findUserRoutes($oauth_user_id);
    $dataRoutes = $this->findRoutesBasesNum($data);
    $dataUser = $this->findUsersRouteBasesNum($data, $oauth_user_id);

    /*
    echo "<pre>";
    print_r($data);
    print_r($dataRoutes);
    print_r($dataUser);
    echo "</pre>";
    */

    $retArr = array();

    foreach($data as $route){
      $route_id = $route['route_id'];
      $route_title = $route['route_title'];
      if($dataRoutes[$route_id]['bases_num'] == $dataUser[$route_id]['user_bases_num']){
        $routeComplete[$route_id]['route_id'] = $route_id;
        $routeComplete[$route_id]['route_title'] = $route_title;
      }else{
        $routeToGo[$route_id]['route_id'] = $route_id;
        $routeToGo[$route_id]['route_title'] = $route_title;
      }

    }

    $retArr['routeComplete'] = $routeComplete;
    $retArr['routeToGo'] = $routeToGo;

    return $retArr;

  }

}


$routeck_api = new ROUTECK_API();


$userRoutes = $routeck_api->checkUserRouteStatus('tw_15776186');

echo "<pre>";
print_r($userRoutes);
echo "</pre>";
 ?>
