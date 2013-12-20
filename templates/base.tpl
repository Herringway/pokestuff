{%import "macros.tpl" as macros%}<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Pokemon {{game}} - {% block title %}Fix Me{%endblock%}</title>
		<link rel="shortcut icon" href="/static/favicon.gif" />
		<link rel="stylesheet" type="text/css" href="/static/css/pkmn.css" title="Default" />
		<link rel="stylesheet" type="text/css" href="/static/css/tables.css" />
		<link rel="stylesheet" type="text/css" href="/static/css/smoothness/jquery-ui-1.8.21.custom.css" />
		<script type="text/javascript" src="/static/js/jquery.js"></script>
		<script type="text/javascript" src="/static/js/jquery.tools.js"></script>
		<script type="text/javascript" src="/static/js/jquery.dataTables.js"></script> 
		<script type="text/javascript" src="/static/js/jquery.dataTables.plugins.js"></script>
	</head>
	<body>
		<div class="menubar ui-widget-header">{%if showmenu%}
			<select onchange="top.location.href = '/' + this.options[this.selectedIndex].value + '/{{mod}}'">
{% for game in games%}
				<option value="{{game.id}}{%if game.lang != deflang%}:{{game.lang}}{%endif%}"{% if (gameid == game.id) and (gamelang == game.lang) %} selected="selected"{%endif%}>Pokemon {{ game.name }} ({{game.locale}})</option>
{%endfor%}
			</select>{%endif%}
			<select onchange="top.location.href = '/{{gameid}}{%if gamelang != deflang%}:{{gamelang}}{%endif%}/' + this.options[this.selectedIndex].value">
{% for module in mods %}
				<option value="{{module.id}}"{%if mod == module.id%} selected="selected"{%endif%}>{{module.name}}</option>
{%endfor%}
			</select>
		</div>
{%block data%}{%endblock%}
	</body>
</html>