<?php
$evostart = (float) array_sum(explode(' ',microtime()));
$bw = 1;
require_once('misc.php');
function checkifevolved($num) {
	$narcfile = new NARCFile('narcs/weng/0/1/9');
	$data = $narcfile->getFile($num);
	if (ord($data[0]) == 0)
		return 1;
	return 0;
}
function getEvolution($num) {
	$narcfile = new NARCFile('narcs/weng/0/1/9');
	$data = $narcfile->getFile($num);
	$output = array();
	for($i = 0; $i < 7; $i++) {
		$t = ord($data[$i*6]);
		if ($t != 0) {
			$output[$i]['type'] = $t;
			$output[$i]['unknown'] = ord($data[$i*6+1]);
			$output[$i]['argument'] = ord($data[$i*6+2]) + (ord($data[$i*6+3])<<8);
			$output[$i]['target'] = ord($data[$i*6+4]) + (ord($data[$i*6+5])<<8);
		}
	}
	return $output;
}
function printevolution($num) {
	$data = getEvolution($num);
	$output = array();
	foreach ($data as $k => $v)
		if (($k == 0) || ($v['type'] != 0))
			$output[] = printevo($v);
	return implode(', ', $output);
}
function printevo($data) {
	global $pkno,$evolutiontypes, $evolutiontrans;
	$output = sprintf($evolutiontypes[$data['type']], array_key_exists($data['type'], $evolutiontrans) ? call_user_func($evolutiontrans[$data['type']],$data['argument']) : $data['argument'], getPokeName($data['target']));
	return $output;
}
function getitemname($i) {
	$file = fopen('itemname.php', 'r');
	$i++;
	while ($i--) {
		$data = fgets($file);
	}
	return $data;
}
if (array_search(__FILE__,get_included_files()) == 0) {
	$included = 1;
	require_once('narc.php');
	for ($i = 0; $i < 668; $i++)
		echo getPokeName($i)." ".printevolution($i).'<br>';
}

$evoend = (float) array_sum(explode(' ',microtime())); 