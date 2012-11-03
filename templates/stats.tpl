{% extends "base.tpl" %}
{%block title%}{%if stats|length > 1%}Stats{%else%}#{{stats.0.id}} - {{stats.0.name}}{%endif%}{%endblock%}
{%block data%}
{%if stats|length > 1%}
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
				$('#pokemon').dataTable( {
				"bPaginate": false,
				"sPaginationType": "full_numbers",
				"bStateSave": true,
				"aoColumns": [null, null, null, null, null, null, null, null, null, { "sType": "title-string" }, { "sType": "title-string" }, { "sType": "percent" }{%for v in stats.0.items%}, null{%endfor%}{%for v in stats.0.abilities%}, null{%endfor%}{%for v in stats.0.egggroups%}, null{%endfor%}]
				} ).fnSetFilteringDelay();
				var $el = $('#pokemon');
				var listView = new infinity.ListView($el);
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
{%for key in stats.0.items|keys%}					<th>{{key}}</th>
{%endfor%}
{%for key in stats.0.egggroups|keys%}					<th>{{key}}</th>
{%endfor%}
{%for key in stats.0.abilities|keys%}					<th>{{key}}</th>
{%endfor%}				</tr>
			</thead>
			<tbody>
{%for poke in stats%}
				<tr>
					<td>{{poke.id}}</td>
					<td><a href="/{{gameid}}:{{gamelang}}/stats/{{poke.name}}">{{poke.name}}</a></td>
					<td>{{poke.hp}}</td>
					<td>{{poke.atk}}</td>
					<td>{{poke.def}}</td>
					<td>{{poke.satk}}</td>
					<td>{{poke.sdef}}</td>
					<td>{{poke.speed}}</td>
					<td>{{poke.speed+poke.sdef+poke.satk+poke.def+poke.atk+poke.hp}}</td>
					<td>{{macros.typeimage(poke.type1)}}</td>
					<td>{{macros.typeimage(poke.type2)}}</td>
					<td>{{"%1.3f"|format(((1+(2*poke.hp+204)*poke.capturerate)/((2*poke.hp+204)*3))/256)*100}}%</td>
{%for item in poke.items%}					<td><a title="{{items[item].description|nl2br|replace({"\n": ' '})}}">{{items[item].name}}</a></td>
{%endfor%}
{%for group in poke.egggroups%}					<td>{{group}}</td>
{%endfor%}
{%for ability in poke.abilities%}					<td><a title="{{abilities[ability].description|nl2br|replace({"\n": ' '})}}">{{abilities[ability].name}}</a></td>
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
{% for poke in stats%}
		<table>
			<thead>
				<tr>
					<th rowspan="2" height="256" width="192" class="{{poke.type1}}">
						{{macros.pokemonsprite(generation,spriteseries,poke.imgid, poke.name,false,false)}}{%if generation != 'gen1'%}{{macros.pokemonsprite(generation,spriteseries,poke.imgid, poke.name,false,true)}}{%endif%}<br />
						{{macros.pokemonsprite(generation,spriteseries,poke.imgid, poke.name,true,false)}}{%if generation != 'gen1'%}{{macros.pokemonsprite(generation,spriteseries,poke.imgid, poke.name,true,true)}}{%endif%}
					</th>
					<td colspan="7" class="{{poke.type1}}">
						{{poke.name}}<br />
						{{macros.typeimage(poke.type1)}}{%if poke.type1 != poke.type2%}{{macros.typeimage(poke.type2)}}{%endif%}<br />
						{{poke.species}}<br />
						{{poke.pokedex|nl2br}}<br />
						{%for ability in poke.abilities%}{%if loop.first != true%}{%if ability%}/{%endif%}{%endif%}<a title="{{abilities[ability].description|nl2br|replace({"\n": ' '})}}">{{abilities[ability].name}}</a>{%endfor%}<br />
{%for evo in poke.evolutions%}
						Evolves into <a href="/{{gameid}}:{{gamelang}}/{{textPokemonNames[evo.Target]}}">{{textPokemonNames[evo.Target]}}</a> {%if evo.Item%}with <img src="/static/images/items/{{items[evo.Item].name|lower|replace({' ':'-', '.':'', 'é':'e','’':'\'','?':''})}}.png" alt="{{items[evo.Item].name}}" title="{{items[evo.Item].name}}" />{%endif%}{%if evo.Level%}at Level {{evo.Level}}{%endif%}{%if evo.Move%} once <a href="/{{gameid}}:{{gamelang}}/{{moves[evo.Move].name}}">{{moves[evo.Move].name}}</a> is learned{%endif%}{%if evo.With%} if <a href="/{{gameid}}:{{gamelang}}/{{textPokemonNames[evo.With]}}">{{textPokemonNames[evo.With]}}</a> is in party{%endif%}{%if evo.Condition%} {{evo.Condition}}{%endif%}<br />
{%endfor%}
						{%if poke.EVs%}EVs: {% for key, val in poke.EVs%}{{key}}: {{val}}{%if loop.last != true%}, {%endif%}{%endfor%}<br />{%endif%}
						{%if poke.formnames%}Forms:{%endif%}
{%for formname in poke.formnames%}{{formname}}{%if loop.last != true%}, {%endif%}{%endfor%}
						<audio style="width: 100%;" controls="controls"><source src="/static/audio/cries/{{poke.id}}.ogg" type="audio/ogg" /></audio><br />
						</td></tr><tr><th>HP</th><th>Attack</th><th>Defense</th><th>Special Attack</th><th>Special Defense</th><th>Speed</th><th>Total</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td>Base</td>
					<td class="def">{{poke.hp}}</td>
					<td class="atk">{{poke.atk}}</td>
					<td class="def">{{poke.def}}</td>
					<td class="atk">{{poke.satk}}</td>
					<td class="def">{{poke.sdef}}</td>
					<td class="atk">{{poke.speed}}</td>
					<td class="tot">{{ poke.hp+poke.atk+poke.def+poke.satk+poke.sdef+poke.speed }}</td>
				</tr>
				<tr>
					<td>Min (L100)</td>
					<td class="def">{{macros.statMinHP(poke.hp)}}</td>
					<td class="atk" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.atk+5)}}"{%endif%}>{{macros.statMin(poke.atk)}}</td>
					<td class="def" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.def+5)}}"{%endif%}>{{macros.statMin(poke.def)}}</td>
					<td class="atk" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.satk+5)}}"{%endif%}>{{macros.statMin(poke.satk)}}</td>
					<td class="def" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.sdef+5)}}"{%endif%}>{{macros.statMin(poke.sdef)}}</td>
					<td class="atk" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.speed+5)}}"{%endif%}>{{macros.statMin(poke.speed)}}</td>
					<td class="tot">{{macros.statMinTotal(poke.hp, poke.atk, poke.def, poke.speed, poke.satk, poke.sdef)}}</td>
				</tr>
				<tr>
					<td>Max (L100)</td>
					<td class="def">{{macros.statMaxHP(poke.hp)}}</td>
					<td class="atk" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.atk+99)}}"{%endif%}>{{macros.statMax(poke.atk)}}</td>
					<td class="def" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.def+99)}}"{%endif%}>{{macros.statMax(poke.def)}}</td>
					<td class="atk" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.satk+99)}}"{%endif%}>{{macros.statMax(poke.satk)}}</td>
					<td class="def" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.sdef+99)}}"{%endif%}>{{macros.statMax(poke.sdef)}}</td>
					<td class="atk" {%if (generation != 'gen1') and (generation != 'gen2')%}title="{{macros.natureprint(2*poke.speed+99)}}"{%endif%}>{{macros.statMax(poke.speed)}}</td>
					<td class="tot">{{macros.statMaxTotal(poke.hp, poke.atk, poke.def, poke.speed, poke.satk, poke.sdef)}}</td>
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
			
			<tbody>{%for movecategory in poke.moves%}{%for move in movecategory%}{%set counter = counter + 1 %}
			
				<tr>
					<td><span title="{{counter}}">{{move.Learned}}</span></td>
					<td><a href="/{{gameid}}:{{gamelang}}/moves/{{moves[move.id].name}}">{{moves[move.id].name}}</a></td>
					<td>{%if moves[move.id].power == 0%}-{%else%}{{moves[move.id].power}}{%endif%}</td>
					<td>{%if moves[move.id].accuracy == 101%}-{%else%}{{moves[move.id].accuracy}}%{%endif%}</td>
					<td>{{moves[move.id].priority}}</td>
					<td>{{moves[move.id].description|replace({"\n": ' '})}}</td>
					<td>{{macros.typeimage(moves[move.id].type)}}</td>
					<td>{{macros.categoryimage(moves[move.id].category)}}</td>
				</tr>
{%endfor%}{%endfor%}
			</tbody>
		</table>
{%endfor%}{%endif%}
{%endblock%}