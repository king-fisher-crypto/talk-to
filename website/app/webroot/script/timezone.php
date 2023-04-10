<?php


$dateTimezoneUser1 = new DateTimeZone('America/Toronto');
$dateTimezoneUser2 = new DateTimeZone('Europe/Paris');

$dateTimeUser = new DateTime('2019-11-13 08:37:32');

var_dump($dateTimezoneUser1->getOffset($dateTimeUser));
var_dump($dateTimezoneUser2->getOffset($dateTimeUser));

?>