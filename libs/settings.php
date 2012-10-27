<?php
class settings implements arrayaccess {
	private $settings;
	private $needswrite = false;
	private $writefile;
	private $defaults;
	public function __construct($defaults, $filename = 'settings.yml') {
		$this->writefile = $filename;
		$this->defaults = $defaults;
		if (!file_exists($filename)) {
			$this->needswrite = true;
			$this->settings = $this->defaults;
		} else
			$this->settings = yaml_parse_file($filename);
	}
	function __destruct() {
		if ($this->needswrite) 
			file_put_contents($this->writefile, yaml_emit($this->settings));
	}
	public function addSetting($key, $value) {
		if (isset($this->defaults[$key]))
			throw new Exception(sprintf('Setting already exists: %s', $key));
		$this->defaults[$key] = $value;
	}
	public function offsetSet($key, $value) {
		if (!isset($this->defaults[$key]))
			throw new Exception(sprintf('Unknown setting: %s!', $key));
		if ($this->settings[$key] !== $value)
			$this->needswrite = true;
		return $this->settings[$key] = $value;
    }
    public function offsetExists($key) {
		return isset($this->defaults[$key]);
    }
    public function offsetUnset($key) {
		if (isset($this->settings[$key]))
			$this->needswrite = true;
		unset($this->settings[$key]);
    }
    public function offsetGet($key) {
		if (!isset($this->defaults[$key]))
			throw new Exception(sprintf('Unknown setting: %s!', $key));
		return default_value($this->settings, $key, $this->defaults[$key]);
    }
}
function default_value($array, $key, $default = false) {
	if (!isset($array[$key]))
		return $default;
	return $array[$key];
}
?>