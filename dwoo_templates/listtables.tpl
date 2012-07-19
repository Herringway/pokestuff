{extends "barebones.tpl"}
{block "title"}Tables{/block}
{block "data"}<table>
<tr><th>Name</th>{loop $data}{if $.loop.default.first}{loop $}<th>{$_key}</th>{/loop}{/if}{/loop}</tr>{loop $data}
<tr><td>{$_key}</td> {loop $}<td>{$}</td>{/loop}</tr>
{/loop}</table>
{/block}
