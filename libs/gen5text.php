<?php
class gen5text implements Iterator, ArrayAccess {
	private $fileid;
	private $numlines;
	private $sections;
	private $data;
	
	function __construct($data) {
		$this->data = $data;
		$numsections = readshort_str($data, 0);
		$this->sections[] = array('offset' => 0x10 + $numsections * 4, 'length' => unpack('Vlength', substr($data, 4, 4))['length']);
		if ($numsections > 1) {
			$v = readint_str($data,0x14)+0x18;
			$this->sections[] = array('offset' => $v, 'length' => readint_str($data, $v-4));
		}
		
		$this->numlines = readshort_str($data, 2);
	}
	public function dump() {
		$output = array();
		foreach ($this as $line)
			$output[] = $line;
		return $output;
	}
	public function count() {
		return $this->numlines;
	}
	
	public function offsetExists($id) { return ($this->numlines > $id); }
	public function offsetGet($id) { return $this->readline($id); }
	public function offsetSet($id, $val) { throw new Exception('Read Only'); }
	public function offsetUnset($id) { throw new Exception('Read Only'); }
    
	public function rewind() { $this->fileid = 0; }
    public function current() { return $this->readline($this->fileid); }
    public function key() { return $this->fileid; }
    public function next() { ++$this->fileid; }
    public function valid() { return $this->fileid < $this->numlines; }
	private function readline($line, $section = 0) {
		$ptr = $this->sections[$section]['offset']+readint_str($this->data, $this->sections[$section]['offset'] + $line*8)-4;
		$len = readshort_str($this->data, $this->sections[$section]['offset'] + $line*8 + 4);
		return $this->decrypttext(array_merge(unpack('v*', substr($this->data, $ptr, $len*2))));
	}
	private function decrypttext($chars) {
		static $specchars = array(0x246D => '♂', 0x246E => '♀', 0x2486 => 'PK', 0x2487 => 'MN', 0xFF28 => 'H', 0xFF30 => 'P', 0xFFFE => "\n");
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
			else if (($b == 0xFF00) || ($b > 0xFFEF) || ($b < 31))
				$output .= sprintf('[%04x]', $b);
			else
				$output .= json_decode(sprintf('"\u%04X"',$b));
		}
		return $output;
	}
}
?>