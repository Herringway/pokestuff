{extends "barebones.tpl"}
{block "title"}POKEYMANS MOVES{/block}
{block "data"}
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.js"></script>
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.tools.min.js"></script>
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.dataTables.min.js"></script> 
		<script type="text/javascript" src="http://elpenguino.net/js/jquery.dataTables.min.plugin.js"></script> 
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
			});
			$(document).ready(function() {
				$('#pokemoves').dataTable( {
				"bPaginate": false,
				"bStateSave": true,
				} );
			} );
		</script>

		<table id="pokemoves">
			<thead>
				<tr>
					{loop $headers}{if (($display != 'debug') || ($_.debug))}<th>{$name}</th>{/if}{/loop}
				</tr>
			</thead>
			<tbody>
{loop $moves}
				<tr>
					<td>{$id}</td>
					<td><a href="/pkmn/moves/{$id}" title="{$effect_string}">{getMove($id)}</a></td>
					<td><img width="32" height="16" src="http://pkmn.elpenguino.net/common/type_images/{$type}.gif"></td>
					<td>{$internal_category}</td>
					<td>{$category}</td>
					<td>{$power}</td>
					<td>{$accuracy}</td>
					<td>{$pp}</td>
					<td>{$priority}</td>
					<td>{$unknown}</td>
					<td>{$unknown2}</td>
					<td>{$unknown3}</td>
					<td>{$unknown4}</td>
					<td>{getMoveDesc($id)}</td>
					<td>{$unknown5}</td>
					<td><a title="{foreach $flags_readable key item}{if $item == 1}{$key}<br />{/if}{/foreach}">{string_format($flags,'%016b')}</a></td>
				</tr>
{/loop}
		</tbody>
		</table>
{/block}