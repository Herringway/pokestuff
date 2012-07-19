<?php
class gen1 extends basegame {
	const generation = 'gen1';
	private $file;
	
	private function loadRom() {
		if ($this->file === null)
			$this->file = fopen('games/'.get_class($this).'.gb','r');
	}
	public function getTextEntry($what, $where) {
		if ($what == 'Move Names') {
			return $this->readText(0xB0000 + 10*$where);
		}
		return sprintf('PLACEHOLDER[%s][%d]', $what, $where);
	}
	function readByte($offset = -1) {
		if ($offset > -1)
			fseek($this->file, $offset);
		return ord(fgetc($this->file));
	}
	function readShort($offset = -1) {
		if ($offset > -1)
			fseek($this->file, $offset);
		$v = unpack('v', fread($this->file, 2));
		return $v[1];
	}
	private function readText($offset, $size = -1) {
		$this->loadRom();
		global $gamecfg;
		if ($size == -1)
			$size = 0x1000;
		fseek($this->file, $offset);
		$output = '';
		for ($i = 0; $i < $size; $i++) {
			$val = $this->readByte();
			if ($val == 0x50)
				break;
			if (!isset($gamecfg['Text Table'][$val]))
				$output .= sprintf('[%02X]', $val);
			else
				$output .= $gamecfg['Text Table'][$val];
		}
		return $output;
	}
	public function getCount($what) {
		if ($what == 'movedata')
			return 165;
	}
	public function getMove($id) {
		global $gamecfg;
		$this->loadRom();
		fseek($this->file, 0x38000 + $id * 6);
		$data = fread($this->file, 6);
		$output = unpack('Crid/Cunknown/Cpower/Cunknown2/Caccuracy/Cpp', $data);
		$output['typeid'] = 0;
		$output['accuracy'] =  intval(100 * $output['accuracy'] / 256);
		$output['category'] = $gamecfg['Types'][$output['typeid']]['Category'];
		$output['priority'] = 0;
		return $output;
	}
}
?>