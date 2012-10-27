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
			if (is_numeric(str_replace(array('.','$'), '', $argv[2])))
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
	function getOptions() {
		return array('Sprite Size' => 96, 'Font Size' => 10, 'Image Border Colour' => 0x000000, 'Image Size' => array('x' => 300, 'y' => 200), 'Font' => 'togoshi-monago.ttf');
	}
	function genImage($data, &$canvas) {
		global $settings;
		switch($data['stats'][0]['type1']) {
			case 'Steel':
			case 'Ice':
			case 'Normal':
			case 'Electric': $textcolor = 0x000000; $textshadowcolor = 0x808080; break;
			default: $textcolor = 0xFFFFFF; $textshadowcolor = 0x000000; break;
		}
		$hbound = 128;
		$imagesize = 96;
		$fontsize = 10;
		$canvas->setSize($settings['stats']['Image Size']['x'],$settings['stats']['Image Size']['y']);
		$borderpadding = min($canvas->getX(),$canvas->getY())/100;
		$canvas->drawRectangle($borderpadding, $borderpadding, $canvas->getX()-$borderpadding*2, $canvas->getY()-$borderpadding*2, $settings['typecolours'][$data['stats'][0]['type1']], $settings['imagebordercolour'], 10);
		$canvas->drawRectangle($hbound/2 - $imagesize/2, 8, $imagesize, $imagesize, $settings['typecolours'][$data['stats'][0]['type1']], $settings['imagebordercolour'], 10);
		$sprite = sprintf('images/%s/%s/pokemon/%d.png', $data['generation'], $data['gameid'], $data['stats'][0]['imgid']);
		$canvas->copyImageScaled($sprite, $hbound/2, $imagesize/2+8, $imagesize/96, true);
		$canvas->drawTextCenteredShadowed($data['stats'][0]['name'], $fontsize, 64, $imagesize+$fontsize*1.6+8, $textcolor, $textshadowcolor, $settings['stats']['Font']);
		if ($data['stats'][0]['type1'] == $data['stats'][0]['type2']) {
			$sprite = sprintf('images/types/%s.png', $data['stats'][0]['type1']);
			$canvas->copyImage($sprite, $hbound/2, $imagesize+$fontsize*1.6+24, true);
		} else {
			$sprite = sprintf('images/types/%s.png', $data['stats'][0]['type1']);
			$canvas->copyImage($sprite, $hbound/2+16, $imagesize+$fontsize*1.6+24, true);
			$sprite = sprintf('images/types/%s.png', $data['stats'][0]['type2']);
			$canvas->copyImage($sprite, $hbound/2-16, $imagesize+$fontsize*1.6+24, true);
		}
		if (isset($data['stats'][0]['abilities'])) {
			$abils = array();
			foreach ($data['stats'][0]['abilities'] as $abil)
				if (($abil != 0) && (!in_array($abil, $abils)))
					$abils[] = $abil;
			for ($i = 0; $i < count($abils); $i++)
				$canvas->drawTextCenteredShadowed($data['abilities'][$abils[$i]]['name'], $fontsize, 64, $imagesize+$fontsize*1.6+48+$i*($fontsize*1.6), $textcolor, $textshadowcolor, $settings['stats']['Font']);
		}
		$htrans = $hbound;
		$vtrans = 10+$borderpadding*3;
		if ($canvas->getX()-$htrans > 0) {
			$sizemult = min($canvas->getX()-$borderpadding-16-$htrans,$canvas->getY()-$borderpadding-16-$vtrans)/4;
			printf('Boundaries: [%d,%d]<br />'.PHP_EOL, $canvas->getX()-$htrans, $canvas->getY()-$vtrans);
			printf('Size: %dx%1$d px<br />'.PHP_EOL, $sizemult*4);
			$this->drawStatsPolygon($canvas, $data, $htrans, $vtrans, $sizemult);
		}
	}
	private function drawStatsPolygon($canvas, $data, $x, $y, $scale) {
		global $settings;
		printf('Size multiplier: %f<br />'.PHP_EOL, $scale);
		$ax = 2*$scale;
		$bx = 4*$scale;
		$cx = 0;
		$ay = 0;
		$by = 1*$scale;
		$cy = 3*$scale;
		$dy = 4*$scale;
		$centx = $ax+$x;
		$centy = $dy/2+$y;
		
		list($hpx,$hpy)     = $this->scaleXY($centx-$x, $centy-$y, $ax, $ay, $this->statScale($data['stats'][0]['hp']));
		list($defx,$defy)   = $this->scaleXY($centx-$x, $centy-$y, $bx, $by, $this->statScale($data['stats'][0]['def']));
		list($sdefx,$sdefy) = $this->scaleXY($centx-$x, $centy-$y, $bx, $cy, $this->statScale($data['stats'][0]['sdef']));
		list($spdx,$spdy)   = $this->scaleXY($centx-$x, $centy-$y, $ax, $dy, $this->statScale($data['stats'][0]['speed']));
		list($satkx,$satky) = $this->scaleXY($centx-$x, $centy-$y, $cx, $cy, $this->statScale($data['stats'][0]['satk']));
		list($atkx,$atky)   = $this->scaleXY($centx-$x, $centy-$y, $cx, $by, $this->statScale($data['stats'][0]['atk']));
		$canvas->drawPolygon(array($ax+$x, $ay+$y, $bx+$x, $by+$y, $bx+$x, $cy+$y, $ax+$x, $dy+$y, $cx+$x, $cy+$y, $cx+$x, $by+$y), $settings['imagebordercolour']);
		$canvas->drawFilledPolygon(array($hpx+$x, $hpy+$y, $defx+$x, $defy+$y, $sdefx+$x, $sdefy+$y, $spdx+$x, $spdy+$y, $satkx+$x, $satky+$y, $atkx+$x, $atky+$y), $settings['Stat Polygon Colour'], $settings['imagebordercolour']);
		$canvas->drawLine($ax+$x, $ay+$y, $centx, $centy, $settings['imagebordercolour']);
		$canvas->drawLine($bx+$x, $cy+$y, $centx, $centy, $settings['imagebordercolour']);
		$canvas->drawLine($bx+$x, $by+$y, $centx, $centy, $settings['imagebordercolour']);
		$canvas->drawLine($cx+$x, $by+$y, $centx, $centy, $settings['imagebordercolour']);
		$canvas->drawLine($cx+$x, $cy+$y, $centx, $centy, $settings['imagebordercolour']);
		$canvas->drawLine($ax+$x, $dy+$y, $centx, $centy, $settings['imagebordercolour']);
		$canvas->drawTextCenteredShadowed('HP',   10, $ax+$x, $ay+$y, 0x000000, 0x0000EF, $settings['stats']['Font']);
		$canvas->drawTextCenteredShadowed('ATK',  10, $cx+$x, $by+$y-5, 0x000000, 0xEF0000, $settings['stats']['Font']);
		$canvas->drawTextCenteredShadowed('DEF',  10, $bx+$x, $by+$y-5, 0x000000, 0x0000EF, $settings['stats']['Font']);
		$canvas->drawTextCenteredShadowed('SATK', 10, $cx+$x, $cy+$y+20, 0x000000, 0xEF0000, $settings['stats']['Font']);
		$canvas->drawTextCenteredShadowed('SDEF', 10, $bx+$x, $cy+$y+20, 0x000000, 0x0000EF, $settings['stats']['Font']);
		$canvas->drawTextCenteredShadowed('SPD',  10, $ax+$x, $dy+$y+10, 0x000000, 0xEF0000, $settings['stats']['Font']);
	}
	private function scaleXY($x1, $y1, $x2, $y2, $factor) {
		$x = $x1+($x2-$x1)*$factor;
		$y = $y1+($y2-$y1)*$factor;
		printf('(%03d,%03d), (%03d,%03d)	f:%f => (%03d,%03d)<br />'.PHP_EOL,$x1,$y1,$x2,$y2,$factor, $x, $y);
		return array($x, $y);
	}
	private function statScale($stat) {
		//return 0.5;
		return pow($stat/255,0.75);
	}
}
?>
