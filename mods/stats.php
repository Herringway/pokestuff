<?php
class stats {
	private $wants = array();
	const name = 'Stats';
	function execute() {
		global $gamemod, $argv, $cache,$game;
		$floor = 0;
		$limit = $gamemod->getCount('stats')-1;
		$ceiling = $limit;
		if (isset($argv[2])) {
			if (is_numeric(str_replace('.', '', $argv[2])))
				list($floor,$ceiling) = rangeStringToRange($argv[2],0,$limit);
			else
				$floor = $ceiling = $gamemod->pokemonNameToID(urldecode($argv[2]));
		}
		$output = array();
		for ($pkmnid = $floor; $pkmnid <= $ceiling; $pkmnid++) {
			$tmp = $gamemod->getData('stats', $pkmnid);
			$output[] = $tmp;
			if (isset($tmp['abilities']))
				foreach ($tmp['abilities'] as $abil)
					if ($abil != 0)
						$this->wants['abilities'][] = $abil;
			if (isset($tmp['items']))
				foreach ($tmp['items'] as $item)
					if ($item != 0)
						$this->wants['items'][] = $item;
			if (isset($tmp['moves']))
				foreach ($tmp['moves'] as $mvtype)
					foreach ($mvtype as $move)
							$this->wants['moves'][] = $move['id'];
		}
		return $output;
	}
	function getMode() {
		return 'stats';
	}
	function getHTMLDependencies() {
		return $this->wants;
	}
	function genImage($data, &$canvas) {
		$typecolors = array('Normal' => 0xA8A878, 'Fight' => 0xC03028, 'Flying' => 0xA890F0, 'Poison' => 0xA040A0, 'Ground' => 0xE0C068, 'Rock' => 0xB8A038, 'Bug' => 0xA8B820, 'Ghost' => 0x705898, 'Steel' => 0xB8B8D0, 'Fire' => 0xF08030, 'Water' => 0x6890F0, 'Grass' => 0x78C850, 'Electric' => 0xF8D030, 'Psychic' => 0xF85888, 'Ice' => 0x98D8D8, 'Dragon' => 0x7038F8, 'Dark' => 0x705848);
		$bgcolor = $typecolors[$data['stats'][0]['type1']];
		$bordercolor = 0x000000;
		switch($data['stats'][0]['type1']) {
			case 'Steel':
			case 'Ice':
			case 'Normal':
			case 'Electric': $textcolor = 0x000000; $textshadowcolor = 0x808080; break;
			default: $textcolor = 0xFFFFFF; $textshadowcolor = 0x000000; break;
		}
		$statpolycolor = 0x007FFF;
		$canvas->setSize(308,208);
		$canvas->drawRectangle(2, 2, $canvas->getX()-4, $canvas->getY()-4, $bgcolor, $bordercolor, 10);
		$canvas->drawRectangle(16, 8, 96, 96, $bgcolor, $bordercolor, 10);
		$sprite = sprintf('images/%s/%s/pokemon/%d.png', $data['generation'], $data['gameid'], $data['stats'][0]['imgid']);
		$canvas->copyImage($sprite, 64, 56, true);
		$canvas->drawTextCenteredShadowed($data['stats'][0]['name'], 10, 64, 120, $textcolor, $textshadowcolor, 'ARIALUNI.TTF');
		if ($data['stats'][0]['type1'] == $data['stats'][0]['type2']) {
			$sprite = sprintf('images/types/%s.png', $data['stats'][0]['type1']);
			$canvas->copyImage($sprite, 64, 136, true);
		} else {
			$sprite = sprintf('images/types/%s.png', $data['stats'][0]['type1']);
			$canvas->copyImage($sprite, 47, 136, true);
			$sprite = sprintf('images/types/%s.png', $data['stats'][0]['type2']);
			$canvas->copyImage($sprite, 81, 136, true);
		}
		if (isset($data['stats'][0]['abilities'])) {
			$abils = array();
			foreach ($data['stats'][0]['abilities'] as $abil)
				if (($abil != 0) && (!in_array($abil, $abils)))
					$abils[] = $abil;
			for ($i = 0; $i < count($abils); $i++)
				$canvas->drawTextCenteredShadowed($data['abilities'][$abils[$i]]['name'], 10, 64, 160+$i*16, $textcolor, $textshadowcolor, 'ARIALUNI.TTF');
		}
		$htrans = 128;
		$vtrans = 16;
		$sizemult = 20;
		$ax = 4*$sizemult;
		$bx = 8*$sizemult;
		$cx = 0;
		$ay = 0;
		$by = 2*$sizemult;
		$cy = 6*$sizemult;
		$dy = 8*$sizemult;
		$centx = $ax+$htrans;
		$centy = $dy/2+$vtrans;
		
		list($hpx,$hpy)     = $this->scaleXY($centx, $centy, $ax, $ay, $this->statScale($data['stats'][0]['hp']),    deg2rad(0));
		list($defx,$defy)   = $this->scaleXY($centx, $centy, $bx, $by, $this->statScale($data['stats'][0]['def']),   deg2rad(60));
		list($sdefx,$sdefy) = $this->scaleXY($centx, $centy, $bx, $cy, $this->statScale($data['stats'][0]['sdef']),  deg2rad(120));
		list($spdx,$spdy)   = $this->scaleXY($centx, $centy, $ax, $dy, $this->statScale($data['stats'][0]['speed']), deg2rad(180));
		list($satkx,$satky) = $this->scaleXY($centx, $centy, $cx, $cy, $this->statScale($data['stats'][0]['satk']),  deg2rad(240));
		list($atkx,$atky)   = $this->scaleXY($centx, $centy, $cx, $by, $this->statScale($data['stats'][0]['atk']),   deg2rad(300));
		/*$atkx = $cx+$htrans;
		$atky = $by+$vtrans;
		$satkx = $cx+$htrans;
		$satky = $cy+$vtrans;
		$sdefx = $bx+$htrans;
		$sdefy = $cy+$vtrans;
		$spdx = $ax+$htrans;
		$spdy = $centy+$this->statScale($data['stats'][0]['speed'])*($centy-$ay);*/
		$canvas->drawPolygon(array($ax+$htrans, $ay+$vtrans, $bx+$htrans, $by+$vtrans, $bx+$htrans, $cy+$vtrans, $ax+$htrans, $dy+$vtrans, $cx+$htrans, $cy+$vtrans, $cx+$htrans, $by+$vtrans), $bordercolor);
		$canvas->drawFilledPolygon(array($hpx, $hpy, $defx, $defy, $sdefx, $sdefy, $spdx, $spdy, $satkx, $satky, $atkx, $atky), $statpolycolor, $bordercolor);
		$canvas->drawLine($ax+$htrans, $ay+$vtrans, $centx, $centy, $bordercolor);
		$canvas->drawLine($bx+$htrans, $cy+$vtrans, $centx, $centy, $bordercolor);
		$canvas->drawLine($bx+$htrans, $by+$vtrans, $centx, $centy, $bordercolor);
		$canvas->drawLine($cx+$htrans, $by+$vtrans, $centx, $centy, $bordercolor);
		$canvas->drawLine($cx+$htrans, $cy+$vtrans, $centx, $centy, $bordercolor);
		$canvas->drawLine($ax+$htrans, $dy+$vtrans, $centx, $centy, $bordercolor);
		$canvas->drawTextCenteredShadowed('HP',   10, $ax+$htrans, $ay+$vtrans, 0x000000, 0x0000EF, 'ARIALUNI.TTF');
		$canvas->drawTextCenteredShadowed('ATK',  10, $cx+$htrans, $by+$vtrans-5, 0x000000, 0xEF0000, 'ARIALUNI.TTF');
		$canvas->drawTextCenteredShadowed('DEF',  10, $bx+$htrans, $by+$vtrans-5, 0x000000, 0x0000EF, 'ARIALUNI.TTF');
		$canvas->drawTextCenteredShadowed('SATK', 10, $cx+$htrans, $cy+$vtrans+20, 0x000000, 0xEF0000, 'ARIALUNI.TTF');
		$canvas->drawTextCenteredShadowed('SDEF', 10, $bx+$htrans, $cy+$vtrans+20, 0x000000, 0x0000EF, 'ARIALUNI.TTF');
		$canvas->drawTextCenteredShadowed('SPD',  10, $ax+$htrans, $dy+$vtrans+10, 0x000000, 0xEF0000, 'ARIALUNI.TTF');
	}
	private function scaleXY($x1, $y1, $x2, $y2, $factor, $angle) {
		$x = $x1-$factor*($x1-$x2);
		$y = $y1-$factor*($y1-$y2);
		return array($x, $y);
	}
	private function statScale($stat) {
		//return 0;
		return $stat/255;
	}
}
?>