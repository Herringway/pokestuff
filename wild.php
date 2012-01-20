<?php
$bw = 1;
require_once 'narc.php';
require_once 'pkmnnos.php';
require_once 'misc.php';

function read_area($id) {
	$data = getfile('narcs/bweng/1/2/6', $id);
	for ($x = 0; $x < strlen($data)/232; $x++) {
		$wild = array(); $b = 0;
		for ($i = 0; $i < 8; $i++)
			$unknown[] = ord($data[$i+$x*232]);
		for ($i = 2; $i < 58; $i++) {
			if (($i == 14) || ($i == 26) || ($i == 38) || ($i == 43) || ($i == 48) || ($i == 53))
				$b++;
			$raw = ord($data[$i*4+$x*232]) + (ord($data[($i*4)+1+$x*232])<<8);
			$id = $raw&0x7FF;
			$flags = $raw&0xF800;
			$poke = getPokeName($id);
			$min = ord($data[($i*4)+2+$x*232]);
			$max = ord($data[($i*4)+3+$x*232]);
			$wild[$b][] = array('id' => $id, 'flags' => $flags, 'pokemon' => $poke, 'level' => array('min' => $min, 'max' => $max));
		}
		$encounters[] = array('grass' => $wild[0], 'deepgrass' => $wild[1], 'uncommon' => $wild[2], 'fishing' => $wild[5], 'deepfishing' => $wild[6], 'surfing' => $wild[3], 'deepsurfing' => $wild[4]);
	}
	return array('encounters' => $encounters, 'unknown' => $unknown);
}

function interpret_poke($data) {
	$output = array();
	//foreach ($data['encounters'] as $numseason => $seasondata)
	//	foreach ($seasondata as $areatitle => $areadata)
	//		foreach ($areadata as $poke)
	//			$output[$areadata['id']] = array('id' => $poke['id'], 'name' => $poke['name'], 'locations' => array('location' => $areatitle, 'season' => $numseason, 'percent' => 'more than 0'));
	
	return $output;
}

function searchForPoke($id) {
	global $places;
	$output = '';
	for ($i = 0; $i < 1; $i++)
		if (search_array($id, read_area($i)))
			$output .= $places[$i].' ';
	return $output;
}

function search_array($needle, $haystack) {
	if (in_array($needle, $haystack))
		return 1;
	foreach ($haystack as $haydimension)
		if (is_array($haydimension))
			return search_array($needle, $haydimension);
	return 0;
}
function read_encounter($i, $data) {
	global $pkno;
	$poke = getPokeName(ord($data[$i*4]) + (ord($data[($i*4)+1])<<8));
	$level = sprintf('(L%u - L%u)', ord($data[($i*4)+2]), ord($data[($i*4)+2]));
	return $poke.$level;
}
if (array_search(__FILE__,get_included_files()) == 0) {
	require_once 'Dwoo/dwooAutoload.php';
	$dwoo = new Dwoo();
	$args = array();
	if (array_key_exists('PATH_INFO', $_SERVER))
		$args = explode('/',$_SERVER['PATH_INFO']);
	if (!array_key_exists(1, $args)) {
		for ($i = 0; $i < NUM_AREAS; $i++)
			$encounters[] = array('enc' => read_area($i), 'areaid' => $i, 'area' => array_key_exists($i, $places) ? $places[$i] : 'AREA #'.$i);
		$dwoo->output('poketemplates/pokemonencounters_all.tpl', array('encounters' => $encounters));
	} else if ($args[1] == 'search') {
		//echo searchForPoke($args[2]);
		var_export($places);
	} else {
		$areaid = $args[1];
		$areadat = read_area($areaid);
		$dwoo->output('poketemplates/pokemonencounters.tpl', array('area' => array_key_exists($areaid, $places) ? $places[$areaid] : 'AREA #'.$areaid, 'encounters' => $areadat, 'enc_rates' => interpret_poke($areadat)));
	}
}
?>