<?php
exit;
ini_set('display_errors', 1); 
set_time_limit ( 0 );
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

$result = $mysqli->query("SELECT * from invoice_agents where date_add >= '2020-06-01 00:00:00' and user_id = 47367 order by id");

while($row = $result->fetch_array(MYSQLI_ASSOC)){
				
	$result2 = $mysqli->query("SELECT * from users where id = '{$row['user_id']}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if($row2['stripe_account']){
	
	
				try {

					$account = \Stripe\Account::retrieve();
					$stripe_balance = 0;
					$balance = \Stripe\Balance::retrieve(
						  ["stripe_account" => $row2['stripe_account']]
						);
				if($balance->available && is_array($balance->available)){
					$available = $balance->available[0];
					$stripe_balance += $available->amount /100;
				}
				if($balance->pending && is_array($balance->pending)){
					$available = $balance->pending[0];
					$stripe_balance += $available->amount /100;
				}
					
					$amount = ($stripe_balance - $row['paid_total']) * 100;
					
					\Stripe\Transfer::create(
					  [
						"amount" => $amount,
						"currency" => "eur",
						"destination" => $account->id
					  ],
					  ["stripe_account" => $row2['stripe_account']]
					);

				 }
				catch (Exception $e) {
				 var_dump($e->getMessage());
				 }
				}
}

exit;

?>