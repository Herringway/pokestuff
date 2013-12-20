<?php
class trainers extends datamod {
	private $wants = array();
	const name = 'Trainers';
	const show = false;
	function execute($argv) {
		$output = array();
		$floor = 0;
		$limit = $this->gamemod->getCount('trainerdata');
		$ceiling = $limit;
		if (isset($argv[2]))
			list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			
		for ($id = $floor; $id <= $ceiling; $id++) {
			$data = $this->gamemod->getDataNew('trainers/'.$id);
			$output[] = $data;
			if (isset($data['items']))
				foreach ($data['items'] as $item)
					if (($item != 0) && !isset($output['Items'][$item]))
						$this->wants['items'][] = $item;
			foreach ($data['pokemon'] as $poke) {
				foreach ($poke['Moves'] as $move)
					if (!isset($output['Moves'][$move]))
						$this->wants['moves'][] = $move;
				$this->wants['stats'][] = $poke['ID'];
				if (isset($poke['Item'])) 
					$this->wants['items'][] = $poke['Item'];
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