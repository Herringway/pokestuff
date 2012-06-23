<?php
$legendaries = array(150, 151, 202, 249, 250, 251, 360, 380, 381, 382, 383, 384, 385, 386, 445, 483, 484, 487, 490, 491, 493, 643, 644, 646, 647, 648, 649, 650, 651, 652, 655, 656, 667);
require_once 'Dwoo/dwooAutoload.php';
require_once 'narc.php';
require_once 'evolutions.php';
error_reporting(E_ERROR);
$pokemon = array();
for ($i = 0; $i < 6; $i++) {
	while (true) {
		$rand = rand(isset($_GET['bwonly']) ? 494 : 1, 649);
		if ((checkifevolved($rand) || isset($_GET['nonevolved'])) && !in_array($rand, $legendaries) && !in_array($rand, $pokemon))
			break;
	}
	$pokemon[] = $rand;
}
$dwoo = new Dwoo();
$dwoo->output('poketemplates/randomteam.tpl', array('randompoke' => $pokemon)); 
?>