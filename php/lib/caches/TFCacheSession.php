<?php

class TFCacheSession extends _TFCache {
    function isAvailable() {
        @session_start();
        return true;
    }
    public function get($type, $key) {
        $longKey = self::_getLongKey($type, $key);
        if(isset($_SESSION[$longKey])) {
            return $_SESSION[$longKey];
        }
    }
    public function set($type, $key, $value) {
        $_SESSION[self::_getLongKey($type, $key)] = $value;
        return $value;
    }
}

?>