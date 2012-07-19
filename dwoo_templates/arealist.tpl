{extends "barebones.tpl"}
{block "title"}Pokemon Gen V Encounters{/block}
{block "data"}
{loop $data.Areas}
	<table id="encounters">
		<thead>
			<tr>
				<th colspan="{count($Encounters)}"><a href="/{$_root.gameid}/areas/{$id}">{$name}</a></th>
			</tr>
			<tr>{loop $Encounters}
				<th>{$_key}</th>{/loop}
			</tr>
		</thead>
		<tbody>
			<tr>
{loop $Encounters}
				<td style="vertical-align: top; text-align: center; width: 260px;">
{loop $}
			<div style="width: 128px; display: inline-block; text-align: center;"><a href="/{$_root.gameid}/stats/{$_key}">{$_root.data.Pokemon.$_key.name}</a><br /><a title="{implode(', ', $flags)}">L{$minlevel} - {$maxlevel}</a></div>{if $.loop.default.index % 2 == 1}<br />{/if}
{/loop}
				</td>
{/loop}
			</tr>
		</tbody>
	</table>
{/block}