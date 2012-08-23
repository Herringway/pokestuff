{%extends "base.tpl"%}
{% block title %}Text{%endblock%}
{% block data %}
{%for id,textfile in text%}TEXTFILE {{id}}<pre class="textfile">
{%for text in textfile%}{{text}}
{%endfor%}
</pre>
{%endfor%}
{%endblock%}