<?php
class moves {
	function execute() {
		global $gamemod;
		$output = array();
		$ceiling = $gamemod->getNumberMoves();
		if (isset($GLOBALS['argv'][2])) {
			$moveid = max(0,min($ceiling,intval($GLOBALS['argv'][2])));
			$output = $gamemod->getMove($moveid);
			$output['name'] = $gamemod->getMoveName($moveid);
		} else {
			for ($i = 0; $i < $ceiling; $i++) {
				$tmp = $gamemod->getMove($i, false);
				$tmp['name'] = $gamemod->getMoveName($i);
				$output[] = $tmp;
			}
		}
		return $output;
	}
	function getMode() {
		if (isset($GLOBALS['argv'][2]))
			return 'move';
		return 'movelist';
	}
}
?>