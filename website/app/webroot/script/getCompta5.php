<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

$mysqli -> set_charset("utf8");

$date_debut = '2020-09-30 23:00:00';
$date_fin = '2020-10-31 22:59:59';

$fact_date_debut = '2020-11-01 00:00:00';
$fact_date_fin = '2020-11-31 22:59:59';

$prepaid = 0;
$prepaid_paypal = 0;
$prepaid_paypal_euro = 0;
$prepaid_paypal_chf = 0;
$prepaid_paypal_dollar = 0;
$prepaid_stripe = 0;
$prepaid_stripe_euro = 0;
$prepaid_stripe_chf = 0;
$prepaid_stripe_dollar = 0;
$prepaid_old = 0;
$prepaid_unknow = 0;

$resultfact = $mysqli->query("SELECT * from  invoice_agents WHERE date_add >= '".$fact_date_debut."' and date_add <= '".$fact_date_fin."' order by id");
while($rowfact = $resultfact->fetch_array(MYSQLI_ASSOC)){
	
	$resultcomm = $mysqli->query("SELECT U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids from  user_credit_history U, user_pay P WHERE U.date_start >= '".$rowfact['date_min']."' and U.date_start <= '".$rowfact['date_max']."' and U.is_factured = 1 and U.agent_id = '".$rowfact['user_id']."' and P.id_user_credit_history = U.user_credit_history order by U.user_credit_history");
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
		if($rowcomm['type_pay'] != 'aud'){
			$prepaid += $rowcomm['ca'];
			
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
										$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_paypal_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_paypal_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_paypal_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworderold['payment_mode'] == 'stripe' || $roworderold['payment_mode'] == 'sepa' || $roworderold['payment_mode'] == 'bancontact'){
										$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_stripe_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_stripe_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_stripe_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
								}else{

									if($roworder['payment_mode'] == 'paypal'){
										$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_paypal_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_paypal_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_paypal_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworder['payment_mode'] == 'stripe' || $roworder['payment_mode'] == 'sepa' || $roworder['payment_mode'] == 'bancontact'){
										$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_stripe_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_stripe_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_stripe_euro += $data['seconds'] * $rowcreditprice['price_euro'];
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
										$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_paypal_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_paypal_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_paypal_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworderold['payment_mode'] == 'stripe' || $roworderold['payment_mode'] == 'sepa' || $roworderold['payment_mode'] == 'bancontact'){
										$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_stripe_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_stripe_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_stripe_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
								}else{

									if($roworder['payment_mode'] == 'paypal'){
										$prepaid_paypal += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_paypal_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_paypal_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_paypal_euro += $data['seconds'] * $rowcreditprice['price_euro'];
									}
									if($roworder['payment_mode'] == 'stripe' || $roworder['payment_mode'] == 'sepa' || $roworder['payment_mode'] == 'bancontact'){
										$prepaid_stripe += $data['seconds'] * $rowcreditprice['price'];
										
										if($rowcomm['ca_currency'] == 'CHF' )
											$prepaid_stripe_chf += $data['seconds'] * $rowcreditprice['price_chf'];
										elseif($rowcomm['ca_currency'] == '$' )
											$prepaid_stripe_dollar += $data['seconds'] * $rowcreditprice['price_dollar'];
										else
											$prepaid_stripe_euro += $data['seconds'] * $rowcreditprice['price_euro'];
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
}
var_dump('prepaid : '.number_format($prepaid,2));
var_dump('prepaid stripe : '.number_format($prepaid_stripe,2));
var_dump('prepaid stripe euro : '.number_format($prepaid_stripe_euro,2));
var_dump('prepaid stripe chf : '.number_format($prepaid_stripe_chf,2));
var_dump('prepaid stripe dollar : '.number_format($prepaid_stripe_dollar,2));
var_dump('prepaid paypal : '.number_format($prepaid_paypal,2));
var_dump('prepaid paypal euro : '.number_format($prepaid_paypal_euro,2));
var_dump('prepaid paypal chf : '.number_format($prepaid_paypal_chf,2));
var_dump('prepaid paypal dollar : '.number_format($prepaid_paypal_dollar,2));


var_dump('prepaid unknow : '.number_format($prepaid_unknow,2));
var_dump('prepaid old : '.number_format($prepaid_old,2));

?>