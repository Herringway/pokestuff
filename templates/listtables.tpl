{%extends "base.tpl"%}
{%block title%}Tables{%endblock%}
{%block data%}{%set cols = 0%}<table>
<tr><th>Name</th>{%for table in dumptable%}{%if table|length > cols%}{%for statname,stat in table%}{%if loop.index0 > cols-1%}<th>{{statname}}</th>{%endif%}{%endfor%}{%set cols = table|length%}{%endif%}{%endfor%}</tr>{%for tablename,table in dumptable%}
<tr><td>{{tablename}}</td> {%for stat in table%}<td>{{stat}}</td>{%endfor%}</tr>
{%endfor%}</table>
{%endblock%}
