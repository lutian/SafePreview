
# lutian/safePreview

> graphics-tools


Show blured image and alert message when picture is not safe


### Version
0.1

### Authors

* [Luciano Salvino] - <lsalvino@hotmail.com>


### Installation

To use the tools of this repo only has to be required in your composer.json:

```
{
   "require":{
      "lutian/safePreview": "dev-master"
   }
}
```


### Use

```

include('SafePreview.php');

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
```


License
----

MIT


[Luciano Salvino]:http://mueveloz.com/


