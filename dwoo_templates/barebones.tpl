{load_templates "templates.tpl"}<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Pokemon {$_root.game} - {block "title"}Fix Me{/block}</title>
		<link rel="shortcut icon" href="/430.gif" />
		<link rel="stylesheet" type="text/css" href="/css/pkmn.css" title="Default" />
	</head>
	<body>
	<div class="menubar"><select onchange="top.location.href = '/' + this.options[this.selectedIndex].value + '/{$mod}'">{loop $games}<option value="{$id}"{if $_.gameid == $id} selected="yes"{/if}>Pokemon {$name} ({$locale})</option>{/loop}</select><select onchange="top.location.href = '/{$gameid}/' + this.options[this.selectedIndex].value">{loop $mods}<option value="{$id}"{if $_.mod == $id} selected="yes"{/if}>{$name}</option>{/loop}</select></div>
{block "data"}{/block}
	</body>
</html>