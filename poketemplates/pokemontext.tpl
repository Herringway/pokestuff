{extends "barebones.tpl"}
{block "title"}POKEYMANS BW TEXT{/block}
{block "data"}
		<table id="poketext">
			<tbody>
{loop $textfiles}
				<tr>
					<th colspan="2">TEXT FILE #{$id}</th>
				</tr>
				<tr>
{loop $sections}
					<td>
{loop $}
					LINE #{string_format($, '%04d')}: {$text}<br/>
{/loop}
					</td>
{/loop}
				</tr>
{/loop}
			</tbody>
		</table>
{/block}