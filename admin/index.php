<?php
session_start();

include('../dbconnect.php');
include('functions.php');

$adminLogged = isAdmin();

$isAlert = false;
$AlertText = "";

if(isset($_POST['uname'])){
  $u = $_POST['uname'];
  $p = $_POST['pwd'];
  if($u == 'finderadmin' && $p=='beeradmin'){
    $_SESSION['is_admin'] = 'finderAdmiN';
    $adminLogged = true;
  }else{
    $isAlert = true;
    $AlertText = "<script>alert('Wrong username or password. Please try again');</script>";
  }
}
 ?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <script src="http://code.jquery.com/jquery-3.2.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <title></title>
    <style>
      .center {
        text-align: center;
      }
      .text-right{
        text-align: right;
      }
      form div{
        padding: 4px;
      }
      .center{
        display: block;
        margin-left: auto;
        margin-right: auto
      }
      li{
        padding: 6px;
      }
    </style>
  </head>
  <?php
if($adminLogged){
$out = <<<EOD

<div class='center'>
<H2>Menu</h2>
<ul>
  <li><a href="question_list.php" target=_blank>List คำถาม</a>
  <li><a href="route_list.php" target=_blank>List route</a>
  <li><a href="base_list.php" target=_blank>List base</a>
</ul>
</div>
EOD;

echo $out;

}else{

$out = <<<EOD
<h2 class="center">Login</h2>
<div class='container'>
  <form method="post">
  <div class='row'>
    <div class="col-md-2 col-md-offset-4 text-right">
      Username
    </div>
    <div class="col-md-2">
      <input type="text" name="uname" class="form-control"></input>
    </div>
  </div>

    <div class='row'>
      <div class="col-md-2 col-md-offset-4 text-right">
        Password
      </div>
      <div class="col-md-2">
        <input type="password" name="pwd" class="form-control"></input>
      </div>
    </div>

    <div class="row">
      <div class="col-md-2 col-md-offset-6">
        <button type=submit class="btn btn-primary">Submit</button>
      </div>
    </div>
  </form>
</div>


EOD;

echo $out;
}

if($isAlert){
  echo $AlertText;
}
   ?>
  <body>

  </body>
</html>
