<?php
class randteam {
	const name = 'Random Team Generator';
	function execute() {
		global $gamemod;
		$upper = $gamemod->getCount('stats')-1;
		for ($i = 0; $i < 6; $i++) {
			while (true) {
				$data = $gamemod->getStatsCached(rand(1, $upper));
				if (!isset($data['evolutions'][0]) && ($data['hp'] + $data['atk'] + $data['def'] + $data['sdef'] + $data['satk'] + $data['speed'] < 600))
					break;
			}
			$pokemon[] = $data;
		}
		return $pokemon;
	}
	function getMode() {
		return 'randomteam';
	}
}
?>