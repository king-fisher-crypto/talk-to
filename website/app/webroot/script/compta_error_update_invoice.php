<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$result_invoice = $mysqli->query("SELECT * FROM  invoice_agents2 WHERE  date_add >= '2020-05-01 00:00:00' and date_add < '2021-04-01 00:00:00' and order_id = 1853 ");//   
while($row = $result_invoice->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT P.ca as ca, P.price as price, U.user_credit_history, U.media, U.sessionid FROM `user_credit_history` U, user_pay_v2 P where U.`agent_id` = '".$row['user_id']."' and U.date_start >= '".$row['date_min']."' and U.date_start <= '".$row['date_max']."' and U.is_factured = 1 and P.id_user_credit_history=U.`user_credit_history`");
	
	//var_dump("SELECT P.ca as ca, P.price as price FROM `user_credit_history` U, user_pay_v2 P where U.`agent_id` = '".$row['user_id']."' and U.date_start >= '".$row['date_min']."' and U.date_start <= '".$row['date_max']."' and U.is_factured = 1 and P.id_user_credit_history=U.`user_credit_history`");
	
	$c_ca = 0;
	$c_price = 0 ;
	$c_penality = 0;
	
	while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
		$is_valid = true;
		if($row2['media'] == 'email'){
			$result3 = $mysqli->query("SELECT * from user_penalities where message_id = '".$row2['sessionid']."' and date_add >= '".$row['date_min']."' and date_add <= '".$row['date_max']."' ");
			$row3 = $result3->fetch_array(MYSQLI_ASSOC);
			
			if($row3){
				//var_dump($row2['sessionid']);
				$is_valid = false;
			}
		}
		if($is_valid){
			$c_ca += number_format($row2['ca'],2);
			$c_price += number_format($row2['price'],2) ;
		}else{
			$c_price += number_format($row2['price'],2) ;
			$c_penality += 12;
		}
	}
	
	
	//var_dump($c_penality);exit;
	
	$result4 = $mysqli->query("SELECT * from user_penalities where user_id = '".$row['user_id']."' and message_id > '0' and date_add >= '".$row['date_min']."' and date_add <= '".$row['date_max']."'");
	//var_dump("SELECT * from user_penalities where user_id = '".$row['user_id']."' and message_id > '0' and date_add >= '".$row['date_min']."' and date_add <= '".$row['date_max']."' ");
	$diff_pena = 0;
	while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
		$diff_pena += 12;
		
	}
	if($diff_pena > $c_penality){
		$pena_plus = $diff_pena - $c_penality;
		$c_penality = $c_penality + $pena_plus;
	}
		
	
	//var_dump($diff_pena);
	//var_dump($c_penality);exit;
	
	$paid = number_format($c_price,2,'.','');
	
	$paid_total = $paid + $row['bonus'] + $row['sponsor'] - $c_penality + $row['other'];
	
	//patch erreur calcul
	if($row['order_id'] == 2790)
		$paid_total = $paid_total -2.12;
	
	if($row['order_id'] == 2131)
		$paid_total = $paid_total -1.23;
	
	if($row['order_id'] == 1900)
		$paid_total = $paid_total +2.83;
	
	if($row['order_id'] == 1890)
		$paid_total = $paid_total -4.10;
	
	if($row['order_id'] == 1860)
		$paid_total = $paid_total -6.48;
	
	if($row['order_id'] == 1805)
		$paid_total = $paid_total +7.39;
	
	$list_expert_without_bankwire_fees = array(47019);//,14549,,48136
	
	if($row['payment_mode'] == 'bankwire' && !in_array($row['user_id'],$list_expert_without_bankwire_fees ) ){
		if($row['order_id'] != 2095){
			if($row['order_id'] >=  1539 && $row['order_id'] <=  1693){
				$paid_total = $paid_total - 0;
			}else{
				$paid_total = $paid_total - 17;
			}
		}
	}
	
	$list_expert_with_bankwire_fees = array(1277,1895,2051,2054,2740);
	
	if(in_array($row['order_id'],$list_expert_with_bankwire_fees )){
		$paid_total = $paid_total + 17;
	}
	
	$amount = number_format($c_ca - $paid_total ,2,'.','');
	
	
	$vat = 0 ;
	if($row['vat_tx']){
		$fees = $c_ca - $paid_total;
		$vat = $fees * $row['vat_tx'] / 100;
		$vat = number_format($vat,2,'.','');
		$paid_total = $paid_total - $vat;
	}
	$amount_total = number_format($amount + $vat,2,'.','');
	//var_dump($paid_total);
	
	
	
	//var_dump($c_ca);
	//var_dump($paid_total);
	//var_dump($vat);
	
	$paid_total_valid = 0;
	if($row['payment_mode'] != 'bankwire')
		$paid_total_valid = $paid_total;
	
	
	
	/*var_dump("UPDATE invoice_agents2 set ca = '".$c_ca."',penality = '".$c_penality."',paid = '".$paid."',paid_total = '".$paid_total."',paid_total_valid = '".$paid_total_valid."',amount = '".$amount."',vat = '".$vat."' ,amount_total = '".$amount_total."'  where id = '".$row['id']."'");
	exit;*/
	
	$mysqli->query("UPDATE invoice_agents2 set ca = '".$c_ca."',penality = '".$c_penality."',paid = '".$paid."',paid_total = '".$paid_total."',paid_total_valid = '".$paid_total_valid."',amount = '".$amount."',vat = '".$vat."' ,amount_total = '".$amount_total."'  where id = '".$row['id']."'");
	
	//var_dump($row['id']);
	//exit;
}


//		


var_dump('fin');exit;
exit;
?>