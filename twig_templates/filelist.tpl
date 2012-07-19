{%extends "base.tpl"%}
{%block title%}Raw data{%endblock%}
{%block data%}{%for tablename,table in data.data%}
<a href="/{{gameid}}/dumptable/{{data.table}}/{{tablename}}">File {{tablename}}</a><br />
{%endfor%}
{%endblock%}