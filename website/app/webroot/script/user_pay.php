<?php

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

$date = date('Y-m-d', strtotime('-1 day'));
$datemin = $date.' 00:00:00';
$datemax = $date.' 23:59:59';


$result = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.media,C.is_factured, C.seconds, U.order_cat, U.mail_price from user_credit_history C, users U WHERE C.agent_id = U.id and  C.agent_id = 45439");//and C.user_credit_history = 162098     C.date_start >= '{$datemin}' and C.date_start <= '{$datemax}'
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$agent_id = $row['agent_id'];		
	$user_credit_history = $row['user_credit_history'];
	$seconds = $row['seconds'];
	$order_cat = $row['order_cat'];
	$mail_price = $row['mail_price'];
	$media = $row['media'];
	$is_factured = $row['is_factured'];
	
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
	
	
	$price = 0;
	if($is_factured){
		switch ($media) {
			case 'phone':
				$price = $seconds * $remuneration_time;
				break;
			case 'chat':
				$price = $seconds * $remuneration_time;
				break;
			case 'email':
				$price = $mail_price;
				break;
		}
	}
	
	$mysqli->query("INSERT INTO user_pay(id_user_credit_history, order_cat_index, mail_price_index, date_pay, price) VALUES ('{$user_credit_history}','{$order_cat}','{$mail_price}',NOW(),'{$price}')");
		
}

?>