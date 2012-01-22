<?php
require_once 'text.php';
require_once 'misc.php';
$textfile = new gen5text('narcs/weng/'.TEXT_NARC);
echo '<pre>';
for ($i = 0; $i < NUM_TRAINERS; $i++)
	printf('[%03X] - %s<br>', $i, $textfile->fetchline(TEXT_TRAINER_NAME, $i));
echo '</pre>';
?>