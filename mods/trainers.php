<?php
class trainers {
	private $wants = array();
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
			$output[] = $data;
			if (isset($data['items']))
				foreach ($data['items'] as $item)
					if (($item != 0) && !isset($output['Items'][$item]))
						$this->wants['items'][] = $item;
			foreach ($data['pokemon'] as $poke) {
				foreach ($poke['move'] as $move)
					if (!isset($output['Moves'][$move]))
						$this->wants['moves'][] = $move;
				$this->wants['stats'][] = $poke['id'];
				if ($poke['item'] != 0) 
					$this->wants['items'][] = $poke['item'];
			}
		}
		return $output;
	}
	function getMode() {
		return 'trainers';
	}
	function getHTMLDependencies() {
		return $this->wants;
	}
}
?>