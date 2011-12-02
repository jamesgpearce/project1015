<?php

class TFVocabularyProbe extends _TFVocabulary {

    protected function _configure($config = null) {
        if(!$config) {return;}
        $this->_config = array_merge(array(
            'script'=>'',
            'scriptFile'=>'',
            'result'=>'{}',
            'beacon'=>'default.js',
            'execute'=>'',
            'cookie'=>$config['name'],
            'nested'=>'true',
            'assignor'=>':',
            'separator'=>'|',
            'subseparator'=>'/',
            'normalize'=>'v',
            'reload'=>'false'
        ), $config);
    }

    private $_script;
    private function _getScript() {
        if(!isset($this->_script)) {
            if($this->_config['scriptFile']) {
                $this->_config['script'] = file_get_contents(implode(DIRECTORY_SEPARATOR, array(
                    __DIR__, '..', '..', '..', 'shared', 'probe', $this->_config['scriptFile']
                )), true);
                unset($this->_config['scriptFile']);
            }
            $this->_script = file_get_contents(implode(DIRECTORY_SEPARATOR, array(
                __DIR__, '..', '..', '..', 'shared', 'probe', 'beacon', $this->_config['beacon']
            )), true);
            $this->_script = preg_replace('/\n\s*/','', $this->_script);
            foreach($this->_config as $key=>$value) {
                $this->_script = str_replace('[' . strtoupper($key) . ']', $value, $this->_script);
            }
        }
        return $this->_script;
    }

    protected function _getPropertyNames() {
        //print $this->_getBeaconScript();
        return array();
    }


    //protected function _getPropertyValue($name) {
    //
    //}

    private $_data;
    private function _getData($reload=false) {
        if (!isset($this->_data) || $reload) {
            $separator = $this->_config['separator'];
            $cookie = $_COOKIE[$this->_config['cookie']];
            foreach (explode($separator, $cookie) as $feature) {
                list($name, $value) = explode(':', $feature, 2);
      if ($value[0]=='/') {
        $value_object = new stdClass();
        foreach (explode('/', substr($value, 1)) as $sub_feature) {
          list($sub_name, $sub_value) = explode(':', $sub_feature, 2);
          $value_object->$sub_name = $sub_value;
        }
        $modernizr->$name = $value_object;
      } else {
        $modernizr->$name = $value;
      }
    }
    return $modernizr;
  }

        }
        return $this->_data;
    }

}

?>
