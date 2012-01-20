<?php
Define('normal', 0);
Define('fighting', 1);
Define('flying', 2);
Define('poison', 3);
Define('ground', 4);
Define('rock', 5);
Define('bug', 6);
Define('ghost', 7);
Define('steel', 8);
if (!isset($bw)) {
Define('curse', 9);
Define('fire', 10);
Define('water', 11);
Define('grass', 12);
Define('electric', 13);
Define('psychic', 14);
Define('ice', 15);
Define('dragon', 16);
Define('dark', 17);
} else {
Define('fire', 9);
Define('water', 10);
Define('grass', 11);
Define('electric', 12);
Define('psychic', 13);
Define('ice', 14);
Define('dragon', 15);
Define('dark', 16);
}
Define('I', 0);
Define('R', .5);
Define('N', 1);
Define('W', 2);
$typelist = array(
'Norm',
'Fight',
'Fly',
'Psn',
'Grnd',
'Rock',
'Bug',
'Ghost',
'Steel',
'???',
'Fire',
'Wtr',
'Grass',
'Elec',
'Psy',
'Ice',
'Drgn',
'Dark');
$typelistBW = array(
'Norm',
'Fight',
'Fly',
'Psn',
'Grnd',
'Rock',
'Bug',
'Ghost',
'Steel',
'Fire',
'Water',
'Grass',
'Elec',
'Psy',
'Ice',
'Drgn',
'Dark');
$typelistBWIMG = array(
'normal',
'fighting',
'flying',
'poison',
'ground',
'rock',
'bug',
'ghost',
'steel',
'fire',
'water',
'grass',
'electric',
'psychic',
'ice',
'dragon',
'dark');
$typecolour = array(
0xA8A878,
0xC03028,
0xA890F0,
0xA040A0,
0xE0C068,
0xB8A038,
0xA8B820,
0x705898,
0xB8B8D0,
0x68A090,
0xF08030,
0x6890F0,
0x78C850,
0xF8D030,
0xF85888,
0x98D8D8,
0x7038F8,
0x705848);
/*				N  Fi Ps Fl Gh Bu Da El Ro Ic Fr Gr Wa St Gs Po Dr
$type[normal]		= array(N ,W ,N ,N ,I ,N ,N ,N ,N ,N ,N ,N ,N ,N ,N ,N ,N);
$type[fighting]		= array(N ,I ,W ,W ,N ,N ,R, N ,R ,N ,N ,N ,N ,N ,N ,N ,N);
$type[psychic]		= array(N ,R, R ,N ,W ,W ,W ,N ,N ,N ,N ,N ,N ,N ,N ,N ,N);
$type[flying]		= array(N ,R, N ,N ,N ,R ,N ,W ,W ,W ,N ,I ,N ,N ,R ,N ,N);
$type[ghost]		= array(I ,I ,N ,N ,W ,R ,W ,N ,N ,N ,N ,N ,N ,N ,N ,R ,N);
$type[bug]		= array(N ,R, N ,W ,N ,N ,N ,N ,W ,N ,W ,R ,N ,N ,R ,N ,N);
$type[dark]		= array(N ,W ,I ,N ,R ,W ,R ,N ,N ,N ,N ,N ,N ,N ,N ,N ,N);
$type[electric]		= array(N ,N ,N ,R ,N ,N ,N ,R ,N ,N ,N ,W ,N ,R ,N ,N ,N);
$type[rock]		= array(R ,W ,N ,R ,N ,N ,N ,N ,N ,N ,R ,W ,W ,W ,W ,N ,N);
$type[ice]		= array(N ,W ,N ,N ,N ,N ,N ,N ,W ,R ,W ,N ,N ,W ,N ,N ,N);
$type[fire]		= array(N ,N ,N ,N ,N ,R ,N ,N ,W ,R ,R ,R ,W ,R ,R ,N ,N);
$type[ground]		= array(N ,N ,N ,N ,N ,N ,N ,I ,R ,W ,N ,N ,W ,N ,W ,R ,N);
$type[water]		= array(N ,N ,N ,N ,N ,N ,N ,W ,N ,R ,R ,N ,R ,R ,W ,N ,N);
$type[steel]		= array(R ,W ,R ,R ,R ,R ,R ,N ,R ,R ,W ,W ,N ,R ,R ,I ,R);
$type[grass]		= array(N ,N ,N ,W ,N ,W ,N ,R ,N ,W ,W ,R ,R ,N ,R, W ,N);
$type[poison]		= array(N ,R, W ,N ,N ,R ,N ,N ,N ,N ,N ,W ,N ,N ,R, N ,N);
$type[dragon]		= array(N ,N ,N ,N ,N ,N ,N ,R ,N ,W ,R ,N ,R ,N ,R ,N ,W);*/
if (!isset($bw)) {
$type = array(
//			 N  Fi Fl Po Gn Ro Bu Gh St ?  Fr Wa Gr El Ps Ic Dg Dk
normal		=> array(N, W, N, N, N, N, N, I, N, N, N, N, N, N, N, N, N, N),
fighting	=> array(N, N, W, N, N, R, R, N, N, N, N, N, N, N, W, N, N, R),
flying		=> array(N, R, N, N, I, W, R, N, N, N, N, N, R, W, N, W, N, N),
poison		=> array(N, R, N, R, W, N, R, N, N, N, N, N, R, N, W, N, N, N),
ground		=> array(N, N, N, R, N, R, N, N, N, N, N, W, W, I, N, W, N, N),
rock		=> array(R, W, R, R, W, N, N, N, W, N, R, W, W, N, N, N, N, N),
bug			=> array(N, R, W, N, R, W, N, N, N, N, W, N, R, N, N, N, N, N),
ghost		=> array(I, I, N, R, N, N, R, W, N, N, N, N, N, N, N, N, N, W),
steel		=> array(R, W, R, I, W, R, R, R, R, N, W, N, R, N, R, R, R, R),
curse		=> array(N, N, N, N, N, N, N, N, N, N, N, N, N, N, N, N, N, N),
fire		=> array(N, N, N, N, W, W, R, N, R, N, R, W, R, N, N, R, N, N),
water		=> array(N, N, N, N, N, N, N, N, R, N, R, R, W, W, N, R, N, N),
grass		=> array(N, N, W, W, R, N, W, N, N, N, W, R, R, R, N, W, N, N),
electric	=> array(N, N, R, N, W, N, N, N, R, N, N, N, N, R, N, N, N, N),
psychic		=> array(N, R, N, N, N, N, W, W, N, N, N, N, N, N, R, N, N, W),
ice			=> array(N, W, N, N, N, W, N, N, W, N, W, N, N, N, N, R, N, N),
dark		=> array(N, W, N, N, N, N, W, R, N, N, N, N, N, N, I, N, N, R),
dragon		=> array(N, N, N, N, N, N, N, N, N, N, R, R, R, R, N, W, W, N));
} else {

$type = array(
//					 N  Fi Fl Po Gn Ro Bu Gh St Fr Wa Gr El Ps Ic Dg Dk
normal		=> array(N, W, N, N, N, N, N, I, N, N, N, N, N, N, N, N, N),
fighting	=> array(N, N, W, N, N, R, R, N, N, N, N, N, N, W, N, N, R),
flying		=> array(N, R, N, N, I, W, R, N, N, N, N, R, W, N, W, N, N),
poison		=> array(N, R, N, R, W, N, R, N, N, N, N, R, N, W, N, N, N),
ground		=> array(N, N, N, R, N, R, N, N, N, N, W, W, I, N, W, N, N),
rock		=> array(R, W, R, R, W, N, N, N, W, R, W, W, N, N, N, N, N),
bug			=> array(N, R, W, N, R, W, N, N, N, W, N, R, N, N, N, N, N),
ghost		=> array(I, I, N, R, N, N, R, W, N, N, N, N, N, N, N, N, W),
steel		=> array(R, W, R, I, W, R, R, R, R, W, N, R, N, R, R, R, R),
fire		=> array(N, N, N, N, W, W, R, N, R, R, W, R, N, N, R, N, N),
water		=> array(N, N, N, N, N, N, N, N, R, R, R, W, W, N, R, N, N),
grass		=> array(N, N, W, W, R, N, W, N, N, W, R, R, R, N, W, N, N),
electric	=> array(N, N, R, N, W, N, N, N, R, N, N, N, R, N, N, N, N),
psychic		=> array(N, R, N, N, N, N, W, W, N, N, N, N, N, R, N, N, W),
ice			=> array(N, W, N, N, N, W, N, N, W, W, N, N, N, N, R, N, N),
dark		=> array(N, W, N, N, N, N, W, R, N, N, N, N, N, I, N, N, R),
dragon		=> array(N, N, N, N, N, N, N, N, N, R, R, R, R, N, W, W, N));
}
?>
