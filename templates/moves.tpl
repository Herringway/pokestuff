{% extends "base.tpl" %}
{%block title%}{%if moves|length > 1%}Move List{%else%}{{moves.0.name}}{%endif%}{%endblock%}
{%block data%}
{%if moves|length > 1%}
		<script type="text/javascript">
			$(function() {
				$("a[title]").tooltip({ position: 'center right'});
			});
			$(document).ready(function() {
				$('#pokemoves').dataTable( {
				"bPaginate": false,
				"bJQueryUI": true,
				"bStateSave": true,
				"sPaginationType": "full_numbers",
				"aoColumns": [{ "sType": "num-html" }, null, { "sType": "title-string" }, { "sType": "title-string" }, null, null, null, null, null]
				} );
			} );
		</script>

		<table id="pokemoves" width="100%">
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
{%for move in moves%}
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
{%for move in moves%}
<table style="width: 500px;" class="pokemove">
	<thead>
		<tr><th colspan="5">{{move.name}}</th></tr>
	</thead>
	<tbody>
		<tr><td colspan="5" style="height: 60px;">{{move.description}}</td></tr>
		<tr>
			<th>Type</th>
			<th>Power</th>
			<th>PP</th>
			<th>Accuracy</th>
			<th>Priority</th>
		</tr>
		<tr>
			<td>{{macros.typeimage(move.type)}}{{macros.categoryimage(move.category)}}</td>
			<td>{{move.power}}</td>
			<td>{{move.pp}}</td>
			<td>{{move.accuracy}}</td>
			<td>{{move.priority}}</td>
		</tr>
		{%for flag, value in move.Flags%}
		<tr><td style="text-align: center;" colspan="4">{{flag}}</td><td><input type="checkbox" {%if value%}checked="checked"{%endif%} disabled="true"/></td></tr>
		{%endfor%}
	</tbody>
</table>
{%endfor%}
{%endif%}
{%endblock%}