{extends "barebones.tpl"}
{block "title"}Pokemon B/W Stats{/block}
{block "data"}
		<script type="text/javascript" src="/js/jquery.js"></script>
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
					{loop $data.0.items}
					<th>{upper($_key)}</th>
					{/loop}
					<th>EGG GROUP 1</th>
					<th>EGG GROUP 2</th>
					{loop $data.0.abilities}
					<th>{upper($_key)}</th>
					{/loop}
				</tr>
			</thead>
			<tbody>
{loop $data}
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
					{loop $items}
					<td><a title="{nl2br($desc)|whitespace}">{$name}</a></td>
					{/loop}
					<td>{$egggrp1}</td>
					<td>{$egggrp2}</td>
					{loop $abilities}
					<td><a title="{nl2br($desc)|whitespace}">{$name}</a></td>
					{/loop}
				</tr>
{/loop}
		</tbody>
		</table>
{/block}