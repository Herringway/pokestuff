<?php
require_once 'narc.php';
class gen5text implements Iterator {
	private $narc;
	private $fileid = 0;
	private $numfiles;
	private $numsections;
	private $sections;
	private $cachedfilename = 'x';
	private $cachedfile;

	function __construct($file) {
		if (!file_exists($file))
			throw new Exception($file.' not found');
		$this->narc = new NARCfile($file);
		$d = $this->narc->getdetails();
		$this->numfiles = $d['numfiles'];
	}
	private function openfile($id) {
		if ($this->cachedfilename !== $id) {
			$this->cachedfile = $this->narc->getfile($id);
			$this->cachedfilename = $id;
		}
		$this->numsections = readshort_str($this->cachedfile, 0);
		$this->sections[0]['offset'] = $this->numsections > 1 ? 0x18 : 0x14;
		$this->sections[0]['length'] = readint_str($this->cachedfile,4);
		if ($this->numsections > 1) {
			$this->sections[1]['offset'] = readint_str($this->cachedfile,0x14)+0x18;
			$this->sections[1]['length'] = readint_str($this->cachedfile, $this->sections[1]['offset']-4);
		}
	}
	public function fetchfile($id) {
		$this->openfile($id);
		$entries = readshort_str($this->cachedfile, 2);
		$text = array();
		for ($i = 0; $i < $this->numsections; $i++) {
			$tmpoutput = array();
			for ($j = 0; $j < $entries; $j++) {
				$ptr = $this->sections[$i]['offset']+readint_str($this->cachedfile, $this->sections[$i]['offset'] + $j*8)-4;
				$len = readshort_str($this->cachedfile, $this->sections[$i]['offset'] + $j*8 + 4);
				$tmpoutput[] = $this->decrypttext($this->cachedfile, array_merge(unpack('v*', substr($this->cachedfile,$ptr,$len*2))));
			}
			if ($this->numsections == 1)
				$text = $tmpoutput;
			else 
				$text[] = $tmpoutput;
		}
		return $text;
	}

	public function fetchline($file, $line, $section = 0) {
		$this->openfile($file);
		$ptr = $this->sections[$section]['offset']+readint_str($this->cachedfile, $this->sections[$section]['offset'] + $line*8)-4;
		$len = readshort_str($this->cachedfile, $this->sections[$section]['offset'] + $line*8 + 4);
		return $this->decrypttext($this->narc->getfile($file), array_merge(unpack('v*', substr($this->cachedfile,$ptr,$len*2))));
	}

	private function decrypttext($file, $rawchars) {
		global $tbl;
		$specchars = array(0x246D => '♂', 0x246E => '♀', 0x2486 => 'PK', 0x2487 => 'MN', 0xFF28 => 'H', 0xFF30 => 'P', 0xFFFE => "\n");
		$chars = array(); 
		if ($rawchars == array())
			return;
		$rawchars = array_reverse($rawchars);
		$key = $rawchars[0]^0xFFFF;
		array_shift($rawchars);
		foreach ($rawchars as $c) {
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
			else if (isset($specchars[$b]))
				$output .= $specchars[$b];
			else if (($b >= 0xFF10) && ($b <= 0xFF1A))
				$output .= chr($b-0xFEE0);
			else if ($b == 0xF100)
				$doubledecrypt = true;
			else if ((($b & 0xF000) == 0xF000) || ($b < 31))
				$output .= sprintf('[%04x]', $b);
			else
				$output .= json_decode(sprintf('"\u%04X"',$b));
		}
		return $output;
	}
	public function __get($property) {
		if ($property == 'files')
			return $this->numfiles;
	}

    function rewind() { $this->fileid = 0; }

    function current() { return $this->fetchfile($this->fileid); }

    function key() { return $this->fileid; }

    function next() { ++$this->fileid; }

    function valid() { return $this->fileid < $this->numfiles; }

}
function readshort_str(&$data, $offset) {
	$b = unpack('v', substr($data,$offset, 4));
	return $b[1];
}
function readint_str(&$data, $offset) {
	$b = unpack('V', substr($data,$offset, 4));
	return $b[1];
}
if (array_search(__FILE__,get_included_files()) == 0) {
	$text = array();
	$narcfile = 'narcs/weng/0/0/2';
	if (isset($_GET['story']))
		$narcfile = 'narcs/weng/0/0/3';
	header('Content-Type: text/plain; charset=utf-8');
	$argc = (isset($_SERVER['PATH_INFO']) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	set_time_limit(500);
	$textobject = new gen5text($narcfile);
	$t = 174;
	if (isset($argc[1]) && ($argc[1] <= $textobject->files))
		$t = $argc[1];
	if (!isset($argc[1])) {
		for ($t = 0; $t < $textobject->files; $t++)
			$text['textfiles'][] = $textobject->fetchfile($t);
	} else
		$text[] = $textobject->fetchfile($t);
	echo yaml_emit($text);
}
?>
