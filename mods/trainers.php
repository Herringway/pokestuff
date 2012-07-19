<?php
class trainers {
	const name = 'Trainers';
	function execute() {
		global $gamemod, $argv, $cache;
		$output = array();
		$floor = 0;
		$limit = $gamemod->getCount('trainerdata');
		$ceiling = $limit;
		if (isset($argv[2]))
			list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			
		for ($id = $floor; $id <= $ceiling; $id++) {
			$data = $gamemod->getTrainerCached($id);
			$output['Trainers'][] = $data;
			foreach ($data['items'] as $item)
				if (($item != 0) && !isset($output['Items'][$item]))
					$output['Items'][$item] = $gamemod->getItemCached($item);
			foreach ($data['pokemon'] as $poke) {
				foreach ($poke['move'] as $move)
					if (!isset($output['Moves'][$move]))
						$output['Moves'][$move] = $gamemod->getMoveCached($move);
				if (!isset($output['Pokemon'][$poke['id']]))
					$output['Pokemon'][$poke['id']] = $gamemod->getStatsCached($poke['id']);
				if (($poke['item'] != 0) && !isset($output['Items'][$poke['item']]))
					$output['Items'][$poke['item']] = $gamemod->getItemCached($poke['item']);
			}
		}
		return $output;
	}
	function getMode() {
		return 'trainers';
	}
}
?>