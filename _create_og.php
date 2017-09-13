<?php
include_once("dbconnect.php");
// Font initial : Paytone Regular
$fontURL = "css/fonts/paytoneone/paytoneone-webfont.ttf";


$_userLevel = 2;
$_user_id= "10155074868768892"; /// Test
$_myCardType = "guardian";

$finishFileUrl = createCardGuardian($_user_id, $_myCardType, "Suphajittt", "The Rocker Ave.", $_userLevel);
echo "<html><body bgcolor='#0'><img src='$finishFileUrl'></body></html>";


function createCardGuardian($_user_id, $_myCardType,$userName, $baseName , $level){
  $greeting = "Bravo!";
  $lastText = "Base Guardian";
  $finishFileUrl = _createCard($_user_id, $_myCardType, $greeting, $userName, $baseName, $level, $lastText);
  return $finishFileUrl;
}


function _createCard($_user_id, $_myCardType, $greeting, $userName, $venue, $level, $lastText){
  global $fontURL;
  $mergedCardFile = mergeAvatarCard($_user_id, $_myCardType);
  writeTextToImage($mergedCardFile, $level, $fontURL, 16, '#464646', 94,172); //// Level
  writeTextToImage($mergedCardFile, $greeting, $fontURL, 30, '#fad533', 200,68); //// Greeting
  writeTextToImage($mergedCardFile, $userName . " is now", $fontURL, 18, '#464646', 200,100); //// Name
  writeTextToImage($mergedCardFile, $venue, $fontURL, 24, '#8d8d8d', 200,140); //// Base Name
  writeTextToImage($mergedCardFile, $lastText, $fontURL, 24, '#8d8d8d', 200,170); ////
  return $mergedCardFile; /// return file url
}





function mergeAvatarCard($user_id, $myCardType){

  global $dbh;

  $AvatarWidth = 120;
  $WIDTH = 476;
  $HEIGHT = 279;

  $cards = array(
    "unlocked" => "ogimages_asset/og-unlocked.png",
    "guardian" => "ogimages_asset/og-guardian.png",
    "complete_route" => "ogimages_asset/og-complete-route.png",
    "level_up" => "ogimages_asset/og-level-up.png"
  );


  $myCard = $cards[$myCardType];

  $sql = "select user_profile_photo from sbfdm_oauth where user_id = $user_id ";
  $stmt = $dbh->prepare($sql);
  $stmt->execute();
  $result = $stmt->fetchAll( PDO::FETCH_ASSOC );
  $avatar = $result[0]['user_profile_photo'];

  //echo "$avatar";
  $ImageProfile = "ogimage/u-" . $user_id . ".jpg";

  $realpath = getcwd();
  $ogfile = $realpath . "/" . $ImageProfile;

  $sourceurl = GetTheImage($avatar);
  $save = fopen($ogfile, "wb"); //this is name of new file that i save
  fwrite($save, $sourceurl);
  fclose($save);



  $ResizedProfileImage = $ImageProfile; // reesized save in same name
  /// Avatar circle
  resizeImage($ImageProfile, $ResizedProfileImage, $AvatarWidth, $AvatarWidth);




  $urlAvatar = $ResizedProfileImage;
  $urlCard = $myCard;

  //echo "<br>URL Avatar : " . $urlAvatar;
  //echo "<br>URL Card : " . $urlCard;

  //$imgAvatar = imagecreatefromjpeg($ResizedProfileImage);
  //$imgCard = imagecreatefrompng($myCard);

  $imgAvatar = imagecreatefromjpeg($urlAvatar);
  $imgCard = imagecreatefrompng($urlCard);


  $nowShort = date("ymdHis");
  $outputFile = "ogimage/" . $myCardType . "-" . $user_id . "-" . $nowShort . ".png";

  //imagecopy($dest_image, $a, 36, 42, 0, 0, $WIDTH, $HEIGHT);
  $imgAvatarTemp = imagecreatetruecolor($WIDTH, $HEIGHT);
  imagecopy($imgAvatarTemp, $imgAvatar , 36, 42, 0, 0, $WIDTH, $HEIGHT);

  imagealphablending($imgCard, true);
  imagesavealpha($imgCard, true);
  imagecopy($imgAvatarTemp, $imgCard , 0, 0, 0, 0, $WIDTH, $HEIGHT);


  imagepng($imgAvatarTemp, $outputFile,0);


  //destroy all the image resources to free up memory
  imagedestroy($imgAvatar);
  imagedestroy($imgCard);
  imagedestroy($imgAvatarTemp);

  return $outputFile;
}

function writeTextToImage($imageUrl, $text, $fontURL , $fontSize, $colorCode /* ex. #006EFF */, $x, $y ){
  $rgb = hexToRgb($colorCode);
  $image = imagecreatefromfile( $imageUrl);
  $colorText = imagecolorallocate($image, $rgb['r'], $rgb['g'], $rgb['b']);
  imagettftext($image, $fontSize /*font size*/, 0 /*angle*/, $x , $y, $colorText, $fontURL, $text);
  imagepng($image, $imageUrl, 0);
  imagedestroy($image);
}

function resizeImage($filename, $newName, $max_width, $max_height){
    list($orig_width, $orig_height) = getimagesize($filename);

    $width = $orig_width;
    $height = $orig_height;

    # taller
    if ($height > $max_height) {
        $width = ($max_height / $height) * $width;
        $height = $max_height;
    }

    # wider
    if ($width > $max_width) {
        $height = ($max_width / $width) * $height;
        $width = $max_width;
    }

    $image_p = imagecreatetruecolor($width, $height);

    $image = imagecreatefromjpeg($filename);

    imagecopyresampled($image_p, $image, 0, 0, 0, 0,
                                     $width, $height, $orig_width, $orig_height);


    //return $image_p;
    imagejpeg($image_p, $newName,100);
    imagedestroy($image_p);
}

function GetTheImage($linky) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_POST, 0);
    curl_setopt($ch, CURLOPT_URL, $linky);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    # ADDED LINE:
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

function generateRandomString($length = 30) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function hexToRgb($hex, $alpha = false) {
   $hex      = str_replace('#', '', $hex);
   $length   = strlen($hex);
   $rgb['r'] = hexdec($length == 6 ? substr($hex, 0, 2) : ($length == 3 ? str_repeat(substr($hex, 0, 1), 2) : 0));
   $rgb['g'] = hexdec($length == 6 ? substr($hex, 2, 2) : ($length == 3 ? str_repeat(substr($hex, 1, 1), 2) : 0));
   $rgb['b'] = hexdec($length == 6 ? substr($hex, 4, 2) : ($length == 3 ? str_repeat(substr($hex, 2, 1), 2) : 0));
   if ( $alpha ) {
      $rgb['a'] = $alpha;
   }
   return $rgb;
}

function imagecreatefromfile( $filename ) {
    switch ( strtolower( pathinfo( $filename, PATHINFO_EXTENSION ))) {
        case 'jpeg':
        case 'jpg':
            return imagecreatefromjpeg($filename);
        break;

        case 'png':
            return imagecreatefrompng($filename);
        break;

        case 'gif':
            return imagecreatefromgif($filename);
        break;

        default:
            throw new InvalidArgumentException('File "'.$filename.'" is not valid jpg, png or gif image.');
        break;
    }
}
 ?>
