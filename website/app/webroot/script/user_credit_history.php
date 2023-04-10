<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

//
// verification que toutes les comms existe en factu experts
//


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");

$result = $mysqli->query("SELECT * from user_credit_last_histories where date_start >= '2021-02-02 05:00:00' and date_start <= '2021-02-02 18:00:00' order by user_credit_last_history ");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from user_credit_history where media = '{$row['media']}' and sessionid='{$row['sessionid']}' and date_start='{$row['date_start']}' ");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if(!$row2){
		
		$type_pay = 'pre';
		$domainid = '';
		if($row['users_id'] == 286 || $row['users_id'] == 3630 || $row['users_id'] == 3631 || $row['users_id'] == 3632 || $row['users_id'] == 3633 || $row['users_id'] == 3634 || $row['users_id'] == 3635 || $row['users_id'] == 3636 || $row['users_id'] == 3637 || $row['users_id'] == 3638 ){
			$type_pay = 'aud';
			switch ($row['users_id']) {
				case '286':
					$domainid = '19';
					break;
				case '3630':
					$domainid = '13';
					break;
				case '3631':
					$domainid = '11';
					break;
				case '3632':
					$domainid = '11';
					break;
				case '3633':
					$domainid = '22';
					break;
				case '3634':
					$domainid = '29';
					break;
				case '3635':
					$domainid = '29';
					break;
				case '3636':
					$domainid = '29';
					break;
				case '3637':
					$domainid = '29';
					break;
				case '3638':
					$domainid = '29';
					break;
			}
		}else{
			$domainid = $row['domain_id'];
		}
		
		
		var_dump($row);
		 $mysqli->query("INSERT INTO user_credit_history (user_id, agent_id, agent_pseudo, media, phone_number, called_number, sessionid, credits, seconds, user_credits_before, user_credits_after, date_start , date_end,type_pay, domain_id ) VALUES ('".$row['users_id']."','".$row['agent_id']."','".$row['agent_pseudo']."','".$row['media']."','".$row['phone_number']."','".$row['called_number']."','".$row['sessionid']."','".$row['credits']."','".$row['seconds']."','".$row['user_credits_before']."','".$row['user_credits_after']."','".$row['date_start']."','".$row['date_end']."','".$type_pay."','".$domainid."') ");
	}
}
var_dump('end');exit;

$list_cost = array();

			$result = $mysqli->query("SELECT * from costs order by id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$list_cost[$row['id']] = $row['cost'] / 60;
			}
$mail_price = 12;
$result = $mysqli->query("SELECT C.agent_id,C.user_id, C.user_credit_history,C.media,C.sessionid,C.is_factured, C.seconds,C.credits,C.ca,C.ca_euros,C.ca_currency, C.ca_ids,C.is_mobile, C.expert_number, U.order_cat, U.mail_price, U.stripe_account, C.type_pay, C.domain_id,U2.payment_opposed,U2.parent_account_opposed from user_credit_history C, users U, users U2 WHERE C.agent_id = U.id and C.user_id = U2.id and C.user_credit_history >= 495115 and C.user_credit_history < 495151");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	var_dump($row['user_credit_history']);
	$agent_id = $row['agent_id'];
	$customer_id = $row['user_id'];
	$user_credit_history = $row['user_credit_history'];
	$seconds = $row['seconds'];
	$order_cat = $row['order_cat'];
	$mail_price = $row['mail_price'];
	$media = $row['media'];
	$is_factured = $row['is_factured'];
	$is_payment_oppose = false;
	
	
	$result2 = $mysqli->query("SELECT * from user_pay WHERE id_user_credit_history = '{$user_credit_history}'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if(!$row2){
		var_dump('ok');
		$remuneration_time = $list_cost[$order_cat];
					if($row['is_mobile']){
						$rem_surcost = 0.10 / 60;
						$result_costphone = $mysqli->query("SELECT * from cost_phones order by id");
						while($row_costphone = $result_costphone->fetch_array(MYSQLI_ASSOC)){
							if(substr($row['expert_number'],0,strlen($row_costphone['indicatif'])) == $row_costphone['indicatif'])
								$rem_surcost = $row_costphone['cost'] / 60;
						}
						
						$remuneration_time = $remuneration_time - $rem_surcost;
					}

					$price = 0;
					if($is_factured){
						switch ($media) {
							case 'phone':
								$price = $seconds * $remuneration_time;
								break;
										$price = $seconds * $remuneration_time;
								break;
							case 'email':
								$price = $mail_price;
						case 'chat':
							break;
						}
					}
		
		 //calculate CA if empty
          if($is_factured && $row['ca'] < 0.1){
            if($row['type_pay'] == 'pre'){
              $result3 = $mysqli->query("SELECT * from orders where user_id = '".$row['user_id']."'  and valid = 1 and product_price > 0 order by id desc limit 1");
              $row3 = $result3->fetch_array(MYSQLI_ASSOC);
              if($row3['product_credits']){
                $pricing = $row3['total'] / $row3['product_credits'];
                $ca = $pricing * $row['credits'];
                $ca_currency = $row3['currency'];
                $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = '{$user_credit_history}'");
                $row['ca'] = $ca;
                $row['ca_currency'] = $ca_currency;
              }
            }
			 if($row['type_pay'] == 'aud'){
					     $ca_currency = '€';
              $the_country_id = 1;
              switch ($row['domain_id']) {
                  case '19':
                    $the_country_id = 1;
                    $ca_currency = '€';
                    break;
                  case '13':
                    $the_country_id = 3;
                    $ca_currency = 'CHF';
                    break;
                  case '11':
                    $the_country_id = 4;
                    $ca_currency = '€';
                    break;
                  case '22':
                    $the_country_id = 5;
                    $ca_currency = '€';
                    break;
                  case '29':
                    $the_country_id = 13;
                    $ca_currency = '$';
                    break;
                }
				 
				 $result99 = $mysqli->query("SELECT surtaxed_minute_cost from country_lang_phone where country_id = '".$the_country_id."'  and lang_id = 1");
                $row99 = $result99->fetch_array(MYSQLI_ASSOC);
			
              $ca = 0;
              if($row99){
                $cost_second = $row99['surtaxed_minute_cost'] / 60;
                $ca = $row['seconds'] * $cost_second;
                $mysqli->query("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = '{$user_credit_history}'");
				//  var_dump("UPDATE user_credit_history set ca = '".$ca ."',ca_currency = '".$ca_currency ."' where user_credit_history = '{$user_credit_history}'");exit;
                $row['ca'] = $ca;
                $row['ca_currency'] = $ca_currency;
              }
            }
          }
		//var_dump('END');exit;
		//calculate CA pay expert
					$result_cu = $mysqli->query("SELECT * from currencies WHERE label = '{$row['ca_currency']}'");
					$row_cu = $result_cu->fetch_array(MYSQLI_ASSOC);

					if(!$row_cu['amount']) $row_cu['amount'] = 1;

					$ca_paid = $row['ca'] * $row_cu['amount'];
					$ca_old = 0;
					if($row['ca_euros'] > 0.1){
						$ca_old = $ca_paid;
						$ca_paid = $row['ca_euros'];
					}
		
						$mysqli->query("INSERT INTO user_pay(id_user_credit_history, order_cat_index, mail_price_index, date_pay, price, ca,ca_old, ca_currency,currency, tx_change) VALUES ('{$user_credit_history}','{$order_cat}','{$mail_price}',NOW(),'{$price}','{$ca_paid}','{$ca_old}','{$row['ca']}','{$row['ca_currency']}','{$row_cu['amount']}')");
		var_dump("INSERT INTO user_pay(id_user_credit_history, order_cat_index, mail_price_index, date_pay, price, ca,ca_old, ca_currency,currency, tx_change) VALUES ('{$user_credit_history}','{$order_cat}','{$mail_price}',NOW(),'{$price}','{$ca_paid}','{$ca_old}','{$row['ca']}','{$row['ca_currency']}','{$row_cu['amount']}')");
	}
}