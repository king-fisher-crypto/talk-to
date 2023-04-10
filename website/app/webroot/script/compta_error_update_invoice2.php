<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$result_invoice = $mysqli->query("SELECT * FROM  invoice_agents WHERE date_add > '2021-02-01 00:00:00' ");
while($row = $result_invoice->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT * from invoice_agents2 where order_id = '".$row['order_id']."' ");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$mysqli->query("UPDATE invoice_agents set ca = '".$row2['ca']."',paid = '".$row2['paid']."',paid_total = '".$row2['paid_total']."',amount = '".$row2['amount']."',vat = '".$row2['vat']."' ,amount_total = '".$row2['amount_total']."'  where id = '".$row['id']."'");
	
	$mysqli->query("UPDATE  invoice_agent_details set amount = '".$row2['amount']."'  where invoice_id = '".$row['id']."'");
}


//		


var_dump('fin');exit;
exit;
?>