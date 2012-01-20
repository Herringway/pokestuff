<?php
$narcfile = 'narcs/bweng/0/2/4';
$bw = 1;
require_once 'narc.php';
require_once 'misc.php';

function get_readable_item_flags($val) {
	$flags = array('', '', '', '', '', 'CANNOT_BE_GIVEN', 'IS_REGISTERABLE', '', '', '', '', '', '', 'RESTORES_HP', 'HEALS_STATUS', '');
	$output = array();
	for ($i = 0; $i < 16; $i++)
		if ($val & pow(2,$i))
			$output[] = $flags[$i];
	return $output;
}

function getItemInfo($id) {
	$rawdata = getfile('narcs/bweng/0/2/4', $id);
	for ($i = 0; $i < strlen($rawdata); $i++)
		$data[] = ord($rawdata[$i]);
	$output = array();
	$output['id'] = $id;
	$output['name'] = getItem($id);
	$output['desc'] = getItemDesc($id);
	$output['price'] = ($data[0] + ($data[1]<<8))*10;
	$output['unknown1'] = $data[2];
	$output['hp_pp_restored'] = $data[3];
	$output['unknown2'] = $data[4];
	$output['unknown3'] = $data[5];
	$output['weight'] = $data[6];
	$output['natural_gift_power'] = $data[7];
	$output['unknownflags'] = $data[8] + ($data[9]<<8);
	$output['readable_flags'] = implode(', ',get_readable_item_flags($output['unknownflags']));
	$output['unknown4'] = $data[10];
	$output['unknown5'] = $data[11];
	$output['unknown6'] = $data[12];
	$output['unknown7'] = $data[13];
	$output['unknown8'] = $data[14];
	$output['unknown9'] = $data[15];
	$output['unknown10'] = $data[16] + ($data[17]<<8) + ($data[18]<<16) + ($data[19]<<24) + ($data[20]<<32) + ($data[22]<<40) + ($data[21]<<48);
	$output['unknown11'] = $data[22];
	$output['hpEVdelta'] = uint($data[23]);
	$output['atkEVdelta'] = uint($data[24]);
	$output['defEVdelta'] = uint($data[25]);
	$output['speedEVdelta'] = uint($data[26]);
	$output['spatkEVdelta'] = uint($data[27]);
	$output['spdevEVdelta'] = uint($data[28]);
	$output['unknown12'] = $data[29];
	$output['unknown13'] = $data[30];
	$output['unknown14'] = uint($data[31]);
	$output['unknown15'] = uint($data[32]);
	$output['unknown16'] = uint($data[33]);
	$output['unknown17'] = $data[34];
	$output['unknown18'] = $data[35];
	return $output;
}
if (array_search(__FILE__,get_included_files()) == 0) {
	require_once 'Dwoo/dwooAutoload.php';
	$args = array();
	if (array_key_exists('PATH_INFO', $_SERVER))
		$args = explode('/',$_SERVER['PATH_INFO']);
		
	$dwoo = new Dwoo();
	if (!array_key_exists(0, $args)) {
		for ($i = 0; $i < NUM_ITEMS; $i++)
			$itemlist[] = getItemInfo($i);
		foreach (array_keys($itemlist[0]) as $key)
			if ((substr($key,0,7) == 'unknown') || (substr($key,0,7) == 'name') || (substr($key,0,7) == 'id'))
				$headers[] = array('name' => $key);
		$items = array('headers' => $headers, 'items' => $itemlist);
		$dwoo->output('poketemplates/pokemonitems.tpl', $items);
	} else {
		$item = array('item' => getItemInfo($args[1]));
		$dwoo->output('poketemplates/pokemonitemdetails.tpl', $item);
	}
}
?>