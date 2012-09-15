<?php
class cache implements arrayaccess {
	private $enabled = false;
	private $cannotenable = false;
	private static $apcfuncs = array('apc_clear_cache', 'apc_store', 'apc_exists', 'apc_delete', 'apc_fetch', 'apc_cache_info');
	private $pool;
	
	public function __construct($pool = 'default') {
		$this->pool = $pool;
		foreach (self::$apcfuncs as $testfunc)
			if (!function_exists($testfunc))
				$this->cannotenable = true;
		$this->enable();
		debugmessage('Cache is '.($this->cannotenable ? 'disabled' : 'enabled'), 'info');
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		return apc_clear_cache() && apc_clear_cache('user');
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		return apc_store($this->pool.'/'.$name, $object, 0);
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return apc_exists($this->pool.'/'.$name);
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return apc_delete($this->pool.'/'.$name);
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return apc_fetch($this->pool.'/'.$name);
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