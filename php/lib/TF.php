<?php

class _TFConfigurable {
    protected $_config;
    public function __construct($config = null) {
        $this->_configure($config);
    }
    protected function _configure($config = null) {
        $this->_config = $config;
    }
}

class _TFCache extends _TFConfigurable {
    protected static function _getLongKey($type, $key) {
        return 'tf_' . md5(print_r(array(
            'type'=>$type,
            'key'=>$key
        ), 1));
    }
    function isAvailable() {
        return false;
    }
    public function get($type, $key) {}
    public function set($type, $key, $value) {
        return $value;
    }
}

class _TFVocabulary extends _TFConfigurable {
    protected $_cache;
    protected function _configure($config = null) {
        $this->_config = $config;
        $this->_cache = $config['cache'];
    }
    private $_propertyNames;
    protected function _getPropertyNames() {
        return array();
    }
    final public function getPropertyNames() {
        if (!isset($this->_propertyNames)) {
            $this->_propertyNames = $this->_getPropertyNames();
        }
        return $this->_propertyNames;
    }
    private $_propertyValues;
    protected function _getPropertyValue($name) {
        return array();
    }
    final public function getPropertyValue($name) {
        if (!isset($this->_propertyValues[$name])) {
            $this->_propertyValues[$name] = $this->_getPropertyValue($name);
        }
        return $this->_propertyValues[$name];
    }
}

final class TF extends _TFConfigurable {
    private $_cache;
    protected function _configure($config = null) {
        $this->_config = $config ? $config : json_decode (
            file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . 'TF.json'),
            true
        );
        foreach($this->_config['cachePriority'] as $cacheName) {
            $cache = $this->_getInstance('cache', $this->_config['caches'][$cacheName]);
            if ($cache && $cache->isAvailable()) {
                $this->_cache = $cache;
                break;
            }
        }
    }
    private function _getInstance($type, $config) {
        $className = $this->_getClass($type, $config['type']);
        $instance = new $className;
        $instance->_configure($config);
        return $instance;
    }
    private function _getClass($type, $subtype) {
        $className = 'TF' .
            strtoupper($type[0]) . substr($type, 1) .
            strtoupper($subtype[0]) . substr($subtype, 1);
        if (!class_exists($className)) {
            include_once(
                (substr($type, -1)=='y' ?
                    substr($type, 0, -1) . 'ies' :
                    $type . 's'
                ) . DIRECTORY_SEPARATOR . $className . '.php'
            );
        }
        return $className;
    }

    public function getCache() {
        return $this->cache;
    }

    public function getVocabularyNames() {
        return $this->_config['vocabularyPriority'];
    }

    private $_vocabularies = array();
    public function getVocabulary($name) {
        if (!isset($this->_vocabularies[$name])) {
            $config = $this->_config['vocabularies'][$name];
            $config['cache'] = $this->_cache;
            $this->_vocabularies[$name] = $this->_getInstance('vocabulary', $config);
        }
        return $this->_vocabularies[$name];
    }



}
class TFUtil {
    public static function yamlDecode($yaml) {
        return self::_verySimpleYamlDecode($yaml);
    }

    private static function _verySimpleYamlDecode($yaml) {
        // does the least required for browserscope ua parser files
        $data = array();
        foreach (explode("\n", $yaml) as $line) {
            if (!($trimmed_line=trim($line)) || $trimmed_line[0]=='#') {
                continue;
            }
            if ($line[0] != ' ') {
                $level1 = substr($trimmed_line, 0, -1);
                $level2 = -1;
                $data[$level1] = array();
                continue;
            }
            if (substr($line, 0, 4) == '  - ') {
                $level2++;
                $data[$level1][$level2] = array();
            }
            list($key, $value) = explode(':', substr($line, 4), 2);
            $value = trim($value);
            if ($value[0] == "'" && substr($value, -1) == "'") {
                $value = substr($value, 1, -1);
            }
            $data[$level1][$level2][trim($key)] = $value;
        }
        return $data;
    }

    public static function deepSeek(&$array, $keys) {
        $key = array_shift($keys);
        if (is_int($key)) {
            $_keys = array_keys($array);
            $key = $_keys[$key];
        }
        if (!isset($array[$key])) {
            return;
        }
        if (sizeof($keys)) {
            return self::deepSeek($array[$key], $keys);
        }
        return $array[$key];
    }
}

?>