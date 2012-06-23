<?php
require_once 'libs/narc.php';
require_once 'libs/gen5text.php';
class b2jpn {
	const name = 'Black 2';
	const locale = 'Japan';
	const generation = 'gen5';
	const dexFile = 442;
	private $textdata;
	private $storyTextData;
	private $narcs;
	
	static function getNumberPokemon() {
		$narc = new NARCFile('games/b2jpn/0/1/6');
		return $narc->Files-1;
	}
	function loadText() {
		if ($this->textdata === null)
			$this->textdata = new gen5text('games/b2jpn/0/0/2');
	}
	function loadStoryText() {
		if ($this->storyTextData === null)
			$this->storyTextData = new gen5text('games/b2jpn/0/0/3');
	}
	function getText() {
		$this->loadText();
		$this->loadStoryText();
		foreach ($this->textdata as $textfile)
			$output[] = $textfile;
		foreach ($this->storyTextData as $textfile)
			$output[] = $textfile;
		return $output;
	}
	function getName($id) {
		$this->loadText();
		return $this->textdata[90][$id];
	}
	function getSpecies($id) {
		$this->loadText();
		return $this->textdata[464][$id];
	}
	function getPokedexEntry($id) {
		$this->loadText();
		return $this->textdata[self::dexFile][$id];
	}
	function getMoveName($id) {
		$this->loadText();
		return $this->textdata[403][$id];
	}
	function getMoveDesc($id) {
		$this->loadText();
		return $this->textdata[402][$id];
	}
	function getAbilityName($id) {
		$this->loadText();
		return $this->textdata[374][$id];
	}
	function getAbilityDesc($id) {
		$this->loadText();
		return $this->textdata[375][$id];
	}
	function getItemName($id) {
		$this->loadText();
		return $this->textdata[64][$id];
	}
	function getItemDesc($id) {
		$this->loadText();
		return $this->textdata[63][$id];
	}
	function loadNarc($name) {
		static $translation = array(
			'sprites' => '0/0/4',
			'stats' => '0/1/6', 
			'levelupmovedata' => '0/1/8',
			'evolutiondata' => '0/1/9',
			'baby' => '0/2/0',
			'movedata' => '0/2/1',
			'eggmovedata' => '1/2/4',
			'wildencounters' => '1/2/6');
		if (!isset($translation[$name]))
			throw new Exception('Unknown narc');
		if (!isset($this->narcs[$name])) 
			$this->narcs[$name] = new NARCFile('games/'.get_class($this).'/'.$translation[$name]);
	}
	function getFile($name, $id, $cache = false) {
		$this->loadNarc($name);
		if ($cache && isset($this->cachefiles[$name][$id]))
			return $this->cachefiles[$name][$id];
		if ($cache)
			return $this->cachefiles[$name][$id] = $this->narcs[$name]->getFile($id);
		return $this->narcs[$name]->getFile($id);
	}
	function getStats($id) {
		static $formorig = array(650 => 386, 651 => 386, 652 => 386, 653 => 413, 654 => 413, 655 => 492, 656 => 487,657 => 479,658 => 479,659 => 479,660 => 479,661 => 479,662 => 351,663 => 351,664 => 351,665 => 550,666 => 555,667 => 648);
		static $abilitytypes = array('Ability 1', 'Ability 2', 'Dream World ability');
		static $itemtypes = array('Item held', 'Rare item held', 'Dream World item');
		$baseid = $id;
		if (isset($formorig[$id]))
			$baseid = $formorig[$id];

		$rawdata = $this->getFile('stats', $id);
		$poke = unpack('Chp/Catk/Cdef/Cspeed/Csatk/Csdef/C2type/Ccapturerate/Cxprate/vEVraw/v3itemID/Cfemalechance/Chatchsteps/Cbasehappiness/Cgrowthrate/C2egggrp/C3ability/Cunknownflags/Cformflags/Cformcount/Ccolour/vunknown/vheight/vweight', $rawdata);
		for ($i = 40; $i < strlen($rawdata); $i++)
			$poke['Raw_Unknown'][] = ord($rawdata[$i]);
			
		for ($i = 1; $i <= 3; $i++) {
			$aid = $poke['ability'.$i];
			$iid = $poke['itemID'.$i];
			$poke['abilities'][$abilitytypes[$i-1]] = array('name' => $this->getAbilityName($aid), 'desc' => $this->getAbilityDesc($aid), 'id' => $aid);
			$poke['items'][$itemtypes[$i-1]] = array('name' => $this->getItemName($iid), 'desc' => $this->getItemDesc($iid), 'id' => $iid);
		}
		$poke['id'] = $id;
		$poke['species'] = $this->getSpecies($baseid);
		static $EVlist = array('HP', 'Attack', 'Defense', 'Speed', 'Sp. Attack', 'Sp. Defense');
		for ($i = 0; $i < count($EVlist); $i++)
			if (($poke['EVraw']&(3<<2*$i))>>($i*2))
				$poke['EVs'][$EVlist[$i]] = (($poke['EVraw']&(3<<2*$i))>>($i*2));
		return $poke;
	}
	function getMoveList($id, $level = -1) {
		static $TMMap = array(468,337,473,347,46,92,258,339,474,237,241,269,58,59,63,113,182,240,477,219,218,76,479,85,87,89,216,91,94,247,280,104,115,482,53,188,201,126,317,332,259,263,488,156,213,168,490,496,497,315,502,411,412,206,503,374,451,507,510,511,261,512,373,153,421,371,514,416,397,148,444,521,86,360,14,522,244,523,524,157,404,525,526,398,138,447,207,365,369,164,430,433,528,249,555,15,19,57,70,127,291);
		static $tutormap = array(0 => 520,1 => 519,2 => 518,3 => 338,4 => 307,5 => 308,6 => 434);
		static $formorig = array(650 => 386, 651 => 386, 652 => 386, 653 => 413, 654 => 413, 655 => 492, 656 => 487,657 => 479,658 => 479,659 => 479,660 => 479,661 => 479,662 => 351,663 => 351,664 => 351,665 => 550,666 => 555,667 => 648);

		$baseid = $id;
		if (isset($formorig[$id]))
			$baseid = $formorig[$id];
		$rawdata = $this->getFile('stats', $id);
		if ($baseid <= 649) {
			$child = $this->getFile('baby', $baseid);
			$child = ord($child[0]) + (ord($child[1])<<8);
		} else
			$child = 0;
		foreach ($this->getLevelUpMoveData($id, $level) as $data)
			$moves[] = array_merge(array('Learned' => 'Level '.$data['learned']), $this->getPrintMove($data['move']));
		if ($level == -1) {
			foreach ($this->getEggMoveData($child) as $data)
				$moves[] = array_merge(array('Learned' => $data['learned']), $this->getPrintMove($data['move']));
			
			$str = '';
			for ($i = 40; $i < 53; $i++)
				$str .= strrev(sprintf('%08b', ord($rawdata[$i])));

			for ($i = 0; $i < 101; $i++)
				if (substr($str, $i, 1) == '1')
					$moves[] = array_merge(array('Learned' => $i > 94 ? 'HM'.($i-94) : 'TM'.($i+1)), $this->getPrintMove($TMMap[$i]));
			for ($i = 0; $i < 8; $i++)
				if (ord($rawdata[56]) & pow(2,$i))
					$moves[] = array_merge(array('Learned' => 'Tutor'), $this->getPrintMove($tutormap[$i]));
		}
		return $moves;
	}
	function getPrintMove($id) {
		$data = $this->getMove($id);
		$output = array('id' => $id, 'Name' => $this->getMoveName($id), 'Power' => $data['power'], 'Accuracy' => $data['accuracy'], 'Priority' => $data['priority'], 'Description' => $this->getMoveDesc($id)); 
		if ($GLOBALS['format'] != 'html') {
			$output['Type'] = $data['typeid'];
			$output['Category'] = $data['category'];
		} else {
			$output['Type'] = sprintf('<img src="/images/%s/types/%d.png" />', self::generation, $data['typeid']);
			$output['Category'] = sprintf('<img src="/images/%s/categories/%d.png" />', self::generation, $data['category']);
		}
		return $output;
	}
	function getMove($id) {
		$output = unpack('Ctypeid/Cinternal_category/Ccategory/Cpower/Caccuracy/Cpp/cpriority/Chits/Cstatus/Cunknown/Ceffectchance/Cunknown2/Cunknown3/Cunknown4/Ccritlevel/Cflinchchance/veffect/cdrain_percentage/cheal_percentage/Cunknown5/C3stat/c3statdelta/C3stat_chance/C2always_83/vflags/C2null', $this->getFile('movedata', $id, true));
		$output['id'] = $id;
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
		}
		return $output;
	}
	function getEggMoveData($id) {
		$this->loadNarc('eggmovedata');
		$data = $this->narcs['eggmovedata']->getFile($id);
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