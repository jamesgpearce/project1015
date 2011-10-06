<?php

ini_set('display_errors', true);

require('../lib/TF.php');
$tf = new TF(array(
    'cacers'=>array(
        'basic'=>'TFCMemcached'
    )
));

print_r($tf);
print_r($tf->getVocabularies());
print_r($tf->getVocabularies());

//require('../lib/api.php');
//
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