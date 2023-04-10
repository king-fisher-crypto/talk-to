<?php

$date = new DateTime('2020-12-02 10:05:53');
$timestamp_rep = $date->getTimestamp();

$date_last = new DateTime('2020-12-02 11:12:43');
$timestamp_last = $date_last->getTimestamp();

var_dump($timestamp_rep);
var_dump($timestamp_last);

$diff = gmdate("H:i:s", $timestamp_last - $timestamp_rep );
var_dump($diff);
?>