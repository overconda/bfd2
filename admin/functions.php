<?php
session_start();
$_isAdmin = false;
if(isset($_SESSION['is_admin'])){
  if($_SESSION['is_admin']=='finderAdmiN'){
    $_isAdmin = true;
  }
}

function isAdmin(){
  return $_isAdmin;
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
 ?>
