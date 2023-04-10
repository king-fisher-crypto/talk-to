<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");



require_once('../../Lib/stripe7/init.php');

$stripe = new \Stripe\StripeClient(
						  'sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs'
						);


$result = $mysqli->query("SELECT * from orders WHERE payment_mode = 'sepa' and valid = 1 order by id asc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from  order_sepatransactions WHERE order_id = '".$row['id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if($row2['charge_id']){
		
		var_dump($row2['charge_id']);
		
		try {
			
			$charge = $stripe->charges->retrieve(
											  $row2['charge_id'],
											  []
											);
			
			$timestamp = $charge->created;
			$date_upd = date('Y-m-d H:i:s',$timestamp);
								$mysqli->query("UPDATE orders set date_upd = '".$date_upd."' where id = '".$row['id']."'");
								var_dump("UPDATE orders set date_upd = '".$date_upd."' where id = '".$row['id']."'");
							
					}
					 catch (Exception $e) {
									
					}	
	}
	//var_dump('fin');exit;
}
var_dump('fin');exit;
exit;
?>