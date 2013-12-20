{%extends "base.tpl"%}
{%block title%}{%if trainers|length > 1%}Trainers{%else%}{{trainers.0.class}} {{trainers.0.name}}{%endif%}{%endblock%}
{%block data%}
{%if trainers|length > 1%}
		<table border="1" style="text-align: center;">
{%for trainer in trainers%}
			<tr>
				<td><a href="/{{gameid}}/trainers/{{trainer.id}}">{{trainer.class}} {{trainer.name}}</a><br />{%if trainer.battletype%}{{trainer.battletype}}<br />{%endif%}{%for item in trainer.items%}{{items[item].name}}<br />{%endfor%}</td>
{%for poke in trainer.pokemon%}
				<td><a href="/{{gameid}}/stats/{{poke.ID}}">{{macros.pokemonsprite(generation,spriteseries,poke.ID,stats[poke.ID].name)}}<br/>L{{poke.Level}} {{stats[poke.ID].name}}</a></td>
{%endfor%}
			</tr>
{%endfor%}
		</table>{%else%}{{dump(trainers)}}
		{{trainers.0.class}} {{trainers.0.name}}<br />
{%if trainers.0.items%}		Carries {%for item in trainers.0.items%}{{items[item].name}}{%if loop.last != true%}, {%endif%}{%endfor%}{%endif%}
		<table style="text-align: center;">
			<tr>
{%for poke in trainers.0.pokemon%}
				<td style="background: white; width: 150px;">
					<a href="/{{gameid}}/stats/{{poke.ID}}">{{macros.pokemonsprite(generation,spriteseries,poke.ID)}}<br />
					{{stats[poke.ID].name}}<br /></a>
					Level {{poke.Level}}<br />
					Holding: {{items[poke.Item].name|default('Nothing')}}<br />
{%for move in poke.Moves%}
					{{moves[move].name}}<br />
{%endfor%}
				</td>
{%endfor%}
			</tr>
		</table>
{%endif%}
{%endblock%}