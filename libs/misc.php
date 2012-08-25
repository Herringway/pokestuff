<?php
class basegame {
	protected $gameid;
	public function getData($what, $id) {
		switch ($what) {
			case 'moves':		return $this->getMoveCached($id);
			case 'stats':		return $this->getStatsCached($id);
			case 'trainers':	return $this->getTrainerCached($id);
			case 'items':		return $this->getItemCached($id);
			case 'areas':		return $this->getAreaCached($id);
			case 'abilities':	return $this->getAbilityCached($id);
			default: throw new Exception('Unknown data requested');
		}
	}
	public function getMoveCached($id) {
		if (!method_exists($this, 'getMove'))
			throw new Exception('Unsupported');
		global $cache;
		if (isset($cache[sprintf('%s/moves/%d', get_class($this), $id)]))
			return $cache[sprintf('%s/moves/%d', get_class($this), $id)];
		else {
			$data = $this->getMove($id);
			$data['id'] = $id;
			$data['name'] = $this->getTextEntry('Move Names', $id);
			$data['description'] = $this->getTextEntry('Move Descriptions', $id);
			$cache[sprintf('%s/moves/%d', get_class($this), $id)] = $data;
			return $data;
		}
	}
	public function getAbilityCached($id) {
		global $cache;
		if (isset($cache[sprintf('%s/abilities/%d', get_class($this), $id)]))
			return $cache[sprintf('%s/abilities/%d', get_class($this), $id)];
		else {
			$data['name'] = $this->getTextEntry('Ability Names', $id);
			$data['description'] = $this->getTextEntry('Ability Descriptions', $id);
			$cache[sprintf('%s/abilities/%d', get_class($this), $id)] = $data;
			return $data;
		}
	}
	public function getStatsCached($id) {
		if (!method_exists($this, 'getStats'))
			throw new Exception('Unsupported');
		global $cache;
		if (isset($cache[sprintf('%s/stats/%d', get_class($this), $id)]))
			return $cache[sprintf('%s/stats/%d', get_class($this), $id)];
		else {
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
			$cache[sprintf('%s/stats/%d', get_class($this), $id)] = $data;
			return $data;
		}
	}
	public function getTrainerCached($id) {
		if (!method_exists($this, 'getTrainerData'))
			throw new Exception('Unsupported');
		global $cache;
		if (isset($cache[sprintf('%s/trainers/%d', get_class($this), $id)]))
			return $cache[sprintf('%s/trainers/%d', get_class($this), $id)];
		else {
			$data = $this->getTrainerData($id);
			$data['id'] = $id;
			$data['name'] = $this->getTextEntry('Trainer Names', $id);
			$data['pokemon'] = $this->getTrainerPokemon($id);
			$cache[sprintf('%s/trainers/%d', get_class($this), $id)] = $data;
			return $data;
		}
	}
	public function getItemCached($id) {
		if (!method_exists($this, 'getItem'))
			throw new Exception('Unsupported');
		global $cache;
		if (isset($cache[sprintf('%s/trainers/%d', get_class($this), $id)]))
			return $cache[sprintf('%s/trainers/%d', get_class($this), $id)];
		else {
			$data = $this->getItem($id);
			$data['id'] = $id;
			$data['name'] = $this->getTextEntry('Item Names', $id);
			$data['description'] = $this->getTextEntry('Item Descriptions', $id);
			$cache[sprintf('%s/trainers/%d', get_class($this), $id)] = $data;
			return $data;
		}
	}
	public function getAreaCached($id) {
		if (!method_exists($this, 'getArea'))
			throw new Exception('Unsupported');
		global $cache;
		if (isset($cache[sprintf('%s/areas/%d', get_class($this), $id)]))
			return $cache[sprintf('%s/areas/%d', get_class($this), $id)];
		else {
			$data = $this->getArea($id);
			$data['id'] = $id;
			$cache[sprintf('%s/areas/%d', get_class($this), $id)] = $data;
			return $data;
		}
	}
	public function getTextEntryCached($name, $id) {
		global $cache;
		if (isset($cache[sprintf('%s/text/%s/%d', get_class($this), $name, $id)]))
			return $cache[sprintf('%s/text/%s/%d', get_class($this), $name, $id)];
		else {
			$data = $this->getTextEntry($name, $id);
			$cache[sprintf('%s/text/%s/%d', get_class($this), $name, $id)] = $data;
			return $data;
		}
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
	function __construct($id) {
		$this->gameid = $id;
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
?>