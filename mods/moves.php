<?php
class moves extends datamod {
	const name = 'Move List';
	function execute($argv) {
		$output = array();
		$floor = 0;
		$limit = $this->gamemod->getCount('movedata')-1;
		$ceiling = $limit;
		if (isset($argv[2])) {
			if (is_numeric(str_replace(array('.','$'), '', $argv[2])))
				list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			else
				$floor = $ceiling = $this->gamemod->moveNameToID(urldecode($argv[2]));
		}
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++)
			$output[] = $this->gamemod->getDataNew('moves/'.$moveid);
				
		return $output;
	}
	function getMode() {
		return 'moves';
	}
	function getHTMLDependencies() {
		return array();
	}
	function getOptions() {
		return array('Font' => 'togoshi-monago.ttf');
	}
	function genImage($data, &$canvas) {
		global $settings;
		$canvas->setSize(512,32);
		$bgcolor = 0xE0E0E0;
		$fontsize = 10;
		$bordersize = 1;
		$canvas->drawRectangle(1, 1, $canvas->getX()-2, $canvas->getY()-2, $bgcolor, 0x000000, 3);
		$imgoffs = -14;
		$canvas->copyImage(sprintf('%s/types/%s.png', $settings['Base Image Path'], $data['moves'][0]['type']), $imgoffs += 40, $canvas->getY()/2, true);
		$canvas->copyImage(sprintf('%s/categories/%s.png', $settings['Base Image Path'], $data['moves'][0]['category']), $imgoffs += 40, $canvas->getY()/2, true);
		
		$canvas->drawTextShadowed($data['moves'][0]['name'], $fontsize, $imgoffs += 24, ($canvas->getY()+$fontsize)/2, 0x000000, 0x000000, $settings['moves']['Font']);
		return $canvas;
	}
}
?>