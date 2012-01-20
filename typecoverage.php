<?php
include 'inc/pokemon/types.php';
function checkCoverage($typearray) {
	global $typelist, $type;
	$output = '';
	$coveredtypes['type1'] = $typearray[0];
	$coveredtypes['type2'] = $typearray[1];
	$coveredtypes['type3'] = $typearray[2];
	$coveredtypes['type4'] = $typearray[3];
	foreach ($typearray as $curtype) {
		for ($i = 0; $i < 18; $i++) {
			if ($type[$i][$curtype] == 2) {
				$coveredtypes[$i] = 1;
			}
		}
	}
	return $coveredtypes;
}
function makePrettyCoverage($coveredtypes) {
	global $typelist;
	$x = 0; $output = "";
	for ($i = 0; $i < 18; $i++) {
		if ($coveredtypes[$i] == 1) {
			$x++;
			$output .= $typelist[$i].' ';
		}
	}
	return $output.' ('.$x.'/17)';
}
if ($_GET[type1] !== null) {
		echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><link rel="shortcut icon" href="http://pokemon.elpenguino.net/icon/430.gif" /><link rel="stylesheet" type="text/css" href="/pokemon/css.php" title="Default" /><title>Pokeymans Type Coverage</title></head><body>
EOT;
	for ($i = 0; $i < 17; $i++)
		$typecover[$i] = (($_GET['type'.($i+1)] != NULL) ? $_GET['type'.($i+1)] : -2);
	rsort($typecover, SORT_NUMERIC);
	if ($typecover[3] != -1) {
		echo $typelist[$typecover[0]].', '.$typelist[$typecover[1]].', '.$typelist[$typecover[2]].', and '.$typelist[$typecover[3]].' can cover: ';
		echo makePrettyCoverage(checkCoverage(array($typecover[0],$typecover[1],$typecover[2],$typecover[3])));
	} else {
		$largest = 0;
		for ($i = 0; $i < 18; $i++) {
			$typecoverage[$i] = checkCoverage(array($typecover[0],$typecover[1],$typecover[2],$i));
			$typecoverage[$i]['type'] = $i;
			if (count($typecoverage[$i]) > $largest)
				$largest = count($typecoverage[$i]);
		}
		foreach ($typecoverage as $t) {
			if (count($t) == $largest) {
				echo $typelist[$typecover[0]].', '.$typelist[$typecover[1]].', '.$typelist[$typecover[2]].', and '.$typelist[$t['type']].' can cover: ';
				echo makePrettyCoverage($t).'<br>';
			}
		}
	}
	echo '</body></html>';
} elseif (!array_key_exists('test', $_GET)) {
	echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><link rel="shortcut icon" href="http://pokemon.elpenguino.net/icon/430.gif" /><link rel="stylesheet" type="text/css" href="/pokemon/css.php" title="Default" /><title>Pokeymans Type Coverage</title></head><body>
<form action="typecoverage.php" method="get">
EOT;
	for ($i = 1; $i < 5; $i++) {
		echo "<select name=\"type$i\">";
		echo "<option value=\"-1\">None</option>";
		for ($j = 0; $j < 18; $j++)
			if ($j != curse)
				echo "<option value=\"$j\">".$typelist[$j].'</option>';
		echo '</select>';
	}
	echo '<input type="submit" value="Submit"></form>';
	echo '</body></html>';
} else {
	for ($x = 0; $x < 18*18*18*18; $x++) {
		$i = $x%18;
		$j = ($x/18)%18;
		$k = ($x/18/18)%18;
		$l = ($x/18/18/18)%18;
		if (($i == curse) || ($j == curse) || ($k == curse) || ($l == curse))
			continue;
		$rarray = array($i,$j,$k,$l);
		rsort($rarray, SORT_NUMERIC);
		$c = checkCoverage(array($i,$j,$k,$l));
		if (count($c) > 14)
			$typecoverage[$rarray[0].':'.$rarray[1].':'.$rarray[2].':'.$rarray[3].':'] = $c;
	}
	foreach($typecoverage as $typescovered) {
		echo $typelist[$typescovered['type1']].', '.$typelist[$typescovered['type2']].', '.$typelist[$typescovered['type3']].', and '.$typelist[$typescovered['type4']].' can cover: ';
		echo makePrettyCoverage($typescovered).'<br>';
	}
}
?>