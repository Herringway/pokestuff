<?php
class moves {
	const name = 'Move List';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array();
		$floor = 0;
		$limit = $gamemod->getCount('movedata')-1;
		$ceiling = $limit;
		if (isset($argv[2])) {
			if (is_numeric(str_replace(array('.','$'), '', $argv[2])))
				list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			else
				$floor = $ceiling = $gamemod->moveNameToID(urldecode($argv[2]));
		}
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++)
			$output[] = $gamemod->getData('moves', $moveid);
				
		return $output;
	}
	function getMode() {
		return 'moves';
	}
	function getHTMLDependencies() {
		return array();
	}
	function genImage($data) {
		$canvas = imagecreatetruecolor(512,32);
		$canvasx = imagesx($canvas);
		$canvasy = imagesy($canvas);
		$bgcolor = 0xE0E0E0;
		$fontsize = 10;
		$bordersize = 1;
		$black = imagecolorallocate($canvas, 0, 0, 0);
		$lightgray = imagecolorallocate($canvas, $bgcolor>>16, ($bgcolor&0xFF00)>>8, $bgcolor&0xFF);
		$pink = imagecolorallocate($canvas, 255, 0, 255);
		imagefill($canvas, 0, 0, $pink);
		imagecolortransparent($canvas, $pink);
		ImageRectangleWithRoundedCorners($canvas, 2, 2, $canvasx-2, $canvasy-2, 10, $black);
		ImageRectangleWithRoundedCorners($canvas, 3, 3, $canvasx-2-$bordersize, $canvasy-2-$bordersize, 10, $lightgray);
		imagettftext($canvas, $fontsize, 0, 16, 22, $black, 'ARIALUNI.TTF', $data['moves'][0]['name']);
		
		$sprfile = sprintf('images/types/%s.png', $data['moves'][0]['type']);
		if (file_exists($sprfile)) {
			$sprite = imagecreatefrompng($sprfile);
			$sprx = imagesx($sprite);
			$spry = imagesy($sprite);
			imagecopy($canvas, $sprite, 128, 10, 0, 0, $sprx, $spry);
		}
		return $canvas;
	}
}
?>