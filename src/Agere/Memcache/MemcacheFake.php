<?php
namespace Agere\Memcache;

class MemcacheFake {
	
	public function get($k) { return false;	}
	
	public function set($z) { return false; }
	
	public function add($z) { return false; }
	
	public function delete($z) { return false; }
	
	public function deleteByTag($z) { return false; }
	
	public function flush() { return false; }

	public function increment($key, $value = 1, $tag = null) { return false; }
	
	public function getServerStatus($host, $port) { return 0; }
}