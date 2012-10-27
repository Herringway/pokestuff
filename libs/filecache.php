<?php
class jsonfilecache implements cache_interface {
	private $enabled = false;
	private $cannotenable = false;
	private static $funcs = array('json_encode', 'json_decode');
	private $pool;
	public static function getTier() { return 1; }
	public function __construct($pool = 'default') {
		$this->pool = $pool;
		foreach (self::$funcs as $testfunc)
			if (!function_exists($testfunc))
				$this->cannotenable = true;
		$this->enable();
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		rrmdir('cache/'.$this->pool);
		return true;
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		if (!file_exists(dirname('cache/'.$this->pool.'/'.$name.'.json')))
			mkdir(dirname('cache/'.$this->pool.'/'.$name.'.json'),0777, true);
		return file_put_contents('cache/'.$this->pool.'/'.$name.'.json', json_encode($object, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return file_exists('cache/'.$this->pool.'/'.$name.'.json');
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return unlink('cache/'.$this->pool.'/'.$name.'.json');
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return json_decode(file_get_contents('cache/'.$this->pool.'/'.$name.'.json'), true);
    }
	public function stats() {
		if (!$this->enabled)
			return null;
		return true;
	}
	public function disable() {
		$this->enabled = false;
	}
	public function enable() {
		if (!$this->cannotenable)
			$this->enabled = true;
	}
	public function status() {
		return $this->enabled;
	}
	public function mode() {
		return 'JSON_FILE';
	}
}
class yamlfilecache implements cache_interface {
	private $enabled = false;
	private $cannotenable = false;
	private static $funcs = array('yaml_parse_file', 'yaml_emit');
	private $pool;
	public static function getTier() { return 0; }
	public function __construct($pool = 'default') {
		$this->pool = $pool;
		foreach (self::$funcs as $testfunc)
			if (!function_exists($testfunc))
				$this->cannotenable = true;
		$this->enable();
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		rrmdir('cache/'.$this->pool);
		return true;
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		if (!file_exists(dirname('cache/'.$this->pool.'/'.$name.'.yml')))
			mkdir(dirname('cache/'.$this->pool.'/'.$name.'.yml'),0777, true);
		return file_put_contents('cache/'.$this->pool.'/'.$name.'.yml', yaml_emit($object, YAML_UTF8_ENCODING));
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return file_exists('cache/'.$this->pool.'/'.$name.'.yml');
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return unlink('cache/'.$this->pool.'/'.$name.'.yml');
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return yaml_parse_file('cache/'.$this->pool.'/'.$name.'.yml');
    }
	public function stats() {
		if (!$this->enabled)
			return null;
		return true;
	}
	public function disable() {
		$this->enabled = false;
	}
	public function enable() {
		if (!$this->cannotenable)
			$this->enabled = true;
	}
	public function status() {
		return $this->enabled;
	}
	public function mode() {
		return 'YAML_FILE';
	}
}
class serialfilecache implements cache_interface {
	private $enabled = false;
	private $cannotenable = false;
	private static $funcs = array('serialize', 'unserialize');
	private $pool;
	public static function getTier() { return 2; }
	public function __construct($pool = 'default') {
		$this->pool = $pool;
		foreach (self::$funcs as $testfunc)
			if (!function_exists($testfunc))
				$this->cannotenable = true;
		$this->enable();
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		rrmdir('cache/'.$this->pool);
		return true;
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		if (!file_exists(dirname('cache/'.$this->pool.'/'.$name.'.ser')))
			mkdir(dirname('cache/'.$this->pool.'/'.$name.'.ser'),0777, true);
		return file_put_contents('cache/'.$this->pool.'/'.$name.'.ser', serialize($object));
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return file_exists('cache/'.$this->pool.'/'.$name.'.ser');
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return unlink('cache/'.$this->pool.'/'.$name.'.ser');
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return unserialize(file_get_contents('cache/'.$this->pool.'/'.$name.'.ser'));
    }
	public function stats() {
		if (!$this->enabled)
			return null;
		return true;
	}
	public function disable() {
		$this->enabled = false;
	}
	public function enable() {
		if (!$this->cannotenable)
			$this->enabled = true;
	}
	public function status() {
		return $this->enabled;
	}
	public function mode() {
		return 'PHPSERIAL_FILE';
	}
}
class igbinfilecache implements cache_interface {
	private $enabled = false;
	private $cannotenable = false;
	private static $funcs = array('igbinary_serialize', 'igbinary_unserialize');
	private $pool;
	public static function getTier() { return 3; }
	public function __construct($pool = 'default') {
		$this->pool = $pool;
		foreach (self::$funcs as $testfunc)
			if (!function_exists($testfunc))
				$this->cannotenable = true;
		$this->enable();
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		rrmdir('cache/'.$this->pool);
		return true;
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		if (!file_exists(dirname('cache/'.$this->pool.'/'.$name.'.igbin')))
			mkdir(dirname('cache/'.$this->pool.'/'.$name.'.igbin'),0777, true);
		return file_put_contents('cache/'.$this->pool.'/'.$name.'.igbin', igbinary_serialize($object));
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return file_exists('cache/'.$this->pool.'/'.$name.'.igbin');
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return unlink('cache/'.$this->pool.'/'.$name.'.igbin');
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return igbinary_unserialize(file_get_contents('cache/'.$this->pool.'/'.$name.'.igbin'));
    }
	public function stats() {
		if (!$this->enabled)
			return null;
		return true;
	}
	public function disable() {
		$this->enabled = false;
	}
	public function enable() {
		if (!$this->cannotenable)
			$this->enabled = true;
	}
	public function status() {
		return $this->enabled;
	}
	public function mode() {
		return 'IGBINARY_FILE';
	}
}
function rrmdir($dir) {
    foreach(glob($dir . '/*') as $file) {
        if(is_dir($file))
            rrmdir($file);
        else
            unlink($file);
    }
    rmdir($dir);
}
?>