{%extends "base.tpl"%}
{%block title%}File {{dumptable.id}}{%endblock%}
{%block data%}
<table>
<tr>
{%for k,tabledata in dumptable.data.0.data %}
{%if loop.index0 % 4 == 0%}</tr><tr>{%endif%}
<td style="width: 30px;">{{k}}:{{tabledata}}</td>
{%endfor%}
</tr></table>
{%endblock%}