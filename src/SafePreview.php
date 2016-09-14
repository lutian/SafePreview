<?php

/**
 * Class SafePreview
 * @package Lutian\SafePreview
 * @author Luciano Salvino <lsalvino@hotmail.com>
 */

class SafePreview
{

	/** @var boolean */
    private $isSafe;
	
	/** @var object */
    private $image;
	
	/** @var string */
    private $pathImage;
	
	/** @var string */
    private $urlImage;
	
	/** @var string */
    private $pathBlured;
	
	/** @var string */
    private $urlBlured;
	
	/** @var string */
    private $logo;
	
	/** @var string */
    private $lang;
	
	/** @var string */
    private $message;
	
	/** @var booblean */
    private $condition = TRUE;
	
	/** @var int */
    private $level = 2;
	
	/** @var array */
	private $arrLogoSize;
	
	/** @var array */
	private $arrBluredSize;
	
	/** @var int */
    private $areaW;
	
	/** @var int */
    private $areaH;
	
	/** @var int */
    private $areaX;
	
	/** @var int */
    private $areaY;
	
	/**
     * @var string txtColor
     */
	private $txtColor = '';
	
	/**
     * @var string font file path
     */
	private $fontFile = 'fonts/arial.ttf';
	
	/**
     * @var string font size
     */
	private $fontSize = 22;

    /**
     * @param $file
     * @param $message
     * @return file blured image with message insert on it
     */
    private function blurImage()
    {
		$this->arrBluredSize = $this->getSizeImage($this->pathImage);
		
		$this->image = ImageCreateTrueColor($this->arrBluredSize['w'], $this->arrBluredSize['h']);
		
		$gdExt = substr($this->pathImage, -3);

		if(strtolower($gdExt) == "gif") {
            if (!$this->image = imagecreatefromgif($this->pathImage)) {
                exit;
            }
        } else if(strtolower($gdExt) == "jpg") {
            if (!$this->image = imagecreatefromjpeg($this->pathImage)) {
                exit;
            }
        } else if(strtolower($gdExt) == "png") {
            if (!$this->image = imagecreatefrompng($this->pathImage)) {
                exit;
            }
        } else {
            die;
        }

		imagefilter($this->image, IMG_FILTER_PIXELATE, 20, true);
		
		imagefilter($this->image, IMG_FILTER_GAUSSIAN_BLUR);

    }

    /**
     * @return string
     */
    private function messageToLang()
    {
        switch($this->lang) {
			case 'en':
				$message = 'This image is not safe. ' . ($this->condition == TRUE ? 'Do you want to see it anyway?' : '');
				break;
			case 'es':
				$message = 'La imagen posee contenido sensible. ' . ($this->condition == TRUE ? '¿Desea verla de todas formas?' : '');
				break;
			default:
				$message = 'This image is not safe. ' . ($this->condition == TRUE ? 'Do you want to see it anyway?' : '');
				break;
		}
		
		$this->setMessage($message);
    }
	
	private function addLogo() {
		$this->arrLogoSize = $this->getSizeImage($this->logo);
		
		$this->areaW = ($this->arrBluredSize['w']-$this->arrLogoSize['w']-20);
		$this->areaH = ($this->arrBluredSize['h']-20);
		$this->areaX = 20;
		$this->areaY = ($this->arrBluredSize['h']-$this->arrLogoSize['h']+20);
		
		// Get most significant colors from image
		$colorsKeys=array_keys($this->getColor());
		
		$this->txtColor = $this->hex2rgb($this->getBrightness($colorsKeys[0]));
		
		// logo pos
		$xLogo = ($this->arrBluredSize['w']-$this->arrLogoSize['w']-20);
		$yLogo = ($this->arrBluredSize['h']-$this->arrLogoSize['h']-20);
		
		$gdExt = substr($this->logo, -3);

		$imageLogo = ImageCreateTrueColor($this->arrBluredSize['w'], $this->arrBluredSize['h']);
		
		if(strtolower($gdExt) == "gif") {
            if (!$imageLogo = imagecreatefromgif($this->logo)) {
                exit;
            }
        } else if(strtolower($gdExt) == "jpg") {
            if (!$imageLogo = imagecreatefromjpeg($this->logo)) {
                exit;
            }
        } else if(strtolower($gdExt) == "png") {
            if (!$imageLogo = imagecreatefrompng($this->logo)) {
                exit;
            }
        } else {
            die;
        }
		
		imagecopyresampled($this->image, $imageLogo, $xLogo, $yLogo, 0, 0, $this->arrBluredSize['w'], $this->arrBluredSize['h'], $this->arrBluredSize['w'], $this->arrBluredSize['h']);
		imagealphablending($this->image,true);
		imagedestroy($imageLogo);
	}
	
	private function addMessage() {
		$this->messageToLang();
		$messageColor = imagecolorallocate($this->image, $this->txtColor['r'], $this->txtColor['g'], $this->txtColor['b']);
		$wField = $this->arrBluredSize['w']-40;
		$hField = $this->areaH;
		$arrWords = $this->fitTextOnBox($this->message,$wField,$hField);

		$xBox = 0; 
        $yBox = 10;
        for($t=0;$t<count($arrWords);$t++) {
            $bbox2 = imagettfbbox ( $this->fontSize, 0, $this->fontFile, $arrWords[$t] );
            $ww = $bbox2[4] - $bbox2[6];  
            $hh = $bbox2[1] - $bbox2[7];  
			$xBox = $this->areaX+($wField/2)-($ww/2); 
			$yBox += ($hh*2);
            imagettftext($this->image, $this->fontSize, 0, $xBox, $yBox, $messageColor, $this->fontFile, $arrWords[$t]);
        }
	}
	
	public function mergeImages() {
		
		// blur image
		$this->blurImage();
		// add logo
		$this->addLogo();
		// add message
		$this->addMessage();
		
		imagejpeg($this->image, $this->pathBlured, 90);
		imagedestroy($this->image);
	}
	
	/*
	* Convert HEX to RGB color
	* @param: string $hex color code
	* @return: array $rgb colors 
	*/
	
	private function hex2rgb($hex) {
      $color = str_replace('#','',$hex);
      $RGBColors = array('r' => hexdec(substr($color,0,2)),
                   'g' => hexdec(substr($color,2,2)),
                   'b' => hexdec(substr($color,4,2)));
      return $RGBColors;
    }
	
	/*
	* Get the brightness of a color
	* @param: string $hex html code (ex: #dd2200)
	* @return: string $brightness in html code 
	*/
	
	private function getBrightness($hex) {
		// returns brightness value from 0 to 255

		// strip off any leading #
		$hex = str_replace('#', '', $hex);

		$c_r = hexdec(substr($hex, 0, 2));
		$c_g = hexdec(substr($hex, 2, 2));
		$c_b = hexdec(substr($hex, 4, 2));

		$color = (($c_r * 299) + ($c_g * 587) + ($c_b * 114)) / 1000;
	
		if ($color > 130) $brightness = '#000000';
		else $brightness = '#FFFFFF';
		return $brightness;
	
	}
	
	private function fitTextOnBox($string,$width,$height) {
		// detect the w & h of string
		$bbox2 = imagettfbbox ( $this->fontSize, 0, $this->fontFile, $string );
        $ww = $bbox2[4] - $bbox2[6];  
        $hh = $bbox2[1] - $bbox2[7];  
		if($width > $ww) {
			if($height > $hh) {
				return array($string);
			} else {
				// try with font smaller
				$this->fontSize = ($this->fontSize-1);
				return $this->fitTextOnBox($string,$width,$height);
			}
		} else {
			// get font width for each letter
			$letterWidth = ceil($ww/mb_strlen($string));
			$widthBlock = ceil($width / $letterWidth)-2;
			// split string in blocks
			$blocks = $this->longWordWrap($string,$widthBlock);
			// verify tha string height is grater than height box
			$totH = (count($blocks)*($hh+0));
			if($totH > $height) {
				$this->fontSize = ($this->fontSize-1);
				return $this->fitTextOnBox($string,$width,$height);
			} else {
				return $blocks;
			}
		}
	}

	/*
	* Fit headlines on area
	* @param: string $string text of headline
	* @param: integer $len length of paragraph
	* @return: array $outstring 
	*/
	
	private function longWordWrap($string,$len) {
       $string = wordwrap($string,$len,"\n",1);
       $lines = explode("\n", $string); 
       return $lines;
    }
	
	/*
	* Get most significant color from image
	* @param: string $image image path
	* @param: int $x X position of box area
	* @param: int $y Y position of box area 
	* @param: int $w width of box area
	* @param: int $h height of box area
	* @return: array of significant colors
	*/
	
	private function getColor()
	{
		if (is_file($this->pathImage))
		{
			// Resize image to get most significant colors
			$arrayHex = array();
			$PREVIEW_WIDTH    = 150;  
			$PREVIEW_HEIGHT   = 150;
			$size = GetImageSize($this->pathImage);
			$scale=1;
			if ($size[0]>0)
			$scale = min($PREVIEW_WIDTH/$size[0], $PREVIEW_HEIGHT/$size[1]);
			if ($scale < 1)
			{
				$width = floor($scale*$size[0]);
				$height = floor($scale*$size[1]);
				// Set the headlines area coordinates and measures
				$areaW = floor($scale*$this->areaW);
				$areaH = floor($scale*$this->areaH);
				$areaX = floor($scale*$this->areaX);
				$areaY = floor($scale*$this->areaY);
			}
			else
			{
				$width = $size[0];
				$height = $size[1];
				$areaW = $this->areaW;
				$areaH = $this->areaH;
				$areaX = $this->areaX;
				$areaY = $this->areaY;
			}
			$im = imagecreatetruecolor($width, $height);
			if ($size[2]==1)
			$imageOrig=imagecreatefromgif($this->pathImage);
			if ($size[2]==2)
			$imageOrig=imagecreatefromjpeg($this->pathImage);
			if ($size[2]==3)
			$imageOrig=imagecreatefrompng($this->pathImage);
			imagecopyresampled($im, $imageOrig, 0, 0, 0, 0, $width, $height, $size[0], $size[1]); 
			// crop the image to fit the area selected
			$area = array('x'=>$areaX,'y'=>$areaY,'width'=>$areaW,'height'=>$areaH);
			$im = imagecrop($im,$area);
			$imgWidth = imagesx($im);
			$imgHeight = imagesy($im);
			for ($y=0; $y < $imgHeight; $y++)
			{
				for ($x=0; $x < $imgWidth; $x++)
				{
					$index = imagecolorat($im,$x,$y);
					$Colors = imagecolorsforindex($im,$index);
					$Colors['red']=intval((($Colors['red'])+15)/32)*32;  
					$Colors['green']=intval((($Colors['green'])+15)/32)*32;
					$Colors['blue']=intval((($Colors['blue'])+15)/32)*32;
					if ($Colors['red']>=256)
					$Colors['red']=240;
					if ($Colors['green']>=256)
					$Colors['green']=240;
					if ($Colors['blue']>=256)
					$Colors['blue']=240;
					$arrayHex[]=substr("0".dechex($Colors['red']),-2).substr("0".dechex($Colors['green']),-2).substr("0".dechex($Colors['blue']),-2);
				}
			}
			$arrayHex=array_count_values($arrayHex);
			natsort($arrayHex);
			$arrayHex=array_reverse($arrayHex,true);
			return $arrayHex;

		}
		else die();
	}
	
	public function showPreview() {
		// show html button to accept view original image
		$htmlImage = '<img id="imgBlured" src="' . $this->urlBlured . '" alt="' . $this->message . '">';
		if($this->condition == TRUE) {
			$htmlImage = '<a href="javascript:showOriginal();" title="' . $this->message . '">' . $htmlImage . '</a>';
			$htmlImage .= '<script>function showOriginal(){ $("#imgBlured").attr("src","' . $this->urlImage . '"); }</script>';
		}
		
		return $htmlImage;
	}
	
	public function showOriginal() {
		return '<img src="'.$this->urlImage.'">';
	}
	
	private function getSizeImage($image) {
		$arr = array('w' => 0, 'h' => 0);
		if(is_file($image)) {
            $arrImage = @getimagesize($image);
			$arr = array('w' => $arrImage[0], 'h' => $arrImage[1]);
		}
		return $arr;
	}
	
	public function isImageSafe(){

		//$this->setIsSafe(FALSE);
		//return $this->getIsSafe();
		
		// You can try to use External provider like
		// https://market.mashape.com/sphirelabs/advanced-porn-nudity-and-adult-content-detection#nudity-check
		$queryString = '?url='.urlencode($this->urlImage);
		$url = 'https://sphirelabs-advanced-porn-nudity-and-adult-content-detection.p.mashape.com/v1/get/index.php'.$queryString;
		$apiKey = '{YOUR-API-KEY-HERE}'; 
		$contentType = 'application/json';
		$ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT,'SafePreview 0.1');
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Accept: '.$contentType, 
            'X-Mashape-Key: '.$apiKey
        ));
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 120);
        curl_setopt($ch, CURLOPT_TIMEOUT, 120);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$jsonResult = curl_exec($ch);
        $responseInfo = curl_getinfo($ch);
		
		$arrResult = json_decode($jsonResult,true);

		if(isset($arrResult['Is Porn']) && $arrResult['Is Porn'] != 'True') $this->setIsSafe(TRUE);
		$this->setIsSafe(FALSE);
		return $this->getIsSafe();
		
	}
	
	/*
	* SETTER AND GETTERS
	*/
	
	public function setLang($lang) {
		$this->lang = $lang;
	}
	
	public function getLang() {
		return $this->lang;
	}
	
	public function setMessage($message) {
		$this->message = $message;
	}
	
	public function getMessage() {
		return $this->message;
	}
	
	public function setLogo($logo) {
		$this->logo = $logo;
	}
	
	public function getLogo() {
		return $this->logo;
	}
	
	public function setPathImage($pathImage) {
		$this->pathImage = $pathImage;
	}
	
	public function getPathImage() {
		return $this->pathImage;
	}
	
	public function setPathBlured($pathBlured) {
		$this->pathBlured = $pathBlured;
	}
	
	public function getPathBlured() {
		return $this->pathBlured;
	}
	
	public function setUrlImage($urlImage) {
		$this->urlImage = $urlImage;
	}
	
	public function getUrlImage() {
		return $this->urlImage;
	}
	
	public function setUrlBlured($urlBlured) {
		$this->urlBlured = $urlBlured;
	}
	
	public function getUrlBlured() {
		return $this->urlBlured;
	}
	
	public function setLevel($level) {
		$this->level = $level;
	}
	
	public function getLevel() {
		return $this->level;
	}
	
	public function setIsSafe($isSafe) {
		$this->isSafe = $isSafe;
	}
	
	public function getIsSafe() {
		return $this->isSafe;
	}
	
	public function setCondition($condition) {
		$this->condition = $condition;
	}
	
	public function getCondition() {
		return $this->condition;
	}


}

