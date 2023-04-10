<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

// Processing may take a while
//set_time_limit(0);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");



$list_expert = array(
45090);



foreach($list_expert as $agent_id){

	$result_invoice = $mysqli->query("SELECT * from invoice_accounts WHERE agent_id = '".$agent_id."' order by id");
	while($row_invoice = $result_invoice->fetch_array(MYSQLI_ASSOC)){
		
		$result_agent = $mysqli->query("SELECT invoice_vat_id from users WHERE id = '".$agent_id."'");
		$row_agent = $result_agent->fetch_array(MYSQLI_ASSOC);
		
		$result_vat = $mysqli->query("SELECT  * from invoice_vats WHERE id = '".$row_agent['invoice_vat_id']."'");
		$row_vat = $result_vat->fetch_array(MYSQLI_ASSOC);
		
		if($row_vat){
						$taux = 1 + ($row_vat['rate'] / 100);
						$amount = $row_invoice['total_amount']  / $taux;
						$vat_amount = $row_invoice['total_amount'] - $amount;
						$total_amount = $row_invoice['total_amount'];
						$vat_id = $row_vat['id'];
						$vat_tx = $row_vat['rate'];
						
					}else{
						$amount = $row_invoice['total_amount'];
						$vat_id = 0;
						$vat_amount = 0;
						$total_amount = $amount;	
						$vat_tx = 0;
					}
		$mysqli->query("update invoice_accounts set amount = '".$amount."', vat_id = '".$vat_id."', vat_tx = '".$vat_tx."', vat_amount = '".$vat_amount."' WHERE id = '".$row_invoice['id']."'");
		var_dump("update invoice_accounts set amount = '".$amount."', vat_id = '".$vat_id."', vat_tx = '".$vat_tx."', vat_amount = '".$vat_amount."' WHERE id = '".$row_invoice['id']."'");
	}
}
?>