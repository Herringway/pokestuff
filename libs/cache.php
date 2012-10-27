<?php
interface cache_interface extends arrayaccess {
	public static function getTier();
	public function clear();
	public function stats();
	public function enable();
	public function disable();
	public function status();
	public function mode();
}
class cache implements cache_interface {
	private $enabled = false;
	private $cannotenable = false;
	private $cachetypes = array();
	private $cache;
	
	public static function getTier() { return -1; }
	public function __construct($pool = 'default') {
		foreach (get_declared_classes() as $v)
			if (is_subclass_of($v, 'cache_interface') && ($v != get_class($this)))
				$this->cachetypes[] = $v;
		usort($this->cachetypes, array($this,'cacheSort'));
		foreach (array_reverse($this->cachetypes) as $type) {
			$this->cache = new $type($pool);
			if ($this->cache->status()) {
				$this->enable();
				return;
			}
		}
		$this->cannotenable = true;
	}
	private function cacheSort($a, $b) {
		return ($a::getTier() > $b::getTier()) ? 1 : -1;
	}
	public function clear() {
		if (!$this->enabled)
			return false;
		return $this->cache->clear();
	}
	public function offsetSet($name, $object) {
		if (!$this->enabled)
			return false;
		return $this->cache->offsetSet($name, $object);
    }
    public function offsetExists($name) {
		if (!$this->enabled)
			return false;
		return $this->cache->offsetExists($name);
    }
    public function offsetUnset($name) {
		if (!$this->enabled)
			return false;
		return $this->cache->offsetUnset($name);
    }
    public function offsetGet($name) {
		if (!$this->enabled)
			return false;
		return $this->cache->offsetGet($name);
    }
	public function stats() {
		if (!$this->enabled)
			return null;
		return $this->cache->stats();
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
		return $this->cache->mode();
	}
}
require 'apccache.php';
require 'filecache.php';
?>