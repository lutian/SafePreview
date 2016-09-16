
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

// Init
$safePreview = new SafePreview();
// set lang
$safePreview->setLang('en');
// set logo path
$pathLogo = 'images/logo/logo.png';
$safePreview->setLogo($pathLogo);
// set image path
$pathImage = 'images/origin/image.jpg';
$safePreview->setPathImage($pathImage);
// set image blured path
$pathBlured = 'images/blured/blured.jpg';
$safePreview->setPathBlured($pathBlured);
// set image url
$urlImage = 'http://{YOUR_URL}/safePreview/src/images/origin/image.jpg';
$safePreview->setUrlImage($urlImage);
// set image blured url
$urlBlured = 'http:/{YOUR_URL}/safePreview/src/images/blured/blured.jpg';
$safePreview->setUrlBlured($urlBlured);
// set the condition (add alert message: do you want to see it anyway?)
$safePreview->setCondition(TRUE);
// verify if image safe
$isSafe = $safePreview->isImageSafe();
if(!$isSafe) {
	// if image not safe
	$safePreview->mergeImages();
	// show blured image
	$html = $safePreview->showPreview();
} else {
	// show original image
	$html = $safePreview->showOriginal();
}
echo $html;

```


License
----

MIT


[Luciano Salvino]:http://mueveloz.com/


