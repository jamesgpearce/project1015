<?php

include_once('TFCBasic.php');

class TFCMemcached extends TFCBasic {
    public function get($genre, $name, $options=null) {
        if ($object = parent::get($genre, $name, $options)) {
            return $object;
        }
        $optionsKey = self::_optionsKey($options);
        if(isset($this->store[$genre])
           && isset($this->store[$genre][$name])
           && isset($this->store[$genre][$name][$optionsKey])
        ) {
            return $this->store[$genre][$name][$optionsKey];
        }
    }
    public function set($genre, $name, $object, $options=null) {
        $optionsKey = self::_optionsKey($options);
        if(!isset($this->store[$genre])) {
            $this->store[$genre] = array();
        }
        if(!isset($this->store[$genre][$name])) {
            $this->store[$genre][$name] = array();
        }
        $this->store[$genre][$name][$optionsKey] = $object;
        return $object;
    }
}


?>