<?php

// Font initial : Paytone Regular
$fontURL = "css/fonts/paytoneone/paytoneone-webfont.ttf";

$cards = array(
  "unlocked" => "ogimages_asset/og-unlocked.png",
  "guardian" => "ogimages_asset/og-guardian.png",
  "complete_route" => "ogimages_asset/og-complete-route.png",
  "level_up" => "ogimages_asset/og-level-up.png"
);


$AvatarWidth = 120;
$WIDTH = 476;
$HEIGHT = 279;

include_once("dbconnect.php");

//// user_id got from POST
$user_id= "10155074868768892"; /// Test
$myCardType = "guardian";
$myCard = $cards[$myCardType];
//echo "mycrd: " . $myCard;
//exit;

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

//$imgAvatar = imagecreatefromjpeg($ResizedProfileImage);
//$imgCard = imagecreatefrompng($myCard);

$imgAvatar = imagecreatefromjpeg($urlAvatar);
$imgCard = imagecreatefrompng($urlCard);

echo "IMGAVATAR: " . $urlAvatar;
echo "<Br>IMGCARD: " . $urlCard;
echo "<br>";
echo "<br>Res IMGAVATAR: " . $imgAvatar;
echo "<Br>Res IMGCARD: " . $imgCard;
//echo "IMGAVATAR: " . $imgAvatar;

$nowShort = date("ymdHis");
$outputFile = "ogimage/" . $myCardType . "-" . $user_id . "-" . $nowShort . ".jpg";

//imagecopy($dest_image, $a, 36, 42, 0, 0, $WIDTH, $HEIGHT);
$imgAvatarTemp = imagecreatetruecolor($WIDTH, $HEIGHT);
imagecopy($imgAvatarTemp, $imgAvatar , 36, 42, 0, 0, $WIDTH, $HEIGHT);

imagealphablending($imgCard, true);
imagesavealpha($imgCard, true);
imagecopy($imgAvatarTemp, $imgCard , 0, 0, 0, 0, $WIDTH, $HEIGHT);
echo "<br>";
//echo "<br>Res DEST: " . $dest;
//header('Content-Type: image/jpeg');

imagejpeg($imgAvatarTemp, $outputFile,100);



//destroy all the image resources to free up memory
imagedestroy($imgAvatar);
imagedestroy($imgCard);
imagedestroy($imgAvatarTemp);


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
 ?>
