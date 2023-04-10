<?php

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$result = $mysqli->query("SELECT * from user_credit_history where date_start >= '2020-11-00 00:00:00' and is_factured = 1 order by user_credit_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
				


$result2 = $mysqli->query("SELECT * FROM user_pay where id_user_credit_history = '".$row['user_credit_history']."'");
$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
if(!$row2){
	
	$result3 = $mysqli->query("SELECT * FROM users where id = '".$row['agent_id']."'");
	$row3 = $result3->fetch_array(MYSQLI_ASSOC);
	
	$order_cat = $row3['order_cat'];
	
	$remuneration_time = 0;
	switch ($order_cat) {
		case 0:
			$remuneration_time = 0;
			break;
		case 1:
			$remuneration_time = 0.00583333;//21€ / heure 
			break;
		case 2:
			$remuneration_time = 0.00616667;//22.20€ / heure
			break;
		case 3:
			$remuneration_time = 0.00683333;//24.6€ / heure
			break;
		case 4:
			$remuneration_time = 0.0075;//27€ / heure 
			break;
		case 5:
			$remuneration_time = 0;//XX€ / heure 
			break;
		case 6:
			$remuneration_time = 0;//XX€ / heure 
			break;
	}
	
	$ca = $row['ca'];
	
	if($row['ca_currency'] == '$') $ca = $ca * 0.641931;
	if($row['ca_currency'] == 'CHF') $ca = $ca * 0.935191;
	
	if($row['media'] != 'email')
		$gain = $remuneration_time * $row['credits'];
	else
		$gain = 12;
	
	var_dump("INSERT INTO user_pay (id_user_credit_history,order_cat_index,mail_price_index,date_pay,price,ca) VALUES('".$row['user_credit_history']."','".$row3['order_cat']."','12',NOW(),'".$gain."','".$ca."') ");
	//$mysqli->query("INSERT INTO user_pay (id_user_credit_history,order_cat_index,mail_price_index,date_pay,price,ca) VALUES('".$row['user_credit_history']."','".$row3['order_cat']."','12',NOW(),'".$gain."','".$ca."') ");
	/*
	$pp = number_format($ca,2,'.','') * 100;
try {
										
											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "destination" => $row3['stripe_account'],
											]);
																			}
								   catch (Exception $e) {
									 var_dump($e->getMessage());
									
									}*/
	var_dump($pp);
}

				
}
exit;
?>