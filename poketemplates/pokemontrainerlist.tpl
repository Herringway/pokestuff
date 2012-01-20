{extends "barebones.tpl"}
{block "title"}POKEYMANS TRAINERS{/block}
{block "data"}
		<table border="1" style="text-align: center;">
{loop $trainer}
			<tr>
				<th colspan="6"><a href="/pkmn/trainers/{$id}">{$class} {$name}</a><br />{$battletype}</th>
			</tr>
			<tr>
{loop $pokemon}
				<td><a href="http://elpenguino.net/pkmn/stats/{$id}"><img src="http://elpenguino.net/bw-sprites/ani/{string_format($id, '%03d')}.gif" title="{$name}" /><br/>L{$level} {$name}</a></td>
{/loop}
			</tr>
{/loop}
		</table>
{/block}