<?php

// Le message
$message = "Line 1\r\nLine 2\r\nLine 3";

// Dans le cas où nos lignes comportent plus de 70 caractères, nous les coupons en utilisant wordwrap()
$message = wordwrap($message, 70, "\r\n");

// Envoi du mail
$test = mail('contact@web-sigle.fr', 'Mon Sujet', $message);
var_dump($test);
?>