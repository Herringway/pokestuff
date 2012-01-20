{template printmoves moves}
{loop $moves}
		<tr>
			<td title="{$.loop.default.iteration}">{$learned}</td>
			<td><a href="http://pkmn.elpenguino.net/moves/{$data.id}">{getMove($data.id)}</a></td>
			<td>{$data.power}</td>
			<td>{$data.accuracy}{if $data.accuracy != '-'}%{/if}</td>
			<td>{$data.priority}</td>
			<td style="text-align: center;"><img title="{$data.type}" src="http://pkmn.elpenguino.net/common/type_images/{lower($data.type)}.gif"/></td>
			<td>{getMoveDesc($data.id)|whitespace}</td>
			<td style="text-align: center;"><img title="{$data.category}" src="http://pkmn.elpenguino.net/common/type_images/{lower($data.category)}.png"/></td>
		</tr>
{/loop}
{/template}
{template printsprite id pokename="" htmlid="" back="" shiny=""}
<img {if $htmlid}id="{$htmlid}" {/if}src="/bw-sprites/{if $back}back/{/if}{if $shiny}shiny/{/if}{$id}.png" width="96" height="96" title="{$pokename}">{/template}

{template printanisprite pokeid pokename="" htmlid=""}
<img {if $htmlid}id="{$htmlid}" {/if}src="/bw-sprites/ani/{string_format($pokeid, '%03u')}.gif" title="{optional $pokename}" />{/template}

{template printencountershort data}
{loop $data}
				<td><a href="/stats/{$id}">{printsprite $id $pokemon}<br />{$pokemon}</a><br />L{$level.min} - {$level.max}</td>
{/loop}
{/template}
{template printencounters data width=2}
					<table style="text-align: center;">
						<tr>
{loop $data}
							{if ((($_key%$_.width) == 0) && ($_key != 0))}</tr><tr>
							{/if}<td><a href="/stats/{$id}">{printsprite $id $pokemon 'i'}<br />{$pokemon}</a><br /><a title="{$flags}">L{$level.min} - {$level.max}</a></td>
{/loop}
						</tr>
					</table>
{/template}

{template pokemonselector pokelist}
{foreach $pokelist key val}
<option value="{math("$key+1")}">{$val}</option>
{/foreach}
{/template}