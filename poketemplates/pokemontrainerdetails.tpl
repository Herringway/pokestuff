{extends "barebones.tpl"}
{block "title"}{$class} {$name}{/block}
{block "data"}
{$class} {$name}<br />
Carries {foreach $items item implode=", "}{$item.name}{/foreach}
<table style="text-align: center;">
	<tr>
{loop $pokemon}
		<td style="background: white; width: 150px;">
			<a href="/pkmn/stats/{$id}"><img src="/bw-sprites/ani/{string_format($id, '%03u')}.gif" /><br />
			{$name}<br /></a>
			Level {$level}<br />
			Holding: {default $item "None"}<br />
{foreach $move key moves}
			{$moves}<br />
{/foreach}
		</td>
{/loop}
	</tr>
</table>
{/block}