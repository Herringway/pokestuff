{%extends "base.tpl"%}
{%block title%}Random Team Generator{%endblock%}
{%block data%}
{{dump()}}
		<table>
			<tr>
			{%for poke in randteam%}
				<td style="text-align: center; width: 150px; background: white;">
					{{macros.pokemonsprite(generation,gameid,poke.imgid,poke.name)}}<br /><a href="/{{gameid}}/stats/{{poke.id}}">{{poke.name}}</a>
				</td>
{%endfor%}
			</tr>
		</table>
{%endblock%}