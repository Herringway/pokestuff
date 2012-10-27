<?php

class GDDraw {
	private $canvas;
	private $x;
	private $y;
	function setSize($w, $h) {
		$this->canvas = imagecreatetruecolor($w, $h);
		$pink = imagecolorallocatealpha($this->canvas, 255, 255, 255, 127);
		imagefill($this->canvas, 0, 0, $pink);
		imagecolortransparent($this->canvas, $pink);
		imagesavealpha($this->canvas, true);
		imageantialias($this->canvas, true);
		//imagealphablending($this->canvas, false);
	}
	public function getX() {
		if (!isset($this->canvas))
			throw new Exception('You must set canvas size first!');
		if (!isset($this->x))
			$this->x = imagesx($this->canvas);
		return $this->x;
	}
	public function getY() {
		if (!isset($this->canvas))
			throw new Exception('You must set canvas size first!');
		if (!isset($this->y))
			$this->y = imagesy($this->canvas);
		return $this->y;
	}
	public function copyImage($filename, $x, $y, $centered = false) {
		if (file_exists($filename)) {
			$exp = explode('.', $filename);
			switch($exp[count($exp)-1]) {
				case 'gif': $fromimg = imagecreatefromgif($filename); break;
				case 'jpeg':
				case 'jpg': $fromimg = imagecreatefromjpg($filename); break;
				case 'png': $fromimg = imagecreatefrompng($filename); break;
				default: $fromimg = imagecreatefrompng('images/missing.png');
			}
		} else
			$fromimg = imagecreatefrompng('images/missing.png');
		$fromx = imagesx($fromimg);
		$fromy = imagesy($fromimg);
		if ($centered) {
			$x -= $fromx/2;
			$y -= $fromy/2;
		}
		imagecopy($this->canvas, $fromimg, $x, $y, 0, 0, $fromx, $fromy);
	}
	public function copyImageScaled($filename, $x, $y, $scale, $centered = false) {
		if ($scale === 1) {
			$this->copyImage($filename,$x,$y,$centered);
			return;
		}
		if (file_exists($filename)) {
			$exp = explode('.', $filename);
			switch($exp[count($exp)-1]) {
				case 'gif': $fromimg = imagecreatefromgif($filename); break;
				case 'jpeg':
				case 'jpg': $fromimg = imagecreatefromjpg($filename); break;
				case 'png': $fromimg = imagecreatefrompng($filename); break;
				default: $fromimg = imagecreatefrompng('images/missing.png');
			}
		} else
			$fromimg = imagecreatefrompng('images/missing.png');
		$fromx = imagesx($fromimg);
		$fromy = imagesy($fromimg);
		if ($centered) {
			$x -= ($fromx*$scale)/2;
			$y -= ($fromy*$scale)/2;
		}
		imagecopyresized($this->canvas, $fromimg, $x, $y, 0, 0, $fromx*$scale, $fromy*$scale, $fromx, $fromy);
	}
	public function drawRectangle($x1, $y1, $w, $h, $color, $bordercolor = -1, $radius = 0) {
		$color = $this->getColor($color);
		$bordercolor = $this->getColor($bordercolor);
		$x2 = $x1 + $w;
		$y2 = $y1 + $h;
        imagefilledrectangle($this->canvas, $x1+$radius, $y1, $x2-$radius, $y2, $color);
        imagefilledrectangle($this->canvas, $x1, $y1+$radius, $x1+$radius-1, $y2-$radius, $color);
        imagefilledrectangle($this->canvas, $x2-$radius+1, $y1+$radius, $x2, $y2-$radius, $color);

        imagefilledarc($this->canvas,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $color, IMG_ARC_PIE);
        imagefilledarc($this->canvas,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $color, IMG_ARC_PIE);
        imagefilledarc($this->canvas,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $color, IMG_ARC_PIE);
        imagefilledarc($this->canvas,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $color, IMG_ARC_PIE);
		if ($bordercolor > -1) {
			imageline($this->canvas, $x1+$radius, $y1, $x2-$radius, $y1, $bordercolor);
			imageline($this->canvas, $x1+$radius, $y2, $x2-$radius, $y2, $bordercolor);
			imageline($this->canvas, $x1, $y1+$radius, $x1, $y2-$radius, $bordercolor);
			imageline($this->canvas, $x2, $y1+$radius, $x2, $y2-$radius, $bordercolor);

			imagearc($this->canvas,$x1+$radius, $y1+$radius, $radius*2, $radius*2, 180 , 270, $bordercolor);
			imagearc($this->canvas,$x2-$radius, $y1+$radius, $radius*2, $radius*2, 270 , 360, $bordercolor);
			imagearc($this->canvas,$x1+$radius, $y2-$radius, $radius*2, $radius*2, 90 , 180, $bordercolor);
			imagearc($this->canvas,$x2-$radius, $y2-$radius, $radius*2, $radius*2, 360 , 90, $bordercolor);
		}
	}
	public function drawLine($x1, $y1, $x2, $y2, $color) {
		imageline($this->canvas, $x1, $y1, $x2, $y2, $this->getColor($color));
	}
	public function drawPolygon($points, $color) {
		imagepolygon($this->canvas, $points, count($points)/2, $this->getColor($color));
	}
	public function drawFilledPolygon($points, $color, $bordercolor = -1) {
		imagefilledpolygon($this->canvas, $points, count($points)/2, $this->getColor($color));
		if ($bordercolor > -1)
			imagepolygon($this->canvas, $points, count($points)/2, $this->getColor($bordercolor));
	}
	public function drawText($string, $fontsize, $x, $y, $color, $font = '') {
		imagettftext($this->canvas, $fontsize, 0, $x, $y, $this->getColor($color), $font, $string);
	}
	public function drawTextShadowed($string, $fontsize, $x, $y, $color, $shadowcolor, $font = '') {
		imagettftext($this->canvas, $fontsize, 0, $x+1, $y+1, $this->getColor($shadowcolor), $font, $string);
		imagettftext($this->canvas, $fontsize, 0, $x, $y, $this->getColor($color), $font, $string);
	}
	
	public function drawTextCentered($string, $fontsize, $x, $y, $color, $font = '') {
		$bbox = imagettfbbox($fontsize, 0, $font, $string);
		$this->drawText($string, $fontsize, $x - $bbox[2]/2, $y, $color, $font);
	}
	public function drawTextCenteredShadowed($string, $fontsize, $x, $y, $color, $shadowcolor, $font = '') {
		$bbox = imagettfbbox($fontsize, 0, $font, $string);
		$this->drawTextShadowed($string, $fontsize, $x - $bbox[2]/2, $y, $color, $shadowcolor, $font);
	}
	public function renderPNG($filename = 'php://output') {
		if (!isset($this->canvas))
			throw new Exception('You must set canvas size first!');
		imagepng($this->canvas, $filename, 9);
	}
	public function renderGIF($filename = 'php://output') {
		if (!isset($this->canvas))
			throw new Exception('You must set canvas size first!');
		imagegif($this->canvas, $filename);
	}
	public function renderJPG($filename = 'php://output') {
		if (!isset($this->canvas))
			throw new Exception('You must set canvas size first!');
		imagejpeg($this->canvas, $filename, 100);
	}
	private function getColor($color) {
		if (($id = imagecolorexactalpha($this->canvas, ($color&0xFF0000)>>16, ($color&0xFF00)>>8, $color&0xFF, ($color&0x7F000000)>>24)) != -1)
			return $id;
		return imagecolorallocatealpha($this->canvas, ($color&0xFF0000)>>16, ($color&0xFF00)>>8, $color&0xFF, ($color&0x7F000000)>>24);
	}
}
?>