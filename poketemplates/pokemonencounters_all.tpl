{extends "barebones.tpl"}
{block "title"}Pokemon Gen V Encounters{/block}
{block "data"}
	<table>
		<thead>
			<tr>
				<th>Area</th>
				<th colspan="12">Grass</th>
				<th colspan="12">Deep Grass</th>
				<th colspan="12">Shaking/Dusty</th>
				<th colspan="10">Fishing</th>
				<th colspan="10">Surfing</th>
			</tr>
		</thead>
		<tbody>
{loop $encounters}
			<tr>
				<td><a href="/wild/{$areaid}">{$area}</a></td>
{loop $enc.encounters}
{printencountershort $grass}
{printencountershort $deepgrass}
{printencountershort $uncommon}
{printencountershort $fishing}
{printencountershort $surfing}
{/loop}
			</tr>
{/loop}
		</tbody>
	</table>
{/block}