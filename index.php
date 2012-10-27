<?php
$init = microtime(true);
require_once 'libs/cache.php';
require_once 'libs/settings.php';
require_once 'libs/misc.php';
require_once 'libs/chromephp/ChromePhp.php';
$settings = new settings(array(
		'cache' => true,
		'defaultgame' => 'black 2',
		'defaultmod' => 'stats',
		'default language' => 'eng',
		'available languages' => array('eng', 'jpn'),
		'debug' => false,
		'Generation 1 Internal IDs' => true,
		'Default Output Format' => 'html',
		'Stat Polygon Colour' => 0x007FFF,
		'imagebordercolour' => 0x000000,
		'typecolours' => array('Normal' => 0xA8A878, 'Fight' => 0xC03028, 'Flying' => 0xA890F0, 'Poison' => 0xA040A0, 'Ground' => 0xE0C068, 'Rock' => 0xB8A038, 'Bug' => 0xA8B820, 'Ghost' => 0x705898, 'Steel' => 0xB8B8D0, 'Fire' => 0xF08030, 'Water' => 0x6890F0, 'Grass' => 0x78C850, 'Electric' => 0xF8D030, 'Psychic' => 0xF85888, 'Ice' => 0x98D8D8, 'Dragon' => 0x7038F8, 'Dark' => 0x705848),
		'flushcache' => false), 'settings.yml');

$cache = new cache('Pokedex');
$cachehits = 0;
$cachemisses = 0;
if (!$settings['cache'])
	$cache->disable();

debugmessage('Cache is '.($cache->status() ? 'enabled' : 'disabled'), 'info');
if ($cache->status())
	debugmessage('Cache in '.$cache->mode().' mode', 'info');
	
if ($settings['flushcache']) {
	echo 'Flushing cache...';
	if ($cache->clear())
		echo 'Succeeded';
	unset($cache);
	$settings['flushcache'] = false;
}
if (isset($_SERVER['REQUEST_URI']))
	$argv = array_merge(array_filter(explode('/', urldecode($_SERVER['REQUEST_URI'])), 'clean_array'));
$format = $settings['Default Output Format'];
if (isset($argv[0])) {
	$formatdetect = explode('.', $argv[count($argv)-1]);
	if (!isset($formatdetect[count($formatdetect)-1])) {

	} else if ($formatdetect[count($formatdetect)-1] == 'yml')
		$format = 'yml';
	else if ($formatdetect[count($formatdetect)-1] == 'json')
		$format = 'json';
	else if ($formatdetect[count($formatdetect)-1] == 'png')
		$format = 'png';
	else if ($formatdetect[count($formatdetect)-1] == 'gif')
		$format = 'gif';
	else if ($formatdetect[count($formatdetect)-1] == 'devimg')
		$format = 'devimg';
	else if ($formatdetect[count($formatdetect)-1] == 'jpg')
		$format = 'jpg';
	if ($format != $settings['Default Output Format'])
		$argv[count($argv)-1] = implode('.', array_slice($formatdetect, 0, count($formatdetect)-1));
}
$game = isset($argv[0]) ? $argv[0] : $settings['defaultgame'];
$split = explode(':', $game);
$game = $split[0];
$lang = isset($split[1]) ? $split[1] : $settings['default language'];
$mod = isset($argv[1]) ? $argv[1] : $settings['defaultmod'];
foreach ($settings['available languages'] as $tlang) {
	$gamesdir = opendir('games/'.$tlang);
	while (false !== ($entry = readdir($gamesdir))) {
			if ($entry != "." && $entry != ".." && (substr($entry, -3) == 'yml')) {
				$gameinfo = yaml_parse_file('games/'.$tlang.'/'.$entry,0);
				$modname = substr($entry,0,-4);
				$games[] = array('id' => $modname, 'name' => $gameinfo['Title'], 'locale' => $gameinfo['Release'], 'lang' => $tlang);
			}
		}
	closedir($gamesdir);
}
$moddir = opendir('mods');
while (false !== ($entry = readdir($moddir))) {
		if ($entry != "." && $entry != ".." && (substr($entry, -3) == 'php')) {
			include_once 'mods/'.$entry;
			$modname = substr($entry,0,-4);
			if (!defined($modname.'::show') || $modname::show)
				$mods[] = array('id' => $modname, 'name' => $modname::name);
		}
	}
closedir($moddir);
usort($mods, 'modsort');
usort($games, 'modsort');
if (file_exists('otherpages/'.$game.'.php')) {
	require_once 'otherpages/'.$game.'.php';
	$mod = new $game();
	echo $mod->execute();

} else {
	if (!file_exists('games/'.$lang.'/'.$game.'.yml') && (count($argv) <= 1)) {
		$game = $settings['defaultgame'];
		$mod = $argv[0];
		$argv = array($game, $argv[0]);
	}
	if (file_exists('mods/'.$game.'.php')) {
		$mod = $game;
		$game = $settings['defaultgame'];
		$argv = array_merge(array($game), $argv);
	}
	if (!file_exists('games/'.$lang.'/'.$game.'.yml'))
		throw new Exception('Game not found ('.$game.')');
	$gameinfo = yaml_parse_file('games/'.$lang.'/'.$game.'.yml',0);
	require_once 'libs/gen'.$gameinfo['Generation'].'common.php';
	$argv[0] = $game;
	$gamecfg = (file_exists('libs/gen'.$gameinfo['Generation'].'.yml') ? yaml_parse_file('libs/gen'.$gameinfo['Generation'].'.yml') : array()) + yaml_parse_file('games/'.$lang.'/'.$game.'.yml',1);
	$mname = 'gen'.$gameinfo['Generation'];
	$gamemod = new $mname($game,$lang);
	$settings->addSetting($mname, $gamemod->getOptions());
	debugvar($game, 'game');
	debugvar($lang, 'language');
	debugvar($mod, 'mod');
	debugvar($argv, 'args');
	if (!file_exists('mods/'.$mod.'.php')) {
		if (($nmod = $gamemod->findAppropriateMod(urldecode($mod))) !== false) {
			$mod = $nmod;
			array_splice($argv, 1, 0, $mod);
		} else {
			foreach ($settings['available languages'] as $lang) {
				if ($lang == $settings['default language'])
					continue;
				$gamemod = new $mname($game, $lang);
				$settings->addSetting($mname, $gamemod->getOptions());
				if (($nmod = $gamemod->findAppropriateMod(urldecode($mod))) !== false) {
					$mod = $nmod;
					array_splice($argv, 1, 0, $mod);
					break;
				}
			}
			if ($nmod === false)
				throw new Exception(sprintf('Mod %s not found', $mod));
		}
	}

	$argv[1] = $mod;
	$datamod = new $mod();
	$settings->addSetting($mod, $datamod->getOptions());
	debugvar($settings, 'settings');
	$data = $datamod->execute();
	if ($settings['debug']) {
		ini_set('xdebug.var_display_max_children', -1);
		ini_set('xdebug.var_display_max_data', -1);
		ini_set('xdebug.var_display_max_depth', -1);
	}
	switch($format) {
		case 'html':
			require_once 'Twig/Autoloader.php';
			Twig_Autoloader::register();
			require_once 'peng/twigext.php';
			header('Content-Type: text/html; charset=utf-8');
			$loader = new Twig_Loader_Filesystem('templates');
			$twig = new Twig_Environment($loader, array('debug' => $settings['debug']));
			$twig->addExtension(new Twig_Extension_Debug());
			$twig->addExtension(new Twig_Extension_Sandbox(new Twig_Sandbox_SecurityPolicy()));
			$twig->addExtension(new Penguin_Twig_Extensions());
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'gamelang' => $lang, 'games' => $games, 'mods' => $mods, 'deflang' => $settings['default language'], 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			debugmessage(sprintf('took %f seconds', microtime(true)-$init), 'info');
			debugmessage(sprintf('Cache hits: %d/%d', $cachehits, $cachehits+$cachemisses), 'info');
			debugmessage(sprintf('Memory used: %1.2fMB', memory_get_peak_usage()/1024/1024), 'info');
			echo $twig->render($datamod->getMode().'.tpl', $outputstuff); break;
		case 'json':
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT); break;
		case 'yml':
			header('Content-Type: text/plain; charset=utf-8');
			echo yaml_emit($data, YAML_UTF8_ENCODING, YAML_ANY_BREAK); break;
		case 'png':
			ob_start();
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			ob_end_clean();
			header ('Content-Type: image/png');
			$canvas->renderPNG();
			break;
		case 'devimg':
			header('Content-Type: text/html');
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			break;
		case 'jpg':
			ob_start();
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			ob_end_clean();
			header('Content-Type: image/jpeg');
			$canvas->renderJPG();
			break;
		case 'gif':
			ob_start();
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			ob_end_clean();
			header('Content-Type: image/gif');
			$canvas->renderGIF();
			break;
	}
}
unset($settings);
?>