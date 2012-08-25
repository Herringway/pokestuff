<?php
require_once 'libs/ndslib.php';
require_once 'libs/gen5text.php';
class gen5 extends basegame {
	private $textFiles;
	private $storyTextFiles;
	private $narcs;
	private $stats = array('systemtextloads' => 0, 'storytextloads' => 0);
	private $rom;
	const generation = 'gen5';

	function listTables($dirname = null) {
		$this->loadRom();
		$this->rom->autoNARC();
		$output = array();
		foreach ($this->rom as $k=>$narc) {
			if ($narc instanceof NARCfile)
				$output[$k] = array('Data Size' => $narc->FIMG['size'], 'Type' => 'NARC', 'Files' => $narc->Files, 'Average File Size' => intval(round($narc->FIMG['size']/$narc->Files)));
			else
				$output[$k] = array('Data Size' => strlen($narc), 'Type' => 'Unknown');
		}
		ksort($output);
		return $output;
	}
	
	function getCount($what) {
		$this->loadNarc($what);
		return $this->narcs[$what]->Files-1;
	}
	function getRawData($what, $where) {
		$output = array();
		$output['id'] = $what;
		$output['data'] = array();
		$data = $this->getFile($what, $where);
		for ($i = 0; $i < strlen($data); $i++)
			$output['data'][] = ord($data[$i]);
		return $output;
	}
	function getTextCount($what) {
		global $gamecfg;
		if (!isset($this->textFiles[$gamecfg['text']['File Map'][$what]]))
			$this->textFiles[$gamecfg['text']['File Map'][$what]] = new gen5text($this->getFile('systemtext', $gamecfg['text']['File Map'][$what]));
		return $this->textFiles[$gamecfg['text']['File Map'][$what]]->count();
	}
	function getText() {
		$this->loadNarc('systemtext');
		$this->loadNarc('storytext');
		foreach ($this->narcs['systemtext'] as $textdata) {
			$textfile = new gen5text($textdata);
			$output[] = $textfile->dump();
		}
		foreach ($this->narcs['storytext'] as $textdata) {
			$textfile = new gen5text($textdata);
			$output[] = $textfile->dump();
		}
		return $output;
	}
	function getTextEntry($type, $id) {
		global $gamecfg;
		if (($type == 'Pokemon Names') && ($id > $this->getTextCount('Pokemon Names') - 3))
			return $gamecfg['text']['Unlisted Pokemon'][$id - ($this->getTextCount('Pokemon Names') - 2)];
		if (isset($gamecfg['text']['File Map'][$type])) {
			if (!isset($this->textFiles[$gamecfg['text']['File Map'][$type]]))
				$this->textFiles[$gamecfg['text']['File Map'][$type]] = new gen5text($this->getFile('systemtext', $gamecfg['text']['File Map'][$type]));
			if (isset($this->textFiles[$gamecfg['text']['File Map'][$type]][$id]))
				return $this->textFiles[$gamecfg['text']['File Map'][$type]][$id];
			return sprintf('Invalid entry %d', $id);
		}
		return sprintf('Warning: %s not found', $type);
	}
	function getExecutionStats() {
		return $this->stats;
	}
	function loadRom() {
		if ($this->rom == null)
			$this->rom = new ndsrom('games/'.$this->gameid.'.nds');
	}
	function getBaseID($id) {
		if (isset($GLOBALS['gamecfg']['Original Forms'][$id]))
			return $GLOBALS['gamecfg']['Original Forms'][$id];
		else return $id;
	}
	function loadNarc($name) {
		global $gamecfg;
		$this->loadRom();
		if (!isset($gamecfg['narcs'][$name]))
			throw new Exception('Unknown narc');
		if (!isset($this->narcs[$name])) {
			$this->narcs[$name] = $this->rom->getNARC('/a/'.$gamecfg['narcs'][$name]);
			if (!isset($this->stats['narc_loads'][$name]))
				$this->stats['narc_loads'][$name] = 0;
			$this->stats['narc_loads'][$name]++;
		}
	}
	function getFile($name, $id, $cache = false) {
		$this->loadNarc($name);
		if ($cache && isset($this->cachefiles[$name][$id]))
			return $this->cachefiles[$name][$id];
		if ($cache)
			return $this->cachefiles[$name][$id] = $this->narcs[$name][$id];
		if (!isset($this->stats['narc_file'][$name][$id]))
			$this->stats['narc_file'][$name][$id] = 0;
		$this->stats['narc_file'][$name][$id]++;
		return $this->narcs[$name][$id];
	}
	function getStats($id) {
		global $gamecfg;
		$baseid = $this->getBaseID($id);

		$rawdata = $this->getFile('stats', $id);
		$poke = unpack('Chp/Catk/Cdef/Cspeed/Csatk/Csdef/C2type/Ccapturerate/Cxprate/vEVraw/v3itemID/Cfemalechance/Chatchsteps/Cbasehappiness/Cgrowthrate/C2egggrp/C3ability/Cunknownflags/Cformflags/Cformcount/Ccolour/C5unknown/vheight/vweight/C*unknown_', $rawdata);
		$poke['height'] /= 10;
		$poke['weight'] /= 10;
		if ($baseid <= $this->getCount('baby')) {
			$poke['child'] = $this->getFile('baby', $baseid);
			$poke['child'] = ord($poke['child'][0]) + (ord($poke['child'][1])<<8);
		}
		$poke['femalechance'] == 255 ? -1 : round(100*($poke['femalechance']/254),1);
		for ($i = 1; $i <= 3; $i++) {
			$poke['abilities'][$gamecfg['Ability Types'][$i-1]] = $poke['ability'.$i];
			$poke['items'][$gamecfg['Item Types'][$i-1]] = $poke['itemID'.$i];
			unset($poke['ability'.$i]);
			unset($poke['itemID'.$i]);
		}
		for ($i = 1; $i <= 2; $i++) {
			$poke['egggroups']['Egg Group '.$i] = $gamecfg['Egg Groups'][$poke['egggrp'.$i]];
			unset($poke['egggrp'.$i]);
		}
		$poke['type1'] = $gamecfg['Types'][$poke['type1']];
		$poke['type2'] = $gamecfg['Types'][$poke['type2']];
		$poke['id'] = $id;
		$poke['imgid'] = $id;
		static $EVlist = array('HP', 'Attack', 'Defense', 'Speed', 'Sp. Attack', 'Sp. Defense');
		for ($i = 0; $i < count($EVlist); $i++)
			if (($poke['EVraw']&(3<<2*$i))>>($i*2))
				$poke['EVs'][$EVlist[$i]] = (($poke['EVraw']&(3<<2*$i))>>($i*2));
		unset($poke['EVraw']);
		return $poke;
	}
	function getEvolutions($id) {
		global $gamecfg;
		$output = array();
		$data = $this->getFile('evolutiondata', $id);
		for ($i = 0; $i < 7; $i++) {
			$tmp = unpack('CType/Cunknown/vArgument/vTarget', substr($data, $i*6, 6));
			if ($tmp['Type'] != 0) {
				$tmp['Type'] = $gamecfg['Evolution Types'][$tmp['Type']];
				$output[] = $tmp;
			}
		}
		return $output;
	}
	function getMoveList($id, $level = -1) {
		global $gamecfg;
		$moves = array();
		$baseid = $this->getBaseID($id);
		$rawdata = $this->getFile('stats', $id);
		if ($baseid <= $this->getCount('baby')) {
			$child = $this->getFile('baby', $baseid);
			$child = ord($child[0]) + (ord($child[1])<<8);
		} else
			$child = 0;
		foreach ($this->getLevelUpMoveData($id, $level) as $data)
			$moves['Levelup'][] = array('Learned' => 'Level '.$data['learned'], 'id' => $data['move']);
		if ($level == -1) {
			foreach ($this->getEggMoveData($child) as $data)
				$moves['Egg'][] = array('Learned' => $data['learned'], 'id' => $data['move']);
			
			$str = '';
			for ($i = 40; $i < 53; $i++)
				$str .= strrev(sprintf('%08b', ord($rawdata[$i])));

			for ($i = 0; $i < 101; $i++)
				if (substr($str, $i, 1) == '1')
					$moves['TM'][] = array('Learned' => $i > 94 ? 'HM'.sprintf('%02d',$i-94) : 'TM'.sprintf('%02d',$i+1), 'id' => $gamecfg['TM Map'][$i]);
			if (isset($rawdata[56]))
				for ($i = 0; $i < 8; $i++)
					if (ord($rawdata[56]) & pow(2,$i))
						$moves['Tutor'][] = array('Learned' => 'Tutor', 'id' => $gamecfg['Tutor Map'][$i]);
			if (isset($rawdata[60]))
				for ($j = 0; $j < 16; $j++)
					for ($i = 0; $i < 8; $i++)
						if (ord($rawdata[60+$j]) & pow(2,$i))
							$moves['Tutor'][] = array('Learned' => isset($gamecfg['Tutor Map'][$j*8+$i+8]) ? 'Tutor' : 'Tutor (Unknown)', 'id' => isset($gamecfg['Tutor Map'][$j*8+$i+8]) ? $gamecfg['Tutor Map'][$j*8+$i+8] : $j*8+$i+8);
		} else
			$moves = $moves['Levelup'];
		return $moves;
	}
	function getMove($id) {
		global $gamecfg;
		static $flags = array('MAKES_CONTACT', 'POWER_HERB', 'HYPER_BEAM', 'BRIGHTPOWDER', 'MAGIC_COAT', 'IS_SNATCHABLE', 'UNKNOWN', 'IS_PUNCH', 'IS_SOUND', 'FAILS_IN_GRAVITY', 'DETHAWS_USER', 'CAN_HIT_NON-ADJACENT', 'HEALS', 'UNKNOWN2', 'UNKNOWN3', 'UNKNOWN4');
		$output = unpack('Ctype/Cinternal_category/Ccategory/Cpower/Caccuracy/Cpp/cpriority/Chits/Cstatus/Cunknown/Ceffectchance/Cunknown2/Cunknown3/Cunknown4/Ccritlevel/Cflinchchance/veffect/cdrain_percentage/cheal_percentage/Cunknown5/C3stat/c3statdelta/C3stat_chance/C2always_83/vflags/C2null', $this->getFile('movedata', $id, true));
		$output['type'] = $gamecfg['Types'][$output['type']];
		for ($i = 0; $i < count($flags); $i++)
			$output['Flags'][$flags[$i]] = ($output['flags'] & pow(2,$i)) != 0;
		return $output;
	}
	function getLevelUpMoveData($id, $maxlevel = -1) {
		$data = $this->getFile('levelupmovedata', $id);
		$output = array();
		for ($i = 0; $i < strlen($data)/4; $i++) {
			$d = unpack('vmove/vlearned', substr($data, $i*4, 4));
			if (($d['learned'] == 65535) || (($maxlevel != -1) && ($d['learned'] > $maxlevel)))
				continue;
			$output[] = $d;
			if (($maxlevel > -1) && (count($output) > 4))
				array_shift($output);
		}
		return $output;
	}
	function getTrainerData($id) {
		$trdata = $this->getFile('trainerdata', $id);
		$output['class'] = $this->getTextEntry('Trainer Classes', ord($trdata[1]));
		$output['classid'] = ord($trdata[1]);
		$output['battletype'] = $this->getTextEntry('Battle Types', ord($trdata[2]));
		$output['items'] = array();
		for ($i = 0; $i < 4; $i++) {
			$item = ord($trdata[4+$i*2])+(ord($trdata[5+$i*2])<<8);
			if ($item > 0)
				$output['items'][] = $item;
		}
		return $output;
	}
	function getTrainerPokemon($id) {
		$trdata = $this->getFile('trainerdata', $id);
		$trpoke = $this->getFile('trainerpokemon', $id);
		$output = array();
		$datatype = ord($trdata[0]);
		$numpokes = ord($trdata[3]);
		$length = 8 + ($datatype & 1)*8 + ($datatype&2);
		for ($i = 0; $i < $numpokes; $i++) {
			$id = ord($trpoke[$i*$length+4]) + (ord($trpoke[$i*$length+5])<<8);
			$level = ord($trpoke[$i*$length+2]);
			$item = '';
			$moves = array();
			if ($datatype & 2)
				$item = ord($trpoke[$i*$length+8])+(ord($trpoke[$i*$length+9])<<8);
			if ($datatype & 1)
				for ($j = 0; $j < 4; $j++)
					$moves[] = ord($trpoke[$i*$length+8+$j*2])+(ord($trpoke[$i*$length+9+$j*2])<<8);
			else {
				$movelist = $this->getMoveList($id, $level);
				foreach ($movelist as $m)
					$moves[] = $m['id'];
			}
			$output[] = array('level' => $level, 'id' => $id, 'move' => $moves, 'item' => $item);
		}
		return $output;
	}
	function getItem($id) {
		$output = unpack('vvalue/Cunknown/CHPPPRestored/Cunknown2/Cunknown3/Cweight/Cnatural_gift_power/vFlags/Cunknown4/Cunknown5/Cunknown6/Cunknown7/Cunknown8/Vunknown10/Vunknown11/cHPEVdelta/catkEVdelta/cdefEVdelta/cspeedEVdelta/cspatkEVdelta/cspdefEVdelta/Cunknown12/Cunknown13/Cunknown14/Cunknown15/Cunknown16/Cunknown17/Cunknown18', $this->getFile('itemdata', $id));
		$output['value'] *= 10;
		return $output;
	}
	function getArea($id) {
		global $gamecfg;
		$data = $this->getFile('areadata', $id);
		for ($i = 0; $i < strlen($data)/232; $i++) {
			$k = 0;
			if (strlen($data)/232 == 1)
				$season = '';
			else
				$season = ' ('.$gamecfg['Area Data']['Seasons'][$i].')';
			for ($j = 0; $j < 8; $j++)
				$output['unknown'][] = ord($data[$j+$i*232]);
			for ($j = 2; $j < 58; $j++) {
				if (in_array($j, $gamecfg['Area Data']['Separators']))
					$k++;
				$tmpval = ord($data[$j*4+$i*232]) + (ord($data[($j*4)+1+$i*232])<<8);
				$pokeid = $tmpval&0x7FF;
				if ($pokeid == 0)
					continue;
				$tmp['flags'][] = ($tmpval&0xF800) >> 11;
				$tmp['minlevel'] = ord($data[($j*4)+2+$i*232]);
				$tmp['maxlevel'] = ord($data[($j*4)+3+$i*232]);
				if (!isset($output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid]))
					$output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid] = $tmp;
				else {
					$output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid]['flags'][] = $tmp['flags'][0];
					$output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid]['minlevel'] = min($output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid]['minlevel'], $tmp['minlevel']);
					$output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid]['maxlevel'] = max($output['Encounters'][$gamecfg['Area Data']['Labels'][$k].$season][$pokeid]['maxlevel'], $tmp['maxlevel']);
				}
			}
		}
		
		$output['name'] = (isset($gamecfg['Area Data']['Names'][$id])) ? $gamecfg['Area Data']['Names'][$id]: $id;
		return $output;
	}
	function getEggMoveData($id) {
		if ($id > $this->getCount('eggmovedata'))
			return array();
		$data = $this->getFile('eggmovedata', $id);
		if ($data == null)
			return array();
		$output = array();
		if ((ord($data[0]) == 0) && (ord($data[1]) == 0))
			return $output;
		for ($i = 0; $i < strlen($data)/2; $i++) {
			$moveRAW = ord($data[$i*2]) + (ord($data[$i*2+1])<<8);
			$output[] = array('learned' => 'Egg', 'move' => $moveRAW);
		}
		return $output;
	}
}
?>