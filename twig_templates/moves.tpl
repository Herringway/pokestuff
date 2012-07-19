{% extends "base.tpl" %}
{%block title%}{%if data|length > 1%}Move List{%else%}{{data.0.name}}{%endif%}{%endblock%}
{%block data%}
{%if data|length > 1%}
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
			});
			$(document).ready(function() {
				$('#pokemoves').dataTable( {
				"bPaginate": false,
				"bStateSave": true,
				"aoColumns": [{ "sType": "num-html" }, null, { "sType": "title-string" }, { "sType": "title-string" }, null, null, null, null, null]
				} );
			} );
		</script>

		<table id="pokemoves">
			<thead>
				<tr>
					<th>ID</th>
					<th>Name</th>
					<th>Type</th>
					<th>Category</th>
					<th>Power</th>
					<th>Accuracy</th>
					<th>PP</th>
					<th>Priority</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
{%for move in data%}
				<tr>
					<td><a href="/{{gameid}}/moves/{{move.id}}">{{move.id}}</a></td>
					<td><a href="/{{gameid}}/moves/{{move.id}}">{{move.name}}</a></td>
					<td>{{macros.typeimage(move.type)}}</td>
					<td>{{macros.categoryimage(move.category)}}</td>
					<td>{{move.power}}</td>
					<td>{{move.accuracy}}</td>
					<td>{{move.pp}}</td>
					<td>{{move.priority}}</td>
					<td>{{move.description|replace({"\n": ' '})}}</td>
				</tr>
{%endfor%}
			</tbody>
		</table>{%else%}
{{dump(data)}}
{%endif%}
{%endblock%}