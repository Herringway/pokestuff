{extends "barebones.tpl"}
{block "title"}{if (count($data.Trainers) > 1)}Trainers{else}{$data.Trainers.0.class} {$data.Trainers.0.name}{/if}{/block}
{block "data"}
{if (count($data.Trainers) > 1)}
		<table border="1" style="text-align: center;">
{loop $data.Trainers}
			<tr>
				<td><a href="/{$_root.gameid}/trainers/{$id}">{$class} {$name}</a><br />{if $battletype}{$battletype}<br />{/if}{loop $items}{$name}<br />{/loop}</td>
{loop $pokemon}
				<td><a href="/{$_root.gameid}/stats/{$id}">{printsprite $id $_root.generation $_root.gameid title=$_root.data.Pokemon.$id.name}<br/>L{$level} {$_root.data.Pokemon.$id.name}</a></td>
{/loop}
			</tr>
{/loop}
		</table>{else}
{with $data.Trainers.0}
{$class} {$name}<br />
{if $items}Carries {loop $items}{$_root.data.Items.$.name}{if !$.loop.default.last}, {/if}{/loop}{/if}
<table style="text-align: center;">
	<tr>
{loop $pokemon}
		<td style="background: white; width: 150px;">
			<a href="/{$_root.gameid}/stats/{$id}">{printsprite $id $_root.generation $_root.gameid}<br />
			{$_root.data.Pokemon.$id.name}<br /></a>
			Level {$level}<br />
			Holding: {default $_root.data.Items.$item.name "None"}<br />
{loop $move}
			{$_root.data.Moves.$.name}<br />
{/loop}
		</td>
{/loop}
	</tr>
</table>
{/with}{/if}
{/block}