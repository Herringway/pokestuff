{extends "barebones.tpl"}
{block "title"}Random Team Generator{/block}
{block "data"}
		<table>
			<tr>
			{loop $randompoke}
				<td style="text-align: center; width: 150px; background: white;">
					{printsprite $}<br />
					{getpokename($)}
				</td>
			{/loop}
			</tr>
		</table>
{/block}