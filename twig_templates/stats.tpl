{% extends "base.tpl" %}
{%block title%}{%if data.stats|length > 1%}Stats{%else%}#{{data.stats.0.id}} - {{data.stats.0.name}}{%endif%}{%endblock%}
{%block data%}
{%if data.stats|length > 1%}
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
				$('#pokemon').dataTable( {
				"bPaginate": false,
				"sPaginationType": "full_numbers",
				"bStateSave": true,
				"aoColumns": [null, null, null, null, null, null, null, null, null, { "sType": "title-string" }, { "sType": "title-string" }, { "sType": "percent" }, null, null, null, null, null, null, null, null]
				} ).fnSetFilteringDelay();
			});

		</script>
		<table id="pokemon">
			<thead>
				<tr>
					<th>ID</th>
					<th>NAME</th>
					<th>HP</th>
					<th>ATK</th>
					<th>DEF</th>
					<th>SP. ATK</th>
					<th>SP.DEF</th>
					<th>SPEED</th>
					<th>TOTAL</th>
					<th>TYPE 1</th>
					<th>TYPE 2</th>
					<th>CAPTURE RATE</th>
{%for key in data.stats.0.items|keys%}					<th>{{key}}</th>
{%endfor%}					<th>EGG GROUP 1</th>
					<th>EGG GROUP 2</th>
{%for key in data.stats.0.abilities|keys%}					<th>{{key}}</th>
{%endfor%}				</tr>
			</thead>
			<tbody>
{%for stats in data.stats%}
				<tr>
					<td>{{stats.id}}</td>
					<td><a href="/{{gameid}}/stats/{{stats.id}}">{{stats.name}}</a></td>
					<td>{{stats.hp}}</td>
					<td>{{stats.atk}}</td>
					<td>{{stats.def}}</td>
					<td>{{stats.satk}}</td>
					<td>{{stats.sdef}}</td>
					<td>{{stats.speed}}</td>
					<td>{{stats.speed+stats.sdef+stats.satk+stats.def+stats.atk+stats.hp}}</td>
					<td>{{macros.typeimage(stats.type1)}}</td>
					<td>{{macros.typeimage(stats.type2)}}</td>
					<td>{{"%1.3f"|format(((1+(2*stats.hp+204)*stats.capturerate)/((2*stats.hp+204)*3))/256)*100}}%</td>
{%for item in stats.items%}					<td><a title="{{data.items[item].description|nl2br|replace({"\n": ' '})}}">{{data.items[item].name}}</a></td>
{%endfor%}					<td>{{stats.egggrp1}}</td>
					<td>{{stats.egggrp2}}</td>
{%for ability in stats.abilities%}					<td><a title="{{data.abilities[ability].description|nl2br|replace({"\n": ' '})}}">{{data.abilities[ability].name}}</a></td>
{%endfor%}
				</tr>
{%endfor%}
		</tbody>
		</table>{%else%}
		<script type="text/javascript">
			$(function() {
				$("[title]").tooltip({ position: 'center right'});
				$('#movelist').dataTable( {
				"bPaginate": false,
				"bFilter": false,
				"bInfo": false,
				"bStateSave": false,
				"aoColumns": [{ "sType": "title-string" }, null, null, null, null, null, null, null]
				} ).fnSetFilteringDelay();
			});
		</script>
{{dump(data)}}
{% for stats in data.stats%}
		<table>
			<thead>
				<tr>
					<th rowspan="2">
						{{macros.pokemonsprite(generation,gameid,stats.imgid, stats.name,false,false)}}{{macros.pokemonsprite(generation,gameid,stats.imgid, stats.name,false,true)}}<br />
						{{macros.pokemonsprite(generation,gameid,stats.imgid, stats.name,true,false)}}{{macros.pokemonsprite(generation,gameid,stats.imgid, stats.name,true,true)}}
					</th>
					<td colspan="7">
						{{stats.name}}<br />
						{{macros.typeimage(stats.type1)}}{%if stats.type1 != stats.type2%}{{macros.typeimage(stats.type2)}}{%endif%}<br />
						{{stats.species}}<br />
						{{stats.pokedex|replace({"\n": ' '})}}<br />
						{%for ability in stats.abilities%}{%if loop.first != true%}{%if ability%}/{%endif%}{%endif%}<a title="{{data.abilities[ability].description|nl2br|replace({"\n": ' '})}}">{{data.abilities[ability].name}}</a>{%endfor%}<br />
{%for evo in stats.evolutions%}
						Evolves into <a href="/{{gameid}}/stats/{{evo.Target}}">{{evo.name}}</a> ({{evo.Argument}})<br />
{%endfor%}
						EVs: {% for key, val in stats.EVs%}{{key}}: {{val}}{%if loop.last != true%}, {%endif%}{%endfor%}<br />
						{%if stats.formnames%}Forms:{%endif%}
{%for formname in stats.formnames%}{{formname}}{%if loop.last != true%}, {%endif%}{%endfor%}</td></tr><tr><th>HP</th><th>Attack</th><th>Defense</th><th>Special Attack</th><th>Special Defense</th><th>Speed</th><th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Base</td>
					<td class="def">{{stats.hp}}</td>
					<td class="atk">{{stats.atk}}</td>
					<td class="def">{{stats.def}}</td>
					<td class="atk">{{stats.satk}}</td>
					<td class="def">{{stats.sdef}}</td>
					<td class="atk">{{stats.speed}}</td>
					<td class="tot">{{ stats.hp+stats.atk+stats.def+stats.satk+stats.sdef+stats.speed }}</td>
				</tr>
				<tr>
					<td>Min (L100)</td>
					<td class="def">{{2*stats.hp+110}}</td>
					<td class="atk" title="{{macros.natureprint(2*stats.atk+5)}}">{{2*stats.atk+5}}</td>
					<td class="def" title="{{macros.natureprint(2*stats.def+5)}}">{{2*stats.def+5}}</td>
					<td class="atk" title="{{macros.natureprint(2*stats.satk+5)}}">{{2*stats.satk+5}}</td>
					<td class="def" title="{{macros.natureprint(2*stats.sdef+5)}}">{{2*stats.sdef+5}}</td>
					<td class="atk" title="{{macros.natureprint(2*stats.speed+5)}}">{{2*stats.speed+5}}</td>
					<td class="tot">{{(stats.hp+stats.atk+stats.def+stats.satk+stats.sdef+stats.speed)*2+135}}</td>
				</tr>
				<tr>
					<td>Max (L100)</td>
					<td class="def">{{2*stats.hp+204}}</td>
					<td class="atk" title="{{macros.natureprint(2*stats.atk+99)}}">{{2*stats.atk+99}}</td>
					<td class="def" title="{{macros.natureprint(2*stats.def+99)}}">{{2*stats.def+99}}</td>
					<td class="atk" title="{{macros.natureprint(2*stats.satk+99)}}">{{2*stats.satk+99}}</td>
					<td class="def" title="{{macros.natureprint(2*stats.sdef+99)}}">{{2*stats.sdef+99}}</td>
					<td class="atk" title="{{macros.natureprint(2*stats.speed+99)}}">{{2*stats.speed+99}}</td>
					<td class="tot">{{(stats.hp+stats.atk+stats.def+stats.satk+stats.sdef+stats.speed)*2+699}}</td>
				</tr>
			</tbody>
		</table>
		<table id="movelist">
			<thead>
				<tr>
					<th>Learned</th>
					<th>Name</th>
					<th>Power</th>
					<th>Accuracy</th>
					<th>Priority</th>
					<th>Description</th>
					<th>Type</th>
					<th>Cat.</th>
				</tr>
			</thead>{%set counter = -1 %}
			
			<tbody>{%for movecategory in stats.moves%}{%for move in movecategory%}{%set counter = counter + 1 %}
			
				<tr>
					<td><span title="{{counter}}">{{move.Learned}}</span></td>
					<td><a href="/{{gameid}}/moves/{{move.id}}">{{data.moves[move.id].name}}</a></td>
					<td>{{data.moves[move.id].power}}</td>
					<td>{%if data.moves[move.id].accuracy == 101%}-{%else%}{{data.moves[move.id].accuracy}}%{%endif%}</td>
					<td>{{data.moves[move.id].priority}}</td>
					<td>{{data.moves[move.id].description|replace({"\n": ' '})}}</td>
					<td>{{macros.typeimage(data.moves[move.id].type)}}</td>
					<td>{{macros.categoryimage(data.moves[move.id].category)}}</td>
				</tr>
{%endfor%}{%endfor%}
			</tbody>
		</table>
{%endfor%}{%endif%}
{%endblock%}