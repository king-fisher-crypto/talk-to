<?php
//exit;
ini_set('display_errors', 1); 
set_time_limit ( 0 );
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_test_JFhyexc86xNJjf5rCxnGm7ks00Id6GSvbw');

//$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
//$mysqli->set_charset("utf8");

/*

try {
								$payout = \Stripe\Payout::retrieve('po_1FEurjDVjtzrn9Re3PHCwqtU',
									  ["stripe_account" => 'acct_1ErU4fDVjtzrn9Re']
									);
	var_dump($payout);
								
							
							} catch (\Stripe\Error\Base $e) {
var_dump($e);
							}

exit;*/

//single pay
$pp = number_format(50,2,'.','') * 100;
try {
										
											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "destination" => 'acct_1J5WCwLtl8JQt6sv',
											]);
																			}
								   catch (Exception $e) {
									 var_dump($e->getMessage());
									
									}


exit;



$html_email = '';
//creer compte associé
$result2 = $mysqli->query("SELECT * from user_pay WHERE date_pay >='2019-07-02 01:00:47' and id_user_pay >= 212401  order by id_user_pay");
while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
	
	$result = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.media,C.is_factured, C.seconds, C.ca_ids, U.order_cat, U.mail_price, U.stripe_account from user_credit_history C, users U WHERE C.agent_id = U.id and C.user_credit_history = '{$row2['id_user_credit_history']}'");
	
	$row = $result->fetch_array(MYSQLI_ASSOC);
	$price = $row2['price'];
	var_dump('====> '.$row2['id_user_pay']. ' stripe : '.$row['stripe_account']);
	if($row['stripe_account']){
							
							//recup infos charge client
							$source_transaction = '';
							$transfer_group = '';
							
							if($row['ca_ids']){
								$list_cas = explode('_',$row['ca_ids']);
								$last_charge = "";
								foreach($list_cas as $ca){
									$last_charge = $ca;
								}
								
								if($last_charge){
									
									$result3 = $mysqli->query("SELECT id_user_credit from user_credit_prices WHERE id = '{$last_charge}'");
									$row3 = $result3->fetch_array(MYSQLI_ASSOC);
									
									$result4 = $mysqli->query("SELECT order_id from user_credits WHERE id = '{$row3['id_user_credit']}'");
									$row4 = $result4->fetch_array(MYSQLI_ASSOC);
									
									$result5 = $mysqli->query("SELECT payment_mode from orders WHERE id = '{$row4['order_id']}' and date_add > '2019-07-01 12:00:00' ");
									$row5 = $result5->fetch_array(MYSQLI_ASSOC);
									
									/*if($row4["payment_mode"] == 'sepa'){
										$result5 = $mysqli->query("SELECT id, cart_id from order_sepatransactions WHERE order_id = '{$row3['order_id']}'");
										$row5 = $result5->fetch_array(MYSQLI_ASSOC);
										
									}*/
									
									if($row5["payment_mode"] == 'stripe'){
										$result6 = $mysqli->query("SELECT id, cart_id from order_stripetransactions WHERE order_id = '{$row4['order_id']}'");
										$row6 = $result6->fetch_array(MYSQLI_ASSOC);
										if(substr_count($row6['id'],'ch_' )){
											$source_transaction = $row6['id'];
											$transfer_group = $row6['cart_id'];
										}else{
											if(substr_count($row6['id'],'pi_' )){
												$paymentIntent =  \Stripe\PaymentIntent::retrieve($row6['id']);
	
												$charges = $paymentIntent->charges->data;
												$charge = $charges[0];
												$source_transaction = $charge->id;
												$transfer_group = $row6['cart_id'];
											}
										}
									}
								}
								
							}
							$pp = number_format($price,2,'.','') * 100;
		
							//patch 
							$is_done = 0;
							if( $row['stripe_account'] == 'acct_1ErTLRL45nLRW3g3' && $pp == 370) $is_done = 1;
		
							if(!$is_done){
								//if($source_transaction && $transfer_group){

									/*	$html_email .= '$transfer = \Stripe\Transfer::create([
										  "amount" => '.$pp.',
										  "currency" => "eur",
										  "source_transaction" => '.$source_transaction.',
										  "destination" => '.$row['stripe_account'].',
										  "transfer_group" => '.$transfer_group.',
										]);	

										';
									}else{
										$html_email .= '$transfer = \Stripe\Transfer::create([
										  "amount" => '.$pp.',
										  "currency" => "eur",
										  "destination" =>'.$row['stripe_account'].',
										]);

										';
									}*/
									try {
										if($source_transaction && $transfer_group){
											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "source_transaction" => $source_transaction,
											  "destination" => $row['stripe_account'],
											  "transfer_group" => $transfer_group,
											]);	
										}else{
											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "destination" => $row['stripe_account'],
											]);
										}
									}
								   catch (Exception $e) {
									 var_dump($e->getMessage());
									/*$datasEmail = array(
												'content' => $e->getMessage(). ' UserCreditHistory =>'.$user_credit_history,
												'PARAM_URLSITE' => 'https://fr.spiriteo.com' 
									);
									$extractrl->sendEmail('system@web-sigle.fr','DEBUG transfert stripe','default',$datasEmail);*/
									}
								//}
	
							}
	}
}
echo $html_email;
exit;

?>