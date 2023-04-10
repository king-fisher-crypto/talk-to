<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

//
// Database `spiriteo`
//

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT C.seconds,C.is_factured,C.media, C.user_credit_history,C.sessionid from user_credit_history C, users U WHERE U.id = 23220 and C.agent_id = U.id and  C.user_credit_history >= 439298" );//C.date_start >= '2019-04-03 18:30:00' and C.date_start <= '2019-04-07 15:52:33'
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	var_dump($row['id_user_credit_history']. ' '.$row['sessionid']);
	$order_cat = 3;
	$mail_price = 12;
	$remuneration_time = 0;
	$tx_minute = 0;
	switch ($order_cat) {
		case 0:
			$remuneration_time = 0;
			$tx_minute = 0;
			break;
		case 1:
			$remuneration_time = 0.00583333;//21€ / heure 
			$tx_minute = 0.35;
			break;
		case 2:
			$remuneration_time = 0.00616667;//22.20€ / heure
			$tx_minute = 0.37;
			break;
		case 3:
			$remuneration_time = 0.00683333;//24.6€ / heure
			$tx_minute = 0.41;
			break;
		case 4:
			$remuneration_time = 0.0075;//27€ / heure 
			$tx_minute = 0.45;
			break;
		case 5:
			$remuneration_time = 0;//XX€ / heure 
			$tx_minute = 0.32;
			break;
		case 6:
			$remuneration_time = 0;//XX€ / heure 
			$tx_minute = 0.34;
			break;
	}
	
	$price = 0;
	if($row['is_factured']){
		switch ($row['media']) {
			case 'phone':
				$price = $row['seconds'] * $remuneration_time;
				break;
			case 'chat':
				$price = $row['seconds'] * $remuneration_time;
				break;
			case 'email':
				$price = $mail_price;
				break;
		}
	}
	var_dump("UPDATE user_pay SET order_cat_index = ".$order_cat." , price = '{$price}' where id_user_credit_history = '{$row['user_credit_history']}'");
	$mysqli->query("UPDATE user_pay SET order_cat_index = ".$order_cat." , price = '{$price}' where id_user_credit_history = '{$row['user_credit_history']}'");
	$mysqli->query("UPDATE export_coms SET tx_minute = ".$tx_minute." , tx_second = '{$remuneration_time}', price = '{$price}' where user_credit_history_id = '{$row['user_credit_history']}'");
	/*var_dump("INSERT INTO user_pay(id_user_credit_history,order_cat_index,mail_price_index,date_pay,price) values('{$row['user_credit_history']}', 2,12,NOW(),'{$price}')");
	$mysqli->query("INSERT INTO user_pay(id_user_credit_history,order_cat_index,mail_price_index,date_pay,price) values('{$row['user_credit_history']}', 2,12,NOW(),'{$price}')");*/

}