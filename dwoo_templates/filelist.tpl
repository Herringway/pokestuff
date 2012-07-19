{extends "barebones.tpl"}
{block "title"}Pokemon {$_root.game} Raw data{/block}
{block "data"}{loop $data.data}
<a href="/{$_root.gameid}/dumptable/{$_root.data.table}/{$_key}">File {$_key}</a><br />
{/loop}
{/block}