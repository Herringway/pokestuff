<?php
require_once 'narc.php';
require_once 'gen4_textlib.php';
$plattextfile = 'narcs/plat/msgdata/pl_msg.narc';
$dptextfile = 'narcs/dp/msgdata/msg.narc';
$hgsstextfile = 'narcs/hgss/0/2/7';
class gen4text implements Iterator {
	private $narc;
	private $key;
	private $fileid = 0;
	private $numfiles;
	
	function __construct($file) {
		if (!file_exists($file))
			die($file.' not found');
		$this->narc = new NARCfile($file);
		$d = $this->narc->getdetails();
		$this->numfiles = $d['numfiles'];
	}
	
	public function fetchfile($id) {
		$f = $this->narc->getfile($id);
		$entries = ord($f[0]) + (ord($f[1])<<8);
		$this->key = ((ord($f[2]) + (ord($f[3])<<8))*0x2FD)&0xFFFF;
		for ($i = 0; $i < $entries; $i++) {
			$ktmp = ($this->key*($i+1)&0xffff);
            $ktmp2 = $ktmp | ($ktmp<<16);
			$ptr = (ord($f[4+$i*8])+(ord($f[5+$i*8])<<8)+(ord($f[6+$i*8])<<16)+(ord($f[7+$i*8])<<24))^$ktmp2;
			$size = (ord($f[8+$i*8])+(ord($f[9+$i*8])<<8)+(ord($f[10+$i*8])<<16)+(ord($f[11+$i*8])<<24))^$ktmp2;
			$text[0]['textlines'][] = $this->decrypttext($f, $ptr, $i, $size);
		}
		return $text;
	}
	
	public function fetchline($file, $line) {
		$f = $this->narc->getfile($file);
		$entries = ord($f[0]) + (ord($f[1])<<8);
		if ($line >= $entries)
			return 'Out of range';
		$this->key = ((ord($f[2]) + (ord($f[3])<<8))*0x2FD)&0xFFFF;
		$ktmp = ($this->key*($line+1)&0xffff);
        $ktmp2 = $ktmp | ($ktmp<<16);
		$ptr = (ord($f[4+$line*8])+(ord($f[5+$line*8])<<8)+(ord($f[6+$line*8])<<16)+(ord($f[7+$line*8])<<24))^$ktmp2;
		$size = (ord($f[8+$line*8])+(ord($f[9+$line*8])<<8)+(ord($f[10+$line*8])<<16)+(ord($f[11+$line*8])<<24))^$ktmp2;
		return $this->decrypttext($f, $ptr, $line, $size);
	}
	
	private function decrypttext($file, $offset, $id, $len) {
		global $tbl;
		$key = (0x91BD3*($id+1))&0xffff;
		for ($i = 0; $i < $len; $i++) {
			$chars[] = (ord($file[$offset+$i*2])+(ord($file[$offset+$i*2+1])<<8))^$key;
			$key = ($key + 0x493D)&0xFFFF;
		}
		$output = '';
		$cap = 0;
		foreach ($chars as $b) {
			if ($b == 0xFFFF)
				break;
			if ($cap) {
				if ($cap == 2)
					$output .= sprintf('[%04X', $b);
				if ($cap == 1)
					$output .= sprintf('%04X]', $b);
				$cap--;
				continue;
			}
			if ($b == 0xFFFE) {
				$cap = 2;
				continue;
			}
			$output .= array_key_exists($b, $tbl) ? $tbl[$b] : sprintf('[%04X]', $b);
		}
		return $output;
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
if (array_search(__FILE__,get_included_files()) == 0) {
	//require_once 'Dwoo/dwooAutoload.php';
	require_once 'spyc.php';
	require_once '../hexview.php';
	//require_once 'misc.php';
	//$dwoo = new Dwoo();
	set_time_limit(10000);
	header('Content-Type: text/plain; charset=utf-8');
	$text = new gen4text($dptextfile);
	$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	if (array_key_exists(1, $argc) && ($argc[1] != null))
		$textdata['textfiles'][] = $text->fetchfile($argc[1]);
	else {
		$i = 0;
		foreach ($text as $file)
			$textdata['textfiles'][] = $file;
	}
	echo Spyc::YAMLDump($textdata);
	//$dwoo->output('poketemplates/pokemontext.tpl', $textdata);
}
?>