<?php
class gen1 extends basegame {
	private $file;
	private $internalIDs = true;
	
	private function loadRom() {
		if ($this->file === null)
			$this->file = fopen('games/'.$this->gameid.'.gb','r');
	}
	public function getTextEntry($what, $where) {
		$this->loadRom();
		global $gamecfg;
		if ($what == 'Move Names') {
			return $this->readTextT(0xB0000, $where);
		} else if ($what == 'Pokemon Names') {
			if ($this->internalIDs)
				return $this->readText(0x1C21E+$where*10,10);
			return $this->readText(0x1C21E+$this->getIntID($where-1)*10,10);
		} else if ($what == 'Species Names') {
			if ($this->internalIDs)
				$ptr = $this->readShort(0x4047E + $where*2) - 0x4000 + 0x40000;
			else
				$ptr = $this->readShort(0x4047E + $this->getIntID($where-1)*2) - 0x4000 + 0x40000;
			return $this->readText($ptr).' Pokemon';
		} else if ($what == 'Pokedex Entries') {
			if ($this->internalIDs)
				$ptr = $this->readShort(0x4047E + $where*2) - 0x4000 + 0x40000;
			else
				$ptr = $this->readShort(0x4047E + $this->getIntID($where-1)*2) - 0x4000 + 0x40000;
			$this->readText($ptr);
			fseek($this->file, 5, SEEK_CUR);
			$ptr = $this->readShort()-0x4000 + ($this->readByte()<<14);
			return $this->readText($ptr);
		} else if ($what == 'Item Names') {
			return $this->readTextT(0x472B, $where);
		} else if ($what == 'Move Descriptions') {
			return '';
		} else if ($what == 'Item Descriptions') {
			return '';
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
	private function readTextT($offset, $count) {
		$this->loadRom();
		global $gamecfg;
		$size = 0x1000;
		fseek($this->file, $offset);
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
	public function getCount($what) {
		switch ($what) {
			case 'movedata': return 165;
			case 'stats': return $this->internalIDs ? 256 : 152;
			case 'itemdata': return 83;
			case 'trainerdata': return 2;
		}
	}
	public function getItem($id) {
		$this->loadRom();
		$output = array();
		$output['value'] = $this->readByte(0x4606 + $id*2+1);
		return $output;
	}
	public function getMove($id) {
		global $gamecfg;
		$this->loadRom();
		fseek($this->file, 0x38000 + $id * 6);
		$data = fread($this->file, 6);
		$output = unpack('Crid/Ceffect/Cpower/Ctypeid/Caccuracy/Cpp', $data);
		$output['type'] = isset($gamecfg['Types'][$output['typeid']]['Name']) ? $gamecfg['Types'][$output['typeid']]['Name'] : 'Unknown';
		$output['accuracy'] =  intval(100 * $output['accuracy'] / 256);
		$output['category'] = isset($gamecfg['Types'][$output['typeid']]['Category']) ? $gamecfg['Types'][$output['typeid']]['Category'] : 2;
		$output['priority'] = 0;
		return $output;
	}
	public function getStats($id) {
		global $gamecfg;
		$this->loadRom();
		if (!$this->internalIDs) {
			$cid = $id;
			$intid = $this->getIntID($cid);
			if ($id == 0)
				return array('id' => 0, 'imgid' => 0, 'hp' => 0, 'atk' => 0, 'def' => 0, 'speed' => 0, 'satk' => 0, 'sdef' => 0, 'type1' => 'Normal', 'type2' => 'Normal');
		} else {
			$cid = $this->internalToCanonical($id);
			$intid = $id;
		}
		//printf('[i:%d,c:%d]<br />', $intid, $cid);
		if ($cid != 151)
			fseek($this->file, 0x383DE + (($cid-1)&0xFF) * 0x1C);
		else
			fseek($this->file, 0x425B);
		//echo ftell($this->file);
		$data = fread($this->file, 0x1C);
		$output = unpack('Cid/Chp/Catk/Cdef/Cspeed/Csatk/C2type/Ccapturerate/Cxprate/Cspritedimension/vFrontSpritePtr/vBackSpritePtr/C4wildmoves/Cgrowthrate', $data);
		for ($i = 1; $i <= 2; $i++) {
			if (isset($gamecfg['Types'][$output['type'.$i]]['Name']))
				$output['type'.$i] = $gamecfg['Types'][$output['type'.$i]]['Name'];
			else
				$output['type'.$i] = 'Unknown';
		}
		$output['sdef'] = $output['satk'];
		$output['imgid'] = $cid;
		$ptr = $this->readShort(0x4047E + $intid*2) - 0x4000 + 0x40000;
		$this->readText($ptr);
		//039446
		$output['cry']['id'] = $this->readByte(0x039446 + $intid * 3);
		$output['cry']['pitch'] = $this->readByte();
		$output['cry']['length'] = $this->readByte();
		$output['height'] = $this->readByte().'\''.$this->readByte().'"';
		$output['weight'] = sprintf('%0.1f lbs', $this->readShort()/10);
		return $output;
	}
	function getIntID($cid) {
		for ($i = 0; $i < 255; $i++)
			if ($this->readByte(0x41024+$i) == $cid+1)
				return $i;
		//$flip = array_flip($gamecfg['Normal to Internal']);
		//return $flip[$id];
	}
	function internalToCanonical($intid) {
		return $this->readByte(0x41024+$intid);
	}
	function getTrainerData($id) {
		$output = array();
		//$ptr = $this->readShort(0x4047E + $this->getIntID($where-1)*2) - 0x4000 + 0x40000;
		return $output;
	}
	function getTrainerPokemon($id) {
		$output = array();
		//$ptr = $this->readShort(0x39D3B + $this->getIntID($where)*2) - 0x4000 + 0x34000;
		//$output[] = array('level' => 0, 
		return $output;
	}
	function getMoveList($id, $level = -1) {
		global $gamecfg;
		if ($this->internalIDs)
			$id = $this->internalToCanonical($id);
		$this->loadRom();
		$output = array();
		$lvlupptr = $this->readShort(0x3B05C + $id*2) + 0x34000;
		$upper = $this->readShort();
		/*while ($lvlupptr < $upper+0x34000) {
			$move = $this->readShort($lvlupptr);
			$lvlupptr += 2;
			$output['Levelup'][] = array('Learned' => 'Level '.($move&0xFF), 'id' => ($move>>8));
		}*/
		if ($id != 151)
			$o = (0x383DE + $id * 0x1C) + 15;
		else
			$o = 0x425B + 15;
		//B/R: 13773 G: 12276 Y: 1232D
		for ($i = 0; $i < 4; $i++) {
			$byte = $this->readByte($o+$i);
			if ($byte != 0)
				$output['WILD'][] = array('Learned' => 'WILD', 'id' => $byte);
		}
		for ($i = 0; $i < 7; $i++) {
			$byte = $this->readByte($o+$i+5);
			for ($j = 0; $j < 8; $j++) {
				if ($i*8+$j > 54)
					break;
				if ($byte & pow(2,$j))
					$output['TM'][] = array('Learned' => ($i*8+$j > 49) ? sprintf('HM%02d',$i*8+$j-49) : sprintf('TM%02d',$i*8+$j+1), 'id' => $this->readByte(0x13773 + $i*8+$j)-1);
			}
		}
		return $output;
	}
}
?>
