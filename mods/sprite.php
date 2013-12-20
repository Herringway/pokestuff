<?php
class sprite extends datamod {
	const name = 'Sprites';
	const show = false;
	function execute($argv) {
		global $settings;
		$id = $argv[count($argv)-1];
		$back = false;
		$shiny = false;
		foreach ($argv as $arg) {
			if ($arg == 'back')
				$back = true;
			if ($arg == 'shiny')
				$shiny = true;
		}
		$checkid = $this->gamemod->pokemonNameToID($id);
		if ($checkid !== false) {
			debugmessage(sprintf('Using ID %s instead of %s', $checkid, $id), 'info');
			$id = $checkid;
		}
		$sprstring = null;
		if ($id > $this->gamemod->getCount('stats')-1)
			$sprstring = sprintf('%s/nopkmnsprite.png', $settings['Base Image URL']);
		else {
			$generation = $this->gamemod->getGeneration();
			$series = $this->gamemod->getSpriteSeries();
			$sprstring = sprintf('%s/gen%d/%s/pokemon/%s%s%d.png', $settings['Base Image URL'], $generation, $series, $back ? 'back/' : '', $shiny ? 'shiny/' : '', $id);
		}
		if ($sprstring !== null)
			header('Location: '.$sprstring);
		return [];
	}
	function getMode() {
		return 'sprite';
	}
	function getHTMLDependencies() {
		return array();
	}
}
?>