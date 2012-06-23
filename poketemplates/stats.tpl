{extends barebones.tpl}
{block "title"}#{$data.id} - {$data.name}{/block}
{block "data"}
{template natureprint stat}
Negative Nature: {math("$stat*0.9", "%u")}&lt;br /&gt;
Neutral Nature: {$stat}&lt;br /&gt;
Positive Nature: {math("$stat*1.1", "%u")}&lt;br /&gt;
{/template}
<script type="text/javascript" src="/js/jquery.js"></script>
<script type="text/javascript" src="/js/jquery.tools.min.js"></script>
<script type="text/javascript" src="/js/jquery.dataTables.min.js"></script> 
<script type="text/javascript" src="/js/jquery.dataTables.min.plugin.js"></script> 
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
{with $data}
<table>
	<thead>
		<tr>
			<th rowspan="2">
<img src="/images/{$_root.generation}/{$_root.gameid}/{$id}.png"><img src="/images/{$_root.generation}/{$_root.gameid}/shiny/{$id}.png"><br />
<img src="/images/{$_root.generation}/{$_root.gameid}/back/{$id}.png"><img src="/images/{$_root.generation}/{$_root.gameid}/back/shiny/{$id}.png"></th><td colspan="7">
{$name}<br />
<img width="32" height="16" src="/images/{$_root.generation}/types/{$type1}.png">
<img width="32" height="16" src="/images/{$_root.generation}/types/{$type2}.png"><br />
{$species}<br />
{nl2br($pokedex)|whitespace}<br />
{loop $abilities}<a title="{nl2br($desc)|whitespace}">{$name}</a>{if !$.loop.default.last}/{/if}{/loop}<br />
{loop $evolutions}
Evolves into <a href="/stats/{$target}">{$name}</a> ({$argument})<br />
{/loop}
EVs: {kimplode($EVs)}<br />
{if $formnames}Forms:{/if}
{foreach $formnames val implode=", "}{$val.name}{/foreach}</td></tr><tr><th>HP</th><th>Attack</th><th>Defense</th><th>Special Attack</th><th>Special Defense</th><th>Speed</th><th>Total</th>
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
		<tr>{loop $moves.0}{if $_key != 'id'}
			<th>{$_key}</th>
{/if}{/loop}
		</tr>
	</thead>
	<tbody>{loop $moves}
		<tr>{loop $}{if $_key != 'id'}<td>{if $_key == 'Name'}<a href="/{$_root.gameid}/moves/{$_.id}">{/if}{$}{if $_key == 'Name'}</a>{/if}</td>{/if}{/loop}</tr>
{/loop}
	</tbody>
</table>
{/with}
{dump $}
{/block}