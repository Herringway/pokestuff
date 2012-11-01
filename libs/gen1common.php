<?php
class gen1 extends basegame {
	
	private function loadRom() {
		if ($this->rom === null)
			$this->rom = fopen('data/'.$this->lang.'/'.$this->gameid.'.gb','r');
	}
	public function getOptions() {
		return array('Generation 1 Internal IDs' => true);
	}
	public function getTextEntry($what, $where) {
		$this->loadRom();
		global $gamecfg, $settings;
		if ($what == 'Move Names') {
			return $this->readTextT($gamecfg['Move Name Offset'], $where);
		} else if ($what == 'Pokemon Names') {
			if ($settings['gen1']['Generation 1 Internal IDs'])
				return $this->readText($gamecfg['Pokemon Name Offset']+$where*10,10);
			return $this->readText($gamecfg['Pokemon Name Offset']+$this->getIntID($where-1)*10,10);
		} else if ($what == 'Species Names') {
			if ($settings['gen1']['Generation 1 Internal IDs'])
				$ptr = $this->readShort($gamecfg['Pokedex Data Offset'] + $where*2) - 0x4000 + ($gamecfg['Pokedex Data Offset']&0xFF0000);
			else
				$ptr = $this->readShort($gamecfg['Pokedex Data Offset'] + $this->getIntID($where-1)*2) - 0x4000 + ($gamecfg['Pokedex Data Offset']&0xFF0000);
			return $this->readText($ptr).' Pokemon';
		} else if ($what == 'Pokedex Entries') {
			if ($settings['gen1']['Generation 1 Internal IDs'])
				$ptr = $this->readShort($gamecfg['Pokedex Data Offset'] + $where*2) - 0x4000 + ($gamecfg['Pokedex Data Offset']&0xFF0000);
			else
				$ptr = $this->readShort($gamecfg['Pokedex Data Offset'] + $this->getIntID($where-1)*2) - 0x4000 + ($gamecfg['Pokedex Data Offset']&0xFF0000);
			$this->readText($ptr);
			fseek($this->rom, 5, SEEK_CUR);
			$ptr = $this->readShort()-0x4000 + ($this->readByte()<<14);
			return $this->readText($ptr);
		} else if ($what == 'Item Names') {
			return $this->readTextT($gamecfg['Item Name Table'], $where);
		} else if ($what == 'Move Descriptions') {
			return '';
		} else if ($what == 'Item Descriptions') {
			return '';
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
		//return;
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
	public function getCount($what) {
		global $settings;
		switch ($what) {
			case 'movedata': return 165;
			case 'stats': return $settings['gen1']['Generation 1 Internal IDs'] ? 256 : 152;
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
		fseek($this->rom, $gamecfg['Move Data Table'] + $id * 6);
		$data = fread($this->rom, 6);
		$output = unpack('Crid/Ceffect/Cpower/Ctypeid/Caccuracy/Cpp', $data);
		$output['type'] = isset($gamecfg['Types'][$output['typeid']]['Name']) ? $gamecfg['Types'][$output['typeid']]['Name'] : 'Unknown';
		$output['accuracy'] =  intval(100 * $output['accuracy'] / 256);
		$output['category'] = isset($gamecfg['Types'][$output['typeid']]['Category']) ? $gamecfg['Types'][$output['typeid']]['Category'] : 2;
		$output['priority'] = 0;
		return $output;
	}
	public function getStats($id) {
		global $gamecfg, $settings;
		$this->loadRom();
		if (!$settings['gen1']['Generation 1 Internal IDs']) {
			$cid = $id;
			$intid = $this->getIntID($cid);
			if ($id == 0)
				return array('id' => 0, 'imgid' => 0, 'hp' => 0, 'atk' => 0, 'def' => 0, 'speed' => 0, 'satk' => 0, 'sdef' => 0, 'type1' => 'Normal', 'type2' => 'Normal');
		} else {
			$cid = $this->internalToCanonical($id);
			$intid = $id;
		}
		//printf('[i:%d,c:%d]<br />', $intid, $cid);
		if (isset($gamecfg['Pokemon Stats Offsets'][$cid]))
			fseek($this->rom, $gamecfg['Pokemon Stats Offsets'][$cid]);
		else
			fseek($this->rom, $gamecfg['Pokemon Stats Offsets']['default'] + (($cid-1)&0xFF) * 0x1C);
		//echo ftell($this->rom);
		$data = fread($this->rom, 0x1C);
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
		global $gamecfg;
		for ($i = 0; $i < 255; $i++)
			if ($this->readByte($gamecfg['Internal Order Table Offset']+$i) == $cid+1)
				return $i;
		//$flip = array_flip($gamecfg['Normal to Internal']);
		//return $flip[$id];
	}
	function internalToCanonical($intid) {
		global $gamecfg;
		return $this->readByte($gamecfg['Internal Order Table Offset']+$intid);
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
		global $gamecfg, $settings;
		if ($settings['gen1']['Generation 1 Internal IDs'])
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
		if (isset($gamecfg['Pokemon Stats Offsets'][$id]))
			$o = $gamecfg['Pokemon Stats Offsets'][$id] + 15;
		else
			$o = $gamecfg['Pokemon Stats Offsets']['default'] + (($id-1)&0xFF) * 0x1C + 15;
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
					$output['TM'][] = array('Learned' => ($i*8+$j > 49) ? sprintf('HM%02d',$i*8+$j-49) : sprintf('TM%02d',$i*8+$j+1), 'id' => $this->readByte($gamecfg['TM Table Offset'] + $i*8+$j)-1);
			}
		}
		return $output;
	}
}
?>
