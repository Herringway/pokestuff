{%extends "base.tpl"%}
{%block title%}{%if data.Trainers|length > 1%}Trainers{else}{{data.Trainers.0.class}} {{data.Trainers.0.name}}{%endif%}{%endblock%}
{%block data%}
{%if data.Trainers|length > 1%}
		<table border="1" style="text-align: center;">
{%for trainer in data.Trainers%}
			<tr>
				<td><a href="/{{gameid}}/trainers/{{trainer.id}}">{{trainer.class}} {{trainer.name}}</a><br />{%if trainer.battletype%}{{trainer.battletype}}<br />{%endif%}{%for item in trainer.items%}{{data.Items[item].name}}<br />{%endfor%}</td>
{%for poke in trainer.pokemon%}
				<td><a href="/{{gameid}}/stats/{{poke.id}}">{{macros.pokemonsprite(generation,gameid,poke.id,data.Pokemon[poke.id].name)}}<br/>L{{poke.level}} {{data.Pokemon[poke.id].name}}</a></td>
{%endfor%}
			</tr>
{%endfor%}
		</table>{%else%}{{dump(data)}}
		{{data.Trainers.0.class}} {{data.Trainers.0.name}}<br />
{%if data.Trainers.0.items%}		Carries {%for item in data.Trainers.0.items%}{{data.Items[item].name}}{%if loop.last != true%}, {%endif%}{%endfor%}{%endif%}
		<table style="text-align: center;">
			<tr>
{%for poke in data.Trainers.0.pokemon%}
				<td style="background: white; width: 150px;">
					<a href="/{{gameid}}/stats/{{poke.id}}">{{macros.pokemonsprite(generation,gameid,poke.id)}}<br />
					{{data.Pokemon[poke.id].name}}<br /></a>
					Level {{poke.level}}<br />
					Holding: {{data.Items[poke.item].name|default('Nothing')}}<br />
{%for move in poke.move%}
					{{data.Moves[move].name}}<br />
{%endfor%}
				</td>
{%endfor%}
			</tr>
		</table>
{%endif%}
{%endblock%}