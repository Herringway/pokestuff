<?php
require_once 'libs/cache.php';
require_once 'libs/settings.php';
require_once 'libs/misc.php';
require_once 'libs/chromephp/ChromePhp.php';
$settings = new settings('settings.yml');

$cache = new cache();
if (!$settings['cache'])
	$cache->disable();
	
if ($settings['flushcache']) {
	echo 'Flushing cache...';
	if ($cache->clear())
		echo 'Succeeded';
	unset($cache);
	$settings['flushcache'] = false;
}
if (isset($_SERVER['REQUEST_URI'])) {
	$sect = strtok($_SERVER['REQUEST_URI'], '/');
	$argv = array_merge(array_filter(explode('/', $_SERVER['REQUEST_URI']), 'clean_array'));
}
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
	else if ($formatdetect[count($formatdetect)-1] == 'jpg')
		$format = 'jpg';
	if ($format != $settings['Default Output Format'])
		$argv[count($argv)-1] = implode('.', array_slice($formatdetect, 0, count($formatdetect)-1));
}
$game = isset($argv[0]) ? $argv[0] : $settings['defaultgame'];
$mod = isset($argv[1]) ? $argv[1] : $settings['defaultmod'];
$gamesdir = opendir('games');
while (false !== ($entry = readdir($gamesdir))) {
		if ($entry != "." && $entry != ".." && (substr($entry, -3) == 'yml')) {
			$gameinfo = yaml_parse_file('games/'.$entry,0);
			$modname = substr($entry,0,-4);
			$games[] = array('id' => $modname, 'name' => $gameinfo['Title'], 'locale' => $gameinfo['Release']);
		}
	}
closedir($gamesdir);
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
	if (!file_exists('games/'.$game.'.yml') && (count($argv) <= 1)) {
		$game = $settings['defaultgame'];
		$mod = $argv[0];
		$argv = array($game, $argv[0]);
	}
	if (!file_exists('games/'.$game.'.yml'))
		throw new Exception('Game not found ('.$game.')');
	$gameinfo = yaml_parse_file('games/'.$game.'.yml',0);
	require_once 'libs/gen'.$gameinfo['Generation'].'common.php';
	$argv[0] = $game;
	$gamecfg = array();
	if (file_exists('libs/gen'.$gameinfo['Generation'].'.yml'))
		$gamecfg = yaml_parse_file('libs/gen'.$gameinfo['Generation'].'.yml');
	$gamecfg += yaml_parse_file('games/'.$game.'.yml',1);
	$mname = 'gen'.$gameinfo['Generation'];
	$gamemod = new $mname($game);
	if (!file_exists('mods/'.$mod.'.php')) {
		if (($nmod = $gamemod->findAppropriateMod(urldecode($mod))) !== false) {
			$mod = $nmod;
			array_splice($argv, 1, 0, $mod);
		} else
			throw new Exception(sprintf('Mod %s not found', $mod));
	}

	$argv[1] = $mod;
	$datamod = new $mod();
	$data = $datamod->execute();
	if ($settings['debug']) {
		ini_set('xdebug.var_display_max_children', -1);
		ini_set('xdebug.var_display_max_data', -1);
		ini_set('xdebug.var_display_max_depth', -1);
		if (method_exists($gamemod, 'getExecutionStats'))
			ChromePhp::log('Execution stats: ', $gamemod->getExecutionStats());
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
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			echo $twig->render($datamod->getMode().'.tpl', $outputstuff); break;
		case 'json':
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT); break;
		case 'yml':
			header('Content-Type: text/plain; charset=utf-8');
			echo yaml_emit($data, YAML_UTF8_ENCODING, YAML_ANY_BREAK); break;
		case 'png':
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			header ('Content-Type: image/png');
			$canvas->renderPNG();
			break;
		case 'jpg':
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			header('Content-Type: image/jpeg');
			$canvas->renderJPG();
			break;
		case 'gif':
			require 'libs/gddraw.php';
			$outputstuff = array('game' => $gameinfo['Title'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => 'gen'.$gameinfo['Generation'], $mod => $data);
			$wants = $datamod->getHTMLDependencies();
			foreach ($wants as $what=>$ids)
				foreach ($ids as $id)
					$outputstuff[$what][$id] = $gamemod->getData($what,$id);
			$canvas = new GDDraw();
			$datamod->genImage($outputstuff, $canvas);
			header('Content-Type: image/gif');
			$canvas->renderGIF();
			break;
	}
}
unset($settings);
?>