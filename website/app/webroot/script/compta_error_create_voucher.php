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

foreach($list_expert as $user_id){
	
	$mysqli->query("INSERT INTO  invoice_voucher_agents (user_id,invoice_id,amount,date_add,status) values ('".$user_id."','-2','0','2021-03-05 12:00:00','1')");
	$voucher_id = $mysqli->insert_id;
	
	$total = 0;
	$total_vat = 0;
	
	$result_invoice = $mysqli->query("SELECT * FROM  invoice_agents2 WHERE  date_add >= '2020-05-01 00:00:00' and date_add < '2021-01-15 00:00:00' and user_id = '".$user_id."'");  
	while($row = $result_invoice->fetch_array(MYSQLI_ASSOC)){

		$result_old = $mysqli->query("SELECT * FROM  invoice_agents WHERE  order_id = '".$row['order_id']."'");  
		$row_old = $result_old->fetch_array(MYSQLI_ASSOC);

		$diff =  ($row['amount'] - $row_old['amount']);
		$diff_tva = ($row['vat'] - $row_old['vat']);
		if($diff < 0){
			
			$diif = -1 * $diff;
			$diif_tva = -1 * $diff_tva;
			$total += $diif;
			$total_vat += $diif_tva;
		
		$mysqli->query("INSERT INTO   invoice_voucher_agent_details (invoice_voucher_id,invoice_order_id,old_amount,new_amount,unit_price) values ('".$voucher_id."','".$row_old['order_id']."','".$row_old['amount']."','".$row['amount']."','".$diif."')");

		}
	}
	
	if(in_array($user_id,$list_tva)){
		$total_tva = $total * 0.23;
	}
	
	$tot = $total + $total_vat;
	$mysqli->query("UPDATE invoice_voucher_agents set amount = '".$total."',vat = '".$total_vat."', amount_total = '".$tot."' where id = '".$voucher_id."'");
	
	
}
exit;



var_dump('fin');exit;
?>