{%extends "base.tpl" %}
{% block title%}{%if items|length > 1%}Items{%else%}{{items.0.name}}{%endif%}{%endblock%}
{% block data%}{%if items|length > 1%}
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
{%for item in items %}
				<tr>
					<td><a href="/{{gameid}}/items/{{item.id}}">{{item.id}}</a></td>
					<td><a href="/{{gameid}}/items/{{item.name}}">{{item.name}}</a></td>
					<td>{{item.value}}</td>
					<td>{{item.description}}</td>
				</tr>
{%endfor%}
		</tbody>
		</table>{%else%}
{%for item in items%}
<table style="width: 500px;">
<tr><th><img src="/static/images/items/{{item.name|lower|replace({' ':'-', '.':'', 'é':'e','’':'\'','?':''})}}.png" alt="{{item.name}}"/>{{item.name}}</th><th>Value</th></tr>
<tr><td style="height: 60px;">{{item.description}}</td><td>{{item.value}}</td></tr>
</table>
{%endfor%}
{%endif%}
{%endblock%}