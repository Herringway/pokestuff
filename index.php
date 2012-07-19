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
	else if ($formatdetect[count($formatdetect)-1] == 'test')
		$format = 'twig';
	else if ($formatdetect[count($formatdetect)-1] == 'json')
		$format = 'json';
	if ($format != $settings['Default Output Format'])
		$argv[count($argv)-1] = implode('.', array_slice($formatdetect, 0, count($formatdetect)-1));
}
$game = isset($argv[0]) ? $argv[0] : $settings['defaultgame'];
$mod = isset($argv[1]) ? $argv[1] : $settings['defaultmod'];
$gamesdir = opendir('games');
while (false !== ($entry = readdir($gamesdir))) {
		if ($entry != "." && $entry != ".." && (substr($entry, -3) == 'php')) {
			include_once 'games/'.$entry;
			$modname = substr($entry,0,-4);
			$games[] = array('id' => $modname, 'name' => $modname::name, 'locale' => $modname::locale);
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
if (file_exists('mods/'.$game.'.php')) {
	$mod = $game;
	$game = 'all';
} else {
	if (!file_exists('games/'.$game.'.php'))
		throw new Exception('Game not found');
	if (!file_exists('mods/'.$mod.'.php'))
		throw new Exception(sprintf('Mod %s not found', $mod));
}
$argv[0] = $game;
$argv[1] = $mod;
$gamecfg = array();
if (file_exists('games/'.$game::generation.'.yml'))
	$gamecfg = yaml_parse_file('games/'.$game::generation.'.yml');
if (file_exists('games/'.$game.'.yml'))
	$gamecfg += yaml_parse_file('games/'.$game.'.yml');
$gamemod = new $game();
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
	case 'dwoo':
		header('Content-Type: text/html; charset=utf-8');
		require_once 'Dwoo/dwooAutoload.php';
		$dwoo = new Dwoo();
		$dwoo->output('dwoo_templates/'.$datamod->getMode().'.tpl', array('game' => $game::name, 'debug' => $settings['debug'], 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => $game::generation, 'data' => $data)); break;
	case 'twig':
		require_once 'Twig/Autoloader.php';
		Twig_Autoloader::register();
		header('Content-Type: text/html; charset=utf-8');
		$loader = new Twig_Loader_Filesystem('twig_templates');
		$twig = new Twig_Environment($loader, array('debug' => $settings['debug']));
		$twig->addExtension(new Twig_Extension_Debug());
		echo $twig->render($datamod->getMode().'.tpl', array('game' => $game::name, 'mod' => $mod, 'gameid' => $game, 'games' => $games, 'mods' => $mods, 'generation' => $game::generation, 'data' => $data)); break;
	case 'json':
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT); break;
	case 'yml':
		header('Content-Type: text/plain; charset=utf-8');
		echo yaml_emit($data, YAML_UTF8_ENCODING, YAML_ANY_BREAK); break;
}
unset($settings);
?>