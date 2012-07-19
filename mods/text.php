<?php
class text {
	const name = 'Text';
	const show = false;
	function execute() {
		return $GLOBALS['gamemod']->getText();
	}
	function getMode() {
		return 'text';
	}
}
?>