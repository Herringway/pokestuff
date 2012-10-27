<?php
class basegame {
	protected $gameid;
	protected $lang;
	public function getData($what, $id) {
		$id = intval($id);
		global $cache,$cachehits,$cachemisses;
		if (isset($cache[sprintf('%s (%s)/%s/%d', $this->gameid, $this->lang, $what, $id)])) {
			$cachehits++;
			return $cache[sprintf('%s (%s)/%s/%d', $this->gameid, $this->lang, $what, $id)];
		}
		switch ($what) {
			case 'moves':		$data = $this->getMoveCached($id); break;
			case 'stats':		$data = $this->getStatsCached($id); break;
			case 'trainers':	$data = $this->getTrainerCached($id); break;
			case 'items':		$data = $this->getItemCached($id); break;
			case 'areas':		$data = $this->getAreaCached($id); break;
			case 'abilities':	$data = $this->getAbilityCached($id); break;
			default: throw new Exception('Unknown data requested');
		}
		$cache[sprintf('%s (%s)/%s/%d', $this->gameid, $this->lang, $what, $id)] = $data;
		$cachemisses++;
		return $data;
	}
	protected function getMoveCached($id) {
		if (!method_exists($this, 'getMove'))
			throw new Exception('Unsupported');
		$data = $this->getMove($id);
		$data['id'] = $id;
		$data['name'] = $this->getTextEntry('Move Names', $id);
		$data['description'] = $this->getTextEntry('Move Descriptions', $id);
		return $data;
	}
	protected function getAbilityCached($id) {
		$data['name'] = $this->getTextEntry('Ability Names', $id);
		$data['description'] = $this->getTextEntry('Ability Descriptions', $id);
		return $data;
	}
	protected function getStatsCached($id) {
		if (!method_exists($this, 'getStats'))
			throw new Exception('Unsupported');
		$data = $this->getStats($id);
		if (method_exists($this, 'getBaseID'))
			$baseid = $this->getBaseID($id);
		else
			$baseid = $id;
		$data['id'] = $id;
		$data['name'] = $this->getTextEntry('Pokemon Names', $id);
		$data['pokedex'] = $this->getTextEntry('Pokedex Entries', $baseid);
		$data['species'] = $this->getTextEntry('Species Names', $baseid);
		if (method_exists($this, 'getMoveList'))
			$data['moves'] = $this->getMoveList($id);
		if (method_exists($this, 'getEvolutions'))
			$data['evolutions'] = $this->getEvolutions($id);
		return $data;
	}
	protected function getTrainerCached($id) {
		if (!method_exists($this, 'getTrainerData'))
			throw new Exception('Unsupported');
		$data = $this->getTrainerData($id);
		$data['id'] = $id;
		$data['name'] = $this->getTextEntry('Trainer Names', $id);
		$data['pokemon'] = $this->getTrainerPokemon($id);
		return $data;
	}
	protected function getItemCached($id) {
		if (!method_exists($this, 'getItem'))
			throw new Exception('Unsupported');
		$data = $this->getItem($id);
		$data['id'] = $id;
		$data['name'] = $this->getTextEntry('Item Names', $id);
		$data['description'] = $this->getTextEntry('Item Descriptions', $id);
		return $data;
	}
	protected function getAreaCached($id) {
		if (!method_exists($this, 'getArea'))
			throw new Exception('Unsupported');
		$data = $this->getArea($id);
		$data['id'] = $id;
		return $data;
	}
	protected function getTextEntryCached($name, $id) {
		return $this->getTextEntry($name, $id);
	}
	public function getCount($what) {
		return 0;
	}
	private function _nameToID($tablename, $textfile, $name) {
		$id = false;
		for ($i = 0; $i < $this->getCount($tablename); $i++) {
			$tmpnam = $this->getTextEntryCached($textfile, $i);
			if (strtolower($tmpnam) == strtolower($name)) {
				$id = $i;
				break;
			}
		}
		return $id;
	}
	public function getOptions() {
		return null;
	}
	public function pokemonNameToID($name) {
		return $this->_nameToID('stats', 'Pokemon Names', $name);
	}
	public function moveNameToID($name) {
		return $this->_nameToID('movedata', 'Move Names', $name);
	}
	public function itemNameToID($name) {
		return $this->_nameToID('itemdata', 'Item Names', $name);
	}
	public function findAppropriateMod($name) {
		if ($this->pokemonNameToID($name) !== false)
			return 'stats';
		if ($this->moveNameToID($name) !== false)
			return 'moves';
		if ($this->itemNameToID($name) !== false)
			return 'items';
		return false;
	}
	function __construct($id,$lang) {
		$this->gameid = $id;
		$this->lang = $lang;
	}
}
function kimplode($array, $glue = ': ', $separator = ', ') {
	if (!is_array($array))
		return $array;
	$val = array();
	foreach ($array as $k => $v)
		$val[] = $k.$glue.$v;
	return implode($separator, $val);
}
function clean_array($input) {
	return (($input !== NULL) && ($input !== ''));
}
function rangeStringToRange($input, $min, $max) {	
	if (strpos($input, '..') !== false) {
		$range = explode('..', $input);
		$floor   = max($min, min($max, intval(str_replace('$', $max, $range[0]))));
		$ceiling = max($min, min($max, intval(str_replace('$', $max, $range[1]))));
	} else
		$floor = $ceiling = intval(str_replace('$', $max, $input));
	if ($floor > $ceiling)
		return array($ceiling, $floor);
	return array($floor,$ceiling);
}
function normalizeRange($range, $min, $max) {
	return $range;
}
function modsort($a, $b) {
	return strcmp($a['name'], $b['name']);
}
function readshort_str(&$data, $offset) {
	$b = unpack('v', substr($data,$offset, 4));
	return $b[1];
}
function readint_str(&$data, $offset) {
	$b = unpack('V', substr($data,$offset, 4));
	return $b[1];
}
function debugvar($var, $label) {
	static $limit = 100;
	if ($limit-- > 0)
		ChromePhp::log($label, $var);
}
function debugmessage($message, $level = 'error') {
	global $settings;
	static $limit = 100;
	if ($limit-- > 0) {
		if ($level === 'error')
			ChromePhp::error($message);
		else if ($settings['debug'] && ($level === 'devfatal')) {
			ChromePhp::error($message);
			die($message);
		} else if ($level === 'warn')
			ChromePhp::warn($message);
		else
			ChromePhp::log($message);
	}
}
function deprecated() {
	$d = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
	var_dump($d);
	debugmessage(sprintf('Deprecated function %s called (%s:%d)', (isset($d[1]['class']) ? $d[1]['class'].$d[1]['type'] : '').$d[1]['function'], str_replace(str_replace('libs', '', dirname(__FILE__)), '', $d[0]['file']), $d[0]['line']), 'devfatal');
}
?>