<?php
if (!extension_loaded('exif')) {
    throw new Exception('SimpleImage needs the EXIF PHP extension.');
}

if (!extension_loaded('gd')) {
    throw new Exception("SimpleImage needs the GD  PHP extension.");
}

class SimpleImage
{
    private $width;

    private $height;

    private $type;
	
	private $ext;

    private $size;

    private $newImage;

    private $file;

    private $font;

    private $fontSize = 14;

    private $fontColor;
	
	private $name;

	const GIF  = '.gif';
	
	const JPG = '.jpg';

    const PNG  = '.png';
	    
	public static $types = array(1 => '.gif', 2 => '.jpg', 3 => '.png');

    public function __construct($image = null)
    {
        $this->initialize($image);
    }

    private function initialize($image)
    {
        $this->newImage  = $this->createImage($image);
        $this->setType($this->getImageType($image));
        $this->setWidth($this->getImageWidth($this->newImage));
        $this->setHeight($this->getImageHeight($this->newImage));
        $this->setFile($image);
        $this->setSize($this->getImageSize($this->file));
		$this->setName(sha1(md5($image . microtime())));
    }

    private function updateValues($image)
    {
        $this->setWidth(imagesx($this->newImage));
        $this->setHeight(imagesy($this->newImage));
    }

    private function createImage($image)
    {
        $this->isValid($image);			
		
        if ($this->getImageType($image) == self::GIF) {
            return imagecreatefromgif($image);
        } else if (self::getImageType($image) == self::JPG) {
            return imagecreatefromjpeg($image);
        } else if (self::getImageType($image) == self::PNG) {
            return imagecreatefrompng($image);
        }
    }

    private function createNewImage($width, $height, $type)
    {
        $this->validNumber(array($width, $height));

        if ($this->getImageType($this->getFile()) == self::GIF) {
            return imagecreate($width, $height);
        } else {
            return imagecreatetruecolor($width, $height);
        }
    }

    private function copy($temp)
    {
        return imagecopy($temp, $this->newImage, 0, 0, 0, 0, $this->getWidth(), $this->getHeight());
    }

    public function cloneToPNG()
    {
        $this->cloneImage(self::PNG);
    }

	public function cloneToJPG()
    {
        $this->cloneImage(self::JPG);
    }

    public function cloneToGIF()
    {
        $this->cloneImage(self::GIF);
    }

    private function cloneImage($type)
    {
        $temp = $this->createNewImage($this->getWidth(), $this->getHeight(), $type);
        $this->copy($temp);
        $this->setType($type);
        $this->updateValues($this->newImage);
    }

    public function imageRotate($ang)
    {
        $this->validNumber(array($ang));		
		$this->cloneImage(self::PNG);		
        $temp = $this->createNewImage($this->getWidth(), $this->getHeight(), $this->getType());	        
		$this->copy($temp);		
        $this->newImage = imagerotate($this->newImage, $ang, imagecolorallocatealpha( $temp,0,0,0,127 ), 1);	
        $this->updateValues($this->newImage);
    }

    public function resize($width = null, $height = null)
    {
        $this->validNumber(array($width, $height));
        $temp = $this->createNewImage($width, $height, $this->getType());
        imagecopyresampled($temp, $this->newImage, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
        $this->newImage = $temp;
        $this->updateValues($temp);
    }

    public function resizeProportional($width = null, $height = null)
    {
        $this->validNumber(array($width, $height));
        list($width, $height) = $this->calcProportional($width, $height);
        $this->resize($width, $height); 
    }

    public function crop($width = null, $height = null, $percentToRight = 0, $percentToBottom = 0)
    {
        $this->validNumber(array($width, $height));
        $this->validNumber(array($percentToRight, $percentToBottom), 100);

        $temp = $this->createNewImage($width, $height, $this->getType());
        $x = $this->getWidth() * ($percentToRight / 100);
        $y = $this->getHeight() * ($percentToBottom / 100);

        imagecopyresampled($temp, $this->newImage, 0, 0, $x, $y, $width, $height, $width, $height);
        $this->newImage = $temp;
        $this->updateValues($temp);
    }

    public function merge($image = null, $percentToRight = 0, $percentToBottom = 0)
    {
        $this->validNumber(array($percentToRight, $percentToBottom), 100);

        $temp = $this->createImage($image);
        $x = $this->getWidth() * ($percentToRight / 100);
        $y = $this->getHeight() * ($percentToBottom / 100);

        imagecopy($this->newImage, $temp, $x, $y, 0, 0, $this->getImageWidth($temp), $this->getImageHeight($temp));
        $this->updateValues($this->newImage);
    }

    public function write($text = '', $percentToRight = 5, $percentToBottom = 5)
    {
        $this->validNumber(array($percentToRight, $percentToBottom), 100);

        $temp =  $this->createNewImage(1, 1, $this->getType());
        list($r, $g, $b) = $this->getFontColor();
        $color = imagecolorallocate($temp, $r, $g, $b);

        $boxText = $this->getTextSize($text);
        $sizeText = $boxText[2];
        $sizeImage = $this->getImageWidth($this->newImage);

        $x = $this->getWidth() * ($percentToRight / 100);
        $y = $this->getHeight() * ($percentToBottom / 100);
		
		/*
		$quebra = (($sizeImage - $boxText[2])-$x);		
		
		imagettftext($this->newImage, $this->getFontSize(), 0, $x, $y, $color, $this->getFont(), wordwrap($text, '36'));
		*/
		
        if ($sizeText > $sizeImage) {
            $words = explode(' ', $text);
            $count = 0;
            $newArray = array($words[0]);

            for ($a = 1; $a <= count($words); $a++ ) {
                $tmp = $this->getTextSize($newArray[$count]);

                if ($tmp[2] <= $sizeImage - ($percentToRight + $this->getWidth() * 0.21)) {
                    $newArray[$count].= isset($words[$a]) ? ' '.$words[$a] : '';
                } else {
                    $count++;
                    if (!isset($newArray[$count])) {
                        $newArray[$count] = isset($words[$a]) ? $words[$a] : '';
                    } else {
                        $newArray[$count].= isset($words[$a]) ? $words[$a] : '';
                    }
                }
            }

            foreach ($newArray as $key => $value) {
                imagettftext($this->newImage, $this->getFontSize(), 0, $x, $y, $color, $this->getFont(), $value);
                $y = $y + $this->getFontSize() + ($this->getFontSize() * 0.10);
            }

        }else {
            imagettftext($this->newImage, $this->getFontSize(), 0, $x, $y, $color, $this->getFont(), $text);
        }
    }

	public function toPB()
    {      
		imagefilter($this->newImage, IMG_FILTER_GRAYSCALE);
    }
	
	public function setBrightness($number)
    {      
		$this->validNumber(array($number));
		imagefilter($this->newImage, IMG_FILTER_BRIGHTNESS, $number);
    }
		
	public function setContrast($number)
    {      
		$this->validNumber(array($number));
		imagefilter($this->newImage, IMG_FILTER_CONTRAST, $number);
    }
	
	public function toNegative()
    {      
		imagefilter($this->newImage, IMG_FILTER_NEGATE);
    }
	
	public function toPixel($size, $effect = true)
    {      
		$this->validNumber(array($size));
		imagefilter($this->newImage, IMG_FILTER_PIXELATE, $size, $effect);
    }	
	
	public function addColorFilter($hex1, $hex2, $hex3, $opacity)
    {      
		$this->validNumber(array($hex1, $hex2, $hex3, $opacity));
		imagefilter($this->newImage, IMG_FILTER_COLORIZE, $hex1, $hex2, $hex3, $opacity);
    }		
	
	public function setName($name)
    {
        $this->name = $name;
    }
	
    public function setImage($image = null)
    {
        $this->initialize($image);
    }

    private function setType($type)
    {
        $this->type = $type;
    }

    private function setWidth($width)
    {
        $this->width = $width;
    }

    private function setHeight($height)
    {
        $this->height = $height;
    }

    private function setSize($size)
    {
        $this->size = $size;
    }

    private function setFile($file)
    {
        $this->file = $file;
    }

    public function setFont($font)
    {
        $this->font = $font;
    }

    public function setFontSize($size)
    {
        $this->fontSize = $size;
    }

    public function setFontColor($hexadecimal = '#000000')
    {
        $this->fontColor = $this->hexaToRgb($hexadecimal);
    }

	public function getName()
    {
        return $this->name;
    }	
	
    public function getSize()
    {
        return $this->size;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function getWidth()
    {
        return $this->width;
    }
    
    public function getHeight()
    {
        return $this->height;
    }

    private function getFont()
    {
        if (is_null($this->font)) {
            throw new InvalidArgumentException('Undefined font.');
        }
        return $this->font;
    }

    private function getFontSize()
    {
        $this->validNumber(array($this->fontSize));
        return $this->fontSize;
    }

    private function getFontColor()
    {
       return $this->fontColor;
    }

    private function getImageType($image)
    {
		if (!array_key_exists(strtolower(exif_imagetype($image)), self::$types)) {
            throw new InvalidArgumentException('Image type not supported.');
        }
        return self::$types[strtolower(exif_imagetype($image))];
    }

    private function getImageWidth($img)
    {
        return imagesx($img);
    }

    private function getImageHeight($img)
    {
        return imagesy($img);
    }

    private function getImageSize($img)
    {
        return filesize($img);
    }

    private function calcProportional($width, $height)
    {
        if ($width <= 0 || $height <= 0) {
            return array(0, 0);
        }

        $m = $this->getWidth() / $this->getHeight();

        if (($width / $height) > $m) {
            $width = $height * $m;
        } else {
            $height = $width / $m;
        }

        return array($width, $height);
    }

    private function hexaToRgb($hex) {
       $hex = str_replace("#", "", $hex);

       if (strlen($hex) == 3) {
           list($r, $g, $b) = array(substr($hex,0,1).substr($hex,0,1), substr($hex,1,1).substr($hex,1,1), substr($hex,2,1).substr($hex,2,1));
       } else {
           list($r, $g, $b) = array(substr($hex,0,2), substr($hex,2,2), substr($hex,4,2));
       }
       
       return array_map('hexdec', array($r, $g, $b));
    }

    private function getTextSize($text)
    {
        return imagettfbbox($this->getFontSize(), 0, $this->getFont(), $text);
    }

    private function validNumber($arrNumber, $limiter = null)
    {
        array_walk($arrNumber, function($k) use ($limiter){
            if (!is_numeric($k) || $k < 0) {
                throw new InvalidArgumentException('Invalid numbers.');
            }

            if (!is_null($limiter) && $k > $limiter) {
                throw new OutOfRangeException('Number out of range.');
            }
        });
    }

    private function validNewImage()
    {
        if (is_null($this->newImage)) {
            throw new RuntimeException('No operation performed.');
        }
    }

    private function isValid($file)
    {
        if (is_null($file)) {
            throw new OutOfBoundsException('Image not found.');
        }
		
		if(!getimagesize($file))
		{
			throw new RuntimeException('Image is broken');		
		}
    }

    public function save($path = '')
    {
        $this->validNewImage();

        $path = $path . DIRECTORY_SEPARATOR . $this->name . $this->getType();			
		
        if ($this->getType() == self::PNG) {            
			imagealphablending( $this->newImage, false );		
			imagesavealpha( $this->newImage, true );	
				
			imagepng($this->newImage, $path, 9);
        } else if ($this->getType() == self::JPG) {        
			imagejpeg($this->newImage, $path, 75);
        } else if ($this->getType() == self::GIF) {            
			imagegif($this->newImage, $path, 9);
        }

        return $this->name . $this->getType();		
    }

    public function clean()
    {
        imagedestroy($this->newImage);
        $this->newImage = null;
    }
}