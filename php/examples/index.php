<?php

ini_set('display_errors', true);

require('../lib/TF.php');
$tf = new TF();


foreach($tf->getVocabularyNames() as $vocabularyName) {
    $vocabulary = $tf->getVocabulary($vocabularyName);
    print "<h2>$vocabularyName</h2>";
    foreach($vocabulary->getPropertyNames() as $propertyName) {
        $propertyValue = $vocabulary->getPropertyValue($propertyName);
        print "$propertyName: $propertyValue<br/>";
    }
}



//foreach(TenFifteen::getVocabularies() as $vocabulary_name=>$vocabulary) {
//    print "<h1>$vocabulary->name</h1>";
//    foreach(TenFifteen::getProperties($vocabulary) as $property_name=>$property) {
//        print "<br/>$property: ";
//        print TenFifteen::getPropertyValue($property, $vocabulary);
//    }
//
//}
//

?>