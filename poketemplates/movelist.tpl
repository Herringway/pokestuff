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
					<th>ID</th>
					<th>Name</th>
					<th>Type</th>
					<th>Category</th>
					<th>Power</th>
					<th>Accuracy</th>
					<th>PP</th>
					<th>Priority</th>
					<th>Description</th>
					<th>Flags</th>
				</tr>
			</thead>
			<tbody>
{loop $data}
				<tr>
					<td>{$id}</td>
					<td><a href="/{$_root.gameid}/moves/{$id}">{$name}</a></td>
					<td><img width="32" height="16" src="/images/{$_root.generation}/types/{$typeid}.png"></td>
					<td>{$category}</td>
					<td>{$power}</td>
					<td>{$accuracy}</td>
					<td>{$pp}</td>
					<td>{$priority}</td>
					<td>{$desc}</td>
					<td><a title="{foreach $flags_readable key item}{if $item == 1}{$key}<br />{/if}{/foreach}">{string_format($flags,'%016b')}</a></td>
				</tr>
{/loop}
		</tbody>
		</table>
{/block}