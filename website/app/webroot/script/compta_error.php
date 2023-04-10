<?php

//Glassgen Comptabilité
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");

$fees_boi = 17.5;

echo 'num fact;date fact;societe;mode;date debut;date fin;CA ;fees;tva;Fees;paid;rem'.'<br />';

$result = $mysqli->query("SELECT * FROM `invoice_agents` where date_add >= '2021-04-01 00:00:00' order by id");// and order_id = 1738 
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
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
	$old_fees 		= $row['amount'];
	$old_vat 		= $row['vat'];
	$old_fees_ttc 		= $row['amount_total'];
	$old_paid 		= $row['paid'];
	$old_paid_total = $row['paid_total'];
	
	
	echo $row['order_id'].';'.$date_fact.';'.$row['society_name'].';'.$mode.';'.$date_com_min.';'.$date_com_max.';'.number_format($old_ca,2,',','') .';'.number_format($old_fees,2,',','') .';'.number_format($old_vat,2,',','') .';'.number_format($old_fees_ttc,2,',','') .';'.number_format($old_paid,2,',','') .';'.number_format($old_paid_total,2,',','') .';'.'<br />';
	

}

?>