<?php
$included = 1;
$bw = 1;
require_once 'narc.php';
require_once 'misc.php';
require_once 'learnedmoves.php';
require_once 'Dwoo/dwooAutoload.php';

header('Content-Type: text/html; charset=utf-8'); 
$dwoo = new Dwoo();
$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));

function translate_trpoke($trdata, $trpoke) {
	$output = array();
	$datatype = ord($trdata[0]);
	$numpokes = ord($trdata[3]);
	$movelist = $datatype & 1;
	$helditem = $datatype & 2;
	$length = 8 + ($datatype & 1)*8 + ($datatype&2);
	for ($i = 0; $i < $numpokes; $i++) {
		$id = ord($trpoke[$i*$length+4]) + (ord($trpoke[$i*$length+5])<<8);
		$name = getPokeName($id);
		$level = ord($trpoke[$i*$length+2]);
		$item = '';
		$moves = array();
		if ($helditem)
			$item = getItem(ord($trpoke[$i*$length+8])+(ord($trpoke[$i*$length+9])<<8));
		if ($movelist)
			for ($j = 0; $j < 4; $j++)
				$moves[] = getMove(ord($trpoke[$i*$length+$length-8+$j*2])+(ord($trpoke[$i*$length+$length-7+$j*2])<<8));
		else
			$moves = getLastFourMoves($id, $level);
		$output[] = array('name' => $name, 'level' => $level, 'id' => $id, 'move' => $moves, 'item' => $item);
	}
	return $output;
}
$output = '';
if ($argc[1] == null) {
	$trdatanarc = new NARCFile('narcs/bweng/0/9/2');
	$trpokenarc = new NARCFile('narcs/bweng/0/9/3');
	foreach ($trdatanarc as $i => $trdata)
		$trainers[] = array('class' => getTrainerClass(ord($trdata[1])), 'id' => $i, 'name' => getTrainerName($i), 'battletype' => getBattleType(ord($trdata[2])), 'pokemon' => translate_trpoke($trdata, $trpokenarc->getfile($i)));
	$dwoo->output('poketemplates/pokemontrainerlist.tpl', array('trainer' => $trainers));
} else {
	$trdata = getfile('narcs/bweng/0/9/2', $argc[1]);
	$pokemon = translate_trpoke($trdata, getfile('narcs/bweng/0/9/3', $argc[1]));
	for ($i = 0; $i < 4; $i++)
		$items[]['name'] = getItem(ord($trdata[4+$i*2])+(ord($trdata[5+$i*2])<<8));
	$trainer = array('items' => $items, 'pokemon' => $pokemon, 'name' => getTrainerName($argc[1]), 'class' => getTrainerClass(ord($trdata[1])));
	$dwoo->output('poketemplates/pokemontrainerdetails.tpl', $trainer);
	
}
?>