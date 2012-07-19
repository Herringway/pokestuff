{%extends "base.tpl" %}
{% block title%}{%if data|length > 1%}Items{%else%}{{data.0.name}}{%endif%}{%endblock%}
{% block data%}{%if data|length > 1%}
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
					<th>Value</th>
					<th>Description</th>
				</tr>
			</thead>
			<tbody>
{%for item in data %}
				<tr>
					<td><a href="/{{gameid}}/items/{{item.id}}">{{item.id}}</a></td>
					<td><a href="/{{gameid}}/items/{{item.id}}">{{item.name}}</a></td>
					<td>{{item.value}}</td>
					<td>{{item.description}}</td>
				</tr>
{%endfor%}
		</tbody>
		</table>{%else%}
{{dump(data)}}{%endif%}
{%endblock%}