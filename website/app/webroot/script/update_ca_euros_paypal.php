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

$result_tx = $mysqli->query("SELECT * from invoice_agent_tx_conversions WHERE year = '2021' and month = '3'");
$row_tx = $result_tx->fetch_array(MYSQLI_ASSOC);
$list_tx = array();
$list_tx['CHF']= $row_tx['paypal_chf'];
$list_tx['$']= $row_tx['paypal_cad'];

$result = $mysqli->query("SELECT * from orders WHERE payment_mode = 'paypal' and currency !='€' and valid = 1 and total_euros < 1 and date_add >= '2021-02-28 23:00:00' and date_add <= '2021-03-31 22:00:00' order by id asc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	
	
								$euros = $row['total'] * $list_tx[$row['currency']];
								
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

	//var_dump('fin');exit;
}
var_dump('fin');exit;
exit;
?>