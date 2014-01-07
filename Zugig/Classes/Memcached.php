<?php
class Memcached {
    private $memcached;

    protected function __construct() {
        $this->memcached = new Memcached();
        $this->memcached->addServer($cache_servers[ENVIROMENT]['host'], $cache_servers[ENVIROMENT]['port']);
    }

    public function setCache($key, $value, $ttl = 0) {
        if(!$res = $this->memcached->add($key, $value, $ttl)) {
            $res = $this->memcached->replace($key, $value, $ttl);
        }
        return $res;
    }

    public function getCache($key) {
        return $this->memcached->get($key);
    }

    public function flushCache() {
        return $this->memcached->flush();
    }

    public function delete($key) {
        $res = false;
        if(!is_array(($key))) $res = $this->memcached->delete($key);
        return !$res || $res == Memcached::RES_NOTFOUND;
    }

    public function multi_delete(Array $key) {
        $res = $this->memcached->deleteMulti($key);
        return !$res || $res == Memcached::RES_NOTFOUND;
    }
}