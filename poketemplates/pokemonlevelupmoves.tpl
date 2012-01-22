{extends "barebones.tpl"}
{block "title"}POKEYMANS LEARNED MOVES{/block}
{block "data"}
		<table id="pokemoves">
{loop $pokemondata}
			<thead>
				<tr>
					<th colspan="2">{$pokemon}</th>
				</tr>
			</thead>
			<tbody>
{loop $moves}
				<tr>
					<td>{getMove($move)}</td>
					<td>{$learned}</td>
				</tr>
{/loop}
		</tbody>
{/loop}
		</table>
{/block}