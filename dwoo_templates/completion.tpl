{extends "barebones.tpl"}
{block "title"}Module Completion Status{/block}
{block "data"}
		<table>
			<tr>
				<th>What</th>
				<th>Status</th>
			</tr>
{loop $data}
			<tr>
				<td><a href="/{$_root.gameid}/{$mod}">{$_key}</a></td>
				<td>{implode(', ', $status)}</td>
			</tr>{/loop}
		</table>
{/block}