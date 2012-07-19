{%extends "base.tpl"%}
{% block title %}Text{%endblock%}
{% block data %}
{%for textfile in data%}<pre class="textfile">
{%for text in textfile%}{{text}}
{%endfor%}
</pre>
{%endfor%}
{%endblock%}