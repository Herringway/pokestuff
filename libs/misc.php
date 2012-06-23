<?php
function kimplode($array, $glue = ': ', $separator = ', ') {
	if (!is_array($array))
		return $array;
	$val = array();
	foreach ($array as $k => $v)
		$val[] = $k.$glue.$v;
	return implode($separator, $val);
}
?>