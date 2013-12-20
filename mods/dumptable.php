<?php
class dumptable extends datamod {
	const name = 'Table Dumper';
	const show = false;
	private $list = true;
	private $show = true;
	private $listtables = false;
	function execute($argv) {
		$this->show = isset($argv[2]);
		if (!isset($argv[2])) {
			$this->listtables = true;
			return $this->gamemod->listTables();
		}
		$output = array();
		$floor = 0;
		try {
			$limit = $this->gamemod->getCount($argv[2]);
		} catch(Exception $e) {
			$this->show = false;
			return;
		}
		$ceiling = $limit;
		if (isset($argv[3]))
			list($floor,$ceiling) = rangeStringToRange($argv[3],0,$limit);
			
		for ($moveid = $floor; $moveid <= $ceiling; $moveid++)
			$output[] = $this->gamemod->getRawData($argv[2], $moveid);
				
		if ($floor - $ceiling == 0)
			$this->list = false;
		return array('table' => $argv[2], 'data' => $output);
	}
	function getMode() {
		if ($this->listtables)
			return 'listtables';
		if (!$this->show)
			return 'null';
		if (!$this->list)
			return 'file';
		return 'filelist';
	}
	function getHTMLDependencies() {
		return array();
	}
}
?>