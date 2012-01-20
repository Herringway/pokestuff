<?php
$root = '/var/www/pkmn/narcs/';
$defFolder = '/var/www/pkmn/narcs/bweng/';
$validdirs = array('bw', 'plat', 'dp', 'bwtrans', 'hgss', 'bweng');

class NARCfile implements Iterator {
	private $fileid = 0;
	private $files = array();
	private $file;
	private $directories = array();
	private $chunks = array();

    public function __construct($file) {
        $this->fileid = 0;
		$this->file = fopen($file, 'r');
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
				$this->chunks[$datatype]['dirs'][0xF000]['ptr'] = $this->getint();
				$this->chunks[$datatype]['dirs'][0xF000]['unknown'] = $this->getshort();
				$this->chunks[$datatype]['dirs'][0xF000]['parent'] = $this->getshort();
				for ($i = 1; $i < $this->chunks[$datatype]['dirs'][0xF000]['parent']; $i++) {
					$this->chunks[$datatype]['dirs'][0xF000+$i]['ptr'] = $this->getint();
					$this->chunks[$datatype]['dirs'][0xF000+$i]['unknown'] = $this->getshort();
					$this->chunks[$datatype]['dirs'][0xF000+$i]['parent'] = $this->getshort();
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
		return array('numfiles' => count($this->files), 'chunks' => $this->chunks);
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
		return unpack('V', fread($this->file,4));
	}
	private function getshort() {
		return unpack('v', fread($this->file,2));
	}
	private function getchar() {
		return ord(fgetc($this->file));
	}
	function getFile($id) {
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

}

function read_narc($filename, $print_files = 0) {
	$output = array();
	$file = fopen($filename, 'r') or die('Could not open '.$filename.'!');
	if (fread($file, 4) != 'NARC')
		die ($filename.' - NOT A NARC FILE');
	fseek($file, 0x10);
	$length = 0x10;
	$datatypes = array(
	'BTAF' => 'File Allocation Table',
	'BTNF' => 'Filename Table',
	'GMIF' => 'Data');
	$i = 0;
	while ($length < filesize($filename)) {
		$datatype = fread($file, 4);
		$output[$i] = (array_key_exists($datatype, $datatypes) ? $datatypes[$datatype] : $datatype).' - ';
		$datasize = getint($file);
		$length += $datasize;
		$output[$i] .= round($datasize/1024, 3).'KB';
		if ($datatype == 'BTAF') {
			$entries = getint($file);
			$output[$i] .= ' - '.$entries.' Entries';
			if ($print_files)
				$output[$i] .= process_file_list($file, $entries);
		} else if ($datatype =='BTNF') {
			$output[$i] .= process_btnf($file, $datasize);
		}
		fseek($file,$length);
		$i++;
	}

	fclose($file);
	return $output;
}
function analyze_narc($filename) {
	$output = array();
	$file = fopen($filename, 'r') or die('Analyze_narc() could not open '.$filename.'!');
	if (fread($file, 4) != 'NARC')
		die ($filename.' - NOT A NARC FILE');
	fseek($file, 0x10);
	$length = 0x10;
	while ($length < filesize($filename)) {
		$datatype = fread($file, 4);
		$datasize = getint($file);
		$length += $datasize;
		$output[$datatype]['Size'] = $datasize;
		if ($datatype == 'BTAF')
			$output['Files'] = getint($file);
		fseek($file,$length);
	}

	fclose($file);
	return $output;
}
function getint($file) {
	return ord(fgetc($file))+(ord(fgetc($file))<<8)+(ord(fgetc($file))<<16)+(ord(fgetc($file))<<24);
}
function getshort($file) {
	return ord(fgetc($file))+(ord(fgetc($file))<<8);
}
function getchar($file) {
	return ord(fgetc($file));
}
function process_file_list($file, $entries) {
	$output = ''; $finaltype = '';
	global $argc;
	$filename = 'narcs/'.implode('/', array_slice($argc, 2));
	$filetypes = array(
	'BMD0' => '3D Model',
	'BTX0' => 'Texture',
	'RGCN' => 'Image',
	'RLCN' => 'Palette',
	'RCSN' => 'Screen Resource',
	'RECN' => 'CEII Resource',
	'RGC' => 'Character Graphic Resource',
	'RNAN' => 'Animation data?',
	);
	for ($i = 0; $i < $entries; $i++) {
		$output .= '<br>';
		$filebegin = getint($file);
		$fdata = getfile($filename,$i,8);
		$fileend = getint($file);
		$output .= sprintf('<a href="/pkmn/narc/download/%1$s/%2$u">%2$u - %3$u Bytes</a> <a href="/pkmn/narc/view/%1$s/%2$u">(H)</a>',implode('/', array_slice($argc, 2)),$i, $fileend-$filebegin);
		if ($fileend-$filebegin > 4) {
			$filetype = substr($fdata,0,4);
			$filetype2 = substr($fdata,5,4);
			if (substr($filetype2,0,1) == 'þ') {
				$finaltype = $filetype;
			} else {
				$finaltype = $filetype2;
			}
			$output .= ' - '.(array_key_exists($finaltype, $filetypes) ? $finaltype.' - '.$filetypes[$finaltype] : $finaltype);
		}
	}
	return $output;
}
function process_btnf($file, $size) {
	$output = '<br>';
	$firstdir = getint($file);
	$output .= getshort($file);
}
function getfile($filename, $filenumber, $numbytes = -1) {
	$file = @fopen($filename, 'r') or die('getfile() could not open '.$filename.'!');
	fseek($file, 0x1C+$filenumber*8);
	$offset = getint($file);
	$size = getint($file)-$offset;
	fseek($file, 0x10);
	$length = 0x10;
	while (!feof($file)) {
		$datatype = fread($file, 4);
		if ($datatype == 'GMIF')
			break;
		$datasize = getint($file);
		$length += $datasize;
		fseek($file,$length);
	}
	fseek($file, $offset+4, SEEK_CUR);
	if ($numbytes == -1) {
		if ($size > 0)
			return fread($file,$size);
		return;
	}
	return fread($file, $numbytes);
}
function read_folder($dirProcess) {
	global $root;
	$output = '';
	$dir = opendir($dirProcess);
	while(($filename = readdir($dir)) != null) {
		if (substr($filename,0,1) != '.') {
			if (is_dir($dirProcess.$filename))
				$output .= read_folder($dirProcess.$filename.'/');
			else {
				$dat = analyze_narc($dirProcess.$filename);
				$output .= sprintf('<tr><td><a href="/pkmn/narc/list/%1$s">%1$s</a> <a href="/pkmn/narc/table/%1$s">(T)</a></td><td>FAT: %2$s Files</td><td>Data: %3$sKB</td><td>~%4$s</td></tr>', str_replace($root, '',$dirProcess).$filename, $dat['Files'], ceil($dat['GMIF']['Size']/1024), ceil($dat['GMIF']['Size']/$dat['Files']));
			}
		}
	}
	return $output;
}
function viewfile($filename, $subfile) {
	return hexview(getfile($filename, $subfile), array_key_exists('width', $_GET) ? $_GET['width'] : 16);
}
function read_narc_as_table($filename) {
	$details = analyze_narc($filename);
	echo '<table style="font-family: monospace;" border="1">';
	for ($i = 0; $i < $details['Files']; $i++) {
		$rawdata = getfile($filename, $i);
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
	require '../hexview.php';
	$argc = (array_key_exists('PATH_INFO', $_SERVER) ? explode('/', $_SERVER['PATH_INFO']) : array('',''));
	if (array_key_exists(1, $argc) && in_array($argc[1], $validdirs))
		$defFolder = $root.$argc[1].'/';
	if (array_key_exists(2, $argc) && in_array($argc[2], $validdirs))
		$defFolder = $root.$argc[2].'/';
	switch($argc[1]) {
	case 'list':
		$dat = read_narc($defFolder.implode('/', array_slice($argc, 3)),1);
		foreach ($dat as $cur)
			echo $cur.'<br>';
		break;
	case 'download':
		header('Content-type: application/octet-stream');
		header('Content-Disposition: attachment; filename="'.implode(array_slice($argc, -1)).'"');
		echo getfile($defFolder.implode('/', array_slice($argc, 3,-1)), implode(array_slice($argc, -1)));
		break;
	case 'view':
		echo viewfile($defFolder.implode('/', array_slice($argc, 3,-1)), implode(array_slice($argc, -1)));
		break;
	case 'table':
		echo read_narc_as_table($defFolder.implode('/', array_slice($argc, 3)));
		break;
	default:
			echo '<table><tr><td>File</td><td>FAT size</td><td>Data size</td><td>Average file size</td></tr>';
			echo read_folder($defFolder).'</table>'; break;
	}
}
?>
