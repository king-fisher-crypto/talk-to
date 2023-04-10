<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

//
// Database `spiriteo`
//

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

//remettre a zero tous les credit epuisé ( client credit a 0 )
/*$result = $mysqli->query("SELECT C.id as creditid, U.id as client from user_credit_prices C, users U WHERE U.id = C.id_user_credit and C.status = 0 and U.credit = 0" );
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	var_dump($row['creditid']. ' '.$row['client']);	
	$mysqli->query("UPDATE user_credit_prices SET seconds_left = 0 , status = '1' where id = '{$row['creditid']}'");
}*/


//remettre au bon niveau credit left
/*$result = $mysqli->query("SELECT C.id as creditid, U.id as client from user_credit_prices C, users U WHERE U.id = C.id_user_credit and C.status = 0 and U.credit < C.seconds_left" );
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	var_dump($row['creditid']. ' '.$row['client']);	
}*/


/*	var_dump("UPDATE user_pay SET order_cat_index = ".$order_cat." , price = '{$price}' where id_user_credit_history = '{$row['user_credit_history']}'");
	$mysqli->query("UPDATE user_pay SET order_cat_index = ".$order_cat." , price = '{$price}' where id_user_credit_history = '{$row['user_credit_history']}'");
	$mysqli->query("UPDATE export_coms SET tx_minute = ".$tx_minute." , tx_second = '{$remuneration_time}', price = '{$price}' where user_credit_history_id = '{$row['user_credit_history']}'");
*/

$paypal_euro = 0;
$paypal_dollar = 0;
$paypal_chf = 0;
$stripe_euro = 0;
$stripe_dollar = 0;
$stripe_chf = 0;
$total_euro_test = 0;
$result = $mysqli->query("SELECT id,payment_mode,total, currency, valid from orders WHERE date_add >= '2020-09-30 22:00:00' and date_add <= '2020-10-31 22:59:59' and valid > 0 and valid < 4 and payment_mode != 'refund'" );
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT U.price, U.seconds_left, U.seconds from user_credit_prices U,user_credits C ,orders O WHERE C.id = U.id_user_credit and O.id = '".$row['id']."' and O.id = C.order_id" );
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);

	$amount = $row['total'];
	
	if($row['payment_mode'] == 'paypal'){
		$amount = $row2['price'] * ($row2['seconds'] - $row2['seconds_left']);
		if($row['currency'] == 'CHF'){
			$paypal_chf += $amount;
		}elseif($row['currency'] == '$'){
			$paypal_dollar += $amount;
		}else{
			$paypal_euro += $amount;
		}
	}else{
		if($row['valid'] < 4){
			
			$amount = $row2['price'] * ($row2['seconds'] - $row2['seconds_left']);
			if($row['currency'] == 'CHF'){
				$stripe_chf += $amount;
			}elseif($row['currency'] == '$'){
				$stripe_dollar += $amount;
			}else{
				$stripe_euro += $amount;
				$total_euro_test += $row['total'];
			}
		}
	}



}




/*



$result = $mysqli->query("SELECT O.payment_mode, U.devise, U.price, U.seconds_left, U.seconds, O.total from user_credit_prices U,user_credits C ,orders O WHERE C.id = U.id_user_credit and O.id = C.order_id  and O.date_add >= '2020-09-30 22:00:00' and O.date_add <= '2020-10-31 22:59:59'" );
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$amount = $row['total'];//$row['price'] * ($row['seconds'] - $row['seconds_left']);
	
	if($row['payment_mode'] == 'paypal'){
		if($row['devise'] == 'CHF'){
			$paypal_chf += $amount;
		}elseif($row['devise'] == '$'){
			$paypal_dollar += $amount;
		}else{
			$paypal_euro += $amount;
		}
	}else{
		if($row['devise'] == 'CHF'){
			$stripe_chf += $amount;
		}elseif($row['devise'] == '$'){
			$stripe_dollar += $amount;
		}else{
			$stripe_euro += $amount;
		}
	}
	
}*/
var_dump('stripe_euro : '.$stripe_euro);
var_dump('stripe_dollar : '.$stripe_dollar);
var_dump('stripe_chf : '.$stripe_chf);
var_dump('paypal_euro : '.$paypal_euro);
var_dump('paypal_dollar : '.$paypal_dollar);
var_dump('paypal_chf : '.$paypal_chf);
var_dump($total_euro_test);
