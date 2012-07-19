<?php
class ndsrom implements Iterator,ArrayAccess {
	private $gamedetails;
	private $handle;
	private $files;
	private $filemap;
	private $dirs;
	private $FNT;
	private $FAT;
	private $fileid = 0;
	private $autonarc = false;
	private $firstfile;
	
	//For Array Access
	public function offsetExists($id) { 
		if (!is_string($id))
			return (count($this->files) > $id);
		return isset($this->filemap[$id]);
	}
	public function offsetGet($id) { 
		if (!is_string($id)) 
			return $this->getFileByID($id);
		return $this->getFile($id);
	}
	public function offsetSet($id, $val) { throw new Exception('Read Only'); }
	public function offsetUnset($id) { throw new Exception('Read Only'); }
	//for Iteration
    function rewind() { $this->fileid = 0; }
    function current() { return $this->getFileByID($this->fileid); }
    function key() { return $this->files[$this->fileid]['fullpath']; }
    function next() { ++$this->fileid; }
    function valid() { return $this->fileid < count($this->files); }
	
	function __construct($file) {
		if (!file_exists($file))
			throw new Exception($file.' not found');
		$this->handle = fopen($file, 'rb');
		$this->gamedetails['name'] = trim(fread($this->handle, 12));
		$this->gamedetails['code'] = fread($this->handle, 4);
		$this->gamedetails['maker'] = fread($this->handle, 2);
		$this->readFNTFAT();
		fseek($this->handle, 0x50);
		$arm9addr = $this->readInt();
		$arm9size = $this->readInt();
		$arm7addr = $this->readInt();
		$arm7size = $this->readInt();
		if ($arm9size > 0)
			$this->loadOverlayTable($arm9addr, $arm9size, '/OVERLAYS/9');
		if ($arm7size > 0)
			$this->loadOverlayTable($arm7addr, $arm7size, '/OVERLAYS/7');
	}
	private function loadOverlayTable($addr, $size, $dir) {
		fseek($this->handle, $addr);
		if ($dir[strlen($dir)-1] != '/')
			$dir .= '/';
		for ($i = 0; $i < $size / 32; $i++) {
			$id = $this->readInt();
			$ramaddr = $this->readInt();
			$ramsize = $this->readInt();
			$bsssize = $this->readInt();
			$initstart = $this->readInt();
			$initend = $this->readInt();
			$fileid = $this->readInt();
			$reserved = $this->readInt();
			$this->files[$fileid]['fullpath'] = $dir.$i;
			$this->files[$fileid]['LoadAddress'] = $ramaddr;
			$this->files[$fileid]['LoadSize'] = $ramsize;
			$this->files[$fileid]['BSSSize'] = $bsssize;
			$this->files[$fileid]['StaticInitBegin'] = $initstart;
			$this->files[$fileid]['StaticInitEnd'] = $initend;
			$this->filemap[$dir.$i] = $fileid;
		}
	}
	private function addFile($name, $addr, $size) {
		$this->files[] = array('fullpath' => $name, 'Address' => $addr, 'Size' => $size);
		$this->filemap[$name] = count($this->files)-1;
	}
	public function autoNARC($set = true) {
		$this->autonarc = $set;
	}
	public function getFile($name) {
		if ($name[0] != '/')
			$name = '/'.$name;
		return $this->getFileByID($this->filemap[$name]);
	}
	public function getFileByID($id) {
		if ($this->files[$id]['Size'] == 0)
			return '';
		fseek($this->handle, $this->files[$id]['Address']);
		if ($this->autonarc && (fread($this->handle, 4) == 'NARC'))
			return $this->getNARCByID($id);
		fseek($this->handle, $this->files[$id]['Address']);
		return fread($this->handle, $this->files[$id]['Size']);
	}
	public function getNARC($name) {
		require_once __DIR__.'/narc.php';
		if ($name[0] != '/')
			$name = '/'.$name;
		return new NARCFile($this->handle, $this->files[$this->filemap[$name]]['Address'],  $this->files[$this->filemap[$name]]['Size']);
	}
	public function getNARCByID($id) {
		require_once __DIR__.'/narc.php';
		return new NARCFile($this->handle, $this->files[$id]['Address'],  $this->files[$id]['Size']);
	}
	private function readFNTFAT() {
		fseek($this->handle, 0x40);
		$this->FNT = $this->readInt();
		$FNTsize = $this->readInt();
		$this->FAT = $this->readInt();
		$FATsize = $this->readInt();
		fseek($this->handle, $this->FNT);
		$this->dirs[0xF000] = unpack('Vptr/vfirst/vparent', fread($this->handle, 8));
		
		for ($i = 1; $i < $this->dirs[0xF000]['parent']; $i++)
			$this->dirs[0xF000+$i] = unpack('Vptr/vfirst/vparent', fread($this->handle, 8));
		
		$v = $this->travel_directory_tree(0xF000);
		
		fseek($this->handle, $this->FAT);
		for ($i = 0; $i < $FATsize/8; $i++) {
			$this->files[$i]['Address'] = $this->readInt();
			$this->files[$i]['Size'] = $this->readInt() - $this->files[$i]['Address'];
		}
		foreach ($v as $key => $val) {
			$this->files[$key+$this->dirs[0xF000]['first']]['fullpath'] = $val['path'].'/'.$val['filename'];
			$this->filemap[$val['path'].'/'.$val['filename']] = $key+$this->dirs[0xF000]['first'];
		}
	}
	private function readShort() {
		return unpack('vPtr', fread($this->handle, 2))['Ptr'];
	}
	private function readInt() {
		return unpack('VPtr', fread($this->handle, 4))['Ptr'];
	}
	private function readByte() {
		return ord(fgetc($this->handle));
	}
	private function travel_directory_tree($id, $path = '',$entries = array()) {
		fseek($this->handle, $this->dirs[$id]['ptr']+$this->FNT);
		$len = $this->readByte();
		$i = 0;
		while ($len != 0) {
			if ($len&0x80) {
				$name = fread($this->handle, $len&0x7F);
				$id = $this->readShort();
				$curpos = ftell($this->handle);
				if ($id > 0xF000)
					$entries = $this->travel_directory_tree($id, $path.'/'.$name, $entries);
				fseek($this->handle, $curpos);
			} else {
				$entries[] = array('filename' => fread($this->handle, $len&0x7F), 'path' => $path);
			}
			$len = $this->readByte();
		}
		return $entries;
	}
	
	public function __get($var) {
		return $this->gamedetails[$var];
	}
}

if (array_search(__FILE__,get_included_files()) == 0) {
	$rom = new ndsrom('../games/platinume.nds');
	ini_set('xdebug.var_display_max_children', -1);
	var_dump($rom);

}
?>