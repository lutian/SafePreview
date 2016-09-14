<?php

include('safePreview.php');

$pathLogo = 'images/logo/logo.png';
$pathImage = 'images/origin/image.jpg';
$pathBlured = 'images/blured/blured.jpg';
$urlImage = 'http://{YOUR_URL}/safePreview/src/images/origin/image.jpg'; 
$urlBlured = 'http:/{YOUR_URL}/safePreview/src/images/blured/blured.jpg';


// Init
$safePreview = new SafePreview();
// set lang
$safePreview->setLang('en');
// set logo path
$safePreview->setLogo($pathLogo);
// set image path
$safePreview->setPathImage($pathImage);
// set image blured path
$safePreview->setPathBlured($pathBlured);
// set image url
$safePreview->setUrlImage($urlImage);
// set image blured url
$safePreview->setUrlBlured($urlBlured);
// set the condition (do you want to see it anyway?)
$safePreview->setCondition(TRUE);
// verify if image safe
$isSafe = $safePreview->isImageSafe();
if(!$isSafe) {
	// if image not safe
	$safePreview->mergeImages();
	// show image blured
	$html = $safePreview->showPreview();
} else {
	$html = $safePreview->showOriginal();
}

?>

<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.0.2/jquery.min.js"></script>
<?php echo $html;?>