<?php
$included = 1;
$bw = 1;
$legendaries = array(150, 151, 202, 249, 250, 251, 360, 380, 381, 382, 383, 384, 385, 386, 445, 483, 484, 487, 490, 491, 493, 643, 644, 646, 647, 648, 649, 650, 651, 652, 655, 656, 667);
require_once '../dwoo/dwooAutoload.php';
require_once 'narc.php';
require_once 'evolutions.php';
for ($i = (array_key_exists('bwonly', $_GET) ? 493 : 1); $i <= (array_key_exists('bwonly', $_GET) ? 649 : 667); $i++)
	if (!in_array($i, $legendaries))
		if (checkifevolved($i) || array_key_exists('nonevolved', $_GET))
			$evolved[] = $i;

for ($i = 0; $i < 6; $i++)
	$rand[] = $evolved[rand(0, count($evolved)-1)];
$dwoo = new Dwoo();
foreach ($rand as $poke)
	$pokemon[] = array('id' => $poke,'name' => getPokeName($poke));
$dwoo->output('poketemplates/randomteam.tpl', array('randompoke' => $pokemon)); 
?>