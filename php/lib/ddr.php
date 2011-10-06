<?php

class DDRService {

    private $_evidenceProcessor;
    private $_defaultVocabularyIRI;
    private $_vocabularies;
    private $_vocabularyAliases;

    function __construct($defaultVocabularyIRI, $configuration) {
        $this->initialize($defaultVocabularyIRI, $configuration);
    }
    public function initialize($defaultVocabularyIRI, $configuration) {
        $this->_evidenceProcessor = $configuration['evidenceProcessor'];
        $this->_vocabularies = $configuration['vocabularies'];
        $this->_vocabularyAliases = $configuration['vocabularyAliases'];
        $this->_defaultVocabularyIRI = $defaultVocabularyIRI;
    }

    private static function _getEvidenceClues($evidence, $clues) {
        foreach($clues as $clue) {
            if (isset($evidence[$clue])) {
                return $evidence[$clue];
            }
        }
    }

    private function _processEvidence($evidence) {
        switch ($this->_evidenceProcessor['type']) {
            case 'browserscope_yaml':
            default:
                if (!isset($this->_evidenceProcessor['data'])) {
                    $this->_evidenceProcessor['data'] = self::_parseVerySimpleYaml($this->_evidenceProcessor['data_file']);
                }
                foreach ($this->_evidenceProcessor['data']['user_agent_parsers'] as $parser) {
                    $userAgent = self::_getEvidenceClues($evidence, array('HTTP_USER_AGENT', 'userAgent'));
                    $family = $v1 = $v2 = $v3 = null;

                    preg_match("'".$parser['regex']."'", $userAgent, $match);
                    if($match) {
                        if (isset($parser['family_replacement'])) {
                            $family = $parser['family_replacement'];
                            if(stripos($family, '$1')) {
                                $family = str_replace('$1', $match[1], $family);
                            }
                        } else {
                            $family = $match[1];
                        }
                        if (isset($parser['major_version_replacement'])) {
                            $v1 = $parser['major_version_replacement'];
                        } elseif (isset($parser['v1_replacement'])) {
                            $v1 = $parser['v1_replacement'];
                        } elseif (sizeof($match)>2) {
                            $v1 = $match[2];
                        }
                        if (sizeof($match)>3) {
                            $v2 = $match[3];
                            if (sizeof($match)>4) {
                                $v3 = $match[4];
                            }
                        }
                        if ($family) {
                            return array(
                                'userAgent'=>compact('family', 'v1', 'v2', 'v3')
                            );
                        }
                    }
                }
        }
    }

    private static function _parseVerySimpleYaml($file) {
        // a dreadful parser implementation that does the least required for browserscope_yaml
        $data = array();
        if ($handle = @fopen($file, 'r')) {
            while (($line = fgets($handle))!==false) {
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
            fclose($handle);
        }
        return $data;
    }


    private function _getVocabulary($vocabularyIRI=null) {
        if (!$vocabularyIRI) {
            $vocabularyIRI = $this->_defaultVocabularyIRI;
        }
        if (isset($this->_vocabularyAliases[$vocabularyIRI])) {
            $vocabularyIRI = $this->_vocabularyAliases[$vocabularyIRI];
        }
        if (!isset($this->_vocabularies[$vocabularyIRI])) {
            throw new Exception('NameException: unknown vocabulary');
        }
        $vocabulary = $this->_vocabularies[$vocabularyIRI];
        switch ($vocabulary['type']) {
            case 'browserscope_json':
            default:
                if ($this->_evidenceProcessor['type']!='browserscope_yaml') {
                    throw new Exception('NameException: wrong type of vocabulary for this evidence processor');
                }
                if (!isset($vocabulary['data'])) {
                    $vocabulary['data'] = json_decode(file_get_contents($vocabulary['data_file']), true);
                }
                return $this->_vocabularies[$vocabularyIRI] = $vocabulary;
        }
    }

    public static function arraySeek(&$array, $keys) {
        $key = array_shift($keys);
        if (is_int($key)) {
            $_keys = array_keys($array);
            $key = $_keys[$key];
        }
        if (!isset($array[$key])) {
            return;
        }
        if (sizeof($keys)) {
            return self::arraySeek($array[$key], $keys);
        }
        return $array[$key];
    }

    public function getPropertyValue($evidence, $propertyName, $vocabularyIRI=null) {
        $vocabulary = $this->_getVocabulary($vocabularyIRI);
        switch ($vocabulary['type']) {
            case 'browserscope_json':
            default:
                $clues = $this->_processEvidence($evidence);
                extract($clues['userAgent']);
                $seekKeys = array('results', '', 'results', $propertyName, 'result');
                if (isset($v3) &&
                    ($seekKeys[1] = sprintf('%s %s.%s%s%s', $family, $v1, $v2, (ctype_digit($v3)?'.':''), $v3)) &&
                    $value = self::arraySeek($vocabulary['data'], $seekKeys)
                ) {
                    return $value;
                }
                if (isset($v2) && (
                    ($seekKeys[1] = sprintf('%s %s.%s', $family, $v1, $v2)) &&
                    $value = self::arraySeek($vocabulary['data'], $seekKeys)
                )) {
                    return $value;
                }
                if (isset($v1) && (
                    ($seekKeys[1] = sprintf('%s %s', $family, $v1)) &&
                    $value = self::arraySeek($vocabulary['data'], $seekKeys)
                )) {
                    return $value;
                }
        }

    }

    public function listProperties($vocabularyIRI=null) {
        $vocabulary = $this->_getVocabulary($vocabularyIRI);
        switch ($vocabulary['type']) {
            case 'browserscope_json':
            default:
                return array_keys(self::arraySeek($vocabulary['data'], array('results', 0, 'results')));
        }
    }

}

?>