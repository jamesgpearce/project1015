<?php

class TFCacheMemcached extends _TFCache {
    function isAvailable() {
        return false;
    }
}

?>