<?php
namespace Cache;
class Mem {
	private $expire;
	private $cache;
	private $prefix;

	public function __construct($expire) {
		$this->expire = $expire;
		$this->prefix = DB_DATABASE;

		$this->cache = new \Memcache();
		//$this->cache->pconnect(CACHE_HOSTNAME, CACHE_PORT);
		$this->cache->pconnect("localhost", "11211");
	}

	public function get($key) {
		return $this->cache->get($this->prefix . $key);
	}

	public function set($key,$value) {
		return $this->cache->set($this->prefix . $key, $value, MEMCACHE_COMPRESSED, $this->expire);
	}

	public function delete($key) {
		$this->cache->delete($this->prefix . $key);
	}
}