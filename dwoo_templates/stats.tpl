{extends barebones.tpl}
{block "title"}{if (count($data.stats) > 1)}Stats{else}#{$data.stats.0.id} - {$data.stats.0.name}{/if}{/block}
{block "data"}
{template natureprint stat}
Negative Nature: {math("$stat*0.9", "%u")}&lt;br /&gt;
Neutral Nature: {$stat}&lt;br /&gt;
Positive Nature: {math("$stat*1.1", "%u")}&lt;br /&gt;
{/template}
{if (count($data.stats) > 1)}		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/jquery.tools.min.js"></script>
		<script type="text/javascript" src="/js/jquery.dataTables.min.js"></script> 
		<script type="text/javascript" src="/js/jquery.dataTables.min.plugin.js"></script> 
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
				$('#pokemon').dataTable( {
				"bPaginate": false,
				"sPaginationType": "full_numbers",
				"bStateSave": true,
				"aoColumns": [null, null, null, null, null, null, null, null, null, { "sType": "title-string" }, { "sType": "title-string" }, { "sType": "percent" }, null, null, null, null, null, null, null, null]
				} ).fnSetFilteringDelay();
			});

		</script>
		<table id="pokemon">
			<thead>
				<tr>
					<th>ID</th>
					<th>NAME</th>
					<th>HP</th>
					<th>ATK</th>
					<th>DEF</th>
					<th>SP. ATK</th>
					<th>SP.DEF</th>
					<th>SPEED</th>
					<th>TOTAL</th>
					<th>TYPE 1</th>
					<th>TYPE 2</th>
					<th>CAPTURE RATE</th>
{loop $data.stats.0.items}					<th>{upper($_key)}</th>
{/loop}					<th>EGG GROUP 1</th>
					<th>EGG GROUP 2</th>
{loop $data.stats.0.abilities}					<th>{upper($_key)}</th>
{/loop}				</tr>
			</thead>
			<tbody>
{loop $data.stats}
				<tr>
					<td>{$id}</td>
					<td><a href="/{$_root.gameid}/stats/{$id}">{$name}</a></td>
					<td>{$hp}</td>
					<td>{$atk}</td>
					<td>{$def}</td>
					<td>{$satk}</td>
					<td>{$sdef}</td>
					<td>{$speed}</td>
					<td>{math("$speed+$sdef+$satk+$def+$atk+$hp")}</td>
					<td><img width="32" height="16" src="/images/{$_root.generation}/types/{$type1}.png" title="{$type1}"></td>
					<td><img width="32" height="16" src="/images/{$_root.generation}/types/{$type2}.png" title="{$type2}"></td>
					<td>{string_format(math("(((1+(2*$hp+204)*$capturerate)/((2*$hp+204)*3))/256)*100"),'%01.1f')}%</td>
{loop $items}					<td><a title="{nl2br($_root.data.items.$.description)|whitespace}">{$_root.data.items.$.name}</a></td>
{/loop}					<td>{$egggrp1}</td>
					<td>{$egggrp2}</td>
{loop $abilities}					<td><a title="{nl2br($_root.data.abilities.$.description)|whitespace}">{$_root.data.abilities.$.name}</a></td>
{/loop}
				</tr>
{/loop}
		</tbody>
		</table>{else}
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
		"aoColumns": [{ "sType": "title-string" }, null, null, null, null, null, null, null]
		} ).fnSetFilteringDelay();
	});
</script>
{if $debug}{dump $data}{/if}
{with $data.stats.0}
<table>
	<thead>
		<tr>
			<th rowspan="2">
				{printsprite $imgid $_root.generation $_root.gameid $name}{printsprite $imgid $_root.generation $_root.gameid $name shiny=y}<br />
				{printsprite $imgid $_root.generation $_root.gameid $name back=y}{printsprite $imgid $_root.generation $_root.gameid $name shiny=y back=y}</th><td colspan="7">
				{$name}<br />
				{printtypeimage $type1 $_root.generation}{if $type1 != $type2}{printtypeimage $type2 $_root.generation}{/if}<br />
				{$species}<br />
				{nl2br($pokedex)|whitespace}<br />
				{loop $abilities}{if !$.loop.default.first}{if ($ != 0)}/{/if}{/if}<a title="{nl2br($_root.data.abilities.$.description)|whitespace}">{$_root.data.abilities.$.name}</a>{/loop}<br />
				{loop $evolutions}
				Evolves into <a href="/{$_root.gameid}/stats/{$Target}">{$name}</a> ({$Argument})<br />
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
		<tr>
			<th>Learned</th>
			<th>Name</th>
			<th>Power</th>
			<th>Accuracy</th>
			<th>Priority</th>
			<th>Description</th>
			<th>Type</th>
			<th>Cat.</th>
		</tr>
	</thead>
	<tbody>{loop $moves}{loop $}
		<tr>
		<td><a title="{counter}">{$Learned}</a></td>
		<td><a href="/{$_root.gameid}/moves/{$id}">{$_root.data.moves.$id.name}</a></td>
		<td>{$_root.data.moves.$id.power}</td>
		<td>{if $_root.data.moves.$id.accuracy == 101}-{else}{$_root.data.moves.$id.accuracy}%{/if}</td>
		<td>{$_root.data.moves.$id.priority}</td>
		<td>{$_root.data.moves.$id.description}</td>
		<td>{printtypeimage $_root.data.moves.$id.type $_root.generation}</td>
		<td>{printcategoryimage $_root.data.moves.$id.category $_root.generation}</td>
		</tr>
{/loop}{/loop}
	</tbody>
</table>
{/with}{/if}
{/block}