<?php
Define('normal', 0);
Define('fighting', 1);
Define('flying', 2);
Define('poison', 3);
Define('ground', 4);
Define('rock', 5);
Define('bug', 6);
Define('ghost', 7);
Define('steel', 8);
Define('fire', 9);
Define('water', 10);
Define('grass', 11);
Define('electric', 12);
Define('psychic', 13);
Define('ice', 14);
Define('dragon', 15);
Define('dark', 16);
Define('I', 0);
Define('R', .5);
Define('N', 1);
Define('W', 2);
$type = array(
//					 N  Fi Fl Po Gn Ro Bu Gh St Fr Wa Gr El Ps Ic Dg Dk
normal		=> array(N, W, N, N, N, N, N, I, N, N, N, N, N, N, N, N, N),
fighting	=> array(N, N, W, N, N, R, R, N, N, N, N, N, N, W, N, N, R),
flying		=> array(N, R, N, N, I, W, R, N, N, N, N, R, W, N, W, N, N),
poison		=> array(N, R, N, R, W, N, R, N, N, N, N, R, N, W, N, N, N),
ground		=> array(N, N, N, R, N, R, N, N, N, N, W, W, I, N, W, N, N),
rock		=> array(R, W, R, R, W, N, N, N, W, R, W, W, N, N, N, N, N),
bug			=> array(N, R, W, N, R, W, N, N, N, W, N, R, N, N, N, N, N),
ghost		=> array(I, I, N, R, N, N, R, W, N, N, N, N, N, N, N, N, W),
steel		=> array(R, W, R, I, W, R, R, R, R, W, N, R, N, R, R, R, R),
fire		=> array(N, N, N, N, W, W, R, N, R, R, W, R, N, N, R, N, N),
water		=> array(N, N, N, N, N, N, N, N, R, R, R, W, W, N, R, N, N),
grass		=> array(N, N, W, W, R, N, W, N, N, W, R, R, R, N, W, N, N),
electric	=> array(N, N, R, N, W, N, N, N, R, N, N, N, R, N, N, N, N),
psychic		=> array(N, R, N, N, N, N, W, W, N, N, N, N, N, R, N, N, W),
ice			=> array(N, W, N, N, N, W, N, N, W, W, N, N, N, N, R, N, N),
dark		=> array(N, W, N, N, N, N, W, R, N, N, N, N, N, I, N, N, R),
dragon		=> array(N, N, N, N, N, N, N, N, N, R, R, R, R, N, W, W, N));
$typelist = array(
'Norm',
'Fight',
'Fly',
'Psn',
'Grnd',
'Rock',
'Bug',
'Ghost',
'Steel',
'Fire',
'Water',
'Grass',
'Elec',
'Psy',
'Ice',
'Drgn',
'Dark');
$typecolour = array(
0xA8A878,
0xC03028,
0xA890F0,
0xA040A0,
0xE0C068,
0xB8A038,
0xA8B820,
0x705898,
0x68A090,
0xF08030,
0x6890F0,
0x78C850,
0xF8D030,
0xF85888,
0x98D8D8,
0x7038F8,
0x705848);
function typeTestC($type1, $type2) {
	global $type,$typelist;
	$resists = '';
	$immunities = '';
	$weaknesses = '';
	$w = 0;
	$r = 0;
	for ($i = 0; $i < 17; $i++) {
		$n = $type[$type1][$i]*($type1 == $type2 ? 1 : $type[$type2][$i]);
		if (array_key_exists('levitatemode', $_GET) && ($i == ground))
			$n = 0;
		if (array_key_exists('heatproofmode', $_GET) && ($i == fire))
			$n = $n/2;
		if (array_key_exists('filtermode', $_GET) && ($n > 1))
			$n = $n * (3/4);
		if ($n < 1) {
			$r++;
			if ($n == 0)
				$immunities .= $typelist[$i].' ';
			else
				$resists .= $typelist[$i].'('.(1/$n).'x) ';
		} else if ($n > 1) {
			$w++;
			$weaknesses .= $typelist[$i].'('.$n.'x) ';
		}
	}
	return sprintf("<a title=\"Weaknesses: %s&lt;br /&gt;Resistances: %s&lt;br /&gt;Immunities: %s&lt;br /&gt;\">%s</a>", $weaknesses,$resists,$immunities, $w.':'.$r);
}
function combineColours($colour1, $colour2) {
	return '#'.str_pad(dechex(($colour1 + $colour2)/2),6, '0', STR_PAD_LEFT);
}
echo <<<EOT
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"><head><link rel="shortcut icon" href="http://elpenguino.net/pokeassets/icon/430.gif" /><link rel="stylesheet" type="text/css" href="http://elpenguino.net/pokeassets/pkmn.css" title="Default" /><title>Type Test</title></head><body>
<script type="text/javascript" src="http://elpenguino.net/js/jquery.js"></script>
<script type="text/javascript" src="http://elpenguino.net/js/jquery.tools.min.js"></script>
<script type="text/javascript">
	$(function() {
		$("a[title]").tooltip();
	});
</script>
<style type="text/css">
.tooltip {
	display:none;
	border: 1px solid black;
	background: yellow;
	font-size:12px;
	padding:0px;
	color:#000;	
}
</style>
<table border="1"><tr><td></td>
EOT;
for ($i = 0; $i < 17; $i++)
	echo '<td class="'.$typelist[$i].'">'.$typelist[$i].'</td>';
echo '</tr>';
for ($i = 0; $i < 17; $i++) {
	echo '<tr><td class="'.($typelist[$i] != '???' ? $typelist[$i] : '').'">'.$typelist[$i].'</td>';
	for ($j = 0; $j < 17; $j++) 
		echo '<td bgcolor="'.combineColours($typecolour[$i],$typecolour[$j]).'">'.typeTestC($i, $j).'</td>';
	echo "</tr>\r\n";
}
echo '</table>';
echo '</body></html>';
?>