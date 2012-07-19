<?php
class moves {
	const name = 'Move List';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array();
		$floor = 0;
		$limit = $gamemod->getCount('movedata')-1;
		$ceiling = $limit;
		if (isset($argv[2]))
			list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++)
			$output[] = $gamemod->getMoveCached($moveid);
				
		return $output;
	}
	function getMode() {
		return 'moves';
	}
}
?>