<?php
$bw = 1;
require_once 'stats.php';
require_once 'Dwoo/dwooAutoload.php';
function boosts($i = 0) {
     if ($i <= -1) return 2/(2-max($i,-6));
else if ($i >= 1)  return (2+min($i,6))/2;
else               return 1;
}
$result = '';
if (array_key_exists('results', $_GET)) {
	$moveinfo = processMoveData($_GET['move']);
	
	if (array_key_exists('overridebp', $_GET) && ($_GET['overridebp'] != NULL))
		$moveinfo['power'] = $_GET['overridebp'];
	$types = 1;
	$user = getPokemonData($_GET['user']);
	$foe = getPokemonData($_GET['foe']);
	
	$defmin = 2 * ($moveinfo['category'] == 'Physical' ? $foe['def'] : $foe['sdef']) + 5;
	$defmax = $defmin+94;
	
	$atkmin = 2 * ($moveinfo['category'] == 'Physical' ? $user['atk'] : $user['satk']) + 5;
	$atkmax = $atkmin+94;
	
	$attackboosts = boosts(array_key_exists('atkboosts', $_GET) ? $_GET['atkboosts'] : 0);
	$defenseboosts = boosts(array_key_exists('defboosts', $_GET) ? $_GET['defboosts'] : 0);
	
	if ($_GET['userability'] == 109)
		$defenseboosts = 1;
	if ($_GET['foeability'] == 109)
		$attackboosts = 1;
	
	$atkmin *=  $_GET['usernature'] * $attackboosts;
	$atkmax *=  $_GET['usernature'] * $attackboosts;
	
	$defmin *=  $_GET['foenature'] * $defenseboosts;
	$defmax *=  $_GET['foenature'] * $defenseboosts;

	if (($_GET['userability'] == 122) && ($_GET['weather'] == 1) && ($moveinfo['category'] == 'Physical'))
		$atkmult *= 1.5;
	if (($_GET['foeability'] == 122) && ($_GET['weather'] == 1) && ($moveinfo['category'] == 'Special'))
		$defmult *= 1.5;
	if (($_GET['userability'] == 55) && ($moveinfo['category'] == 'Physical'))
		$atkmult *= 1.5;
	if ((($_GET['userability'] == 37) || ($_GET['userability'] == 74)) && ($moveinfo['category'] == 'Physical'))
		$atkmult *= 2;
	
	if (($_GET['foeability'] == 78) && ($moveinfo['typeid'] == electric))
		$types = 0;
	else if ($foe['type1'] == $foe['type2'])
		$types = $type[$foe['type1']][$moveinfo['typeid']];
	else
		$types = $type[$foe['type1']][$moveinfo['typeid']]*$type[$foe['type2']][$moveinfo['typeid']];
	$type1 = 1;
	$type2 = 1;
	$STAB = ($moveinfo['typeid'] == $user['type1']) || (($moveinfo['typeid'] == $user['type2'])) ? ($_GET['userability'] == 91) ? 2 : 1.5 : 1;
	$CH = 1;
	$CH2 = array_key_exists('enablecrits', $_GET) ? 2 : 1;
	$mod1 = 1;
	$mod2 = 1;
	$mod3 = 1;
	$bp = $moveinfo['power'];
	$minhits = $moveinfo['hits']['min'];
	$maxhits = $moveinfo['hits']['max'];
	$level = 100;
	$atkmult = 1;
	$bpmult = 1;
	
	if (($_GET['foeability'] == 87) && ($moveinfo['typeid'] == fire))
		$bpmult *= 1.25;
	switch($_GET['useritem']) {
		case 112: if ($_GET['user'] == 487) $bpmult *= 1.2; break;
		case 258: if ($_GET['user'] == 105) $atkmult *= 2; break;
		case 270: $mod2 = 1.3; break;
		case 301: if ($moveinfo['typeid'] == grass) $bpmult *= 1.2; break;
	}
	switch($_GET['userability']) {
		case 89: if ($moveinfo['flags'] & IS_PUNCH)      $bpmult *= 1.2;  break;
		case 92: $minhits = $maxhits; break;
		case 101: if ($moveinfo['power'] <= 60) $bpmult *= 1.5; break;
		/*case  3: if ($is_recoil)     $bpmult = 1.2;  break;
		case  4:                             $bpmult = 1.25; break;
		case  5: if ($_GET['type'] == grass) $bpmult = 1.5;  break;
		case  6: if ($_GET['type'] == fire)  $bpmult = 1.5;  break;
		case  7: if ($_GET['type'] == water) $bpmult = 1.5;  break;
		case  8: if ($_GET['type'] == bug)   $bpmult = 1.5;  break;
		case  9: $atkmult = 2;   break;
		case 10: $atkmult = 1.5; break;
		case 11: $atkmult = 1.5; break;
		case 12: $atkmult = 1.5; break;
		case 13: $atkmult = 0.5; break;
		case 14: $atkmult = 1.5; break;
		case 15: $atkmult = 1.5; break;*/
	}
	$damage['minminivsmin'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmin * $atkmult) / 50) / ($defmin) * $mod1) + 2) * $CH * $mod2 * 0.85) * $STAB * $types * $mod3 * $minhits);
	$damage['minmaxivsmin'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmin * $atkmult) / 50) / ($defmax) * $mod1) + 2) * $CH * $mod2 * 0.85) * $STAB * $types * $mod3 * $minhits);
	$damage['maxminivsmin'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmax * $atkmult) / 50) / ($defmin) * $mod1) + 2) * $CH * $mod2 * 0.85) * $STAB * $types * $mod3 * $minhits);
	$damage['maxmaxivsmin'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmax * $atkmult) / 50) / ($defmax) * $mod1) + 2) * $CH * $mod2 * 0.85) * $STAB * $types * $mod3 * $minhits);
	$damage['minminivsmax'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmin * $atkmult) / 50) / ($defmin) * $mod1) + 2) * $CH2 * $mod2)        * $STAB * $types * $mod3 * $maxhits);
	$damage['minmaxivsmax'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmin * $atkmult) / 50) / ($defmax) * $mod1) + 2) * $CH2 * $mod2)        * $STAB * $types * $mod3 * $maxhits);
	$damage['maxminivsmax'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmax * $atkmult) / 50) / ($defmin) * $mod1) + 2) * $CH2 * $mod2)        * $STAB * $types * $mod3 * $maxhits);
	$damage['maxmaxivsmax'] = floor(((((((($level * 2 / 5) + 2) * $bp * $bpmult * $atkmax * $atkmult) / 50) / ($defmax) * $mod1) + 2) * $CH2 * $mod2)        * $STAB * $types * $mod3 * $maxhits);
	
	$result = array('damage' => $damage, 'user' => $user, 'foe' => $foe, 'move' => $moveinfo['id'], 'typemultiplier' => $types * $STAB);
}
for ($i = 1; $i <= NUM_MOVES; $i++)
	$moves[] = getMove($i);
asort($moves);
for ($i = 1; $i <= NUM_POKEMON; $i++)
	$names[] = getPokeName($i);
asort($names);
for ($i = 1; $i <= NUM_ABILITIES; $i++)
	$abilities[] = getAbility($i);
asort($abilities);
for ($i = 1; $i <= NUM_ITEMS; $i++)
	$items[] = getItem($i);
asort($items);
$data = array('names' => $names, 'moves' => $moves, 'abilities' => $abilities, 'items' => $items,'result' => $result);
$dwoo = new Dwoo();
$dwoo->output('poketemplates/damagecalculator.tpl', $data);
?>