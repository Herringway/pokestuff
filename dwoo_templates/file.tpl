{extends "barebones.tpl"}
{block "title"}Pokemon {$_root.game} File {$data.id}{/block}
{block "data"}
<table>
<tr>
{loop $data.data.0.data}
{if $_key % 4 == 0}</tr><tr>{/if}
<td style="width: 30px;">{$}</td>
{/loop}
</tr></table>
{/block}