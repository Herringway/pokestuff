<?php
$bw = 1;

define('NUM_MOVES', 559);
define('NUM_POKEMON', 667);
define('NUM_ABILITIES', 164);
define('NUM_ITEMS', 626);
define('NUM_TRAINERS', 616);
define('NUM_AREAS', 110);

define('TEXT_NARC', '0/0/2');
define('TEXT_TRAINER_NAME', 190);
define('TEXT_TRAINER_CLASS', 191);
define('TEXT_MOVE_DESCRIPTIONS', 202);
define('TEXT_MOVE_NAMES', 203);

require_once 'pkmnnos.php';
require_once 'text.php';
require_once 'narc.php';
function uint($i) {
	if ($i >= 128)
		$i = 0-(256-$i);
	return $i;
}
$physspec = array(
0 => 'Other',
1 => 'Physical',
2 => 'Special');


$int_flags = array(
0 => 'Misc',
1 => 'Affects Status only',
2 => 'Affects Stats only',
3 => 'Heals user',
4 => 'Damage & Status',
5 => 'Status and Stats',
6 => 'Damage & Stats',
7 => 'Damage, raise stats',
8 => 'Absorb HP',
9 => 'OHKOs target',
10 => 'Affects everyone',
11 => 'Affects single party',
12 => 'Forces Switch',
13 => 'Gimmick',

);

$ctype = array(
0 => 'Cool',
1 => 'Beauty',
2 => 'Cute',
3 => 'Smart',
4 => 'Tough'
);
$specabildesc = array(

);

$status = array(
0 => 'None',
1 => 'Paralysis',
2 => 'Sleep',
3 => 'Freeze',
4 => 'Burn',
5 => 'Poison',
6 => 'Confusion',
7 => 'Infatuation',
8 => 'Trap',
9 => 'Nightmare',
10 => '10',
11 => '11',
12 => 'Torment',
13 => 'Disable',
14 => 'Next-turn sleep',
15 => 'No Healing',
16 => '16',
17 => 'Foresight',
18 => 'Leech Seed',
19 => 'Cannot use item',
20 => 'Perish Song',
21 => 'Ingrain',
255 => '255'
);
$shortevotypes = array(
0 => '',
1 => 'happiness',
2 => 'happinessday',
3 => 'happinessnight',
4 => 'levelup',
5 => 'trade',
6 => 'item+trade',
7 => 'tradeforcounterpart',
8 => 'useitem',
9 => 'atkgtdef',
10 => 'atkeqdef',
11 => 'atkltdef',
12 => 'PVlt5',
13 => 'PVgt4',
14 => 'levelup+',
15 => 'ifspaceisavailable',
16 => 'beauty',
17 => 'useitemmale',
18 => 'useitemfemale',
19 => 'helditemday',
20 => 'helditemnight',
21 => 'movelearned',
22 => 'withpokeinparty',
23 => 'levelupmale',
24 => 'levelupfemale',
25 => 'chargestone',
26 => 'mossrock',
27 => 'icerock',

);
$evolutiontypes = array(
0 => 'does not evolve.',
1 => 'evolves into %2$s with maximum happiness',
2 => 'evolves into %2$s with max happiness in the daytime',
3 => 'evolves into %2$s with max happiness in the nighttime',
4 => 'evolves into %2$s at level %1$u',
5 => 'evolves into %2$s when traded',
6 => 'evolves into %2$s with traded holding %1$s',
7 => 'evolves into %2$s when traded for ???',
8 => 'evolves into %2$s when exposed to %1$s',
9  => 'evolves into %2$s with attack > defense at level %1$u',
10 => 'evolves into %2$s with attack = defense at level %1$u',
11 => 'evolves into %2$s with attack < defense at level %1$u',
12 => 'evolves into %2$s if personality < 5 at level %1$u',
13 => 'evolves into %2$s if personality > 4 at level %1$u',
14 => 'evolves into %2$s at level %1$u...',
15 => 'evolves into %2$s if space is in party',
16 => 'evolves into %2$s when levelled up while Beauty > %1$u',
17 => 'evolves into %2$s when %1$s is used and is male',
18 => 'evolves into %2$s when %1$s is used and is female',
19 => 'evolves into %2$s when holding %1$s in the day',
20 => 'evolves into %2$s when holding %1$s at night',
21 => 'evolves into %2$s when %1$s is learned',
22 => 'evolves into %2$s with %1$s in party',
23 => 'evolves into %2$s at level %1$u if male',
24 => 'evolves into %2$s at level %1$u if female',
25 => 'evolves into %2$s when levelled up in Chargestone Cave',
26 => 'evolves into %2$s when levelled up at a Mossy Rock',
27 => 'evolves into %2$s when levelled up near an Ice Rock',
);

$stats = array(
0 => 'None',
1 => 'Attack',
2 => 'Defence',
3 => 'Sp. Atk',
4 => 'Sp. Def',
5 => 'Speed',
6 => 'Accuracy',
7 => 'Evasiveness',
8 => 'Everything'
);

function getTypeIMG($id) {
	global $typelistBWIMG;
	return $typelistBWIMG[$id];
}

function readPokeString($file, $line, $language,$lines = 1) {
	if ($language === 'blackjapan')
		$langfile = 'bjpn';
	else if ($language === 'whitejapan')
		$langfile = 'wjpn';
	else if ($language === 'white')
		$langfile = 'weng';
	else
		$langfile = 'beng';
	static $textfile = array();
	if (!isset($file[$langfile]))
		$textfile[$langfile] = new gen5text('narcs/'.$langfile.'/'.TEXT_NARC);

	return $textfile[$langfile]->fetchline($file, $line);
	if ($lines <= 1)
		return str_replace('\n', "\n",readString('narcs/'.$langfile.'/0/0/2', $file, $line));
	else {
		$tmp = readString('narcs/'.$langfile.'/0/0/2', $file, $line, $lines);
		foreach ($tmp as &$line)
			$line = str_replace('\n', "\n", $line);
		return $tmp;
	}
}

function getItem($num, $language = 0) {
	return readPokeString(54, $num, $language);
}
function getItemDesc($num, $language = 0) {
	return readPokeString(53, $num, $language);
}

function getColour($num, $language = 0) {
	return readPokeString(277, $num+96, $language);
}
function getAbility($num, $language = 0) {
	return readPokeString(182, $num, $language);
}
function getAbilityDesc($num, $language = 0) {
	return readPokeString(183, $num, $language);
}
function getPokedexEntryW($num, $language = 0) {
	return readPokeString(235, $num, $language);
}
function getPokedexEntryB($num, $language = 0) {
	return readPokeString(236, $num, $language);
}
function getSpeciesName($num, $language = 0) {
	return readPokeString(260, $num, $language);
}
function getBattleType($num, $language = 0) {
	return readPokeString(166, $num, $language);
}
function getPokeName($num,$language = 0) {
	global $pknobw;
	if ($num > 667)
		return 'MISSINGNO';
	if ($num > 649)
		return $pknobw[$num-650];
	return readPokeString(70, $num, $language);
}
function getFormNames($num, $lines, $language = 0) {
	$t = readPokeString(244, $num, $language, $lines);
	if (!is_array($t))
		$t = array($t);
	return $t;
}
function getTrainerName($num, $language = 0) {
	return readPokeString(190, $num, $language);
}
function getTrainerClass($num, $language = 0) {
	return readPokeString(191, $num, $language);
}
function getPlaceName($num, $language = 0) {
	return readPokeString(89, $num, $language);
}
function getMove($num, $language = 0) {
	return readPokeString(TEXT_MOVE_NAMES, $num, $language);
}
function getMoveDesc($num, $language = 0) {
	return readPokeString(TEXT_MOVE_DESCRIPTIONS, $num, $language);
}
$evolutiontrans = array(
6 => 'getItem',
8 => 'getItem',
17 => 'getItem',
18 => 'getItem',
19 => 'getItem',
20 => 'getItem',
21 => 'getMove',
22 => 'getPokeName',

);

$effects = array(
0 => 'None',
1 => 'Inflicts sleep on target',
2 => '[EFFECTCHANCE]% chance of poisoning target',
3 => 'User recovers [DRAIN_PERCENTAGE]% of damage done',
4 => '[EFFECTCHANCE]% chance to burn target',
5 => '[EFFECTCHANCE]% chance to freeze target',
6 => '[EFFECTCHANCE]% chance to paralyze target',
7 => 'User faints',
8 => 'User recovers [DRAIN_PERCENTAGE]% of damage done on sleeping targets',
9 => 'User uses target\'s last move',
10 => 'Raises attack power',
11 => 'Raises defense',

16 => 'Raises [STAT1] [STAT1DELTA] stages',
17 => 'Can\'t Miss',
18 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',
19 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',
20 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',

23 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',
24 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',
25 => 'Nullifies all stat changes on everyone',
26 => 'User absorbs energy for 2 turns, then unleashes double damage',
27 => 'Attacks for 2-3 turns, then inflicts confusion on user',
28 => 'Forces target to switch',
29 => 'Hits [HITS_MIN]-[HITS_MAX] times in one turn',
30 => 'User changes type to one of its moves',
31 => '[FLINCHCHANCE]% chance to flinch target',
32 => 'Recovers [HEAL_PERCENTAGE]% health',
33 => 'Inflicts deadly [STATUS] on target',
34 => 'User gains extra money',
35 => 'Lowers damage from special attacks for 5 turns',
36 => '[EFFECTCHANCE]% chance to burn, paralyze, or freeze target',
37 => 'Inflicts sleep on user and recovers all HP',
38 => 'OHKOs target',
39 => 'Charges for one turn',
40 => 'Halves target\'s HP',
41 => 'Deals 40 damage',
42 => '[EFFECTCHANCE]% chance of trapping target',
43 => 'High Critical Hit Chance',
44 => 'Hits twice in one turn',
45 => 'User takes damage if move misses',
46 => 'Prevents stat reductions for 5 turns',
47 => 'Increases user\'s critical hit chance',
48 => 'User takes [DRAIN_PERCENTAGE]% damage in recoil',
49 => 'Inflicts [STATUS] on target',
50 => 'Raises [STAT1] [STAT1DELTA] stages',
51 => 'Raises [STAT1] [STAT1DELTA] stages',
52 => 'Raises [STAT1] [STAT1DELTA] stages',
53 => 'Raises [STAT1] [STAT1DELTA] stages',
54 => 'Raises [STAT1] [STAT1DELTA] stages',

57 => 'User transforms into target',
58 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',
59 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',
60 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',

62 => 'Lowers target\'s [STAT1] [STAT1DELTA] stages',

65 => 'Lowers damage from physical attacks for 5 turns',
66 => 'Inflicts [STATUS] on target',
67 => 'Inflicts [STATUS] on target',
68 => '[STAT1_CHANCE]% chance to raise target\'s [STAT1] [STAT1DELTA] stages',
69 => '[STAT1_CHANCE]% chance to raise target\'s [STAT1] [STAT1DELTA] stages',
70 => '[STAT1_CHANCE]% chance to raise target\'s [STAT1] [STAT1DELTA] stages',
71 => '[STAT1_CHANCE]% chance to raise target\'s [STAT1] [STAT1DELTA] stages',
72 => '[STAT1_CHANCE]% chance to raise target\'s [STAT1] [STAT1DELTA] stages',
73 => '[STAT1_CHANCE]% chance to raise target\'s [STAT1] [STAT1DELTA] stages',

75 => 'Charges for one turn, [FLINCHCHANCE]% chance to flinch target, +[CRITLEVEL] critical hit level',
76 => '[EFFECTCHANCE]% chance to confuse target',
77 => 'Hits [HITS_MIN]-[HITS_MAX] times in one turn, [EFFECTCHANCE]% chance of [STATUS]',

79 => 'User creates a substitute using some of its HP',
80 => 'User recharges next turn',
81 => 'Raises Attack if struck while move is in effect',
82 => 'User copies target\'s last move',
83 => 'Uses a random move',
84 => 'Inflicts [STATUS] on target',
85 => 'Nothing?',
86 => 'Disables target\'s last-used move',
87 => 'Deals damage equal to the user\'s level',
88 => 'Deals damage equal to 0.75x-1.25x user\'s level',
89 => 'Counters physical attacks for double damage',
90 => 'Target is forced to repeat last move',
91 => 'Splits target\'s HP with user',
92 => 'Only usable while asleep, [FLINCHCHANCE]% chance to flinch target',
93 => 'User\'s type changes to resist the last move it was hit with',
94 => 'User\'s next move guaranteed to land',
95 => 'Permanently copies the last move used by the target',

97 => 'Randomly uses one of user\'s moves if asleep',
98 => 'Target faints if user faints',
99 => 'Power increases as user\'s HP decreases',
100 => 'Target\'s last-used move loses 4PP',
101 => 'Leaves target with at least 1 HP',
102 => 'Heals status effects on all party members',
103 => 'User attacks first',

105 => 'Steals target\'s held item',
106 => 'Target cannot escape',

108 => 'Raises [STAT1] [STAT1DELTA] stages',

111 => 'User avoids damage',

115 => 'Creates sandstorm for five turns',
116 => 'User survives next hit',
117 => 'Power doubles with each hit',
118 => 'Inflicts [STATUS] on target and raises [STAT1] [STAT1DELTA] stages',
119 => 'Power increases if used in succession',
120 => 'Inflicts [STATUS] on target',
121 => 'Power based on user\'s happiness',
122 => 'Random power, may heal',
123 => 'Power based on user\'s unhappiness',
124 => 'Prevents status effects for 5 turns',
125 => 'Unfreezes user and [EFFECTCHANCE]% chance of causing a burn',
126 => 'Power varies',
127 => 'User switches out and passes stat changes',
128 => 'Double damage if target switches',
129 => 'Removes Spikes and Stealth Rock',
130 => 'Deals 20 damage',

132 => 'Recovers user\'s hp based on weather',

135 => 'Type and Power depend on IVs',
136 => 'Changes weather to rain',
137 => 'Changes weather to sunny',
138 => '[STAT1_CHANCE]% chance to raise user\'s [STAT1] [STAT1DELTA] stages',
139 => '[STAT1_CHANCE]% chance to raise user\'s [STAT1] [STAT1DELTA] stages',
140 => '[STAT1_CHANCE]% chance to raise user\'s [STAT1] [STAT1DELTA] stages',

145 => 'Raises Defense 1 stage, charges for one turn',

148 => 'Attacks target two turns later',

150 => '[FLINCHCHANCE]% chance to flinch target, double damage on minimized foes',
151 => 'Charges for one turn unless sunny',

153 => 'User flees from battle',

156 => 'Raises [STAT1] [STAT1DELTA] stages, doubles power of rollout',

170 => 'Fails if user is attacked',

175 => 'Target can only use offensive abilities',

184 => 'Allows user to reuse item',
185 => 'Inflicts double damage if user was hit on same turn',

187 => 'Inflicts [STATUS] on target',

189 => 'Reduces target\'s hp to same level as user\'s',
190 => 'Power decreases as user\'s hp decreases',

193 => 'Heals paralysis, burn, or poison on user',

196 => 'Deals more damage to heavier foes',
197 => 'Effect depends on environment',
198 => 'User takes [DRAIN_PERCENTAGE]% damage in recoil',
199 => 'Inflicts [STATUS] on all pokemon',

202 => '[EFFECTCHANCE]% chance to badly poison or flinch target',

204 => '[STAT1_CHANCE]% chance to raise user\'s [STAT1] [STAT1DELTA] stages',

206 => 'Raises user\'s [STAT1] [STAT1DELTA] stages and [STAT2] [STAT2DELTA] stages',

208 => 'Raises user\'s [STAT1] [STAT1DELTA] stages and [STAT2] [STAT2DELTA] stages',
209 => '[EFFECTCHANCE]% chance to poison target',

225 => 'Increases party\'s speed for 3 turns',

228 => 'User switches after damage is dealt',

235 => 'Power increases as PP drops',

239 => 'Cancels effect of target\'s ability',

254 => 'Deals damage, user loses [HEAL_PERCENTAGE]% health',

256 => 'User burrows underground for one turn',

263 => 'Invulnerable for one turn, [EFFECTCHANCE]% chance to inflict [STATUS]',

268 => 'Type matches held plate',
269 => 'User takes [DRAIN_PERCENTAGE]% damage in recoil',

303 => 'Ignores target\'s stat changes',

313 => 'Forces target to switch, deals damage',

315 => 'Target moves last',
316 => 'Raises user\'s [STAT1] [STAT1DELTA] stages and [STAT2] [STAT2DELTA] stages',

321 => 'Raises [STAT1] [STAT1DELTA] stages',
322 => 'Raises user\'s [STAT1] [STAT1DELTA] stages, [STAT2] [STAT2DELTA] stages, and [STAT3] [STAT3DELTA] stages',

332 => 'Charges for one turn, [EFFECTCHANCE]% chance to inflict [STATUS]',
);


function EVs($num) {
	$list = array('HP', 'Attack', 'Defense', 'Speed', 'Sp. Attack', 'Sp. Defense');
	for ($i = 0; $i < count($list); $i++)
		if (($num&(3<<2*$i))>>($i*2))
			$output[] = $list[$i].' +'.(($num&(3<<2*$i))>>($i*2));
	return implode(', ', $output);
}
$egggrp = array(
0x00 => '',
0x01 => 'Monster',
0x02 => 'Water 1',
0x03 => 'Bug',
0x04 => 'Flying',
0x05 => 'Ground',
0x06 => 'Fairy',
0x07 => 'Plant',
0x08 => 'Humanshape',
0x09 => 'Water 3',
0x0A => 'Mineral',
0x0B => 'Indeterminate',
0x0C => 'Water 2',
0x0D => 'Ditto',
0x0E => 'Dragon',
0x0F => 'N/A',
);

$TMMap = array(
468,
337,
473,
347,
46,
92,
258,
339,
474,
237,
241,
269,
58,
59,
63,
113,
182,
240,
477,
219,
218,
76,
479,
85,
87,
89,
216,
91,
94,
247,
280,
104,
115,
482,
53,
188,
201,
126,
317,
332,
259,
263,
488,
156,
213,
168,
490,
496,
497,
315,
502,
411,
412,
206,
503,
374,
451,
507,
510,
511,
261,
512,
373,
153,
421,
371,
514,
416,
397,
148,
444,
521,
86,
360,
14,
522,
244,
523,
524,
157,
404,
525,
526,
398,
138,
447,
207,
365,
369,
164,
430,
433,
528,
249,
555,
15,
19,
57,
70,
127,
291);
$tutormoves = array(
0 => 520,
1 => 519,
2 => 518,
3 => 338,
4 => 307,
5 => 308,
6 => 434,
);
$places = array (
  3 => 'Dreamyard',
  4 => 'Dreamyard (???)',
  5 => 'Pinwheel Forest (Outside)',
  6 => 'Pinwheel Forest (Inside)',
  7 => 'Desert Resort (Entrance)',
  8 => 'Desert Resort (Desert)',
  9 => 'Relic Castle (B1F)',
  10 => 'Relic Castle (B2F)',
  11 => 'Relic Castle (B3F)',
  12 => 'Relic Castle (B4F)',
  13 => 'Relic Castle (B5F)',
  14 => 'Relic Castle (B6F)',
  15 => 'Relic Castle (???)',
  16 => 'Relic Castle (???)',
  17 => 'Relic Castle (???)',
  18 => 'Relic Castle (???)',
  19 => 'Relic Castle (???)',
  20 => 'Relic Castle (???)',
  21 => 'Relic Castle (???)',
  22 => 'Relic Castle (???)',
  23 => 'Relic Castle (???)',
  24 => 'Relic Castle (???)',
  25 => 'Relic Castle (???)',
  26 => 'Relic Castle (???)',
  27 => 'Relic Castle (???)',
  28 => 'Relic Castle (???)',
  29 => 'Relic Castle (???)',
  30 => 'Relic Castle (Sanctum?)',
  31 => 'Relic Castle (Sanctum?)',
  32 => 'Relic Castle (???)',
  33 => 'Relic Castle (???)',
  34 => 'Relic Castle (???)',
  35 => 'Relic Castle (???)',
  36 => 'Relic Castle (???)',
  37 => 'Relic Castle (???)',
  38 => 'Relic Castle (???)',
  39 => 'Relic Castle (???)',
  40 => 'Cold Storage (Outside)',
  41 => 'Chargestone Cave (1F)',
  42 => 'Chargestone Cave (B1F)',
  43 => 'Chargestone Cave (B2F)',
  44 => 'Twist Mountain (???)',
  45 => 'Twist Mountain (???)',
  46 => 'Twist Mountain (???)',
  47 => 'Twist Mountain (???)',
  48 => 'Dragonspiral Tower (Entrance)',
  49 => 'Dragonspiral Tower (Outside)',
  50 => 'Dragonspiral Tower (1F)',
  51 => 'Dragonspiral Tower (2F)',
  52 => 'Victory Road (Outside)',
  53 => 'Victory Road (1F)',
  54 => 'Victory Road',
  55 => 'Victory Road',
  56 => 'Victory Road',
  57 => 'Victory Road',
  58 => 'Victory Road (3F)',
  59 => 'Victory Road',
  60 => 'Victory Road',
  61 => 'Victory Road',
  62 => 'Victory Road',
  63 => 'Victory Road',
  64 => 'Victory Road',
  65 => 'Victory Road',
  66 => 'Victory Road',
  67 => 'Victory Road',
  68 => 'Giant Chasm (Outside)',
  69 => 'Giant Chasm (Cave)',
  70 => 'Giant Chasm (Plains)',
  71 => 'Giant Chasm (Inner Cave)',
  72 => 'Route 17',
  73 => 'Undella Bay',
  74 => 'Driftveil Drawbridge',
  75 => 'Village Bridge',
  76 => 'Marvelous Bridge',
  77 => 'Route 1',
  78 => 'Route 2',
  79 => 'Route 3',
  80 => 'Wellspring Cave (1F)',
  81 => 'Wellspring Cave (B1F)',
  82 => 'Route 4',
  83 => 'Route 5',
  84 => 'Route 6',
  85 => 'Mistralton Cave (1F)',
  86 => 'Mistralton Cave (2F)',
  87 => 'Mistralton Cave (3F)',
  88 => 'Route 7',
  89 => 'Celestial Tower (2F)',
  90 => 'Celestial Tower (3F)',
  91 => 'Celestial Tower (4F)',
  92 => 'Celestial Tower (5F)',
  93 => 'Route 8',
  94 => 'Route 8',
  95 => 'Route 9',
  96 => 'Challenger\'s Cave (1F)',
  97 => 'Challenger\'s Cave (2F)',
  98 => 'Challenger\'s Cave (3F)',
  99 => 'Route 10',
  100 => 'Route 10 (???)',
  101 => 'Route 11',
  102 => 'Route 12',
  103 => 'Route 13',
  104 => 'Route 14',
  105 => 'Abundant Shrine',
  106 => 'Route 15',
  107 => 'Route 16',
  108 => 'Lostlorn Forest',
  109 => 'Route 18',
)
?>