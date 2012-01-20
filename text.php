<?php
require_once 'narc.php';
class gen5text implements Iterator {
	private $narc;
	private $fileid = 0;
	private $numfiles;
	private $numsections;

	function __construct($file) {
		if (!file_exists($file))
			die($file.' not found');
		$this->narc = new NARCfile($file);
		$d = $this->narc->getdetails();
		$this->numfiles = $d['numfiles'];
	}

	public function fetchfile($id) {
		$f = $this->narc->getfile($id);
		$this->numsections = readshort_str($f, 0);
		$sections[0]['offset'] = $this->numsections > 1 ? 0x18 : 0x14;
		$sections[0]['length'] = readint_str($f,4);
		if ($this->numsections > 1) {
			$sections[1]['offset'] = readint_str($f,0x14)+0x18;
			$sections[1]['length'] = readint_str($f, $sections[1]['offset']-4);
		}
		$entries = readshort_str($f, 2);
		$text = array();
		for ($i = 0; $i < $this->numsections; $i++) {
			for ($j = 0; $j < $entries; $j++) {
				$ptr = $sections[$i]['offset']+readint_str($f, $sections[$i]['offset'] + $j*8)-4;
				$len = readshort_str($f, $sections[$i]['offset'] + $j*8 + 4);
				if (array_key_exists('deb', $_GET))
					printf('%u (%u)<br>',$ptr,$len);
				$text[$i]['textlines'][] = $this->decrypttext($f, $ptr, $len);
			}
		}
		return $text;
	}

	public function fetchline($file, $line) {
		//$f = $this->narc->getfile($file);
		//return $this->decrypttext($f, $ptr, $line, $size);
	}

	private function decrypttext($file, $offset, $len) {
		global $tbl;
		$specchars = array(0x246D => '♂', 0x246E => '♀', 0x2486 => 'PK', 0x2487 => 'MN', 0xFF28 => 'H', 0xFF30 => 'P', 0xFFFE => "\n");
		$chars = array(); $rawchars = array();
		for ($i = 0; $i < $len; $i++) {
			if (array_key_exists('deb', $_GET))
				printf('c: %u<br>', readshort_str($file, $offset+$i*2));
			$rawchars[] = readshort_str($file, $offset+$i*2);
		}
		if ($rawchars == array())
			return;
		$rawchars = array_reverse($rawchars);
		$key = $rawchars[0]^0xFFFF;
		array_shift($rawchars);
		foreach ($rawchars as $c) {
			if (array_key_exists('deb', $_GET))
				echo 'k:'.$key.'<br>';
			$key = ($key>>3) + (($key&0x7)<<13);
			$chars[] = $c^$key;
		}
		$chars = array_reverse($chars);
		$output = '';
		$cap = 0;
		foreach ($chars as $b) {
			if ($cap > 0) {
				$cap--;
				$val += $b<<($cap*8);
				if ($cap == 0)
					$output .= sprintf('[%012X]', $val);
				continue;
			}
			if ($b == 0xFFFF)
				break;
			else if ($b == 0xF000) {
				$cap = 3;
				$val = 0;
			}
			else if (array_key_exists($b, $specchars))
				$output .= $specchars[$b];
			else if (($b >= 0xFF10) && ($b <= 0xFF1A))
				$output .= chr($b-0xFEE0);
			else if ($b == 0xF100)
				$doubledecrypt = true;
			else if ((($b & 0xF000) == 0xF000) || ($b < 31))
				$output .= sprintf('[%04x]', $b);
			else
				$output .= unichr($b);
		}
		return $output;
	}
	public function __get($property) {
		if ($property == 'files')
			return $this->numfiles;
	}

    function rewind() {
        $this->fileid = 0;
    }

    function current() {
        return $this->fetchfile($this->fileid);
    }

    function key() {
        return $this->fileid;
    }

    function next() {
        ++$this->fileid;
    }

    function valid() {
        return $this->fileid < $this->numfiles;
    }

}
function unichr($u) {
	return mb_convert_encoding(pack("N",$u), 'UTF-8', 'UCS-2BE');
}
function readshort_str($data, $offset) {
	return ord($data[$offset]) + (ord($data[$offset+1])<<8);
}
function readint_str($data, $offset) {
	return ord($data[$offset]) + (ord($data[$offset+1])<<8) + (ord($data[$offset+2])<<16) + (ord($data[$offset+3])<<24);
}
function readlongint_str($data,$offset) {
	return readvarnum($data, $offset, 8);
}

function readvarnum($data, $offset, $size) {
	$num = 0;
	for ($i = 0; $i < $size; $i++)
		$num += ord($data[$offset+$i])<<($i*8);
	return $num;
}
function decryptText($data) {
	if (!array_key_exists(0, $data))
		return array(ord('B'),ord('A'),ord('D'));
	foreach ($data as $d)
		if (array_key_exists('deb',$_GET))
			printf('c: %u<br>', $d);
	$data = array_reverse($data);
	$key = $data[0]^0xFFFF;
	array_shift($data);
	for ($i = 0; $i < count($data); $i++) {
		if (array_key_exists('deb', $_GET))
			echo 'k:'.$key.'<br>';
		$key = rrot($key, 3, 16);
		$data[$i] = $data[$i]^$key;
	}
	return array_reverse($data);
}

function rrot($byte, $offset = 1, $size = 8) {
	return ($byte>>($offset%$size)) + (($byte&(pow(2,($offset%$size))-1))<<($size-($offset%$size)));
}

function lrot($byte, $offset = 1, $size = 8) {
	return ($byte<<($offset%$size)) + (($byte&(pow(2,($offset%$size))-1))<<($size-($offset%$size)));
}

function decryptTwo($data) {
	$output = '';
	$bytes = array();
	foreach ($data as $byte) {
		$bytes[] = $byte&0xFF;
		$bytes[] = $byte>>8;
	}
	if (count($bytes) > 8)
		$k = $bytes[count($bytes)-1] + ($bytes[count($bytes)-2]<<8);
	$k = rrot($bytes[count($bytes)-1],count($bytes)-1);
	$position = 0;
	foreach ($bytes as $b)
			$output .= @iconv('ASCII', 'UTF-16BE', chrprocess($b,$position++, $k));
	return $output;
}


function chrprocess($chr, $pos, $key) {
	if (array_key_exists('decode', $_GET))
		return chr(rrot($chr, $pos) | 0x40);
		//return chr(rrot($chr, $pos)^($key&(1<<(7-$pos)) ? $mask : 0));
	return sprintf('[%02x]', $chr);
}

function string_to_poketext($data) {
	$output = ''; $skipbyte = 0; $showskipped = false; $debug = false; $doubledecrypt = false; $newarray = array();
	$replacements = array(0x010000010000 => '[YOUR NAME]',0x010100010000 => '[POKEMON]', 0x010100010001 => '[POKEMON2]', 0x010100010002 => '[POKEMON3]', 0x010100010003 => '[POKEMON4]', 0x010100010004 => '[POKEMON5]', 0x010100010005 => '[POKEMON6]', 0x010200010000 => '[POKEMON]', 0x010200010001 => '[ENEMY]', 0x010200010002 => '[ENEMY2]', 0x010600010001 => '[ABILITY]', 0x010600010002 => '[ENEMYABILITY]', 0x010700010001 => '[MOVE]', 0x010900010000 => '[ITEM]', 0x010900010001 => '[HELD ITEM]', 0x010900010002 => '[ENEMY\'S HELD ITEM]', 0x020000010001 => '[COUNTER]', 0x020000010002 => '[COUNTER 2]', 0x020200010000 => '[LEVEL]', 0x020600010000 => '[PRICE]', 0xbe000000fffe => ' ',0xbe010000fffe => '[PAUSE]', 0xbd0200000000 => '', 0xff0000010002 => '<font color="blue">', 0xff0000010000 => '</font>');
	foreach (decryptText(StringToCharArray($data)) as $byte) {
		if (($skipbyte > 0) || ($debug)) {
			$skipbyte--;
			$val += $byte<<$skipbyte*16;
			if ($byte == 0xbd02)
				$skipbyte--;
			if ($skipbyte == 0) {
				$output .= iconv('ASCII', 'UTF-16BE', array_key_exists($val, $replacements) ? $replacements[$val] : sprintf('[%012x]', $val));
				$showskipped = false;
			}
			continue;
		}
		if ($doubledecrypt) {
			$newarray[] = $byte;
			continue;
		}
		if ($byte == 0xFFFF) {}
		else if ($byte == 0x246D)
			$output .= iconv('UTF-8', 'UTF-16BE', '♂');
		else if ($byte == 0x246E)
			$output .= iconv('UTF-8', 'UTF-16BE', '♀');
		else if ($byte == 0x2486)
			$output .= iconv('ASCII', 'UTF-16BE', "PK");
		else if ($byte == 0x2487)
			$output .= iconv('ASCII', 'UTF-16BE', "MN");
		else if ($byte == 0xBE01) 
			$output .= iconv('ASCII', 'UTF-16BE', sprintf('[%04x]', $byte));
		else if ($byte == 0xFFFE)
			$output .= iconv('ASCII', 'UTF-16BE', "\n");
		else if (($byte >= 0xFF10) && ($byte <= 0xFF1A))
			$output .= iconv('ASCII', 'UTF-16BE', chr($byte-0xFEE0));
		else if ($byte == 0xFF28)
			$output .= iconv('ASCII', 'UTF-16BE', 'H');
		else if ($byte == 0xFF30)
			$output .= iconv('ASCII', 'UTF-16BE', 'P');
		else if ($byte == 0xFFFE)
			$output .= iconv('ASCII', 'UTF-16BE', "\n");
		else if ($byte == 0xF100)
			$doubledecrypt = true;
		else if ($byte == 0xF000) {
			$skipbyte += 3;
			$val = 0;
			$showskipped = true;
		} else if ((($byte & 0xF000) == 0xF000) || ($byte < 31))
			$output .= iconv('ASCII', 'UTF-16BE', sprintf('[%04x]', $byte));
		else if ($byte != 0xFFFF)
			$output .= chr(($byte&0xFF00)>>8).chr($byte&0xFF);
	}
	if ($doubledecrypt)
		$output .= decryptTwo($newarray);
	return iconv('UCS-2BE', 'UTF-8', $output);
}
function StringToCharArray($data) {
	$output = array();
	$offset = 0;
	for ($i = 0; $i < strlen($data)/2; $i++)
		$output[] = ((ord($data[$offset++])) + (ord($data[$offset++])<<8));
	return $output;
}

function readString($file, $subfile, $line, $lines = 1) {
	$data = getfile($file, $subfile);
	$section = readHeader($data);
	if ($line+$lines-1 > $section[0][1])
		return 'BAD';
	$data = readSection($data, $section[0][0], $section[0][1]);
	if ($lines <= 1) {
		$offset = readint_str($data, $line*8)-4;
		$len = readshort_str($data, $line*8+4)*2;
		return string_to_poketext(substr($data,$offset,$len));
	} else {
		$output = array();
		for ($i = 0; $i < $lines; $i++) {
			$offset = readint_str($data, ($line+$i)*8)-4;
			$len = readshort_str($data, ($line+$i)*8+4)*2;
			$output[] = string_to_poketext(substr($data,$offset,$len));
		}
		return $output;
	}
}

function readHeader($data) {
	$numsections = readshort_str($data, 0);
	$sections[0][0] = $numsections > 1 ? 0x18 : 0x14;
	$sections[0][1] = readint_str($data,4);
	if ($numsections > 1) {
		$sections[1][0] = readint_str($data,0x14)+0x18;
		$sections[1][1] = readint_str($data, readint_str($data,0x14)+0x14);
	}
	return $sections;
}
function readSubfile($file, $subfile) {
	$output = array();
	$data = getfile($file, $subfile);
	$numsections = readshort_str($data, 0);

	$sections = readHeader($data);
	$numlines = readshort_str($data, 2);
	$a = 0;
	foreach ($sections as $section) {
		$sectiondata = readSection($data, $section[0], $section[1]);
		for ($i = 0; $i < $numlines; $i++) {
			$thisstr = substr($sectiondata, readint_str($sectiondata, $i*8)-4,readshort_str($sectiondata, $i*8+4)*2);
			if (array_key_exists('deb', $_GET))
				printf('%u (%u)<br>',$section[0]+readint_str($sectiondata, $i*8)-4,readshort_str($sectiondata, $i*8+4));
			$output[$a]['textlines'][] = string_to_poketext($thisstr);
		}
		$a++;
	}
	return $output;
}
function readSection($data, $offset, $length) {
	$newdata = substr($data, $offset, $length);
	return $newdata;

}
if (array_search(__FILE__,get_included_files()) == 0) {
	$text = array();
	$narcfile = 'narcs/weng/0/0/2';
	if (array_key_exists('story', $_GET))
		$narcfile = 'narcs/weng/0/0/3';
	header('Content-Type: text/plain; charset=utf-8');
	$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	if (!array_key_exists('newtext', $_GET)) {
		$lines = analyze_narc($narcfile);
		$t = 174;
		if (($argc[1] != null) && ($argc[1] <= $lines['Files']))
			$t = $argc[1];
		if ($argc[1] == null) {
			for ($t = 0; $t < $lines['Files']; $t++)
				$text['textfiles'][] = readSubfile($narcfile, $t);
		} else
			$text['textfiles'][] = readSubfile($narcfile, $t);
	} else {
		set_time_limit(500);
		$textobject = new gen5text($narcfile);
		$t = 174;
		if (($argc[1] != null) && ($argc[1] <= $textobject->files))
			$t = $argc[1];
		if ($argc[1] == null) {
			for ($t = 0; $t < $textobject->files; $t++)
				$text['textfiles'][] = $textobject->fetchfile($t);
		} else
			$text['textfiles'][] = $textobject->fetchfile($t);
	}
	echo yaml_emit($text);
}
?>
