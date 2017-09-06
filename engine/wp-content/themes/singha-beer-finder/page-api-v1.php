<?php

// class api
class SBF_API {
	public $data = array();
	public function __construct(){
		$this->_init();
	}

	public function _init(){
		if($_POST['mode']=="getNearbyBases"){
			$this->getNearbyBases();
			
		}
	}

	public function getNearbyBases(){
		global $wpdb;

		$latpoint = $_POST['latitude'];
		$longpoint = $_POST['longitude'];
		$radius = 30.0;

		$sql = 'SELECT * FROM (SELECT b.ID, b.base_title,b.base_latitude, b.base_longitude, p.radius,
				p.distance_unit
	             * DEGREES(ACOS(COS(RADIANS(p.latpoint))
	             * COS(RADIANS(b.base_latitude))
	             * COS(RADIANS(p.longpoint - b.base_longitude))
	             + SIN(RADIANS(p.latpoint))
	             * SIN(RADIANS(b.base_latitude)))) AS distance
			FROM sbfdm_base AS b

			JOIN (
			    SELECT  13.880901  AS latpoint,  100.369810 AS longpoint,
			            50.0 AS radius,      111.045 AS distance_unit
			) AS p ON 1=1

			WHERE b.base_latitude
			 BETWEEN p.latpoint  - (p.radius / p.distance_unit)
			     AND p.latpoint  + (p.radius / p.distance_unit)
			AND b.base_longitude
			 BETWEEN p.longpoint - (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
			     AND p.longpoint + (p.radius / (p.distance_unit * COS(RADIANS(p.latpoint))))
			) AS d
			WHERE distance <= radius
			ORDER BY distance
			LIMIT 15';

		$query = $wpdb->get_results($sql);
		var_dump($query);

		//$this->data['status'] = 0;
		//$this->output();
	}

	public function output(){
		header('Content-Type: application/json');
		echo json_encode($this->data);
	}
}

// initial api
new SBF_API();