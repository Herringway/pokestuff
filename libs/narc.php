<?php
$root = '../games/';
$defFolder = $root.'b2jpn/';
$validdirs = array('bw', 'plat', 'dp', 'bwtrans', 'hgss', 'bweng', 'b2jpn', 'weng');

$filetypes = array(
'BMD0' => '3D Model',
'BTX0' => 'Texture',
'RGCN' => 'Image',
'RLCN' => 'Palette',
'RCSN' => 'Screen Resource',
'RECN' => 'CEII Resource',
'RGC' => 'Character Graphic Resource',
'RNAN' => 'Animation data?',
'RCMN' => 'Nitro Mapped Cell Resource',
);
class NARCfile implements Iterator, ArrayAccess {
	private $fileid = 0;
	private $files = array();
	private $file;
	private $directories = array();
	private $chunks = array();

    public function __construct($file, $baseoffset = 0, $basesize = 0) {
        $this->fileid = 0;
		$this->baseoffset = $baseoffset;
		if ($basesize == 0)
			$basesize = filesize($file);
		if (is_resource($file))
			$this->file = $file;
		else
			$this->file = fopen($file, 'rb');
		fseek($this->file, $this->baseoffset);
		if (fread($this->file,4) != 'NARC')
			throw new Exception('Not a NARC!');
		fseek($this->file, $this->baseoffset + 0x10);
		$length = 0x10;
		while ($length < $basesize) {
			$datatype = strrev(fread($this->file, 4));
			$datasize = $this->getint();
			$length += $datasize;
			$this->chunks[$datatype]['size'] = $datasize;
			$this->chunks[$datatype]['offset'] = ftell($this->file);
			if ($datatype == 'FATB') {
				$numfiles = $this->getint();
				for ($i = 0; $i < $numfiles; $i++) {
					$offset = $this->getint();
					$size = $this->getint()-$offset;
					$this->files[] = array('offset' => $offset, 'size' => $size);
				}
				$this->chunks[$datatype]['numfiles'] = $numfiles;
			} elseif ($datatype == 'FNTB') {
				$fdata = fread($this->file, 8);
				$this->chunks[$datatype]['dirs'][0xF000] = unpack('Vptr/vunknown/vparent', $fdata);
				for ($i = 1; $i < $this->chunks[$datatype]['dirs'][0xF000]['parent']; $i++) {
					$this->chunks[$datatype]['dirs'][0xF000+$i] = unpack('Vptr/vunknown/vparent', fread($this->file, 8));
				}
				$v = $this->travel_directory_tree(0xF000);
				foreach ($v as $key => $val) {
					$this->files[$key]['filename'] = $val['filename'];
					$this->files[$key]['path'] = $val['path'];
					$this->files[$val['filename']] = array_merge($this->files[$key], array('path' => $val['path']));
				}
			}
			fseek($this->file, $this->baseoffset + $length);
		}
    }

	function getDetails() {
		return array('numfiles' => count($this->files), 'chunks' => $this->chunks, 'files' => $this->files);
	}

	private function travel_directory_tree($id, $path = '',$entries = array()) {
		if (!isset($this->chunks['FNTB']['dirs'][$id]))
			return;
		fseek($this->file, $this->chunks['FNTB']['dirs'][$id]['ptr'] + $this->chunks['FNTB']['offset']);
		$len = $this->getchar();
		$i = 0;
		while ($len != 0) {
			if ($len&0x80) {
				$name = fread($this->file, $len&0x7F);
				$id = $this->getshort();
				$curpos = ftell($this->file);
				//echo $name.'<br>';
				if ($id > 0xF000)
					$entries = $this->travel_directory_tree($id, $path.'/'.$name, $entries);
				fseek($this->file, $curpos);
			} else {
				$entries[] = array('filename' => fread($this->file, $len&0x7F), 'path' => $path);
			}
			$len = $this->getchar();
		}
		return $entries;
	}

	function getChunk($id) {
		fseek($this->file, $this->chunks[$id]['offset']);
		return fread($this->file, $this->chunks[$id]['size']-8);
	}
	function ChunkExists($id) {
		return array_key_exists($id, $this->chunks);
	}
	private function getint() {
		$b = unpack('V', fread($this->file,4));
		return $b[1];
	}
	private function getshort() {
		$b = unpack('v', fread($this->file,2));
		return $b[1];
	}
	private function getchar() {
		return ord(fgetc($this->file));
	}
	private function getFile($id) {
		if (isset($this->files[$id])) {
			if ($this->files[$id]['size'] == 0)
				return '';
			if ($this->files[$id]['size'] < 0)
				Throw new Exception('File size negative');
			fseek($this->file, $this->chunks['FIMG']['offset']+$this->files[$id]['offset'], SEEK_SET);
			return fread($this->file, $this->files[$id]['size']);
		}
		else
			Throw new Exception('File not found');
	}

	function getFileList() {
		$output = array();
		for ($i = 0; $i < $this->chunks['FATB']['numfiles']; $i++)
			$output[] = array_key_exists('filename',$this->files[$i]) ? array('path' => $this->files[$i]['path'], 'filename' => $this->files[$i]['filename']) : array('path' => '', 'filename' => $i);
		return $output;
	}

    function rewind() { $this->fileid = 0; }
    function current() { return $this->getFile($this->fileid); }
    function key() { return $this->fileid; }
    function next() { ++$this->fileid; }
    function valid() { return $this->fileid < count($this->files); }

	public function offsetExists($id) { return isset($this->files[$id]); }
	public function offsetGet($id) { return $this->getFile($id); }
	public function offsetSet($id, $val) { throw new Exception('Read Only'); }
	public function offsetUnset($id) { throw new Exception('Read Only'); }
	
	function __get($property) {
        switch ($property) {
			case 'Files': return count($this->files); break;
			default:
				if (isset($this->chunks[$property]))
					return $this->chunks[$property];
		}
	}
}
?>
