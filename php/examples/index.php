<?php

ini_set('display_errors', true);

require('../lib/api.php');

foreach(TenFifteen::listProperties() as $property) {
    print "<br/>$property: ";
    print TenFifteen::getPropertyValue($property);
}

?>