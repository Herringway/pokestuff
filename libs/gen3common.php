<?php
class gen3 extends basegame {
	
	function loadRom() {
		if ($this->rom === null)
			$this->rom = fopen('data/'.$this->lang.'/'.$this->gameid.'.gba','r');
	}
	function getCount($what) {
		global $gamecfg;
		if (isset($gamecfg['Tables'][$what]['Entries']))
			return $gamecfg['Tables'][$what]['Entries'];
		return 0;
	}
	function getRawData($what, $where) {
		$output['data'] = $this->getRawDataEntry($what, $where);
		$output['id'] = $where;
		return $output;
	}
	function listTables() {
		global $gamecfg;
		foreach ($gamecfg['Tables'] as $tname => $table)
			$output[$tname] = array('Entries' => $table['Entries']);
		return $output;
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
	function readText($offset, $size = -1) {
		$this->loadRom();
		global $gamecfg;
		if ($size == -1)
			$size = 0x1000;
		fseek($this->rom, $offset);
		$output = '';
		for ($i = 0; $i < $size; $i++) {
			$val = $this->readByte();
			if ($val == 0xFF)
				break;
			if (!isset($gamecfg['Text Table'][$val]))
				$output .= sprintf('[%02X]', $val);
			else
				$output .= $gamecfg['Text Table'][$val];
		}
		return $output;
	}
	function getFixedDataEntry($what, $which) {
		$this->loadRom();
		global $gamecfg;
		if (!isset($gamecfg['Tables'][$what]))
			throw new Exception('Table not found');
		fseek($this->rom, $gamecfg['Tables'][$what]['Offset'] + $gamecfg['Tables'][$what]['Entry Size'] * $which);
		return unpack(implode('/', $gamecfg['Tables'][$what]['Format']), fread($this->rom, $gamecfg['Tables'][$what]['Entry Size']));
	}
	function getRawDataEntry($what, $which) {
		$this->loadRom();
		global $gamecfg;
		return $this->getFixedDataEntry($what, $which);
		//fseek($this->rom, $gamecfg['Tables'][$what]['Offset'] + $gamecfg['Tables'][$what]['Entry Size'] * $which);
		//return unpack('C*', fread($this->rom, $gamecfg['Tables'][$what]['Entry Size']));
	}
	function getTextEntry($what, $which) {
		global $gamecfg;
		if (isset($gamecfg['Text Offsets'][$what])) {
			$format = '%s';
			if (isset($gamecfg['Text Offsets'][$what]['Format']))
				$format = $gamecfg['Text Offsets'][$what]['Format'];
			return sprintf($format, $this->readText($gamecfg['Text Offsets'][$what]['Offset']+$gamecfg['Text Offsets'][$what]['Width'] * $which, $gamecfg['Text Offsets'][$what]['Width']));
		} else if ($what == 'Pokedex Entries') {
			$dexdata = $this->getFixedDataEntry('dexdata', $which);
			return $this->readText($dexdata['TextOffset']-0x08000000);
		} else if ($what == 'Move Descriptions') {
			$dexdata = $this->getFixedDataEntry('movedescriptiontable', $which);
			return $this->readText($dexdata['TextOffset']-0x08000000);
		} else if ($what == 'Ability Descriptions') {
			$dexdata = $this->getFixedDataEntry('abilitydescriptiontable', $which);
			return $this->readText($dexdata['TextOffset']-0x08000000);
		} else if ($what == 'Item Descriptions') {
			$dexdata = $this->getFixedDataEntry('itemdata', $which);
			return $this->readText($dexdata['descPtr']-0x08000000);
		}
		return sprintf('PLACEHOLDER: %s[%d]', $what, $which);
	}
	function getBaseID($id) {
		$v = $this->getFixedDataEntry('dexentries', $id);
		return $v['ID'];
	}
	function getItem($id) {
		global $gamecfg;
		$this->loadRom();
		$output = $this->getFixedDataEntry('itemdata', $id);
		$output['id'] = $id;
		
		return $output;
	}
	function getStats($id) {
		global $gamecfg;
		$this->loadRom();
		$vals = array_merge($this->getFixedDataEntry('stats', $id), $this->getFixedDataEntry('dexentries', $id), $this->getFixedDataEntry('hoenndexentries', $id));
		$vals['id'] = $id;
		$vals['imgid'] = $vals['ID'];
		for ($i = 1; $i <= 2; $i++) {
			$vals['abilities']['Ability '.$i] = $vals['ability'.$i];
			unset($vals['ability'.$i]);
			$vals['items']['Item '.$i] = $vals['item'.$i];
			unset($vals['item'.$i]);
			$vals['egggroups']['Egg Group '.$i] = $gamecfg['Egg Groups'][$vals['egggrp'.$i]];
			unset($vals['egggrp'.$i]);
			$vals['type'.$i] = $gamecfg['Types'][$vals['type'.$i]]['Name'];
		}
		$dexdata = $this->getFixedDataEntry('dexdata', $this->getBaseID($id));
		$vals['dex'] = $dexdata;
		$vals['height'] = ($dexdata['Height']/10).'m';
		$vals['weight'] = ($dexdata['Weight']/10).'kg';
		$vals['hatchsteps']++;
		$vals['hatchsteps'] *= 256;
		static $EVlist = array('HP', 'Attack', 'Defense', 'Speed', 'Sp. Attack', 'Sp. Defense');
		for ($i = 0; $i < count($EVlist); $i++)
			if (($vals['EVraw']&(3<<2*$i))>>($i*2))
				$vals['EVs'][$EVlist[$i]] = (($vals['EVraw']&(3<<2*$i))>>($i*2));
		return $vals;
	}
	function getEvolutions($id) {
		$output = array();
		for ($i = 0; $i < 5; $i++) {
			$data = $this->getFixedDataEntry('evodata', $id*5+$i);
			if ($data['Target'] != 0)
				$output[] = array_merge($data, array('name' => $this->getTextEntry('Pokemon Names', $data['Target'])));
		}
		return $output;
	}
	function getMove($id) {
		global $gamecfg;
		$output = $this->getFixedDataEntry('movedata', $id);
		$output['category'] = $gamecfg['Types'][$output['typeid']]['Category'];
		$output['id'] = $id;
		$output['type'] = $gamecfg['Types'][$output['typeid']]['Name'];
		
		return $output;
	}
	function getMoveList($id) {
		global $gamecfg;
		$output = array();
		$tms = $this->getFixedDataEntry('tmcompatibility', $id);
		for ($i = 0; $i < 58; $i++)
			$tmlist[$i] = ($tms['tm'.(intval($i/8)+1)] & (1<<$i%8)) != 0;
		foreach ($tmlist as $k => $v)
			if ($v == true)
				$output['TM'][] = array('Learned' => ($k < 50) ? sprintf('TM%02d',$k+1) : sprintf('HM%02d',$k-49), 'id' => $this->getFixedDataEntry('tmmap', $k)[1]);
		
		$levelupptr = $this->getFixedDataEntry('levelupmoves', $id);
		fseek($this->rom, $levelupptr['Pointer']-0x8000000);
		while (($move = $this->readShort()) != 65535)
			$output['Levelup'][] = array('id' => $move&0x1FF, 'Learned' => 'Level '.($move>>9));
		fseek($this->rom, $gamecfg['eggmoves']);
		$childid = $id;
		while (($b = $this->readEggEntry()) != null)
			if ($b['id'] == $childid) {
				foreach ($b['moves'] as $moveid)
					$output['Egg'][] = array('Learned' => 'Egg', 'id' => $moveid);
				break;
			}
			if ($b['id'] > $childid)
				break;
		return $output;
	}
	private function readEggEntry() {
		$id = $this->readShort();
		if (($id < 20000) || ($id > 20413))
			return null;
		$moves = array();
		while (($v = $this->readShort()) < 20000)
			$moves[] = $v;
		fseek($this->rom, -2, SEEK_CUR);
		return array('id' => $id-20000, 'moves' => $moves);
	}
}

?>