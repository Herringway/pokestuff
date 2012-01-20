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
{loop $textlines}
					LINE #{string_format($linenumber, '%04d')}: {$text}<br/>
{/loop}
					</td>
{/loop}
				</tr>
{/loop}
			</tbody>
		</table>
{/block}