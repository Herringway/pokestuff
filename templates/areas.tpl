{%extends "base.tpl"%}
{%block title%}Wild Pokemon{%endblock%}
{%block data%}{%if areas|length > 1%}
{%for encounter in areas%}
	<table id="encounters">
		<thead>
			<tr>
				<th colspan="{{encounter.Encounters|length}}"><a href="/{{gameid}}/areas/{{encounter.id}}">{{encounter.name}}</a></th>
			</tr>
			<tr>{%for area,enc in encounter.Encounters%}
				<th>{{area}}</th>{%endfor%}
			</tr>
		</thead>
		<tbody>
			<tr>
{%for area,enc in encounter.Encounters%}
				<td style="vertical-align: top; text-align: center;">
{%for pokeid,poke in enc%}
					<div style="display: inline-block; text-align: center;">
						<a href="/{{gameid}}/stats/{{pokeid}}">{{macros.pokemonsprite(generation,gameid,pokeid)}}<br />{{stats[pokeid].name}}</a><br />
						<a title="{{flags|join(', ')}}">L{{poke.minlevel}} - {{poke.maxlevel}}</a>
					</div>
{%if loop.index0 % 2 == 1%}
					<br />
{%endif%}
{%endfor%}
				</td>
{%endfor%}
			</tr>
		</tbody>
	</table>
{%endfor%}
{%else%}
	<table id="encounters">
		<thead>
			<tr>
				<th colspan="{{areas.0.Encounters|length}}">{{areas.0.name}}</th>
			</tr>
			<tr>{%for area,enc in areas.0.Encounters%}
				<th>{{area}}</th>{%endfor%}
			</tr>
		</thead>
		<tbody>
			<tr>
{%for area,enc in areas.0.Encounters%}
				<td style="vertical-align: top; text-align: center;">
{%for pokeid,poke in enc%}
					<div style="display: inline-block; text-align: center;">
						<a href="/{{gameid}}/stats/{{pokeid}}">{{macros.pokemonsprite(generation,gameid,pokeid)}}<br />{{stats[pokeid].name}}</a><br />
						<a title="{{flags|join(', ')}}">L{{poke.minlevel}} - {{poke.maxlevel}}</a>
					</div>
{%if loop.index0 % 2 == 1%}
					<br />
{%endif%}
{%endfor%}
				</td>
{%endfor%}
			</tr>
		</tbody>
	</table>
{%endif%}
{%endblock%}