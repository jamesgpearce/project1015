<?php

class TFVBrowserscope extends _TFConfigurable implements _TFV {
    protected static function _configArguments($key) {
        if ($key===0) {
            return 'filename';
        }
    }
    protected $_filename = '';

}

?>
