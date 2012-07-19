<?php
class basegame {
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