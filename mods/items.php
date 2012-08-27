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
			$output[] = $gamemod->getData('items',$moveid);
				
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
		$canvas->drawRectangle(2, 2, 124, 124, $bgcolor, $bordercolor, 10);
		$canvas->drawRectangle(48, 48, 32, 32, $bgcolor, $bordercolor, 10);
		$sprite = sprintf('images/items/%s.png', strtolower(str_replace(array(' ', '.', 'é'), array('-', '', 'e'), $data['items'][0]['name'])));
		$canvas->copyImage($sprite, 64, 64, true);
		$canvas->drawTextCentered($data['items'][0]['name'], 10, 64, 96, $textcolor, 'ARIALUNI.TTF');
	}
}
?>