<?php
class stats {
	function execute() {
		global $gamemod;
		$output = array();
		$ceiling = $gamemod->getNumberPokemon();
		if (isset($GLOBALS['argv'][2])) {
			$pkmnid = max(0,min($ceiling,intval($GLOBALS['argv'][2])));
			$output = $gamemod->getStats($pkmnid);
			$output['name'] = $gamemod->getName($pkmnid);
			$output['pokedex'] = $gamemod->getPokedexEntry($pkmnid);
			$output['moves'] = $gamemod->getMoveList($pkmnid);
		} else {
			for ($i = 0; $i < $ceiling; $i++) {
				$tmp = $gamemod->getStats($i, false);
				$tmp['name'] = $gamemod->getName($i);
				$output[] = $tmp;
			}
		}
		return $output;
	}
	function getMode() {
		if (isset($GLOBALS['argv'][2]))
			return 'stats';
		return 'statslist';
	}
}
?>