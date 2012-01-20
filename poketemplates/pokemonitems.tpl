{extends "barebones.tpl"}
{block "title"}POKEYMANS ITEMS{/block}
{block "data"}
		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/jquery.tools.min.js"></script>
		<script type="text/javascript" src="/js/jquery.dataTables.min.js"></script> 
		<script type="text/javascript" src="/js/jquery.dataTables.min.plugin.js"></script> 
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
			});
			$(document).ready(function() {
				$('#pokeitems').dataTable( {
				"bPaginate": false,
				"bStateSave": true,
				} );
			} );
		</script>

		<table id="pokeitems">
			<thead>
				<tr>
					{loop $headers}<th>{$name}</th>{/loop}
				</tr>
			</thead>
			<tbody>
{loop $items}
				<tr>
					<td>{$id}</td>
					<td><a href="/pkmn/items/{$id}" title="{$desc}">{$name}</a></td>
					<td>{$unknown1}</td>
					<td>{$unknown2}</td>
					<td>{$unknown3}</td>
					<td>{string_format($unknownflags, '%016b')}</td>
					<td>{$unknown4}</td>
					<td>{$unknown5}</td>
					<td>{$unknown6}</td>
					<td>{$unknown7}</td>
					<td>{$unknown8}</td>
					<td>{$unknown9}</td>
					<td>{string_format($unknown10, '%056b')}</td>
					<td>{$unknown11}</td>
					<td>{$unknown12}</td>
					<td>{$unknown13}</td>
					<td>{$unknown14}</td>
					<td>{$unknown15}</td>
					<td>{$unknown16}</td>
					<td>{$unknown17}</td>
					<td>{$unknown18}</td>
				</tr>
{/loop}
		</tbody>
		</table>
{/block}