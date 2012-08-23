{%import "macros.tpl" as macros%}<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<title>Pokemon {{game}} - {% block title %}Fix Me{%endblock%}</title>
		<link rel="shortcut icon" href="/430.gif" />
		<link rel="stylesheet" type="text/css" href="/css/pkmn.css" title="Default" />
		<link rel="stylesheet" type="text/css" href="/css/smoothness/jquery-ui-1.8.21.custom.css" />
		<script type="text/javascript" src="/js/jquery.js"></script>
		<script type="text/javascript" src="/js/jquery.tools.js"></script>
		<script type="text/javascript" src="/js/jquery.ui.js"></script>
		<script type="text/javascript" src="/js/jquery.dataTables.js"></script> 
		<script type="text/javascript" src="/js/jquery.dataTables.plugins.js"></script> 
		<script type="text/javascript" src="/js/infinity.js"></script> 
	</head>
	<body>
		<div class="menubar">
			<select onchange="top.location.href = '/' + this.options[this.selectedIndex].value + '/{{mod}}'">
{% for game in games%}
				<option value="{{game.id}}"{% if gameid == game.id %} selected="yes"{%endif%}>Pokemon {{ game.name }} ({{game.locale}})</option>
{%endfor%}
			</select>
			<select onchange="top.location.href = '/{{gameid}}/' + this.options[this.selectedIndex].value">
{% for module in mods %}
				<option value="{{module.id}}"{%if mod == module.id%} selected="yes"{%endif%}>{{module.name}}</option>
{%endfor%}
			</select>
		</div>
{%block data%}{%endblock%}

	</body>
</html>