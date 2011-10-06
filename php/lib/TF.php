<?php

interface _TFC {
    function get($genre, $name, $config=null);
    function set($genre, $name, $object, $config=null);
}
interface _TFV {
}

class _TFConfigurable {
    public function __construct($config=null) {
        if ($config) {
            $this->configure($config);
        }
    }
    protected static function _configArguments($key){}
    protected function configure($config=null) {
        if (is_array($config)) {
            foreach($config as $key=>$value) {
                if (property_exists($this, $property="_$key")) {
                    $this->$property = $value;
                } elseif (
                    ($altKey = $this->_configArguments($key)) &&
                    property_exists($this, $property="_$altKey")
                ) {
                    $this->$property = $value;
                }
            }
        }
    }
}

class _TFInstantiator extends _TFConfigurable {
    private $_cacher;
    protected $_cachersLocation = 'cachers';
    protected $_cachers = array(
        'basic'=>'TFCBasic'
    );
    public function __construct($config=null) {
        parent::__construct($config);
        $this->_cacher = $this->_getCacher();
    }
    protected function _getInstance($genre, $name=null, $useCache=true) {
        $genreProperty = "_$genre";
        $genreLocationProperty = "_${genre}Location";
        if (!$name) {
            foreach($this->_getInstanceNames($genre) as $name) {
                try {
                    return $this->_getInstance($genre, $name, $useCache);
                } catch (Exception $e) {}
            }
        }
        $configs = $this->$genreProperty;
        $config = $configs[$name];
        if (is_array($config)) {
            $className = array_shift($config);
            if (sizeof($config)==1 && is_array($config[0])) {
                $config = $config[0];
            }
        } else {
            $className = $config;
            $config = null;
        }
        if ($useCache) {
            if ($object = $this->_cacher->get($genre, $name, $config)) {
                return $object;
            }
        }
        if (!class_exists($className)) {
            include_once($this->$genreLocationProperty . "/$className.php");
        }
        $object = new $className;
        $object->configure($config);
        if ($useCache) {
            $this->_cacher->set($genre, $name, $object, $config);
        }
        return $object;
    }
    protected function _getInstanceNames($genre) {
        $genreProperty = "_$genre";
        return array_keys($this->$genreProperty);
    }
    protected function _getInstances($genre, $useCache=true) {
        $instances = array();
        foreach ($this->_getInstanceNames($genre) as $name) {
            $instance = $this->_getInstance($genre, $name, $useCache);
            if ($instance) {
                $instances[] = $instance;
            }
        }
        return $instances;
    }
    private function _getCachers() {
        return $this->_getInstances('cachers', false);
    }
    private function _getCacher($name=null) {
        return $this->_getInstance('cachers', $name, false);
    }
}

class TF extends _TFInstantiator {
    protected $_vocabulariesLocation = 'vocabularies';
    protected $_vocabularies = array(
        'modernizr'=>'TFVBrowserscope',
        'modernizr2'=>array('TFVBrowserscope', 'modernizr2.0.4')
    );

    public function getVocabularies() {
        return $this->_getInstances('vocabularies');
    }
    public function getVocabularyNames() {
        return $this->_getInstanceNames('vocabularies');
    }
    public function getVocabulary($name=null) {
        return $this->_getInstance('vocabularies', $name);
    }
}

//    private static $_evidenceProcessors = array(
//        'browserscope_yaml' => array (
//            'name'=>'browserscope_yaml',
//            'type'=>'browserscope_yaml',
//            'data_file'=>'../../data/user_agent_parser.yaml'
//        )
//    );
//    private static $_defaultEvidenceProcessor = 'browserscope_yaml';
//
//    private static $_vocabularies = array (
//        'http://www.browserscope.org/user/tests/table/agt1YS1wcm9maWxlcnINCxIEVGVzdBib2KQGDA'=>array(
//            'name'=>'modernizr2.0.4',
//            'type'=>'browserscope_json',
//            'data_file'=>'../../data/modernizr2.0.4.json'
//        )
//    );
//    private static $_vocabularyAliases = array (
//        'modernizr2'=>'http://www.browserscope.org/user/tests/table/agt1YS1wcm9maWxlcnINCxIEVGVzdBib2KQGDA'
//    );
//    private static $_defaultVocabularyIRI = 'modernizr2';
//
//    private static $_ddrService;
//
//    private static function _getDDRService() {
//        if(!self::$_ddrService) {
//            self::$_ddrService = new DDRService(
//                self::$_defaultVocabularyIRI,
//                array(
//                    'evidenceProcessor' => self::$_evidenceProcessors[self::$_defaultEvidenceProcessor],
//                    'vocabularies' => self::$_vocabularies,
//                    'vocabularyAliases' => self::$_vocabularyAliases,
//                )
//            );
//        }
//        return self::$_ddrService;
//    }
//
//    public static function getPropertyValue($propertyName, $evidence=null, $vocabularyIRI=null) {
//        if (!$evidence) {
//            $evidence = $_SERVER;
//        }
//        $ddrService = self::_getDDRService();
//        return $ddrService->getPropertyValue($evidence, $propertyName, $vocabularyIRI);
//    }
//
//    public static function listProperties($vocabularyIRI=null) {
//        $ddrService = self::_getDDRService();
//        return $ddrService->listProperties($vocabularyIRI);
//    }
//}
//



?>