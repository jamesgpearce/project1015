<?php

class TFVocabularyBrowserscope extends _TFVocabulary {

    protected function _getPropertyNames() {
        $data = $this->_getData();
        $first = reset($data['results']);
        return array_keys($first['results']);
    }

    private $_propertyValue = array();
    protected function _getPropertyValue($name, $evidence=null) {
        if (!$evidence) {
            $evidence = $this->_getDefaultEvidence();
        }
        if (is_string($evidence)) {
            $userAgent = $evidence;
        } else {
            $userAgent = $evidence['HTTP_USER_AGENT'];
        }
        if (!isset($_propertyValue[$userAgent])) {
            $_propertyValue[$userAgent] = array();
        }
        if (!isset($_propertyValue[$userAgent][$name])) {
            $data = $this->_getData();
            extract($this->_getUserAgentParts($userAgent));
            //family, major, minor, revision, majorName, minorName, revisionName
            $seek = array('results', '', 'results', $name, 'result');
            foreach (array($revisionName, $minorName, $minorName) as $versionName) {
                if ($versionName) {
                    $seek[1] = $versionName;
                    $value = TFUtil::deepSeek($data, $seek);
                    if ($value !== null) {
                        $_propertyValue[$userAgent][$name] = $value;
                        break;
                    }
                }
            }
        }
        return $_propertyValue[$userAgent][$name];
    }

    private $_data;
    private function _getData() {
        if (!isset($this->_data)) {
            $this->_data = $this->_cache->get('data', $this->_config);
            if (!$this->_data) {
                $this->_data = json_decode(file_get_contents(implode(DIRECTORY_SEPARATOR, array(
                    dirname(__FILE__), '..', '..', '..', 'shared', 'browserscope', $this->_config['data']
                ))), true);
                $this->_cache->set('data', $this->_config, $this->_data);
            }
        }
        return $this->_data;
    }

    private $_uaParsers;
    private function _getUaParsers() {
        if (!isset($this->_uaParsers)) {
            $this->_uaParsers = $this->_cache->get('uaParser', $this->_config);
            if (!$this->_uaParsers) {
                $this->_uaParsers = TFUtil::yamlDecode(file_get_contents(implode(DIRECTORY_SEPARATOR, array(
                    dirname(__FILE__), '..', '..', '..', 'shared', 'browserscope', 'uaparser', $this->_config['uaParser']
                ))), true);
                $this->_uaParsers = $this->_uaParsers['user_agent_parsers'];
                $this->_cache->set('uaParser', $this->_config, $this->_uaParsers);
            }
        }
        return $this->_uaParsers;
    }

    private $_defaultEvidence;
    protected function _getDefaultEvidence() {
        return $_SERVER;
    }

    private $_userAgentParts = array();
    protected function _getUserAgentParts($userAgent) {
        if (!isset($_userAgentParts[$userAgent])) {
            foreach ($this->_getUaParsers() as $uaParser) {
                $family = $major = $minor = $revision = $majorName = $minorName = $revisionName = null;
                preg_match("'".$uaParser['regex']."'", $userAgent, $match);
                if ($match) {
                    if (isset($uaParser['family_replacement'])) {
                        $family = $uaParser['family_replacement'];
                        if(stripos($family, '$1')) {
                            $family = str_replace('$1', $match[1], $family);
                        }
                    } else {
                        $family = $match[1];
                    }
                    if (isset($uaParser['major_version_replacement'])) {
                        $major = $uaParser['major_version_replacement'];
                    } elseif (isset($uaParser['v1_replacement'])) {
                        $major = $uaParser['v1_replacement'];
                    } elseif (sizeof($match)>2) {
                        $major = $match[2];
                    }
                    if ($major) {
                        $majorName = sprintf('%s %s', $family, $major);
                    }
                    if (sizeof($match)>3) {
                        $minor = $match[3];
                        $minorName = sprintf('%s %s.%s', $family, $major, $minor);
                        if (sizeof($match)>4) {
                            $revision = $match[4];
                            $revisionName = sprintf('%s %s.%s%s%s', $family, $major, $minor, (ctype_digit($revision)?'.':''), $revision);
                        }
                    }
                    if ($family) {
                        $_userAgentParts[$userAgent] = compact(
                            'family',
                            'major', 'minor', 'revision',
                            'majorName', 'minorName', 'revisionName'
                        );
                        break;
                    }
                }
            }
        }
        return $_userAgentParts[$userAgent];
    }

}

?>
