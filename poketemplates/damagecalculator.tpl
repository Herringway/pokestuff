{extends "barebones.tpl"}
{block "title"}Pokeymans Damage Calcumalator{/block}
{block "data"}
		<script type="text/javascript">
			function zeroPad(num,count) {
				var numZeropad = num + '';
				while(numZeropad.length < count) {
					numZeropad = "0" + numZeropad;
				}
				return numZeropad;
			}
		</script>
		<form action="/damage" method="get">
		<input type="hidden" name="results" value="1" />
		<table>
			<thead>
				<tr>
					<th>Attacker</th>
					<th>Defender</th>
				</tr>
			<tbody>
				<tr style="text-align: center;">
					<td>
						{printanisprite 460 htmlid="atkimg"}<br />
						<select name="user" onchange="document.getElementById('atkimg').src = 'http://pkmn.elpenguino.net/bw-sprites/ani/' + zeroPad(this.options[this.selectedIndex].value,3) + '.gif'">
{pokemonselector $names}
						</select><br />
						Nature:<input type="radio" name="usernature" value="1.1" />Positive<input type="radio" name="usernature" value="1" checked />Neutral<input type="radio" name="usernature" value="0.9" />Negative<br />
						Ability:<select name="userability">
{foreach $abilities key val}
							<option value="{math("$key+1")}">{$val}</option>
{/foreach}
						</select><br />
						Held Item:<select name="useritem">
{foreach $items key val}
							<option value="{math("$key+1")}">{$val}</option>
{/foreach}
						</select><br />
						Attack Boosts: <input type="text" name="atkboosts" />
					</td>
					<td>
						{printanisprite 460 htmlid="defimg"}<br />
						<select name="foe" onchange="document.getElementById('defimg').src = 'http://pkmn.elpenguino.net/bw-sprites/ani/' + zeroPad(this.options[this.selectedIndex].value,3) + '.gif'">
{pokemonselector $names}
						</select><br />
						Nature:<input type="radio" name="foenature" value="1.1" />Positive<input type="radio" name="foenature" value="1" checked />Neutral<input type="radio" name="foenature" value="0.9" />Negative<br />
						Ability:<select name="foeability">
{foreach $abilities key val}
							<option value="{math("$key+1")}">{$val}</option>
{/foreach}
						</select><br />
						Held Item:<select name="foeitem">
{foreach $items key val}
							<option value="{math("$key+1")}">{$val}</option>
{/foreach}
						</select><br />
						Defense Boosts: <input type="text" name="defboosts" />
					</td>
				</tr>
			</tbody>
		</table>
		Move: <select name="move">
{foreach $moves key val}
		<option value="{math("$key+1")}">{$val}</option>
{/foreach}
		</select>
		Power Override<input type="text" name="overridebp" /><br />
		Weather: <select name="weather">
		<option value="0">Normal</option>
		<option value="1">Sunny</option>
		<option value="2">Raining</option>
		<option value="3">Hailing</option>
		</select>
		<br /><br />
		<input type="submit" value="Submit">
		</form>
{if $result}
		<img src="http://pkmn.elpenguino.net/bw-sprites/ani/{string_format($result.user.id, '%03u')}.gif"> VS <img src="http://pkmn.elpenguino.net/bw-sprites/ani/{string_format($result.foe.id, '%03u')}.gif"><br />
		{getMove($result.move)}!<br />
		Effectiveness: {$result.typemultiplier}x<br />
		Minimum/Minimum: {$result.damage.minminivsmin}-{$result.damage.minminivsmax} ({string_format(math("$result.damage.minminivsmin/(2*$result.foe.hp+204)*100"), '%d')}% - {string_format(math("$result.damage.minminivsmax/(2*$result.foe.hp+110)*100"), '%d')}%)<br />
		Minimum/Maximum: {$result.damage.minmaxivsmin}-{$result.damage.minmaxivsmax} ({string_format(math("$result.damage.minmaxivsmin/(2*$result.foe.hp+204)*100"), '%d')}% - {string_format(math("$result.damage.minmaxivsmax/(2*$result.foe.hp+110)*100"), '%d')}%)<br />
		Maximum/Minimum: {$result.damage.maxminivsmin}-{$result.damage.maxminivsmax} ({string_format(math("$result.damage.maxminivsmin/(2*$result.foe.hp+204)*100"), '%d')}% - {string_format(math("$result.damage.maxminivsmax/(2*$result.foe.hp+110)*100"), '%d')}%)<br />
		Maximum/Maximum: {$result.damage.maxmaxivsmin}-{$result.damage.maxmaxivsmax} ({string_format(math("$result.damage.maxmaxivsmin/(2*$result.foe.hp+204)*100"), '%d')}% - {string_format(math("$result.damage.maxmaxivsmax/(2*$result.foe.hp+110)*100"), '%d')}%)<br />
{/if}
{/block}