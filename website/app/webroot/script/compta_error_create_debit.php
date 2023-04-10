<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");

$list_tva = array(28380,
24915,
51628,
59578,
64426,
21982,
9179,
13289,
23313,
25646,
28833,
31671,
32046,
32106,
40429,
42069,
42423,
44820,
46548,
47439,
48465,
49375,
49985,
54081,
60934,
62366,
64286
);

$list_expert = array();

$result = $mysqli->query("SELECT * FROM `invoice_agents` where date_add >= '2020-05-01 00:00:00' and date_add <= '2021-01-15 00:00:00'  and status != 10   order by order_id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){

	array_push($list_expert,$row['user_id']);
}
$list_expert = array_unique($list_expert);

$order_id = 3059;

foreach($list_expert as $user_id){
	
	$result_info = $mysqli->query("SELECT * FROM  invoice_agents WHERE  user_id = '".$user_id."' order by order_id");  
	$row_info = $result_info->fetch_array(MYSQLI_ASSOC);
	
	$mysqli->query("INSERT INTO   invoice_agents (order_id,user_id,society_name,society_address,society_postalcode,society_city,society_country,society_num,vat_num,vat_status,payment_mode,date_add,date_min,date_max) values ('".$order_id."','".$user_id."','".addslashes($row_info['society_name'])."','".addslashes($row_info['society_address'])."','".addslashes($row_info['society_postalcode'])."','".addslashes($row_info['society_city'])."','".addslashes($row_info['society_country'])."','".addslashes($row_info['society_num'])."','".$row_info['vat_num']."','".$row_info['vat_status']."','".$row_info['payment_mode']."','2021-03-05 12:00:00','2020-04-01 00:00:00','2021-03-31 23:59:59')");
	$invoice_id = $mysqli->insert_id;
	
	$total = 0;
	$total_vat = 0;
	
	$result_invoice = $mysqli->query("SELECT * FROM  invoice_agents2 WHERE  date_add >= '2020-05-01 00:00:00' and date_add < '2021-01-15 00:00:00' and user_id = '".$user_id."'");  
	while($row = $result_invoice->fetch_array(MYSQLI_ASSOC)){

		$result_old = $mysqli->query("SELECT * FROM  invoice_agents WHERE  order_id = '".$row['order_id']."'");  
		$row_old = $result_old->fetch_array(MYSQLI_ASSOC);

		$diff = ($row['amount'] - $row_old['amount']);
		$diff_tva = ($row['vat'] - $row_old['vat']);
		if($diff > 0){
			$total += $diff;
			$total_vat += $diff_tva;
			$label = 'Being short billing in our Invoice Number IN '.$row['order_id'];

			$mysqli->query("INSERT INTO  invoice_agent_details (invoice_id,type,label,amount) values ('".$invoice_id."','fees','".$label."','".$diff."')");
		}

	}
	$tot = $total + $total_vat;
	$vat_tx = 0;
	if($total_vat > 0)$vat_tx = 23;
	
	if(in_array($user_id,$list_tva)){
		$vat_tx = 23;
		$total_tva = $total * 0.23;
		$tot = $total + $total_vat; 
	}
	
	$mysqli->query("UPDATE invoice_agents set amount = '".$total."', amount_total  = '".$tot."', vat  = '".$total_vat."', vat_tx  = '".$vat_tx."' where id = '".$invoice_id."'");
	$order_id ++;
}
exit;



var_dump('fin');exit;
?>