<?php
class items {
	const name = 'Item List';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array();
		$floor = 0;
		$limit = $gamemod->getCount('itemdata')-1;
		$ceiling = $limit;
		if (isset($argv[2])) {
			if (is_numeric(str_replace('.', '', $argv[2])))
				list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			else
				$floor = $ceiling = $gamemod->itemNameToID(urldecode($argv[2]));
		}
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++)
			$output[] = $gamemod->getItemCached($moveid);
				
		return $output;
	}
	function getMode() {
		return 'items';
	}
	function getHTMLDependencies() {
		return array();
	}
	function genImage($data, &$canvas) {
		$bgcolor = 0xE0E0E0;
		$bordercolor = 0x000000;
		$textcolor = 0x000000;
		$canvas->setSize(128,128);
		$canvas->drawRectangle(2, 2, 126, 126, $bgcolor, $bordercolor, 10);
		$canvas->drawRectangle(48, 48, 80, 80, $bgcolor, $bordercolor, 10);
		$sprite = sprintf('images/items/%s.png', strtolower(str_replace(array(' ', '.', 'é'), array('-', '', 'e'), $data['items'][0]['name'])));
		$canvas->copyImage($sprite, 64, 64, true);
		$canvas->drawText($data['items'][0]['name'], 96, 96, $textcolor, 'ARIALUNI.TTF');
	}
	function genCairo($data, $type) {
		if ($type != 'svg')
			$surface = new CairoImageSurface(CairoFormat::ARGB32, 128, 128);
		else
			$surface = new CairoSVGSurface('php://output', 128, 128);
		$canvas = new CairoContext($s);
		CairoRoundedRectangle($canvas, 2, 2, $canvasx-4, $canvasy-4, 10, 0);
		return $surface;
	}
}
?>