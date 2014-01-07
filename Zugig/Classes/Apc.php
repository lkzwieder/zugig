<?php
class Apc {
    public static function set($key, $value, $ttl = 0) {
        return apc_store($key, $value, $ttl);
    }

    public static function get($key) {
        return apc_fetch($key);
    }

    public static function flush() {
        return apc_clear_cache("user");
    }

    public static function delete($key) {
        return apc_delete($key);
    }

    public static function multi_delete(Array $key) {
        return apc_delete($key);
    }
}

class Fwk_Cache extends Fwk_Singleton {
    private $local;
    private $distributed;

    const ALL_LAYERS = 1;
    const LOCAL = 2;
    const DISTRIBUTED = 3;

    protected function __construct() {
        $this->local = Fwk_Apc::getInstance();
        $this->distributed = Fwk_Memcached::getInstance();
    }

    public function setCache($key, $value, $ttl = 0) {
        $setLocal = $this->local->setCache($key, $value, $ttl);
        $setDistributed = $this->distributed->setCache($key, $value, $ttl);
        return $setLocal && $setDistributed;
    }

    public function getCache($key) {
        if(!($value = $this->local->getCache($key))) {
            if($value = $this->distributed->getCache($key)) $this->local->setCache($key, $value);
        }
        return $value;
    }

    public function flushCache() {
        $flushLocal = $this->local->flushCache();
        $flushDistributed = $this->distributed->flushCache();
        return $flushLocal && $flushDistributed;
    }

    public function delete($key, $layer = self::ALL_LAYERS) {
        $function = is_array($key) ? "multi_delete" : "delete";
        $res = false;
        switch($layer) {
            case self::ALL_LAYERS:
                $res = $this->local->{$function}($key) && $this->distributed->{$function}($key);
                break;
            case self::LOCAL:
                $res = $this->local->{$function}($key);
                break;
            case self::DISTRIBUTED:
                $res = $this->distributed->{$function}($key);
                break;
        }
        return $res;
    }
}