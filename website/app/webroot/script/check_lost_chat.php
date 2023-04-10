<?php

//Spiriteo LOST

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$sessions = '';

$list_good = array();
$result = $mysqli->query("SELECT * from chats WHERE consult_date_start IS NOT NULL ");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	array_push($list_good, $row['id']);
}

$list_bad = array();
$list_bad_with_sms = array();
$result = $mysqli->query("SELECT * from chats WHERE consult_date_start IS NULL ");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	array_push($list_bad, $row['id']);
	$result2 = $mysqli->query("SELECT * from sms_histories WHERE id_tchat = '{$row['id']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if($row2['id']){
		array_push($list_bad_with_sms, $row2['id']);
	}
}

$nb = count($list_good) + count ($list_bad);
$nb_lost = count ($list_bad);
$nb_good = count($list_good);
$pourcent_good = number_format($nb_good * 100 / $nb,2);
$pourcent_lost = number_format($nb_lost * 100 / $nb,2);
$nb_lost_sms = count ($list_bad_with_sms);
$pourcent_lost_sms = number_format($nb_lost_sms * 100 / $nb_lost,2);

echo 'NB TCHAT : '.$nb.'<br />';
echo 'NB TCHAT GOOD : '.$nb_good .' ('.$pourcent_good.'%)<br />';
echo 'NB TCHAT LOST : '.$nb_lost. ' ('.$pourcent_lost.'%)<br />';
echo 'NB TCHAT LOST WITH SEND SMS : '.$nb_lost_sms. ' ('.$pourcent_lost_sms.'%) donc tchat perdu > 1 min attente client <br />';

/* dernier mois */
$list_good = array();
$result = $mysqli->query("SELECT * from chats WHERE consult_date_start IS NOT NULL and date_start > '2017-03-01 00:00:00'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	array_push($list_good, $row['id']);
}

$list_bad = array();
$list_bad_with_sms = array();
$result = $mysqli->query("SELECT * from chats WHERE consult_date_start IS NULL and date_start > '2017-03-01 00:00:00'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	array_push($list_bad, $row['id']);
	$result2 = $mysqli->query("SELECT * from sms_histories WHERE id_tchat = '{$row['id']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if($row2['id']){
		array_push($list_bad_with_sms, $row2['id']);
	}
}

$nb = count($list_good) + count ($list_bad);
$nb_lost = count ($list_bad);
$nb_good = count($list_good);
$pourcent_good = number_format($nb_good * 100 / $nb,2);
$pourcent_lost = number_format($nb_lost * 100 / $nb,2);
$nb_lost_sms = count ($list_bad_with_sms);
$pourcent_lost_sms = number_format($nb_lost_sms * 100 / $nb_lost,2);
echo 'MARS 2017 =><br />';
echo 'NB TCHAT : '.$nb.'<br />';
echo 'NB TCHAT GOOD : '.$nb_good .' ('.$pourcent_good.'%)<br />';
echo 'NB TCHAT LOST : '.$nb_lost. ' ('.$pourcent_lost.'%)<br />';
echo 'NB TCHAT LOST WITH SEND SMS : '.$nb_lost_sms. ' ('.$pourcent_lost_sms.'%) donc tchat perdu > 1 min attente client <br />';


exit;
?>