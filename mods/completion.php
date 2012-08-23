<?php
class completion {
	const name = 'Module Completion';
	function execute() {
		global $gamemod;
		try {
			$data = $gamemod->getStatsCached(1);
			$unknowns = 0;
			foreach ($data as $k=>$v)
				if (substr($k, 0, 7) == 'unknown')
					$unknowns++;
			if ($unknowns > 0)
				$status[] = sprintf('%d unknown entries', $unknowns);
			if (!isset($data['moves']))
				$status[] = 'Move data missing';
			if (!isset($data['evolutions']))
				$status[] = 'Evolution data missing';
			if (!isset($status[0]))
				$status[] = 'Completed';
		} catch(Exception $e) {
			$status[] = 'Not Working';
		}
		$output['Stats Data'] = array('status' => $status, 'mod' => 'stats');
		$status = array();
		try {
			$data = $gamemod->getMoveCached(1);
			$unknowns = 0;
			foreach ($data as $k=>$v)
				if (substr($k, 0, 7) == 'unknown')
					$unknowns++;
			if ($unknowns > 0)
				$status[] = sprintf('%d unknown entries', $unknowns);
			if (!isset($status[0]))
				$status[] = 'Completed';
		} catch(Exception $e) {
			$status[] = 'Not Working';
		}
		$output['Move Data'] = array('status' => $status, 'mod' => 'moves');
		$status = array();
		try {
			$data = $gamemod->getItemCached(1);
			$unknowns = 0;
			foreach ($data as $k=>$v)
				if (substr($k, 0, 7) == 'unknown')
					$unknowns++;
			if ($unknowns > 0)
				$status[] = sprintf('%d unknown entries', $unknowns);
			if (!isset($status[0]))
				$status[] = 'Completed';
		} catch(Exception $e) {
			$status[] = 'Not Working';
		}
		$output['Item Data'] = array('status' => $status, 'mod' => 'items');
		$status = array();
		try {
			$data = $gamemod->getTrainerCached(1);
			$unknowns = 0;
			foreach ($data as $k=>$v)
				if (substr($k, 0, 7) == 'unknown')
					$unknowns++;
			if ($unknowns > 0)
				$status[] = sprintf('%d unknown entries', $unknowns);
			else
				$status[] = 'Completed';
		} catch(Exception $e) {
			$status[] = 'Not Working';
		}
		$output['Trainer Data'] = array('status' => $status, 'mod' => 'trainers');
		$status = array();
		try {
			$data = $gamemod->getAreaCached(1);
			$unknowns = 0;
			foreach ($data as $k=>$v)
				if (substr($k, 0, 7) == 'unknown')
					$unknowns++;
			if ($unknowns > 0)
				$status[] = sprintf('%d unknown entries', $unknowns);
			if (!isset($status[0]))
				$status[] = 'Completed';
		} catch(Exception $e) {
			$status[] = 'Not Working';
		}
		$output['Area Data'] = array('status' => $status, 'mod' => 'areas');
		$status = array();
		/*try {
			$data = $gamemod->getText();
			if (!isset($output['Text Data']))
				$status[] = 'Completed';
		} catch(Exception $e) {
			$status[] = 'Not Working';
		}
		$output['Text Data'] = array('status' => $status, 'mod' => 'text');*/
		return $output;
	}
	function getMode() {
		return 'completion';
	}
	function getHTMLDependencies() {
		return array();
	}
}
?>