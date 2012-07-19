<?php

class gen4text implements Iterator, ArrayAccess {
	private $numlines;
	private $basekey;
	private $data;
	
	function __construct($data) {
		$this->data = $data;
		$this->chars = yaml_parse_file('gen4chars.yml');
		
		$tmp = unpack('ventries/vkey', substr($this->data, 0, 4));
		$this->numlines = $tmp['entries'];
		$this->basekey = $tmp['key'];
	}
	public function offsetExists($id) { return ($this->numlines > $id); }
	public function offsetGet($id) { return $this->fetchline($id); }
	public function offsetSet($id, $val) { throw new Exception('Read Only'); }
	public function offsetUnset($id) { throw new Exception('Read Only'); }

	public function rewind() { $this->fileid = 0; }
	public function current() { return $this->fetchline($this->fileid); }
	public function key() { return $this->fileid; }
	public function next() { ++$this->fileid; }
	public function valid() { return $this->fileid < $this->numlines;}
	public function dump() {
		$output = array();
		foreach ($this as $line)
			$output[] = $line;
		return $output;
	}
	
	private function fetchline($id) {
		$location = unpack('Vptr/Vsize', substr($this->data, 4+$id*8, 8));
		$tmpkey = $this->getKey($id);
		$location['ptr'] ^= $tmpkey;
		$location['size'] ^= $tmpkey;
		return $this->decrypttext(array_merge(unpack('v*', substr($this->data, $location['ptr'], $location['size']*2))), (0x1BD3*($id+1))&0xffff);
	}
	private function getKey($id) {
		$ktmp = ($this->basekey*0x2FD*($id+1))&0xFFFF;
        return $ktmp | ($ktmp<<16);
	}
	private function decrypttext($chars, $key) {
		foreach ($chars as &$char) {
			$char ^= $key;
			$key = ($key + 0x493D)&0xFFFF;
		}
		$output = '';
		$cap = 0;
		foreach ($chars as $b) {
			if ($cap) {
				if ($cap == 3)
					$output .= '[';
				$output .= sprintf('%04X', $b);
				if ($cap == 1)
					$output .= ']';
				$cap--;
				continue;
			}
			if ($b == 0xFFFF)
				break;
			else if ($b == 0xFFFE) {
				$cap = 3;
				continue;
			}
			$output .= isset($this->chars[$b]) ? $this->chars[$b] : sprintf('[%04X]', $b);
		}
		return $output;
	}
}

class gen4text_old implements Iterator,ArrayAccess {
	private $narc;
	private $fileid = 0;
	private $numfiles;
	private $cachedfilename = 'x';
	private $cachedfile;
	private $chars;
	
	public function offsetExists($id) { return ($this->numfiles > $id); }
	public function offsetGet($id) { return $this->fetchfile($id); }
	public function offsetSet($id, $val) { throw new Exception('Read Only'); }
	public function offsetUnset($id) { throw new Exception('Read Only'); }

	public function rewind() { $this->fileid = 0; }
	public function current() { return $this->fetchfile($this->fileid); }
	public function key() { return $this->fileid; }
	public function next() { ++$this->fileid; }
	public function valid() { return $this->fileid < $this->numfiles;}
	
	function __construct($file) {
		if ($file instanceof NARCfile)
			$this->narc = $file;
		else {
			if (!file_exists($file))
				die($file.' not found');
			$this->narc = new NARCfile($file);
		}
		$d = $this->narc->getdetails();
		$this->numfiles = $d['numfiles'];
		$this->chars = yaml_parse_file('gen4chars.yml');
	}
	public function fetchfile($id) {
		return $this->getline($id, -1);
	}
	public function fetchline($file, $line) {
		return $this->getline($file, $line);
	}
	private function getkey($basekey, $id) {
		$ktmp = ($basekey*0x2FD*($id+1))&0xFFFF;
        return $ktmp | ($ktmp<<16);
	}
	private function getline($file, $line) {
		if ($this->cachedfilename !== $file) {
			$this->cachedfile = $this->narc[$file];
			$this->cachedfilename = $file;
		}
		$header = unpack('ventries/vkey', substr($this->cachedfile, 0, 4));
		if ($line < 0) {
			for ($i = 0; $i < $header['entries']; $i++) {
				$location = unpack('Vptr/Vsize', substr($this->cachedfile, 4+$i*8, 8));
				$text[] = $this->decrypttext($this->cachedfile, $location['ptr'], $i, $location['size'], $header['key']);
			}
		} else {
			$location = unpack('Vptr/Vsize', substr($this->cachedfile, 4+$line*8, 8));
			$text = $this->decrypttext($this->cachedfile, $location['ptr'], $line, $location['size'], $header['key']);
		}
		return $text;
	}
	private function decrypttext(&$file, $offset, $id, $len, $ptrkey) {
		$ptrkey = $this->getkey($ptrkey, $id);
		$offset ^= $ptrkey;
		$len ^= $ptrkey;
		$key = (0x1BD3*($id+1))&0xffff;
		for ($i = 0; $i < $len; $i++) {
			$chars[] = (ord($file[$offset+$i*2])+(ord($file[$offset+$i*2+1])<<8))^$key;
			$key = ($key + 0x493D)&0xFFFF;
		}
		$output = '';
		$cap = 0;
		foreach ($chars as $b) {
			if ($cap) {
				if ($cap == 3)
					$output .= '[';
				$output .= sprintf('%04X', $b);
				if ($cap == 1)
					$output .= ']';
				$cap--;
				continue;
			}
			if ($b == 0xFFFF)
				break;
			else if ($b == 0xFFFE) {
				$cap = 3;
				continue;
			}
			$output .= isset($this->chars[$b]) ? $this->chars[$b] : sprintf('[%04X]', $b);
		}
		return $output;
	}
}
?>
