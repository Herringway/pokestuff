<?php
class areas {
	private $wants = array();
	const name = 'Areas';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array();
		$floor = 0;
		$limit = $gamemod->getCount('areadata')-1;
		$ceiling = $limit;
		if (isset($argv[2]))
			list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
		
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++) {
			$d = $gamemod->getAreaCached($moveid);
			foreach ($d['Encounters'] as $subarea)
				foreach ($subarea as $id => $encounter)
					$this->wants['stats'][] = $id;
			$output[] = $d;
		}
		return $output;
	}
	function getMode() {
		return 'areas';
	}
	function getHTMLDependencies() {
		return $this->wants;
	}
}
?>