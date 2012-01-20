{extends "barebones.tpl"}
{block "title"}Pokemon B/W Stats{/block}
{block "data"}
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.js"></script>
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.tools.min.js"></script>
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.dataTables.min.js"></script> 
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.dataTables.min.plugin.js"></script> 
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
					<th>COMMON ITEM</th>
					<th>RARE ITEM</th>
					<th>HIDDEN ITEM</th>
					<th>EGG GROUP 1</th>
					<th>EGG GROUP 2</th>
					<th>ABILITY 1</th>
					<th>ABILITY 2</th>
					<th>HIDDEN ABILITY</th>
				</tr>
			</thead>
			<tbody>
{loop $pokemon}
				<tr>
					<td>{$id}</td>
					<td><a href="http://pkmn.elpenguino.net/stats/{$id}">{$name}</a></td>
					<td>{$hp}</td>
					<td>{$atk}</td>
					<td>{$def}</td>
					<td>{$satk}</td>
					<td>{$sdef}</td>
					<td>{$speed}</td>
					<td>{math("$speed+$sdef+$satk+$def+$atk+$hp")}</td>
					<td><img width="32" height="16" src="http://pkmn.elpenguino.net/common/type_images/{getTypeIMG($type1)}.gif" title="{$type1}"></td>
					<td><img width="32" height="16" src="http://pkmn.elpenguino.net/common/type_images/{getTypeIMG($type2)}.gif" title="{$type2}"></td>
					<td>{string_format(math("(((1+(2*$hp+204)*$capturerate)/((2*$hp+204)*3))/256)*100"),'%01.1f')}%</td>
					<td><a title="{nl2br(getItemDesc($comitemID))|whitespace}">{getItem($comitemID)}</a></td>
					<td><a title="{nl2br(getItemDesc($rareitemID))|whitespace}">{getItem($rareitemID)}</a></td>
					<td><a title="{nl2br(getItemDesc($dreamitemID))|whitespace}">{getItem($dreamitemID)}</a></td>
					<td>{$egggrp1}</td>
					<td>{$egggrp2}</td>
					<td><a title="{nl2br(getAbilityDesc($ability1))|whitespace}">{getAbility($ability1)}</a></td>
					<td><a title="{nl2br(getAbilityDesc($ability2))|whitespace}">{getAbility($ability2)}</a></td>
					<td><a title="{nl2br(getAbilityDesc($ability3))|whitespace}">{getAbility($ability3)}</a></td>
				</tr>
{/loop}
		</tbody>
		</table>
{/block}