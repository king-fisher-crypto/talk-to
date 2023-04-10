<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");

/*$result = $mysqli->query("SELECT * from user_credit_history WHERE date_start <= '2020-11-30 22:59:59' order by user_credit_history desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$mysqli->query("UPDATE user_pay set ca_old = ca where id_user_credit_history = '".$row['user_credit_history']."'");
}
var_dump('end');
exit;*/

$result_tx = $mysqli->query("SELECT * from invoice_agent_tx_conversions WHERE year = '2021' and month = '3'");
$row_tx = $result_tx->fetch_array(MYSQLI_ASSOC);
$list_tx = array();
$list_tx['CHF']= $row_tx['paypal_chf'];
$list_tx['$']= $row_tx['paypal_cad'];
$list_tx['€']= 1;
$result = $mysqli->query("SELECT * from user_credit_history WHERE type_pay = 'aud' and ca_euros < 1 and date_start >= '2021-02-28 23:00:00' and date_start <= '2021-03-31 22:00:00' order by user_credit_history asc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$result_tx = $mysqli->query("SELECT * from  user_pay WHERE id_user_credit_history = '".$row['user_credit_history']."'");
	$row_tx = $result_tx->fetch_array(MYSQLI_ASSOC);
	$mysqli->query("UPDATE user_credit_history set ca_euros = '".$row_tx['ca']."' where user_credit_history = '".$row['user_credit_history']."'");
	$mysqli->query("UPDATE export_coms set ca_euro = '".$row_tx['ca']."' where user_credit_history_id = '".$row['user_credit_history']."' and credits > 0");
}

$result = $mysqli->query("SELECT * from user_credit_history WHERE type_pay = 'pre' and ca_euros = 0 and date_start >= '2021-02-28 23:00:00'  and date_start <= '2021-03-31 22:00:00' order by user_credit_history asc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	var_dump($row['user_credit_history']);
	$tab_ca_id = unserialize($row['ca_ids']);
	$ca_euros = 0;
	foreach($tab_ca_id as $usercredit){
		$seconds = $usercredit['seconds'];
		$user_credit_price_id = $usercredit['id'];
		var_dump("SELECT * from  user_credit_prices WHERE id = '".$user_credit_price_id."'");
		$result_tx = $mysqli->query("SELECT * from  user_credit_prices WHERE id = '".$user_credit_price_id."'");
		$row_tx = $result_tx->fetch_array(MYSQLI_ASSOC);
		//var_dump($row_tx);
		if($row_tx['price_euros']>0){
			$ca_euros += $row_tx['price_euros'] * $seconds;
		}else{
			$ca_euros += ($row_tx['price'] * $list_tx[$row_tx['devise']] )* $seconds;
		}
	}
	
	var_dump($ca_euros);exit;
	
	$mysqli->query("UPDATE user_credit_history set ca_euros = '".$ca_euros."' where user_credit_history = '".$row['user_credit_history']."'");
	
	$mysqli->query("UPDATE export_coms set ca_euro = '".$ca_euros."' where user_credit_history_id = '".$row['user_credit_history']."'  and credits > 0");
	
	//$mysqli->query("UPDATE user_pay set ca_old = ca where id_user_credit_history = '".$row['user_credit_history']."'");
	
	$mysqli->query("UPDATE user_pay set ca = '".$ca_euros."' where id_user_credit_history = '".$row['user_credit_history']."'");
	
	//var_dump("UPDATE user_credit_history set ca_euros = '".$ca_euros."' where user_credit_history = '".$row['user_credit_history']."'");exit;
	
}

/*$result = $mysqli->query("SELECT * FROM `user_pay` where ca_old = 0 and ca > 0 ORDER BY `id_user_pay` DESC");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result_tx = $mysqli->query("SELECT * from  user_credit_history WHERE user_credit_history = '".$row['id_user_credit_history']."'");
	$row_tx = $result_tx->fetch_array(MYSQLI_ASSOC);
	
	$mysqli->query("UPDATE user_pay set ca_old = ca where id_user_credit_history = '".$row['id_user_credit_history']."'");
	$mysqli->query("UPDATE user_pay set ca = '".$row_tx['ca_euros']."' where id_user_credit_history = '".$row['id_user_credit_history']."'");
}*/


var_dump('fin');exit;
exit;
?>