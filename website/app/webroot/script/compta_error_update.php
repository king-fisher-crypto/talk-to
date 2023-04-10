<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");
/*
$result = $mysqli->query("SELECT * FROM `user_credit_history` where date_start >= '2020-02-31 22:00:00' and is_factured = 1 and type_pay = 'aud' order by user_credit_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$ca = 0;
	$seconds = $row['credits'];
	$result2 = $mysqli->query("SELECT * FROM `domains` where id = '".$row['domain_id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$result3 = $mysqli->query("SELECT * FROM `country_lang_phone` where country_id = '".$row2['country_id']."'");
	$row3 = $result3->fetch_array(MYSQLI_ASSOC);
	
	$cost = $row3['surtaxed_minute_cost'] / 60;
	
	
	if($row2['country_id']== 3 ){//suisse
		$cost = $cost * 0.92;
	}
	if($row2['country_id'] == 13 ){//canada
		$cost = $cost * 0.63;
	}
	
	$ca = $cost * $seconds;
	$mysqli->query("UPDATE user_pay_v2 set ca = '".$ca."' where id_user_credit_history = '".$row['user_credit_history']."'");
}

var_dump('fin aud');
exit;

*/
$result = $mysqli->query("SELECT * FROM `user_credit_history` where date_start >= '2020-01-01 22:00:00' and is_factured = 1 and type_pay != 'aud'   order by user_credit_history");//and user_credit_history = 425236
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$ca_ids = $row['ca_ids'];
	$ca = 0;
	$seconds = $row['credits'];
	if($ca_ids){
		$unse = @unserialize($ca_ids);

		
		if($unse !== false || $unse === 'b:0;'){
			foreach($unse as $tab){
				$userprice = $tab['id'];
				$seconds = $tab['seconds'];
				$result2 = $mysqli->query("SELECT * FROM `user_credit_prices` where id = '".$userprice."'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				$price = $row2['price_euros'];
				if($price > 0){
					$ca += $row2['price_euros'] * $seconds;
				}else{
					$price = $row2['price'];
					$devise = $row2['devise'];
					if($devise== "CHF" ){//suisse
						$price = $price * 0.92;
					}
					if($devise == "$" ){//canada
						$price = $price * 0.63;
					}
					$ca += $price * $seconds;
				}
			}
			if(!$ca)$ca += $price * $seconds;
		}else{
			$cut = explode('_',$ca_ids);

			$price = 0;
			foreach($cut as $userprice){
				if($userprice){
					$result2 = $mysqli->query("SELECT * FROM `user_credit_prices` where id = '".$userprice."'");
					$row2 = $result2->fetch_array(MYSQLI_ASSOC);
					$price = $row2['price_euros'];
				}
			}
			if($price > 0){
				$ca += $price * $seconds;
			}else{
				$price = $row2['price'];
				$devise = $row2['devise'];
				if($devise== "CHF" ){//suisse
					$price = $price * 0.92;
				}
				if($devise == "$" ){//canada
					$price = $price * 0.63;
				}
				$ca += $price * $seconds;	
			}
			

		}
	}else{
		$price = 0.03316667;
		$ca = $price * $seconds;
	}
	//var_dump($row['user_credit_history']);
	//var_dump($ca);exit;
	if($ca){
		$mysqli->query("UPDATE user_pay_v2 set ca = '".$ca."' where id_user_credit_history = '".$row['user_credit_history']."'");
		//var_dump("UPDATE user_pay_v2 set ca = '".$ca."' where id_user_credit_history = '".$row['user_credit_history']."'");exit;
	}else{
		var_dump($row['user_credit_history'].' -> '.$ca);
	}
	
}
	
var_dump('fin');exit;
exit;
?>