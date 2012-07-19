{extends "barebones.tpl"}
{block "title"}{if (count($data) > 1)}Move List{else}{$data.0.name}{/if}{/block}
{block "data"}
{if (count($data) > 1)}
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
					<th>Type</th>
					<th>Category</th>
					<th>Power</th>
					<th>Accuracy</th>
					<th>PP</th>
					<th>Priority</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
{loop $data}
				<tr>
					<td><a href="/{$_root.gameid}/moves/{$id}">{$id}</a></td>
					<td><a href="/{$_root.gameid}/moves/{$id}">{$name}</a></td>
					<td>{printtypeimage $typeid}</td>
					<td>{printcategoryimage $category}</td>
					<td>{$power}</td>
					<td>{$accuracy}</td>
					<td>{$pp}</td>
					<td>{$priority}</td>
					<td>{$description}</td>
				</tr>
{/loop}
		</tbody>
		</table>{else}
{dump $data}
{/if}
{/block}