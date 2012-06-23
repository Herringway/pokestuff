<?php

class gen4text implements Iterator {
	private $narc;
	private $fileid = 0;
	private $numfiles;
	private $cachedfilename = 'x';
	private $cachedfile;
	private $chars;
	
	function __construct($file) {
		if (!file_exists($file))
			die($file.' not found');
		$this->narc = new NARCfile($file);
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
			$this->cachedfile = $this->narc->getfile($file);
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
	function rewind() { $this->fileid = 0; }
	function current() { return $this->fetchfile($this->fileid); }
	function key() { return $this->fileid; }
	function next() { ++$this->fileid; }
	function valid() { return $this->fileid < $this->numfiles;}
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