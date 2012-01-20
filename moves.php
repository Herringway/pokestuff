<?php
$bw = 1;
define('HEALS',					0x1000);
define('CAN_HIT_NON-ADJACENT', 	0x0800);
define('DETHAWS_USER',			0x0400);
define('FAILS_IN_GRAVITY',		0x0200);
define('IS_SOUND', 				0x0100);
define('IS_PUNCH', 				0x0080);

define('IS_SNATCHABLE',			0x0020);
define('MAGIC_COAT',			0x0010);
define('BRIGHTPOWDER',			0x0008);
define('HYPER_BEAM',			0x0004);
define('POWER_HERB',			0x0002);
define('MAKES_CONTACT',			0x0001);

function print_effect($eff_string, $data) {
	$needle = array();
	$replacement = array();
	foreach($data as $key => $value) {
		if (is_array($value)) {
			foreach($value as $key2 => $value2) {
				$needle[] = '['.strtoupper($key).'_'.strtoupper($key2).']';
				$replacement[] = $value2;
			}
		} else {
			$needle[] = '['.strtoupper($key).']';
			$replacement[] = $value;
		}
	}
	return str_replace($needle, $replacement, $eff_string);
}

function processMoveData($id) {
	$flags_array = array('MAKES_CONTACT', 'POWER_HERB', 'HYPER_BEAM', 'BRIGHTPOWDER', 'MAGIC_COAT', 'IS_SNATCHABLE', 'UNKNOWN1', 'IS_PUNCH', 'IS_SOUND', 'FAILS_IN_GRAVITY', 'DETHAWS_USER', 'CAN_HIT_NON-ADJACENT', 'HEALS', 'UNKNOWN2', 'UNKNOWN3', 'UNKNOWN4');
	global $typelistBWIMG,$int_flags,$physspec,$status,$effects,$stats;
	$output = array();
	$data = getfile('narcs/bweng/0/2/1', $id);
	for ($i = 0; $i < strlen($data); $i++)
		$movedata[$i] = ord($data[$i]);
	$output['id'] = $id;
	$output['type'] = $typelistBWIMG[$movedata[0]];
	$output['typeid'] = $movedata[0];
	$output['internal_category'] = $int_flags[$movedata[1]];
	$output['category'] = $physspec[$movedata[2]];
	$output['power'] = $movedata[3];
	$output['accuracy'] = $movedata[4] == 101 ? '-' : $movedata[4];
	$output['pp'] = $movedata[5];
	$output['priority'] = uint($movedata[6]);
	$output['hits'] = array('min' => max(1,$movedata[7]&0xF),'max' => max(1,($movedata[7]>>4)));
	$output['status'] = $status[$movedata[8]];
	$output['unknown'] = $movedata[9];
	$output['effectchance'] = $movedata[10];
	$output['unknown2'] = $movedata[11];
	$output['unknown3'] = $movedata[12];
	$output['unknown4'] = $movedata[13];
	$output['critlevel'] = $movedata[14];
	$output['flinchchance'] = $movedata[15];
	$output['effect'] = $movedata[16] + ($movedata[17]<<8);
	$output['drain_percentage'] = uint($movedata[18]);
	$output['heal_percentage'] = uint($movedata[19]);
	$output['unknown5'] = $movedata[20];
	$output['stat1'] = array_key_exists($movedata[21], $stats) ? $stats[$movedata[21]] : $movedata[21];
	$output['stat2'] = array_key_exists($movedata[22], $stats) ? $stats[$movedata[22]] : $movedata[22];
	$output['stat3'] = array_key_exists($movedata[23], $stats) ? $stats[$movedata[23]] : $movedata[23];
	$output['stat1delta'] = uint($movedata[24]);
	$output['stat2delta'] = uint($movedata[25]);
	$output['stat3delta'] = uint($movedata[26]);
	$output['stat1_chance'] = $movedata[27];
	$output['stat2_chance'] = $movedata[28];
	$output['stat3_chance'] = $movedata[29];
	$output['always_83'] = $movedata[30];
	$output['always_83_2'] = $movedata[31];
	$output['flags'] = $movedata[32] + ($movedata[33]<<8);
	for ($i = 0; $i < 16; $i++)
		$output['flags_readable'][$flags_array[$i]] = ($output['flags'] & pow(2,$i))>>$i;
	$output['null1'] = $movedata[34];
	$output['null2'] = $movedata[35];
	$output['effect_string'] = array_key_exists($output['effect'], $effects) ? print_effect($effects[$output['effect']], $output) : 'Unknown Effect '.($output['effect']);
	
	return $output;
}


if (array_search(__FILE__,get_included_files()) == 0) {
	require 'narc.php';
	require 'pkmnnos.php';
	require 'types.php';
	require 'misc.php';
	require_once 'Dwoo/dwooAutoload.php';
	$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	$dwoo = new Dwoo();
	if ($argc[1] == null) {
		$headers = array(
		array('name' => 'ID', 'type' => 'i'),
		array('name' => 'Name',  'type' => 'h'),
		array('name' => 'Type',  'type' => 'h'),
		array('name' => 'Internal_Category',  'type' => 'h'),
		array('name' => 'Category',  'type' => 'h'),
		array('name' => 'Power',  'type' => 'i'),
		array('name' => 'Accuracy',  'type' => 'i'),
		array('name' => 'PP',  'type' => 'i'),
		array('name' => 'Priority',  'type' => 'i'),
		array('name' => '???',  'type' => 'i'),
		array('name' => '???',  'type' => 'i'),
		array('name' => '???',  'type' => 'i'),
		array('name' => '???', 'type' => 'i'),
		array('name' => 'Effect',  'type' => 'h'),
		array('name' => '???',  'type' => 'i'),
		array('name' => 'Flags',  'type' => 'i'));
		for ($i = 1; $i <= NUM_MOVES; $i++)
			$movedata[] = processMoveData($i);
		$dwoo->output('poketemplates/pokemonmoves.tpl', array('headers' => $headers, 'moves' => $movedata));
	} else {
		$move = processMoveData($argc[1]);
		$dwoo->output('poketemplates/pokemonmovedetails.tpl', array('move' => $move));
	}
}
?>