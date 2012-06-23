<?php
set_time_limit(1200);
require_once 'Dwoo/dwooAutoload.php';
require_once 'libs/cache.php';
require_once 'libs/settings.php';
require_once 'libs/misc.php';

$cache = new cache();
$settings = new settings('settings.yml');
if ($settings['flushcache']) {
	echo 'Flushing cache...';
	if ($cache->clear())
		echo 'Succeeded';
	unset($cache);
	$settings['flushcache'] = false;
}
function clean_array($input) {
	return (($input !== NULL) && ($input !== ''));
}
if (isset($_SERVER['REQUEST_URI'])) {
	$sect = strtok($_SERVER['REQUEST_URI'], '/');
	$argv = array_merge(array_filter(explode('/', $_SERVER['REQUEST_URI']), 'clean_array'));
}
$format = 'html';
if (isset($argv[0])) {
	$formatdetect = explode('.', $argv[count($argv)-1]);
	if (!isset($formatdetect[1]))
		$format = 'html';
	else if ($formatdetect[1] == 'yml')
		$format = 'yml';
	else if ($formatdetect[1] == 'json')
		$format = 'json';
	if ($format != 'html')
		$argv[count($argv)-1] = $formatdetect[0];
}
$game = isset($argv[0]) ? $argv[0] : 'b2jpn';
$mod = isset($argv[1]) ? $argv[1] : 'stats';
if (file_exists('games/'.$game.'.php'))
	require_once('games/'.$game.'.php');
else
	throw new Exception('Game not found');
	
if (file_exists('mods/'.$mod.'.php'))
	require_once('mods/'.$mod.'.php');
else
	throw new Exception('Mod not found');
$argv[0] = $game;
$argv[1] = $mod;
$gamemod = new $game();
$datamod = new $mod();
if ($settings['cache'] && isset($cache[implode('/', $argv).'/'.$format]))
	$data = $cache[implode('/', $argv).'/'.$format];
else if ($settings['cache'])
	$data = $cache[implode('/', $argv).'/'.$format] = $datamod->execute();
else
	$data = $datamod->execute();
switch($format) {
	case 'html':
		header('Content-Type: text/html; charset=utf-8');
		$dwoo = new Dwoo();
		$dwoo->output('poketemplates/'.$datamod->getMode().'.tpl', array('game' => $game::name, 'gameid' => $game, 'generation' => $game::generation, 'data' => $data)); break;
	case 'json':
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_FORCE_OBJECT); break;
	case 'yml':
		header('Content-Type: text/plain; charset=utf-8');
		echo yaml_emit($data, YAML_UTF8_ENCODING, YAML_ANY_BREAK); break;
}
unset($settings);
?>