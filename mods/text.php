<?php
class text extends datamod {
	const name = 'Text';
	const show = false;
	function execute() {
		return $GLOBALS['gamemod']->getText();
	}
	function getMode() {
		return 'text';
	}
	function getHTMLDependencies() {
		return array();
	}
}
?>