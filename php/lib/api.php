<?php

require('ddr.php');

class TenFifteen {

    private static $_evidenceProcessors = array(
        'browserscope_yaml' => array (
            'name'=>'browserscope_yaml',
            'type'=>'browserscope_yaml',
            'data_file'=>'../../data/user_agent_parser.yaml'
        )
    );
    private static $_defaultEvidenceProcessor = 'browserscope_yaml';

    private static $_vocabularies = array (
        'http://www.browserscope.org/user/tests/table/agt1YS1wcm9maWxlcnINCxIEVGVzdBib2KQGDA'=>array(
            'name'=>'modernizr2.0.4',
            'type'=>'browserscope_json',
            'data_file'=>'../../data/modernizr2.0.4.json'
        )
    );
    private static $_vocabularyAliases = array (
        'modernizr2'=>'http://www.browserscope.org/user/tests/table/agt1YS1wcm9maWxlcnINCxIEVGVzdBib2KQGDA'
    );
    private static $_defaultVocabularyIRI = 'modernizr2';

    private static $_ddrService;

    private static function _getDDRService() {
        if(!self::$_ddrService) {
            self::$_ddrService = new DDRService(
                self::$_defaultVocabularyIRI,
                array(
                    'evidenceProcessor' => self::$_evidenceProcessors[self::$_defaultEvidenceProcessor],
                    'vocabularies' => self::$_vocabularies,
                    'vocabularyAliases' => self::$_vocabularyAliases,
                )
            );
        }
        return self::$_ddrService;
    }

    public static function getProperty($propertyName, $evidence=null, $vocabularyIRI=null) {
        if (!$evidence) {
            $evidence = $_SERVER;
        }
        $ddrService = self::_getDDRService();
        return $ddrService->getProperty($evidence, $propertyName, $vocabularyIRI);
    }

}




?>