{extends "barebones.tpl"}
{block "title"}Random Team Generator{/block}
{block "data"}
		<table>
			<tr>
			{loop $data}
				<td style="text-align: center; width: 150px; background: white;">
					{printsprite $imgid $_root.generation $_root.gameid $name}<br /><a href="/{$_root.gameid}/stats/{$id}">{$name}</a>
				</td>
			{/loop}
			</tr>
		</table>
{/block}