{extends "barebones.tpl"}
{block "title"}Pokemon Gen V Encounters{/block}
{block "data"}
		<script type="text/javascript" src="http://elpenguino.net/js/konami.pack.js"></script>
		<script type="text/javascript">
			konami = new Konami();
			konami.code = function() {
				imgs = document.getElementsByTagName('img');
				for(i in imgs) {
					if (imgs[i].src.split('/')[4] != 'shiny')
						imgs[i].src = '/bw-sprites/shiny/' + imgs[i].src.split('/')[4];
					else
						imgs[i].src = '/bw-sprites/' + imgs[i].src.split('/')[5];
				}
			};
			konami.load();
		</script>
{loop $enc_rates}
{printsprite $id}<br />
{$name}<br />
{loop $locations}
	{$percent}<br />
	{$location}<br />
	{$season}<br />
{/loop}
{/loop}
	<table id="encounters">
		<thead>
			<tr>
				<th colspan="7">{$area}</th>
			</tr>
			<tr>
				<th>Normal</th>
				<th>Deep Grass</th>
				<th>Shaking/Dusty</th>
				<th>Fishing</th>
				<th>Fishing (Deep)</th>
				<th>Surfing</th>
				<th>Surfing (Deep)</th>
			</tr>
		</thead>
		<tbody>
{loop $encounters.encounters}
			<tr>
				<th colspan="7">{cycle values=array("Spring","Summer","Autumn","Winter")}</th>
			</tr>
			<tr>
				<td>
{printencounters $grass 4}
				</td>
				<td>
{printencounters $deepgrass 4}
				</td>
				<td>
{printencounters $uncommon 4}
				</td>
				<td>
{printencounters $fishing 2}
				</td>
				<td>
{printencounters $deepfishing 2}
				</td>
				<td>
{printencounters $surfing 2}
				</td>
				<td>
{printencounters $deepsurfing 2}
				</td>
			</tr>
{/loop}
		</tbody>
	</table>
{/block}