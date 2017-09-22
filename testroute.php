<?php
error_reporting(E_ALL);
ini_set('display_errors', 'on');


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

        /*
        // get base user info
        $result = $this->database->query("SELECT * FROM sbfdm_route_base WHERE route_id={$route_id}");
        $basesHTML = "";

        $data['basesHTML'] = $basesHTML;
        */

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

$response = $route_api->getRouteSvg(1);

echo "<pre>";
print_r($response);
echo "</pre>";


/*
if ($_POST['method'] == "get_route_svg") {
    $response = $route_api->getRouteSvg($_POST['route_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}

*/
/*else if ($_POST['method'] == "get_base_user") {
    $response = $route_api->getBaseUser($_POST['base_id'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
} else if ($_POST['method'] == "get_nbase_user") {
    $response = $route_api->getNBaseUser($_POST['base_ids'], $_POST['oauth_user_id']);
    header('Content-Type: application/json');
    echo(json_encode($response));
}*/
?>
