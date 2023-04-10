<?php

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");
$date = date('Y-m-d', strtotime('-1 day'));
$datemin = $date.' 00:00:00';
$datemax = $date.' 23:59:59';


$result = $mysqli->query("SELECT * from user_pay where ca_currency <= 0 order by id_user_pay");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from user_credit_history where user_credit_history = '".$row['id_user_credit_history']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	if($row2['ca'] > 0){
		
		$ca_currency = $row2['ca'];
		$currency = $row2['ca_currency'];
		$tx_change = number_format(  $row['ca'] / $row2['ca'],2);
		
		$mysqli->query("UPDATE user_pay SET ca_currency = '".$ca_currency."', currency = '".$currency ."', tx_change = '".$tx_change."' where id_user_pay = '".$row['id_user_pay']."' ");
		var_dump("UPDATE user_pay SET ca_currency = '".$ca_currency."', currency = '".$currency ."', tx_change = '".$tx_change."' where id_user_pay = '".$row['id_user_pay']."' ");
		
	}
	
}


?>