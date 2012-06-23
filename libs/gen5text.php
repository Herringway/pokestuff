<?php
class gen5text implements Iterator,ArrayAccess {
	private $narc;
	private $fileid = 0;
	private $numfiles;
	private $numsections;
	private $sections;
	private $cachedfilename = 'x';
	private $cachedfile;

	public function offsetExists($id) {
		return ($this->numfiles > $id);
	}
	public function offsetGet($id) {
		return $this->fetchfile($id);
	}
	public function offsetSet($id, $val) {
		throw new Exception('Read Only');
	}
	public function offsetUnset($id) {
		throw new Exception('Read Only');
	}
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
		$this->sections[0]['offset'] = 0x10 + $this->numsections * 4;
		$this->sections[0]['length'] = unpack('Vlength', substr($this->cachedfile, 4, 4))['length'];
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
			for ($j = 0; $j < $entries; $j++) {
				$location = unpack('Vptr/vsize', substr($this->cachedfile, $this->sections[$i]['offset'] + $j*8, 6));
				$text[] = $this->decrypttext($this->cachedfile, array_merge(unpack('v*', substr($this->cachedfile,$location['ptr']+$this->sections[$i]['offset']-4,$location['size']*2))));
			}
		}
		return $text;
	}

	public function fetchline($file, $line, $section = 0) {
		$this->openfile($file);
		$ptr = $this->sections[$section]['offset']+readint_str($this->cachedfile, $this->sections[$section]['offset'] + $line*8)-4;
		$len = readshort_str($this->cachedfile, $this->sections[$section]['offset'] + $line*8 + 4);
		return $this->decrypttext($this->narc->getfile($file), array_merge(unpack('v*', substr($this->cachedfile,$ptr,$len*2))));
	}

	private function decrypttext($file, $chars) {
		static $specchars = array(0x246D => '?', 0x246E => '?', 0x2486 => 'PK', 0x2487 => 'MN', 0xFF28 => 'H', 0xFF30 => 'P', 0xFFFE => "\n");
		if ($chars == array())
			return;
		$chars = array_reverse($chars);
		$key = $chars[0]^0xFFFF;
		array_shift($chars);
		$output = '';
		foreach ($chars as &$c)
			$c ^= ($key = ($key>>3) + (($key&0x7)<<13));
		$chars = array_reverse($chars);
		$cap = 0;
		foreach ($chars as $b) {
			if ($cap > 0) {
				$cap--;
				$val += $b<<($cap*8);
				if ($cap == 0)
					$output .= sprintf('[%08X]', $val);
				continue;
			}
			if ($b == 0xF000) {
				$cap = 2;
				$val = 0;
			} else if (isset($specchars[$b]))
				$output .= $specchars[$b];
			else if (($b >= 0xFF10) && ($b <= 0xFF1A))
				$output .= chr($b-0xFEE0);
			else if ($b == 0xF100)
				$doubledecrypt = true;
			else if ($b == 0xFFFF) { }
			else if (($b > 0xF000) || ($b < 31))
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
?>