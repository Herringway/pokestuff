{template printsprite pokeid generation game pokename="" back="" shiny=""}<img alt="{optional $pokename}" src="/images/{$generation}/{$game}/pokemon/{if $back}back/{/if}{if $shiny}shiny/{/if}{$pokeid}.png" title="{optional $pokename}" />{/template}

{template printanisprite pokeid generation game pokename=""}<img src="/images/{$generation}/{$game}/pokemon/ani/{string_format($pokeid, '%03u')}.png" title="{optional $pokename}" alt="{$pokeid}" />{/template}

{template trainersprite id generation game name=""}<img src="/images/{$generation}/{$game}/trainers/{$id}.png" title="{optional $name}" alt="{$id}" />{/template}

{template printtypeimage typeid gen="gen5"}
<img alt="" width="32" height="16" src="/images/types/{$typeid}.png" />{/template}

{template printcategoryimage category gen="gen5"}
<img alt="" width="32" height="16" src="/images/categories/{$category}.png" />{/template}