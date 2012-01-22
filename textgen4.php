<?php
require_once 'narc.php';
require_once 'gen4_textlib.php';
$plattextfile = 'narcs/plat/msgdata/pl_msg.narc';
$dptextfile = 'narcs/dp/msgdata/msg.narc';
$hgsstextfile = 'narcs/hgss/0/2/7';
class gen4text implements Iterator {
	private $narc;
	private $fileid = 0;
	private $numfiles;
	
	function __construct($file) {
		if (!file_exists($file))
			die($file.' not found');
		$this->narc = new NARCfile($file);
		$d = $this->narc->getdetails();
		$this->numfiles = $d['numfiles'];
	}
	private function getkey($basekey, $id) {
		$ktmp = ($basekey*0x2FD*($id+1))&0xffff;
        return $ktmp | ($ktmp<<16);
	}
	public function fetchfile($id) {
		$f = $this->narc->getfile($id);
		$header = unpack('ventries/vkey', substr($f, 0, 4));
		for ($i = 0; $i < $header['entries']; $i++) {
			$location = unpack('Vptr/Vsize', substr($f, 4+$i*8, 8));
			$text[] = $this->decrypttext(&$f, $location['ptr'], $i, $location['size'], $header['key']);
		}
		return $text;
	}
	
	public function fetchline($file, $line) {
		$f = $this->narc->getfile($file);
		$header = unpack('ventries/vkey', substr($f, 0, 4));
		if ($line >= $entries)
			return 'Out of range';
		$location = unpack('Vptr/Vsize', substr($f, 4+$line*8, 8));
		return $this->decrypttext(&$f, $location['ptr'], $line, $location['size'], $header['key']);
	}
	
	private function decrypttext(&$file, $offset, $id, $len, $ptrkey) {
		global $tbl;
		$ptrkey = $this->getkey($ptrkey, $id);
		$offset ^= $ptrkey;
		$len ^= $ptrkey;
		$key = (0x91BD3*($id+1))&0xffff;
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
			$output .= isset($tbl[$b]) ? $tbl[$b] : sprintf('[%04X]', $b);
		}
		return $output;
	}
	function rewind() { $this->fileid = 0; }
	function current() { return $this->fetchfile($this->fileid); }
	function key() { return $this->fileid; }
	function next() { ++$this->fileid; }
	function valid() { return $this->fileid < $this->numfiles;}
}
if (array_search(__FILE__,get_included_files()) == 0) {
	require_once '../hexview.php';
	set_time_limit(10000);
	$v = microtime(true);
	header('Content-Type: text/plain; charset=utf-8');
	$text = new gen4text($dptextfile);
	$v2 = microtime(true);
	$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	if (array_key_exists(1, $argc) && ($argc[1] != null))
		$textdata['textfiles'][] = $text->fetchfile($argc[1]);
	else {
		$i = 0;
		foreach ($text as $file)
			$textdata['textfiles'][] = $file;
	}
	$v3 = microtime(true);
	printf('%01.3f - %01.3f'.PHP_EOL,$v3-$v, $v2-$v);
	echo yaml_emit($textdata);
}
?>