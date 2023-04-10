<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
echo date('Y-m-d H:i:s').' ';

$date_bonus = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone('Europe/Paris') );
echo $date_bonus->format('Y-m-d H:i:s');
