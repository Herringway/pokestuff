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
class NARCfile implements Iterator {
	private $fileid = 0;
	private $files = array();
	private $file;
	private $directories = array();
	private $chunks = array();

    public function __construct($file) {
        $this->fileid = 0;
		$this->file = fopen($file, 'rb');
		if (fread($this->file,4) != 'NARC')
			throw new Exception('Not a NARC!');
		fseek($this->file, 0x10);
		$length = 0x10;
		while ($length < filesize($file)) {
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
			fseek($this->file,$length);
		}
    }

	function getDetails() {
		return array('numfiles' => count($this->files), 'chunks' => $this->chunks, 'files' => $this->files);
	}

	private function travel_directory_tree($id, $path = '',$entries = array()) {
		fseek($this->file, $this->chunks['FNTB']['dirs'][$id]['ptr']+$this->chunks['FNTB']['offset']);
		$len = $this->getchar();
		$i = 0;
		while ($len != 0) {
			if ($len&0x80) {
				$name = fread($this->file, $len&0x7F);
				$id = $this->getshort();
				$curpos = ftell($this->file);
				echo $name.'<br>';
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
		return unpack('v', fread($this->file,2));
	}
	private function getchar() {
		return ord(fgetc($this->file));
	}
	public function getFile($id) {
		if (array_key_exists($id, $this->files)) {
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

    function rewind() {
        $this->fileid = 0;
    }

    function current() {
        return $this->getFile($this->fileid);
    }

    function key() {
        return $this->fileid;
    }

    function next() {
        ++$this->fileid;
    }

    function valid() {
        return $this->fileid < count($this->files);
    }

	function __get($property) {
        switch ($property) {
			case 'Files': return count($this->files); break;
			default:
				if (isset($this->chunks[$property]))
					return $this->chunks[$property];
		}
	}
}
function read_folder($root, $dirProcess = '') {
	$output = array();
	$dir = opendir($root.$dirProcess);
	while(($filename = readdir($dir)) != null) {
		if (substr($filename,0,1) != '.') {
			if (is_dir($root.$dirProcess.$filename))
				$output = array_merge($output, read_folder($root, $dirProcess.$filename.'/'));
			else {
				try {
				$dat = new NARCFile($root.$dirProcess.$filename);
				$output[$dirProcess.$filename] = array('Files' => $dat->Files, 'Data Size' => $dat->FIMG['size'], 'Average File Size' => ceil($dat->FIMG['size']/$dat->Files));
				} catch (Exception $e) {} 
			}
		}
	}
	ksort($output);
	return $output;
}
function viewfile($filename, $subfile) {
	$file = new NARCFile($filename);
	return '<pre>'.hexview($file->getFile($subfile), array_key_exists('width', $_GET) ? $_GET['width'] : 16).'</pre>';
}
function read_narc_as_table($filename) {
	$narc = new NARCFile($filename);
	echo '<table style="font-family: monospace;" border="1">';
	for ($i = 0; $i < $narc->Files; $i++) {
		$rawdata = $narc->getFile($i);
		$j = 0;
		printf('<tr><td>%u</td>', $i);
		while (@$rawdata[$j] != null) {
			echo sprintf('<td>%02X</td>', ord($rawdata[$j++]));
		}
		echo '</tr>';
	}
	echo '</table>';

}
if (array_search(__FILE__,get_included_files()) == 0) {
	require '../../hexview.php';
	$argc = explode('/', $_SERVER['PATH_INFO']);
	array_shift($argc);
	$argc = isset($argc[0]) ? $argc : array('');
	if (array_key_exists(1, $argc) && in_array($argc[1], $validdirs))
		$defFolder = $root.$argc[1].'/';
	if (array_key_exists(2, $argc) && in_array($argc[2], $validdirs))
		$defFolder = $root.$argc[2].'/';
	switch($argc[0]) {
	case 'list':
		try {
			$dat = new NARCFile($root.implode('/', array_slice($argc, 1)));
		} catch (Exception $e) {
			echo $e;
			break;
		}
		$output = $dat->getDetails();
		printf('File Allocation Table - %01.2fKB - %d Entries<br>', $output['chunks']['FATB']['size']/1024, $output['chunks']['FATB']['numfiles']);
		$totalsize = 0;
		foreach ($output['files'] as $filename=>$file) {
			$totalsize += $file['size'];
			$filetype = preg_replace('/[^(\x20\x30-\x39\x41-\x5A\x61-\x7A)]*/','', substr($dat->getFile($filename), 0, 4));
			$filedesc = isset($filetypes[$filetype]) ? $filetypes[$filetype] : 'Unknown';
			$fileinfo = (strlen($filetype) == 4) ? sprintf('- %s - %s', $filetype, $filedesc) : '';
			printf('<a href="/libs/narc.php/download/%s/%s">%2$s - %d Bytes</a> <a href="/libs/narc.php/view/%1$s/%2$s">(H)</a>%s<br>', implode('/', array_slice($argc, 1)), $filename, $file['size'], $fileinfo);
		}
		printf('Filename Table - %01.2fKB<br>', $output['chunks']['FNTB']['size']/1024);
		printf('Data - %01.2fKB', $totalsize/1024);
		break;
	case 'download':
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.implode(array_slice($argc, -1)).'"');
		$file = new NARCFile($defFolder.implode('/', array_slice($argc, 2,-1)));
		echo $file->getFile(implode(array_slice($argc, -1)));
		break;
	case 'view':
		echo viewfile($defFolder.implode('/', array_slice($argc, 2,-1)), implode(array_slice($argc, -1)));
		break;
	case 'table':
		echo read_narc_as_table($root.implode('/', array_slice($argc, 1)));
		break;
	default:
		echo '<table><tr><td>File</td><td>FAT size</td><td>Data size</td><td>Average file size</td></tr>';
		$tmp = read_folder($defFolder);
		foreach ($tmp as $filename=>$entry) {
			printf('<tr><td><a href="/libs/narc.php/list/%1$s">%1$s</a> <a href="/libs/narc.php/table/%1$s">(T)</a></td><td>%2$s</td><td>%3$01.1fKB</td><td>%4$s</td></tr>', str_replace($root, '', $defFolder).$filename, $entry['Files'], $entry['Data Size']/1024, $entry['Average File Size']);
		}
		echo '</table>'; break;
	}
}
?>
