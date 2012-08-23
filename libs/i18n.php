<?php
function i18n_init($lang, $name) {
	if (!function_exists('bindtextdomain'))
		return;
	putenv('LC_ALL='.$lang);
	setlocale(LC_ALL, $lang);
	bindtextdomain($name, "./locale");
	textdomain($name);
}
if (!function_exists('gettext')) {
	function gettext($string) {
		return $string;
	}
}
?>