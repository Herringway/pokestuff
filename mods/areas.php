<?php
class areas {
	const name = 'Areas';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array();
		$floor = 0;
		$limit = $gamemod->getCount('areadata')-1;
		$ceiling = $limit;
		if (isset($argv[2]))
			list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
		
		$output = array('Areas' => array(), 'Pokemon' => array());
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++) {
			$d = $gamemod->getAreaCached($moveid);
			foreach ($d['Encounters'] as $subarea)
				foreach ($subarea as $id => $encounter)
					if (!isset($output['Pokemon'][$id]))
						$output['Pokemon'][$id] = $gamemod->getStatsCached($id);
			$output['Areas'][] = $d;
		}
		return $output;
	}
	function getMode() {
		return 'areas';
	}
}
?>