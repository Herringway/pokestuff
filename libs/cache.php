<?php
class cache implements arrayaccess {
	private $enabled = false;
	private $cannotenable = false;
	private static $apcfuncs = array('apc_clear_cache', 'apc_store', 'apc_exists', 'apc_delete', 'apc_fetch', 'apc_cache_info');
	private $mode;
	public function __construct($mode = 'any') {
		foreach (self::$apcfuncs as $testfunc)
			if (!function_exists($testfunc))
				$this->cannotenable = true;
		$this->enable();
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		return apc_clear_cache() && apc_clear_cache('user');
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		return apc_store($name, $object, 0);
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return apc_exists($name);
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return apc_delete($name);
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return apc_fetch($name);
    }
	public function stats() {
		if (!$this->enabled)
			return null;
		return apc_cache_info();
	}
	public function disable() {
		$this->enabled = false;
	}
	public function enable() {
		if (!$this->cannotenable)
			$this->enabled = true;
	}
}
?>