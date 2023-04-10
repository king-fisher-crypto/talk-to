<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

//
// Database `spiriteo`
//

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");
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
$paypal_euro_old = 0;
$paypal_dollar_old = 0;
$paypal_chf_old = 0;
$stripe_euro_old = 0;
$stripe_dollar_old = 0;
$stripe_chf_old = 0;
$prepaid_unknow = 0;


$date_debut = '2020-09-30 22:00:00';
$date_fin = '2020-10-31 22:59:59';

$resultcomm = $mysqli->query("SELECT U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids from  user_credit_history U, user_pay P WHERE U.date_start >= '".$date_debut."' and U.date_start <= '".$date_fin."' and U.is_factured = 1 and U.type_pay = 'pre' and P.id_user_credit_history = U.user_credit_history order by U.user_credit_history");
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
		if($rowcomm['type_pay'] != 'aud'){
			
			if(!$rowcomm['ca_ids']){
				$prepaid_unknow += $rowcomm['ca'];
			}else{
				$tab_ids = unserialize($rowcomm['ca_ids']);
				$user_credit_id = 0;
				if(!$tab_ids){
					$prepaid_unknow += $rowcomm['ca'];	
				}else{
					$id_ids = false;
					foreach($tab_ids as $data){
						if(is_array($data) && $data['id']){
							$id_ids = true;
							$user_credit_id = $data['id'];
							if($user_credit_id){
								$resultcreditprice = $mysqli->query("SELECT * from  user_credit_prices WHERE id = '".$user_credit_id."'");
								$rowcreditprice =$resultcreditprice->fetch_array(MYSQLI_ASSOC);
								$rowcreditprice['price_chf'] = 0;
								$rowcreditprice['price_dollar'] = 0;
								$rowcreditprice['price_euro'] = 0;

								$resultcredit = $mysqli->query("SELECT * from  user_credits WHERE id = '".$rowcreditprice['id_user_credit']."'");
								$rowcredit =$resultcredit->fetch_array(MYSQLI_ASSOC);

								$resultorder = $mysqli->query("SELECT * from  orders WHERE id = '".$rowcredit['order_id']."' and date_add >= '".$date_debut."' and date_add <= '".$date_fin."'");
								$roworder =$resultorder->fetch_array(MYSQLI_ASSOC);

								$resultorderold = $mysqli->query("SELECT * from  orders WHERE id = '".$rowcredit['order_id']."' and date_add <= '".$date_debut."'");
								$roworderold =$resultorderold->fetch_array(MYSQLI_ASSOC);
								
								if($rowcreditprice['devise'] == 'CHF')$rowcreditprice['price_chf'] = $rowcreditprice['price'];
								if($rowcreditprice['devise'] == '€')$rowcreditprice['price_euro'] = $rowcreditprice['price'];
								if($rowcreditprice['devise'] == '$')$rowcreditprice['price_dollar'] = $rowcreditprice['price'];
								if($rowcreditprice['devise'] == 'CHF')$rowcreditprice['price'] = $rowcreditprice['price'] * 0.925412;
								if($rowcreditprice['devise'] == '$')$rowcreditprice['price'] = $rowcreditprice['price'] * 0.643915;

								if($roworderold){
									if($roworderold['payment_mode'] == 'paypal'){
										//$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$paypal_chf_old += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$paypal_dollar_old += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$paypal_euro_old += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworderold['payment_mode'] == 'stripe' || $roworderold['payment_mode'] == 'sepa' || $roworderold['payment_mode'] == 'bancontact'){
										//$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$stripe_chf_old += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$stripe_dollar_old += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$stripe_euro_old += $data['seconds'] * $rowcreditprice['price_euro'];
									}
								}else{
									if($roworder['payment_mode'] == 'paypal'){
										//$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$paypal_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$paypal_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$paypal_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworder['payment_mode'] == 'stripe' || $roworder['payment_mode'] == 'sepa' || $roworder['payment_mode'] == 'bancontact'){
										//$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$stripe_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$stripe_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$stripe_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									
								}
							}
						}else{
							$id_ids = true;
							$user_credit_id = $data;
							if($user_credit_id){
								$resultcreditprice = $mysqli->query("SELECT * from  user_credit_prices WHERE id = '".$user_credit_id."'");
								$rowcreditprice =$resultcreditprice->fetch_array(MYSQLI_ASSOC);
								$rowcreditprice['price_chf'] = 0;
								$rowcreditprice['price_dollar'] = 0;
								$rowcreditprice['price_euro'] = 0;

								$resultcredit = $mysqli->query("SELECT * from  user_credits WHERE id = '".$rowcreditprice['id_user_credit']."'");
								$rowcredit =$resultcredit->fetch_array(MYSQLI_ASSOC);

								$resultorder = $mysqli->query("SELECT * from  orders WHERE id = '".$rowcredit['order_id']."' and date_add >= '".$date_debut."' and date_add <= '".$date_fin."'");
								$roworder =$resultorder->fetch_array(MYSQLI_ASSOC);

								$resultorderold = $mysqli->query("SELECT * from  orders WHERE id = '".$rowcredit['order_id']."' and date_add <= '".$date_debut."'");
								$roworderold =$resultorderold->fetch_array(MYSQLI_ASSOC);
								
								if($rowcreditprice['devise'] == 'CHF')$rowcreditprice['price_chf'] = $rowcreditprice['price'];
								if($rowcreditprice['devise'] == '€')$rowcreditprice['price_euro'] = $rowcreditprice['price'];
								if($rowcreditprice['devise'] == '$')$rowcreditprice['price_dollar'] = $rowcreditprice['price'];
								if($rowcreditprice['devise'] == 'CHF')$rowcreditprice['price'] = $rowcreditprice['price'] * 0.925412;
								if($rowcreditprice['devise'] == '$')$rowcreditprice['price'] = $rowcreditprice['price'] * 0.643915;

								if($roworderold){
									if($roworderold['payment_mode'] == 'paypal'){
										//$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$paypal_chf_old += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$paypal_dollar_old += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$paypal_euro_old += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworderold['payment_mode'] == 'stripe' || $roworderold['payment_mode'] == 'sepa' || $roworderold['payment_mode'] == 'bancontact'){
										//$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$stripe_chf_old += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$stripe_dollar_old += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$stripe_euro_old += $data['seconds'] * $rowcreditprice['price_euro'];
									}
								}else{
									if($roworder['payment_mode'] == 'paypal'){
										//$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$paypal_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$paypal_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$paypal_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworder['payment_mode'] == 'stripe' || $roworder['payment_mode'] == 'sepa' || $roworder['payment_mode'] == 'bancontact'){
										//$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$stripe_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$stripe_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$stripe_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									
								}
							}
						}
					}
					if(!$id_ids)$prepaid_unknow += $rowcomm['ca'];	
				}
			}
		}
	}

var_dump('stripe_euro : '.$stripe_euro);
var_dump('stripe_euro_old : '.$stripe_euro_old);
var_dump('stripe_dollar : '.$stripe_dollar);
var_dump('stripe_dollar_old : '.$stripe_dollar_old);
var_dump('stripe_chf : '.$stripe_chf);
var_dump('stripe_chf_old : '.$stripe_chf_old);
var_dump('paypal_euro : '.$paypal_euro);
var_dump('paypal_euro_old : '.$paypal_euro_old);
var_dump('paypal_dollar : '.$paypal_dollar);
var_dump('paypal_dollar_old : '.$paypal_dollar_old);
var_dump('paypal_chf : '.$paypal_chf);
var_dump('paypal_chf_old : '.$paypal_chf_old);
var_dump($prepaid_unknow);
