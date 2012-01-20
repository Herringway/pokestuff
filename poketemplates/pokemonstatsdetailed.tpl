{extends barebones.tpl}
{block "title"}#{$pokemon.id} - {$pokemon.name}{/block}
{block "data"}
{template natureprint stat}
Negative Nature: {math("$stat*0.9", "%u")}&lt;br /&gt;
Neutral Nature: {$stat}&lt;br /&gt;
Positive Nature: {math("$stat*1.1", "%u")}&lt;br /&gt;
{/template}
<script type="text/javascript" src="http://elpenguino.net/js/jquery.js"></script>
<script type="text/javascript" src="http://elpenguino.net/js/jquery.tools.min.js"></script>
<script type="text/javascript" src="http://elpenguino.net/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="http://elpenguino.net/js/jquery.dataTables.min.plugin.js"></script> 
<script type="text/javascript">
	$(function() {
		$("[title]").tooltip({ position: 'center right'});
		$('#movelist').dataTable( {
		"bPaginate": false,
		"bFilter": false,
		"bInfo": false,
		"bStateSave": false,
		"aoColumns": [{ "sType": "title-string" }, null, null, { "sType": "percent" }, null, { "sType": "title-string" }, null, { "sType": "title-string" }]
		} ).fnSetFilteringDelay();
	});
</script>
{with $pokemon}
<img src="/bw-sprites/{$id}.png"><img src="/bw-sprites/shiny/{$id}.png"><br />
<img src="/bw-sprites/back/{$id}.png"><img src="/bw-sprites/back/shiny/{$id}.png"><br />
{$name}<br />
{$species}<br />
{loop $pokedexENG}
{$game}: {nl2br($entry)|whitespace}<br /><br />
{/loop}
<img width="32" height="16" src="http://pkmn.elpenguino.net/common/type_images/{getTypeIMG($type1)}.gif">
<img width="32" height="16" src="http://pkmn.elpenguino.net/common/type_images/{getTypeIMG($type2)}.gif"><br />
<a title="{nl2br(getAbilityDesc($ability1))|whitespace}">{getAbility($ability1)}</a>/<a title="{nl2br(getAbilityDesc($ability2))|whitespace}">{getAbility($ability2)}</a>/<a title="{nl2br(getAbilityDesc($ability3))|whitespace}">{getAbility($ability3)}</a><br />
{loop $evolutions}
Evolves into <a href="/stats/{$target}">{getPokeName($target)}</a> ({$argument})<br />
{/loop}
EVs: {$EVs}<br />
{if $formnames}Forms:{/if}
{foreach $formnames val implode=", "}{$val.name}{/foreach}
<table>
	<thead>
		<tr>
			<th></th><th>HP</th><th>Attack</th><th>Defense</th><th>Special Attack</th><th>Special Defense</th><th>Speed</th><th>Total</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>Base</td>
			<td class="def">{$hp}</td>
			<td class="atk">{$atk}</td>
			<td class="def">{$def}</td>
			<td class="atk">{$satk}</td>
			<td class="def">{$sdef}</td>
			<td class="atk">{$speed}</td>
			<td class="tot">{math("$hp+$atk+$def+$satk+$sdef+$speed")}</td>
		</tr>
		<tr>
			<td>Min (L100)</td>
			<td class="def">{math("2*$hp+110")}</td>
			<td class="atk" title="{natureprint math("2*$atk+5")}">{math("2*$atk+5")}</td>
			<td class="def" title="{natureprint math("2*$def+5")}">{math("2*$def+5")}</td>
			<td class="atk" title="{natureprint math("2*$satk+5")}">{math("2*$satk+5")}</td>
			<td class="def" title="{natureprint math("2*$sdef+5")}">{math("2*$sdef+5")}</td>
			<td class="atk" title="{natureprint math("2*$speed+5")}">{math("2*$speed+5")}</td>
			<td class="tot">{math("($hp+$atk+$def+$satk+$sdef+$speed)*2+135")}</td>
		</tr>
		<tr>
			<td>Max (L100)</td>
			<td class="def">{math("2*$hp+204")}</td>
			<td class="atk" title="{natureprint math("2*$atk+99")}">{math("2*$atk+99")}</td>
			<td class="def" title="{natureprint math("2*$def+99")}">{math("2*$def+99")}</td>
			<td class="atk" title="{natureprint math("2*$satk+99")}">{math("2*$satk+99")}</td>
			<td class="def" title="{natureprint math("2*$sdef+99")}">{math("2*$sdef+99")}</td>
			<td class="atk" title="{natureprint math("2*$speed+99")}">{math("2*$speed+99")}</td>
			<td class="tot">{math("($hp+$atk+$def+$satk+$sdef+$speed)*2+699")}</td>
		</tr>
	</tbody>
</table>
<table id="movelist">
	<thead>
		<tr>
			<th>Level</th>
			<th>Name</th>
			<th>Power</th>
			<th>Accuracy</th>
			<th>Priority</th>
			<th>Type</th>
			<th>Description</th>
			<th>Cat.</th>
		</tr>
	</thead>
	<tbody>
{printmoves $moves_g5}
	</tbody>
</table>
{/with}
{/block}