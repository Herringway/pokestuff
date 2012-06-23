<?php
$bw = 1;
require_once 'narc.php';
require_once 'types.php';
require_once 'misc.php';
require_once 'evolutions.php';
require_once 'learnedmoves.php';
require_once 'moves.php';
require_once 'text.php';
require_once 'textgen4.php';
require_once 'Dwoo/dwooAutoload.php';

$dwoo = new Dwoo();

function getPokemonData_raw($id) {
	global $typelistBWIMG, $egggrp;
	$formorig = array(650 => 386, 651 => 386, 652 => 386, 653 => 413, 654 => 413, 655 => 492, 656 => 487,657 => 479,658 => 479,659 => 479,660 => 479,661 => 479,662 => 351,663 => 351,664 => 351,665 => 550,666 => 555,667 => 648);
	$narc = new NARCFile('narcs/b2jpn/0/1/6');
	$childnarc = new NARCFile('narcs/b2jpn/0/2/0');
	$rawdata = $narc->getFile($id);
	$poke = unpack('Chp/Catk/Cdef/Cspeed/Csatk/Csdef/C2type/Ccapturerate/Cxprate/vEVs/vcomitemID/vrareitemID/vdreamitemID/Cfemalechance/Chatchsteps/Cbasehappiness/Cgrowthrate/C2egggrp/C3ability/Cunknownflags/Cformflags/Cformcount/Ccolour/vunknown/vheight/vweight', $rawdata);
	$poke['id'] = $id;
	$rid = $id;
	if (array_key_exists($id, $formorig))
		$id = $formorig[$id];
	if ($id <= 649) {
		$child = $childnarc->getFile($id);
		$child = ord($child[0]) + (ord($child[1])<<8);
	} else
		$child = 0;
	$poke['species'] = getSpeciesName($id);
	$poke['evolutions'] = getEvolution($id);
	$poke['name'] = getPokeName($id);
	$poke['EVs'] = EVs($poke['EVs']);
	$poke['femalechance'] = $poke['femalechance'] == 255 ? -1 : round(100*($poke['femalechance']/254),1);
	$poke['egggrp1'] = $egggrp[$poke['egggrp1']];
	$poke['egggrp2'] = $egggrp[$poke['egggrp2']];
	$poke['height'] /= 10;
	$poke['weight'] /= 10;
	$poke['moves_g5'] = array_merge(processLevelUpMoveData(getLevelUpMoveData($rid)), processTMData($rawdata), tutorMoves($rawdata), processEggMoveData(getEggMoveData($child)));
	$poke['childID'] = $child;
	$poke['child'] = getPokeName($child);
	$bwpokedex = getBWPokedexEntries($id);
	$poke['pokedexENG'] = $bwpokedex['eng'];
	$poke['pokedexJPN'] = $bwpokedex['jpn'];
	return $poke;
}
function getBWPokedexEntries($i) {
	$epoke[] = array('entry' => getPokedexEntryW($i), 'game' => 'White');
	$epoke[] = array('entry' => getPokedexEntryB($i), 'game' => 'Black');
	$jpoke[] = array('entry' => getPokedexEntryW($i, 'blackjapan'), 'game' => 'White');
	$jpoke[] = array('entry' => getPokedexEntryB($i, 'blackjapan'), 'game' => 'Black');
	return array('eng' => $epoke, 'jpn' => $jpoke);

}
function getHGSSPokedexEntries($i) {
	global $hgsstextfile;
	$text = new gen4text($hgsstextfile);
	$output[] = array('entry' => $text->fetchline(803, $i), 'game' => 'Soul Silver');
	$output[] = array('entry' => $text->fetchline(804, $i), 'game' => 'Heart Gold');
	return $output;
}
function getDPPtPokedexEntries($i) {
	global $plattextfile;
	$text = new gen4text($plattextfile);
	$output[] = array('entry' => $text->fetchline(698, $i), 'game' => 'Diamond');
	$output[] = array('entry' => $text->fetchline(699, $i), 'game' => 'Pearl');
	$output[] = array('entry' => $text->fetchline(706, $i), 'game' => 'Platinum');
	return $output;
}
function getPokemonData($id) {
	if (!ctype_digit($id) && !is_int($id))
		return -1;
	$db = new SQLite3('./pkmn.db');
	$results = @$db->query("SELECT * from POKEMON where ID=$id") or die('Database locked, is probably being updated');
	$output = $results->fetchArray(SQLITE3_ASSOC);
	foreach ($output as $k => $v) {
		if (@is_array(unserialize($v)))
			$output[$k] = unserialize($v);
	}
	return $output;
}

function savePokemonData($poke) {
	foreach ($poke as $key => $val)
		if (is_array($val))
			$poke[$key] = serialize($val);
	return $poke;
}

function processTMData($data) {
	global $moves, $TMMap;
	$str = ''; $output = array();
	for ($i = 40; $i < 53; $i++)
		$str .= strrev(sprintf('%08b', ord($data[$i])));

	for ($i = 0; $i < 101; $i++) {
		if (substr($str, $i, 1) == '1') {
			$moveRAW = $TMMap[$i];
			$output[] = array('learned' => $i > 94 ? 'HM'.($i-94) : 'TM'.($i+1), 'move' =>  $moveRAW);
		}
	}
	return $output;
}

function tutorMoves($data) {
	global $moves,$tutormoves;
	$output = array();
	for ($i = 0; $i < 8; $i++) {
		if (ord($data[56]) & pow(2,$i)) {
			$moveRAW = $tutormoves[$i];
			$output[] = array('learned' => 'Tutor', 'move' => $moveRAW);
		}
	}
	return $output;
}
function quotes($array) {
	foreach ($array as $k => $v)
		if (is_string($v))
			$array[$k] = '\''.SQLite3::escapeString($v).'\'';
	return implode(', ', $array);
}
function dbinsert($statement, $data) {
	$i = 1;
	foreach ($data as $val)
		$statement->bindValue($i++, $val);
	$statement->execute();
}

if (array_search(__FILE__,get_included_files()) == 0) {
	$output = '';
	$args = array();
	if (array_key_exists('PATH_INFO', $_SERVER))
		$args = explode('/',$_SERVER['PATH_INFO']);
	if (!array_key_exists(1, $args) || ($args[1] == null) || ($args[1] < 0) || ($args[1] > NUM_POKEMON)) {
		for ($i = 1; $i <= NUM_POKEMON; $i++)
			$pokemon[] = getPokemonData_raw($i);
		$stats = array('debug' => false, 'pokemon' => $pokemon);
		
		$dwoo->output('poketemplates/pokemonstats.tpl', $stats);
	} else if (($args[1] == 'dump') && ($args[2] == 'yes')) {
		set_time_limit(500);
		$formoffset = 651;
		for ($i = 1; $i <= NUM_POKEMON; $i++) {
			$rawpdata = getPokemonData_raw($i);
			printf('Dumping Pokemon %u (%s)<br>', $rawpdata['id'], $rawpdata['name']);
			$rawpdata['formnameoffset'] = 0;
			if (($rawpdata['formcount'] > 1) && ($i <= 649)) {
				$rawpdata['formnameoffset'] = $formoffset;
				$formoffset += $rawpdata['formcount']-1;
			}
			$pokedata[] = savePokemonData($rawpdata);
		}
		$columns = array();
		foreach ($pokedata[0] as $c => $t)
			$columns[] .= $c.' '.(is_int($t) ? 'INTEGER' : (is_float($t) ? 'FLOAT' : (is_array($t) ? 'BLOB' : 'TEXT')));
		unlink('./pkmn.db');
		$db = new PDO('sqlite:./pkmn.db');
		$db->exec('CREATE TABLE pokemon ('.implode(', ', $columns).')');
		$statement = $db->prepare('INSERT INTO pokemon VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$db->beginTransaction();
		foreach ($pokedata as $pokemon)
			dbinsert($statement, $pokemon);
		$db->commit();
	} else if ($args[1] == 'yaml') {
		set_time_limit(500);
		for ($i = 1; $i <= NUM_POKEMON; $i++)
			$pokemon[] = getPokemonData_raw($i);
		header("Content-Type: text/plain");
		echo yaml_emit($pokemon);
	}	else if ($args[1] == 'xml') {
	
	} else	{
		$pokedata = getPokemonData_raw(intval($args[1]));
		foreach ($pokedata['moves_g5'] as &$data)
			$data['data'] = processMoveData($data['move']);
		$stats = array('pokemon' => $pokedata);
		//@$formname = array_merge(getFormNames($stats['pokemon']['id'], 1), getFormNames($stats['pokemon']['formnameoffset'], $stats['pokemon']['formcount']));
		//for ($i = 0; $i < $stats['pokemon']['formcount']; $i++)
		//	$stats['pokemon']['formnames'][] = array('name' => $formname[$i]);
		$dwoo->output('poketemplates/pokemonstatsdetailed.tpl', $stats);

	}
}
?>
