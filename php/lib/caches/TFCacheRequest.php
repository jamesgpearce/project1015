<?php

class TFCacheRequest extends _TFCache {
    function isAvailable() {
        return true;
    }
    private $_store = array();
    public function get($type, $key) {
        $longKey = self::_getLongKey($type, $key);
        if(isset($this->_store[$longKey])) {
            return $this->_store[$longKey];
        }
    }
    public function set($type, $key, $value) {
        $this->_store[self::_getLongKey($type, $key)] = $value;
        return $value;
    }
}

?>