<?php

//Glassgen Comptabilité
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");

$fees_boi = 17;



$list_expert = array();

$result = $mysqli->query("SELECT * FROM `invoice_agents` where date_add >= '2020-05-01 00:00:00' and status = 1 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if(!isset($list_expert[$row['user_id']]))$list_expert[$row['user_id']] = 0;
	
	$date_com_min = $row['date_min'];
	$date_com_max = $row['date_max'];
	$date_create  = $row['date_add'];
	$dx = new DateTime($date_create);
	$dx->modify('-1 month');
	$date_fact = $dx->format('Y-m');
	
	$mode = $row['payment_mode'];
	if(!$mode){
		if(!$row['paid_total_valid'])
			$mode = 'bankwire';
		else
			$mode = 'stripe';
	}
	
	$old_ca 		= $row['ca'];
	$old_fees 		= $row['amount_total'];
	$old_tva 	    = $row['vat'];
	
	
	$result2 = $mysqli->query("SELECT * FROM `invoice_agents2` where id = '".$row['id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$ca 			= $row2['ca'];
	$fees 			= $row2['amount_total'];
	$tva 	    	= $row2['vat'];
	
	
	$list_expert[$row['user_id']] += $fees - $old_fees;

}

echo 'ID expert;societe;fees regul amount'.'<br />';

foreach($list_expert as $user_id => $amount){
	
	$result2 = $mysqli->query("SELECT * FROM `invoice_agents2` where user_id = '".$user_id."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	echo $user_id.';'.$row2['society_name'].';'.number_format($amount,2,',','').'<br />';
	
}



?>