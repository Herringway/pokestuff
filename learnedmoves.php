<?php
$bw = 1;
require_once 'types.php';
require_once 'misc.php';
require_once 'narc.php';

function processLevelUpMoveData($data, $maxlevel = 100) {
	$output = array();
	for ($i = 0; $i < strlen($data)/4; $i++) {
		$d = unpack('vmove/vlearned', substr($data, $i*4, 4));
		if ($d['learned'] > $maxlevel)
			continue;
		$output[] = $d;
	}
	return $output;
}
function processEggMoveData($data) {
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
function getLevelUpMoveData($i) {
	$narcfile = new NARCFile('narcs/weng/0/1/8');
	return $narcfile->getFile($i);
}
function getEggMoveData($i) {
	$narcfile = new NARCFile('narcs/weng/1/2/3');
	return $narcfile->getFile($i);
}
function getLastFourMoves($poke, $level) {
	$output = array();
	$allmoves = processLevelUpMoveData(getLevelUpMoveData($poke),$level);
	if (count($allmoves) > 4)
		$allmoves = array_slice($allmoves, -4, 4);
	foreach ($allmoves as $move)
		if (!in_array($move['move'], $output))
			$output[] = $move['move'];
	return $output;
}
if (array_search(__FILE__,get_included_files()) == 0) {
	require_once 'Dwoo/dwooAutoload.php';
	$dwoo = new Dwoo();
	$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	if (!isset($argc[1])) {
		if (!array_key_exists('eggmoves', $_GET)) {
			$narc = new NARCfile('narcs/weng/0/1/8');
			$i = 0;
			$levelup = array();
			foreach ($narc as $movedata)
				$levelup[] = array('pokemon' => getPokeName($i++), 'moves' => processLevelUpMoveData($movedata));
			$data = array('pokemondata' => $levelup);
			$dwoo->output('poketemplates/pokemonlevelupmoves.tpl', $data);
		} else {
			$narc = new NARCfile('narcs/weng/1/2/3');
			$i = 0;
			$levelup = array();
			foreach ($narc as $eggdata) {
				$alldat = processEggMoveData($eggdata);
				$moves = array();
				foreach ($alldat as $dat)
					$moves[] = array('move' => $dat['movename'], 'learned' => 'EGG');
				$levelup[] = array('pokemon' => getPokeName($i++), 'moves' => $moves);
			}
			$data = array('pokemondata' => $levelup);
			$dwoo->output('poketemplates/pokemonlevelupmoves.tpl', $data);
		}
	} else {
		foreach(getLastFourMoves($argc[1], $argc[2]) as $move)
			echo $move.'<br>';
	}
}

?>