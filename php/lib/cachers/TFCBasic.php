<?php

class TFCBasic extends _TFConfigurable implements _TFC {
    protected $store = array();
    protected static function _getKey($genre, $name, $options=null) {
        return md5(print_r(array(
            'genre'=>$genre,
            'name'=>$name,
            'options'=>$options
        ), 1));
    }
    public function get($genre, $name, $options=null) {
        $key = self::_getKey($genre, $name, $options);
        if(isset($this->store[$key])) {
            return $this->store[$key];
        }
    }
    public function set($genre, $name, $object, $options=null) {
        $this->store[self::_getKey($genre, $name, $options)] = $object;
        return $object;
    }
}

?>