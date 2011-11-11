<?php

class TFVocabularyProbe extends _TFVocabulary {

    protected function _configure($config = null) {
        if(!$config) {return;}
        $this->_config = $config;
        $this->_config['beacon'] = array_merge(array(
            'script'=>'default.js',
            'cookie'=>$config['name'],
            'nested'=>'true',
            'separator'=>'|',
            'subseparator'=>'/',
            'normalize'=>'v'
        ), $config['beacon']);
    }

    private $_beaconScript;
    private function _getBeaconScript() {
        if(!isset($this->_beaconScript)) {
            $this->_beaconScript = file_get_contents(implode(DIRECTORY_SEPARATOR, array(
                __DIR__, '..', '..', '..', 'shared', 'probe', 'beacon', $this->_config['beacon']['script']
            )), true);
            $this->_beaconScript = preg_replace('/\n\s*/','', $this->_beaconScript);
            foreach($this->_config['beacon'] as $key=>$value) {
                $this->_beaconScript = str_replace('[' . strtoupper($key) . ']', $value, $this->_beaconScript);
            }
        }
        return $this->_beaconScript;
    }

    protected function _getPropertyNames() {
        //print $this->_getBeaconScript();
        return array();
    }


    //protected function _getPropertyValue($name) {
    //
    //}

}

?>
