{%extends "base.tpl"%}
{%block title%}Module Completion Status{%endblock%}
{%block data%}
		<table>
			<tr>
				<th>What</th>
				<th>Status</th>
			</tr>
{%for modname,mod in completion%}
			<tr>
				<td><a href="/{{gameid}}/{{mod.mod}}">{{modname}}</a></td>
				<td>{{mod.status|join(', ')}}</td>
			</tr>
{%endfor%}
		</table>
{%endblock%}