{%extends "base.tpl"%}
{%block title%}Wild Pokemon{%endblock%}
{%block data%}{%if data.Areas|length > 1%}
{%for encounter in data.Areas%}
	<table id="encounters">
		<thead>
			<tr>
				<th colspan="{{encounter.Encounters|length}}">{{encounter.name}}</th>
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
						<a href="/{{gameid}}/stats/{{pokeid}}">{{macros.pokemonsprite(generation,gameid,pokeid)}}<br />{{data.Pokemon[pokeid].name}}</a><br />
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
				<th colspan="{{data.Areas.0.Encounters|length}}">{{data.Areas.0.name}}</th>
			</tr>
			<tr>{%for area,enc in data.Areas.0.Encounters%}
				<th>{{area}}</th>{%endfor%}
			</tr>
		</thead>
		<tbody>
			<tr>
{%for area,enc in data.Areas.0.Encounters%}
				<td style="vertical-align: top; text-align: center;">
{%for pokeid,poke in enc%}
					<div style="display: inline-block; text-align: center;">
						<a href="/{{gameid}}/stats/{{pokeid}}">{{macros.pokemonsprite(generation,gameid,pokeid)}}<br />{{data.Pokemon[pokeid].name}}</a><br />
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