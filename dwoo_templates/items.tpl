{extends "barebones.tpl"}
{block "title"}{if (count($data) > 1)}Items{else}{$data.0.name}{/if}{/block}
{block "data"}{if (count($data) > 1)}
		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/jquery.tools.min.js"></script>
		<script type="text/javascript" src="/js/jquery.dataTables.min.js"></script> 
		<script type="text/javascript" src="/js/jquery.dataTables.min.plugin.js"></script> 
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
			});
			$(document).ready(function() {
				$('#pokemoves').dataTable( {
				"bPaginate": false,
				"bStateSave": true,
				"aoColumns": [{ "sType": "num-html" }, null, { "sType": "title-string" }, { "sType": "title-string" }, null, null, null, null, null]
				} );
			} );
		</script>

		<table id="pokemoves">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Value</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
{loop $data}
				<tr>
					<td><a href="/{$_root.gameid}/items/{$id}">{$id}</a></td>
					<td><a href="/{$_root.gameid}/items/{$id}">{$name}</a></td>
					<td>{$value}</td>
					<td>{$description}</td>
				</tr>
{/loop}
		</tbody>
		</table>{else}
{dump $data}{/if}
{/block}