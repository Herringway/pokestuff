{extends "barebones.tpl"}
{block "title"}Wild Pokemon{/block}
{block "data"}
	<table id="encounters">
		<thead>
			<tr>
				<th colspan="{count($data.Areas.0.Encounters)}">{$data.Areas.0.name}</th>
			</tr>
			<tr>{loop $data.Areas.0.Encounters}
				<th>{$_key}</th>{/loop}
			</tr>
		</thead>
		<tbody>
			<tr>
{loop $data.Areas.0.Encounters}
				<td style="vertical-align: top; text-align: center;">
{loop $}
			<div style="display: inline-block; text-align: center;"><a href="/{$_root.gameid}/stats/{$_key}">{printsprite $_key $_root.generation $_root.gameid}<br />{$_root.data.Pokemon.$_key.name}</a><br /><a title="{implode(', ', $flags)}">L{$minlevel} - {$maxlevel}</a></div>{if $.loop.default.index % 2 == 1}<br />{/if}
{/loop}
				</td>
{/loop}
			</tr>
		</tbody>
	</table>
{/block}