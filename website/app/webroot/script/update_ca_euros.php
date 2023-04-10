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


$result = $mysqli->query("SELECT * from orders WHERE payment_mode = 'stripe' and currency !='€' and valid = 1 and total_euros < 1 order by id desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from  order_stripetransactions WHERE order_id = '".$row['id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if($row2['id']){
		
		
		
		try {
			
			if(!substr_count($row2['id'],'ch_' )){
				$pi = $stripe->paymentIntents->retrieve(
						  trim($row2['id']),
						  []
						);
			}else{
				$pi = 1;
			}
					
					 	if($pi){
							if(substr_count($row2['id'],'ch_' )){
								$charge = $stripe->charges->retrieve(
											  $row2['id'],
											  []
											);
							}else{
								$charge = $pi->charges->data[0];
							}
							if($charge){

								$balance = $stripe->balanceTransactions->all(['source' => $charge->id]);
								$euros = $balance->data[0]->amount;
								$euros = $euros/100;
								
								$mysqli->query("UPDATE orders set total_euros = '".$euros."' where id = '".$row['id']."'");
								//var_dump("UPDATE orders set total_euros = '".$euros."' where id = '".$row['id']."'");
								
								$result3 = $mysqli->query("SELECT * from user_credits WHERE order_id = '".$row['id']."'");
								$user_credit = $result3->fetch_array(MYSQLI_ASSOC);
								
								$result4 = $mysqli->query("SELECT * from user_credit_prices WHERE id_user_credit = '".$user_credit['id']."'");
								$user_credit_price = $result4->fetch_array(MYSQLI_ASSOC);
								
																
								$credits = $user_credit['credits'];
								$price = ($euros / $credits);
								
								$mysqli->query("UPDATE user_credit_prices set price_euros = '".$price."' where id = '".$user_credit_price['id']."'");
								//var_dump("UPDATE user_credit_prices set price_euros = '".$price."' where id = '".$user_credit_price['id']."'");

							}
						}
					}
					 catch (Exception $e) {
									
					}	
	}
	//var_dump('fin');exit;
}
var_dump('fin');exit;
exit;
?>