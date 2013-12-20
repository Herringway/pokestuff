{%extends "base.tpl"%}
{%block title%}Random Team Generator{%endblock%}
{%block data%}
		<table class="randpoke" style="position: relative;">
			<tr>
				<th colspan="6">Randomly Generated Pokemon Team</th>
			</tr>
			<tr>
			{%for poke in randteam%}
				<td style="text-align: center; width: 150px;">
					{{macros.pokemonsprite(generation,spriteseries,poke.imgid,poke.name)}}<br /><a href="/{{gameid}}/stats/{{poke.id}}">{{poke.name}}</a>
				</td>
{%endfor%}
			</tr>
		</table>
{%endblock%}