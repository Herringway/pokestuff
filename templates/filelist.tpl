{%extends "base.tpl"%}
{%block title%}Raw data{%endblock%}
{%block data%}{%for tablename,table in dumptable.data%}
<a href="/{{gameid}}/dumptable/{{dumptable.table}}/{{tablename}}">File {{tablename}}</a><br />
{%endfor%}
{%endblock%}