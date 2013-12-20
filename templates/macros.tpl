{%macro pokemonsprite(game, gamelang, id, name, back, shiny) %}<img src="/{{game}}:{{gamelang}}/sprite/{%if back%}back/{%endif%}{%if shiny%}shiny/{%endif%}{{id}}" alt="{{name}}" title="{{name}}" />{%endmacro%}
{%macro typeimage(id) %}<img alt="{{id}}" title="{{id}}" width="32" height="16" src="/static/images/types/{{id}}.png" />{%endmacro%}
{%macro categoryimage(id) %}<img alt="{{id}}" title="{{id}}" width="32" height="16" src="/static/images/categories/{{id}}.png" />{%endmacro%}
{%macro natureprint(stat)%}Negative Nature: {{"%u"|format(stat * 0.9)}}&lt;br /&gt;Neutral Nature: {{stat}}&lt;br /&gt;Positive Nature: {{"%u"|format(stat * 1.1)}}&lt;br /&gt;{%endmacro%}
{%macro statCalc(base, level, EV, IV)%}{{((IV+(2*base)+(EV/4))*level)/100 + 5}}{%endmacro%}
{%macro statCalcHP(base, level, EV, IV)%}{{((IV+(2*base)+(EV/4))*level + 100)/100 + 10}}{%endmacro%}
{%macro statMin_(stat)%}{{2*stat+5}}{%endmacro%}
{%macro statMin(stat)%}{{_self.statCalc(stat, 100, 0, 0)}}{%endmacro%}
{%macro statMinHP(stat)%}{%if stat == 1%}1{%else%}{{2*stat+110}}{%endif%}{%endmacro%}
{%macro statMinTotal(hp, atk, def, speed, satk, sdef)%}{{(hp+atk+def+satk+sdef+speed)*2+135}}{%endmacro%}
{%macro statMax_old(stat)%}{{2*stat+99}}{%endmacro%}
{%macro statMax(stat)%}{{_self.statCalc(stat, 100, 252, 31)}}{%endmacro%}
{%macro statMaxHP(stat)%}{%if stat == 1%}1{%else%}{{2*stat+204}}{%endif%}{%endmacro%}
{%macro statMaxTotal(hp, atk, def, speed, satk, sdef)%}{{(hp+atk+def+satk+sdef+speed)*2+699}}{%endmacro%}
