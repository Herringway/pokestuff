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
	static $flags_array = array('MAKES_CONTACT', 'POWER_HERB', 'HYPER_BEAM', 'BRIGHTPOWDER', 'MAGIC_COAT', 'IS_SNATCHABLE', 'UNKNOWN1', 'IS_PUNCH', 'IS_SOUND', 'FAILS_IN_GRAVITY', 'DETHAWS_USER', 'CAN_HIT_NON-ADJACENT', 'HEALS', 'UNKNOWN2', 'UNKNOWN3', 'UNKNOWN4');
	global $typelistBWIMG,$int_flags,$physspec,$status,$effects,$stats;
	$output = array();
	if (!isset($file))
		$tmpfile = new NARCFile('narcs/weng/0/2/1');
	static $file;
	if (empty($file))
		$file = $tmpfile;
	$data = $file->getFile($id);
	$output = unpack('Ctypeid/Cinternal_category/Ccategory/Cpower/Caccuracy/Cpp/cpriority/Chits/Cstatus/Cunknown/Ceffectchance/Cunknown2/Cunknown3/Cunknown4/Ccritlevel/Cflinchchance/veffect/cdrain_percentage/cheal_percentage/Cunknown5/C3stat/c3statdelta/C3stat_chance/C2always_83/vflags/C2null', $data);
	$output['id'] = $id;
	$output['type'] = $typelistBWIMG[$output['typeid']];
	$output['internal_category'] = $int_flags[$output['internal_category']];
	$output['category'] = $physspec[$output['category']];
	$output['hits'] = array('min' => max(1,$output['hits']&0xF),'max' => max(1,($output['hits']>>4)));
	$output['status'] = $status[$output['status']];
	for ($i = 1; $i <= 3; $i++)
		$output['stat'.$i] = $stats[$output['stat'.$i]];
	for ($i = 0; $i < 16; $i++)
		$output['flags_readable'][$flags_array[$i]] = ($output['flags'] & pow(2,$i))>>$i;
	$output['accuracy'] = $output['accuracy'] == 101 ? '-' : $output['accuracy'];
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
	if (!isset($argc[1])) {
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