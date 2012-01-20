<?php
require_once 'text.php';
require_once 'misc.php';
echo '<pre>';
for ($i = 0; $i < NUM_TRAINERS; $i++)
	printf('[%03X] - %s<br>', $i, getTrainerName($i));
echo '</pre>';
?>