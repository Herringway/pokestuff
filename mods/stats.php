<?php
class stats {
	const name = 'Stats';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array('stats' => array(), 'moves' => array());
		$floor = 0;
		$limit = $gamemod->getCount('stats')-1;
		$ceiling = $limit;
		if (isset($argv[2]))
			list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
		for ($pkmnid = $floor; $pkmnid <= $ceiling; $pkmnid++) {
			$tmp = $gamemod->getStatsCached($pkmnid);
			$output['stats'][] = $tmp;
			if (isset($tmp['abilities']))
				foreach ($tmp['abilities'] as $abil)
					if (($abil != 0) && !isset($output['abilities'][$abil]))
						$output['abilities'][$abil] = array('name' => $gamemod->getTextEntry('Ability Names', $abil), 'description' => $gamemod->getTextEntry('Ability Descriptions', $abil));
			if (isset($tmp['items']))
				foreach ($tmp['items'] as $item)
					if (($item != 0) && !isset($output['items'][$item]))
						$output['items'][$item] = $gamemod->getItemCached($item);
			if (isset($tmp['moves']))
				foreach ($tmp['moves'] as $mvtype)
					foreach ($mvtype as $move)
						if (!isset($output['moves'][$move['id']]))
							$output['moves'][$move['id']] = $gamemod->getMoveCached($move['id']);
		}
		ksort($output['moves']);
		return $output;
	}
	function getMode() {
		return 'stats';
	}
}
?>