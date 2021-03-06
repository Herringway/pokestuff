<?php
class gen2 extends basegame {
	
	function loadRom() {
		if ($this->rom === null)
			$this->rom = fopen('data/'.$this->lang.'/'.$this->gameid.'.gbc','r');
	}
	public function getCount($what) {
		switch ($what) {
			case 'movedata': return 251;
			case 'stats': return 252;
			case 'itemdata': return 83;
		}
		return 0;
	}
	private function readText($offset, $size = -1) {
		$this->loadRom();
		global $gamecfg;
		if ($size == -1)
			$size = 0x1000;
		fseek($this->rom, $offset);
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
	private function readTextT($offset, $count) {
		$this->loadRom();
		global $gamecfg;
		$size = 0x1000;
		fseek($this->rom, $offset);
		$count = intval($count);
		while ($count-- >= 0) {
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
		}
		return $output;
	}
	public function getTextEntry($what, $where) {
		global $gamecfg;
		if ($what == 'Move Names') {
			return $this->readTextT($gamecfg['Move Name Offset'], $where);
		} else if ($what == 'Pokemon Names') {
			return $this->readText($gamecfg['Pokemon Name Offset']+$where*10,10);
		}
		return sprintf('PLACEHOLDER[%s][%d]', $what, $where);
	}
	function readByte($offset = -1) {
		if ($offset > -1)
			fseek($this->rom, $offset);
		return ord(fgetc($this->rom));
	}
	function readShort($offset = -1) {
		if ($offset > -1)
			fseek($this->rom, $offset);
		$v = unpack('v', fread($this->rom, 2));
		return $v[1];
	}
	public function getMove($id) {
		global $gamecfg;
		$this->loadRom();
		fseek($this->rom, $gamecfg['Move Data Offset'] + $id * 7);
		$data = fread($this->rom, 7);
		$output = unpack('Crid/Ceffect/Cpower/Ctypeid/Caccuracy/Cpp/C*unknown', $data);
		$output['type'] = $gamecfg['Types'][$output['typeid']]['Name'];
		$output['accuracy'] =  intval(100 * $output['accuracy'] / 256);
		$output['category'] = $gamecfg['Types'][$output['typeid']]['Category'];
		$output['priority'] = 0;
		return $output;
	}
	public function getStats($id) {
		global $gamecfg;
		$this->loadRom();
		fseek($this->rom, $gamecfg['Pokemon Stats Offset'] + $id*32);
		$data = unpack('Cid/Chp/Catk/Cdef/Cspeed/Csatk/Csdef/C2type/Ccapturerate/Cxprate/C13unknown', fread($this->rom, 0x20));
		for ($i = 1; $i <= 2; $i++) {
			if (isset($gamecfg['Types'][$data['type'.$i]]['Name']))
				$data['type'.$i] = $gamecfg['Types'][$data['type'.$i]]['Name'];
			else
				$data['type'.$i] = 'Unknown';
		}
		$data['imgid'] = $id;
		$data['cry']['id'] = $this->readShort($gamecfg['Cry Table Offset'] + $id * 6);
		$data['cry']['pitch'] = $this->readByte();
		$data['cry']['echo'] = $this->readByte();
		$data['cry']['length'] = $this->readShort();
		return $data;
	}
	function getMoveList($id, $level = -1) {
		global $gamecfg;
		$this->loadRom();
		$output = array();
		$lvlupptr = $this->readShort(0x427BD + $id*2) + 0x40000;
		$upper = $this->readShort();
		/*while ($lvlupptr < $upper+0x427BD) {
			$move = $this->readShort($lvlupptr);
			$lvlupptr += 2;
			$output['Levelup'][] = array('Learned' => 'Level '.($move&0xFF), 'id' => ($move>>8));
		}*/
		$o = (0x51AEB + $id * 0x20) + 0x18;
		//B/R: 13773 G: 12276 Y: 1232D 
		for ($i = 0; $i < 7; $i++) {
			$byte = $this->readByte($o+$i);
			for ($j = 0; $j < 8; $j++) {
				if ($i*8+$j > 54)
					break;
				if ($byte & pow(2,$j))
					$output['TM'][] = array('Learned' => ($i*8+$j > 49) ? sprintf('HM%02d',$i*8+$j-49) : sprintf('TM%02d',$i*8+$j+1), 'id' => $this->readByte($gamecfg['TM Table Offset'] + $i*8+$j)-1);
			}
		}
		return $output;
	}
}
?>