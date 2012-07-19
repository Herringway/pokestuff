<?php
class gen2 extends basegame {
	const generation = 'gen2';
	private $file;
	
	function loadRom() {
		if ($this->file === null)
			$this->file = fopen('games/'.get_class($this).'.gbc','r');
	}
}
?>