<?php

$date_bonus = new DateTime("now", new DateTimeZone('Europe/Paris') );
$dd_bonus = $date_bonus->format('Y-m-d H:i:s');
$annee_bonus = $date_bonus->format('Y');
		$mois_bonus = $date_bonus->format('m');
var_dump($dd_bonus);

?>