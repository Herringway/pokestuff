<?php

$included = 1;
require_once 'narc.php';
$narcfile = 'narcs/bw/0/0/4';
for ($i = 0; $i < 649; $i++)
	echo getfile($narcfile, $i*20+4, 4).'<br>';

?>