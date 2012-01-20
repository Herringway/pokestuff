<?php
include 'types.php';
function typeTestC($type1, $type2) {
	global $type,$typelist;
	$resists = '';
	$immunities = '';
	$weaknesses = '';
	$w = 0;
	$r = 0;
	for ($i = 0; $i < 18; $i++) {
		if ($i != 9) {
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
for ($i = 0; $i < 18; $i++) {
	if ($i != 9)
		echo '<td class="'.$typelist[$i].'">'.$typelist[$i].'</td>';
}
echo '</tr>';
for ($i = 0; $i < 18; $i++) {
	if ($i != 9) {
	echo '<tr><td class="'.($typelist[$i] != '???' ? $typelist[$i] : '').'">'.$typelist[$i].'</td>';
	for ($j = 0; $j < 18; $j++) {
		if ($j != 9)
			echo '<td bgcolor="'.combineColours($typecolour[$i],$typecolour[$j]).'">'.typeTestC($i, $j).'</td>';
	}
	echo "</tr>\r\n";
	}
}
echo '</table>';
echo '</body></html>';
?>