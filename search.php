<?php
require_once 'misc.php';

function getClosestMatches($needle, $haystack, $minimum = 10) {
	$closest = $minimum;
	$return = array();
	foreach ($haystack as $val) {
		$v = COMPARE_ALGO($needle, $val['name']);
		if ($v < $closest) {
			$closest = $v;
			$return = array($val);
		} else if ($v == $closest)
			$return[] = $val;
	}
	return $return;
}

function COMPARE_ALGO($val1, $val2) {
	return levenshtein_compare($val1, $val2);
}

function levenshtein_compare($val1, $val2) {
	return levenshtein($val1, $val2);
}

function scrabble_compare($val1, $val2) {
	$scrabbleletters = array(
	'a' => 1, 'b' => 3, 'c' => 3, 'd' => 2, 'e' => 1,
	'f' => 4, 'g' => 2, 'h' => 4, 'i' => 1, 'j' => 8,
	'k' => 5, 'l' => 1, 'm' => 3, 'n' => 1, 'o' => 1,
	'p' => 3, 'q' => 10, 'r' => 1, 's' => 1, 't' => 1,
	'u' => 1, 'v' => 4, 'w' => 4, 'x' => 8, 'y' => 4,
	'z' => 10);
	$val1 = strtolower($val1);
	$val2 = strtolower($val2);
	$v1score = 0;
	for ($i = 0; $i < strlen($val1); $i++)
		$v1score += array_key_exists($val1[$i], $scrabbleletters) ? $scrabbleletters[$val1[$i]] : 0;
	$v2score = 0;
	for ($i = 0; $i < strlen($val2); $i++)
		$v2score += array_key_exists($val2[$i], $scrabbleletters) ? $scrabbleletters[$val2[$i]] : 0;
	return abs($v1score-$v2score);
}

function search_things($query) {
	for ($i = 1; $i <= NUM_POKEMON; $i++)
		$names[] = array('name' => strtoupper(getPokeName($i)), 'id' => $i, 'script' => 'stats');
	for ($i = 1; $i <= NUM_ITEMS; $i++)
		$items[] = array('name' => strtoupper(getItem($i)), 'id' => $i, 'script' => 'items');
	for ($i = 1; $i <= NUM_MOVES; $i++)
		$moves[] = array('name' => strtoupper(getMove($i)), 'id' => $i, 'script' => 'moves');
	for ($i = 0; $i <= NUM_TRAINERS; $i++)
		$trainers[] = array('name' => strtoupper(getTrainerName($i)), 'id' => $i, 'script' => 'trainers');
	for ($i = 0; $i <= NUM_ABILITIES; $i++)
		$abilities[] = array('name' => strtoupper(getAbility($i)), 'id' => $i, 'script' => 'PLACEHOLDER');
		
	$results = getClosestMatches(strtoupper($query), array_merge($names, $items, $moves, $trainers, $abilities), 4);
	return $results;

}
if (array_search(__FILE__,get_included_files()) == 0) {
		echo '<html><head><title>Search Test</title></head><body><form action="http://elpenguino.net/pkmn/search" method="GET"><input type="text" name="search"><input type="submit" value="Search"></form></body></html>';
	if (array_key_exists('search', $_GET)) {
		$results = search_things($_GET['search']);
		if (count($results) == 0)
			echo 'Zero results';
		else if (count($results) > 1) {
			echo count($results).' results found <br>';
			foreach ($results as $p)
				echo '<a href="/'.$p['script'].'/'.$p['id'].'">'.$p['name'].'</a><br>';
		} else
			header('Location: http://pkmn.elpenguino.net/'.$results[0]['script'].'/'.$results[0]['id']);
	}
}
?>