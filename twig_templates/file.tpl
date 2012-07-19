{%extends "base.tpl"%}
{%block title%}File {{data.id}}{%endblock%}
{%block data%}
<table>
<tr>
{%for tabledata in data.data.0.data %}
{%if loop.index0 % 4 == 0%}</tr><tr>{%endif%}
<td style="width: 30px;">{{tabledata}}</td>
{%endfor%}
</tr></table>
{%endblock%}