<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$result = $mysqli->query("SELECT * from invoice_agents where id >= 2710 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
  	$vat_num = '';
	$vat_status = '';
	$payment_mode = '';
	
	$result2 = $mysqli->query("SELECT * from users where id = '".$row['user_id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$vat_num = $row2['vat_num'];
	$vat_status = $row2['vat_num_status'];
	$payment_mode = 'stripe';
	
	$result3 = $mysqli->query("SELECT * from user_orders where user_id = '".$row['user_id']."' and amount = '-17' and date_ecriture >= '2021-01-30 00:00:00'");
	$row3 = $result3->fetch_array(MYSQLI_ASSOC);
	if($row3)$payment_mode = 'bankwire';
	
	$mysqli->query("UPDATE invoice_agents set vat_num = '".addslashes($vat_num) ."',vat_status = '".addslashes($vat_status) ."',payment_mode = '".addslashes($payment_mode) ."' where id = ".$row['id']." ");
	
}
exit;
?>