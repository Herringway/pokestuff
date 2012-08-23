<?php
require_once 'libs/ndslib.php';
require_once 'libs/gen4text.php';
class gen4 extends basegame {
	private $textdata;
	private $narcs;
	private $rom;
	const generation = 'gen4';
	
	function listTables() {
		$this->loadRom();
		$this->rom->autoNARC();
		$output = array();
		foreach ($this->rom as $k=>$narc) {
			if ($narc instanceof NARCfile)
				$output[$k] = array('Data Size' => $narc->FIMG['size'], 'Files' => $narc->Files, 'Average File Size' => ceil($narc->FIMG['size']/$narc->Files));
			else
				$output[$k] = array('Data Size' => strlen($narc));
		}
		ksort($output);
		return $output;
	}
	function getCount($what) {
		$this->loadNarc($what);
		if ($what == 'stats')
			return $this->narcs[$what]->Files;
		if ($what == 'movedata')
			return $this->narcs[$what]->Files;
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
	function getRawFile($what) {
		global $gamecfg;
		$this->loadRom();
		if (!isset($this->rom['/'.$gamecfg['narcs'][$what]]))
			throw new Exception('File nonexistant');
		return $this->rom['/'.$gamecfg['narcs'][$what]];
	}
	function loadText() {
		$this->loadNarc('text');
		if ($this->textdata === null)
			$this->textdata = new gen4text($this->narcs['text']);
	}
	function getText() {
		$this->loadNarc('text');
		foreach ($this->narcs['text'] as $textdata) {
			$textfile = new gen4text($textdata);
			$output[] = $textfile->dump();
		}
		return $output;
	}
	function getTextEntry($type, $id) {
		global $gamecfg;
		if (($type == 'Pokemon Names') && ($id > 493))
			return $gamecfg['text']['Unlisted Pokemon'][$id-494];
		if (($type == 'Move Names') && ($id > 467))
			return $gamecfg['text']['Unlisted Moves'][$id-468];
		if (($type == 'Move Descriptions') && ($id > 467))
			return $gamecfg['text']['Unlisted Move Descriptions'][$id-468];
		if (isset($gamecfg['text']['File Map'][$type])) {
			if (!isset($this->textFiles[$gamecfg['text']['File Map'][$type]])) {
				$this->textFiles[$gamecfg['text']['File Map'][$type]] = new gen4text($this->getFile('text', $gamecfg['text']['File Map'][$type]));
			}
			if (isset($this->textFiles[$gamecfg['text']['File Map'][$type]][$id]))
				return $this->textFiles[$gamecfg['text']['File Map'][$type]][$id];
			return sprintf('Invalid entry %d', $id);
		}
		return sprintf('Warning: %s not found', $type);
	}
	function loadRom() {
		if ($this->rom == null)
			$this->rom = new ndsrom('games/'.get_class($this).'.nds');
	}
	function loadNarc($name) {
		global $gamecfg;
		$this->loadRom();
		if (!isset($gamecfg['narcs'][$name]))
			throw new Exception('Unknown narc');
		if (!isset($this->narcs[$name])) 
			$this->narcs[$name] = $this->rom->getNARC('/'.$gamecfg['narcs'][$name]);
	}
	function getFile($name, $id) {
		$this->loadNarc($name);
		return $this->narcs[$name][$id];
	}
	function getBaseID($id) {
		return $id;
	}
	function getStats($id) {
		global $gamecfg;
		static $abilitytypes = array('Ability 1', 'Ability 2');
		static $itemtypes = array('Item held', 'Rare item held');
		$baseid = $id;

		$rawdata = $this->getFile('stats', $id);
		$poke = unpack('Chp/Catk/Cdef/Cspeed/Csatk/Csdef/C2type/Ccapturerate/Cxprate/vEVraw/v2itemID/Cfemalechance/Chatchsteps/Cbasehappiness/Cgrowthrate/C2egggrp/C2ability/C4unknown/C13TMs', $rawdata);
		$poke['id'] = $id;
		$poke['imgid'] = $id;
		$poke['hatchsteps']++;
		$poke['hatchsteps'] *= 255;
		for ($i = 1; $i <= 2; $i++) {
			$poke['abilities'][$abilitytypes[$i-1]] = $poke['ability'.$i];
			unset($poke['ability'.$i]);
			$poke['items'][$itemtypes[$i-1]] = $poke['itemID'.$i];
			unset($poke['itemID'.$i]);
			$poke['type'.$i] = $gamecfg['Types'][$poke['type'.$i]];
			$poke['egggroups']['Egg Group '.$i] = $gamecfg['Egg Groups'][$poke['egggrp'.$i]];
			unset($poke['egggrp'.$i]);
		}
		static $EVlist = array('HP', 'Attack', 'Defense', 'Speed', 'Sp. Attack', 'Sp. Defense');
		for ($i = 0; $i < count($EVlist); $i++)
			if (($poke['EVraw']&(3<<2*$i))>>($i*2))
				$poke['EVs'][$EVlist[$i]] = (($poke['EVraw']&(3<<2*$i))>>($i*2));
		unset($poke['EVraw']);
		return $poke;
	}
	function getMove($id) {
		global $gamecfg;
		$output = unpack('Cunknown/Cunknown2/Ccategory/Cpower/Ctype/Caccuracy/Cpp/Cunknown7/Cunknown8/Cunknown9/cpriority/Cunknown11/Cunknown12/Cunknown13/Cunknown14/Cunknown15', $this->getFile('movedata', $id, true));
		$output['id'] = $id;
		$output['type'] = $gamecfg['Types'][$output['type']];
		return $output;
	}
	function getLevelUpMoveData($id, $maxlevel = -1) {
		$data = $this->getFile('levelupmovedata', $id);
		$output = array();
		for ($i = 0; $i < strlen($data)/2; $i++) {
			$level = ord($data[$i*2+1])>>1;
			$moveid = (ord($data[$i*2]) + (ord($data[$i*2+1])<<8))&0x1FF;
			if (($level == 127) || (($maxlevel != -1) && ($level > $maxlevel)))
				break;
			$output[] = array('Learned' => 'Level '.$level, 'id' => $moveid);
			if (($maxlevel > -1) && (count($output) > 4))
				array_shift($output);
		}
		return $output;
	}
	function getTrainerData($id) {
		$trdata = $this->getFile('trainerdata', $id);
		$output['class'] = $this->getTextEntry('Trainer Classes', ord($trdata[1]));
		$output['items'] = array();
		for ($i = 0; $i < 4; $i++) {
			$item = ord($trdata[4+$i*2])+(ord($trdata[5+$i*2])<<8);
			if ($item > 0)
				$output['items'][] = $item;
		}
		return $output;
	}
	function getTrainerPokemon($id) {
		global $gamecfg;
		$trdata = $this->getFile('trainerdata', $id);
		$trpoke = $this->getFile('trainerpokemon', $id);
		$output = array();
		$baselength = 6;
		if (!isset($gamecfg['Trainer Ver']) || $gamecfg['Trainer Ver'] != 'old')
			$baselength += 2;
		$datatype = ord($trdata[0]);
		$numpokes = ord($trdata[3]);
		$length = $baselength + ($datatype & 1)*8 + ($datatype&2);
		for ($i = 0; $i < $numpokes; $i++) {
			$id = ord($trpoke[$i*$length+4]) + (ord($trpoke[$i*$length+5])<<8)&0x1FF;
			$special = ord($trpoke[$i*$length+5])>>1;
			$level = ord($trpoke[$i*$length+2]);
			$item = '';
			$moves = array();
			if ($datatype & 2)
				$item = ord($trpoke[$i*$length+6])+(ord($trpoke[$i*$length+7])<<8);
			if ($datatype & 1)
				for ($j = 0; $j < 4; $j++)
					$moves[] = ord($trpoke[$i*$length+$baselength+$j*2])+(ord($trpoke[$i*$length+$baselength+1+$j*2])<<8);
			else if (method_exists($this, 'getMoveList')) {
				$movelist = $this->getLevelUpMoveData($id, $level);
				foreach ($movelist as $m)
					$moves[] = $m['id'];
			}
			$output[] = array('level' => $level, 'id' => $id, 'move' => $moves, 'item' => $item, 'special' => $special);
		}
		return $output;
	}
	function getChild($id) {
		$data = $this->getRawFile('children');
		$cid = unpack('v', substr($data, $id*2,2));
		return $cid[1];
	}
	function getEvolutions($id) {
		global $gamecfg;
		$output = array();
		$data = $this->getFile('evolutiondata', $id);
		for ($i = 0; $i < 7; $i++) {
			$tmp = unpack('CType/Cunknown/vArgument/vTarget', substr($data, $i*6, 6));
			if ($tmp['Type'] != 0) {
				$tmp['name'] = $this->getTextEntry('Pokemon Names', $tmp['Target']);
				$output[] = $tmp;
			}
		}
		return $output;
	}
	function getEggMoveData($id) {
		global $gamecfg;
		if (!isset($gamecfg['eggmoves']) || ($gamecfg['eggmoves'] != 'narc')) {
			$data = $this->getRawFile('eggmoves');
		} else
			$data = $this->getFile('eggmoves', 0);
		$offset = 0;
		if (isset($gamecfg['Egg Moves Offset']))
			$offset = $gamecfg['Egg Moves Offset'];
		$output = array();
		$countpoke = $this->getCount('stats');
		while (true) {
			$tid = unpack('vID', substr($data, $offset, 2));
			$offset += 2;
			if (!isset($tid['ID']))
				throw new Exception('Cannot read data!');
			if ($tid['ID'] < 20000)
				continue;
			if ($tid['ID'] > 20000 + $countpoke)
				break;
			if ($tid['ID']-20000 == $id) {
				$length = 2;
				while (true) {
					$t2id = unpack('vID', substr($data, $offset+$length, 2));
					if ($t2id['ID'] > 20000)
						break;
					$length += 2;
				}
				break;
			}
		}
		if (isset($length)) {
			$b = unpack('vID/v*moves', substr($data, $offset, $length));
			for ($i = 0; $i < count($b)-1; $i++) {
				$output[] = array('Learned' => 'Egg', 'id' => $b['moves'.($i+1)]);
			}
		}
		return $output;
	}
	function getMoveList($id, $level = -1) {
		global $gamecfg;
		$moves = array();
		//$baseid = $this->getBaseID($id);
		$moves['Levelup'] = $this->getLevelUpMoveData($id, $level);
		$moves['TM'] = $this->getTMMoveData($id);
		$child = $this->getChild($id);
		$moves['Egg'] = $this->getEggMoveData($child);
		return $moves;
	}
	function getTMMoveData($id) {
		global $gamecfg;
		$poke = unpack('Chp/Catk/Cdef/Cspeed/Csatk/Csdef/C2type/Ccapturerate/Cxprate/vEVraw/v2itemID/Cfemalechance/Chatchsteps/Cbasehappiness/Cgrowthrate/C2egggrp/C2ability/C4unknown/C13TMs', $this->getFile('stats', $id));
		$output = array();
		for ($i = 0; $i < 13; $i++)
			for ($j = 0; $j < 8; $j++)
				if ($poke['TMs'.($i+1)] & pow(2,$j))
					$output[] = array('Learned' => ($i*8+$j+1 > 92) ? sprintf('HM%02d',$i*8+$j-91) : sprintf('TM%02d',$i*8+$j+1), 'id' => $gamecfg['TM Map'][$i*8+$j]);
		return $output;
	}
	function getItem($id) {
		$this->loadNarc('itemdata');
		if ($id >= $this->narcs['itemdata']->Files)
			$id = $this->narcs['itemdata']->Files-1;
		$output = unpack('vvalue/Cunknown/CHPPPRestored/Cunknown2/Cunknown3/Cweight/C*unknown_', $this->getFile('itemdata', $id));
		return $output;
	}
	function getArea($id) {
		global $gamecfg;
		$data = $this->getFile('areadata', $id);
		$output['name'] = $gamecfg['Area Data']['Names'][$id];
		$output['Encounters'] = array();
		if ($gamecfg['Area Data']['Format'] == 'gen4_1') {
			$output['Rate']['Grass'] = readint_str($data,0);
			$table = array();
			for ($i = 0; $i < 12; $i++) {
				$raw = readint_str($data, ($i * 8) + 4);
				$raw2 = readint_str($data, ($i * 8) + 8);
				$table[] = array($raw, $raw2);
				$id = $raw2 & 0x1FF;
				$lvl = $raw & 0xFF;
				if ($id != 0) {
					if (!isset($output['Encounters']['Grass'][$id]))
						$output['Encounters']['Grass'][$id] = array('minlevel' => $lvl, 'maxlevel' => $lvl, 'flags' => array());
					else {
						$output['Encounters']['Grass'][$id]['minlevel'] = min($output['Encounters']['Grass'][$id]['minlevel'], $lvl);
						$output['Encounters']['Grass'][$id]['maxlevel'] = max($output['Encounters']['Grass'][$id]['maxlevel'], $lvl);
					}
				}
			}
			$mapid = array(0, 1, 2, 3, 2, 3, 4, 5, 10, 11, 8, 9, 8, 9, 8, 9, 8, 9, 8, 9);
			$spectypes = array('Swarm', 'Swarm', 'Daytime/Nighttime', 'Daytime/Nighttime', 'Daytime/Nighttime', 'Pokeradar', 'Pokeradar', 'Pokeradar', 'Pokeradar', 'Pokeradar', 'GBA', 'GBA', 'GBA', 'GBA', 'GBA', 'GBA', 'GBA', 'GBA', 'GBA', 'GBA', 'Pokeradar');
			for ($i = 0; $i < 10; $i++) {
				$raw = readint_str($data, ($i * 4) + 100);
				$id = $raw & 0x1FF;
				if ($id != $table[$mapid[$i]][1]&0x1FF)
					$output['Encounters'][$spectypes[$i]][$id] = array('minlevel' => $table[$mapid[$i]][0], 'maxlevel' => $table[$mapid[$i]][0], 'flags' => array());
			}
			for ($i = 0; $i < 10; $i++) {
				$raw = readint_str($data, ($i * 4) + 164);
				$id = $raw & 0x1FF;
				if ($id != $table[$mapid[$i+10]][1]&0x1FF)
					$output['Encounters'][$spectypes[$i+10]][$id] = array('minlevel' => $table[$mapid[$i+10]][0], 'maxlevel' => $table[$mapid[$i+10]][0], 'flags' => array());
			}
			$areaid = array('Surfing', '', 'Fishing (Old Rod)', 'Fishing (Good Rod)', 'Fishing (Super Rod)');
			for ($i = 0; $i < 5; $i++) {
				$output['Rate'][$areaid[$i]] = readint_str($data, $i * 44 + 204);
				for ($j = 0; $j < 5; $j++) {
					$lvlrange = readint_str($data, $i * 44 + 208 + $j * 8);
					$maxlevel = $lvlrange & 0xFF;
					$minlevel = $lvlrange>>8;
					$id = readint_str($data, $i * 44 + 212 + $j * 8);
					if ($id > 0) {
						if (!isset($output['Encounters'][$areaid[$i]][$id]))
							$output['Encounters'][$areaid[$i]][$id] = array('minlevel' => $minlevel, 'maxlevel' => $maxlevel, 'flags' => array());
						else {
							$output['Encounters'][$areaid[$i]][$id]['minlevel'] = min($output['Encounters'][$areaid[$i]][$id]['minlevel'], $minlevel);
							$output['Encounters'][$areaid[$i]][$id]['maxlevel'] = max($output['Encounters'][$areaid[$i]][$id]['maxlevel'], $maxlevel);
						}
					}
				}
			}
		}
		return $output;
	}
}
?>
