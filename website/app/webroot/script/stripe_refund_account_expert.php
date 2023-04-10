<?php
//exit;
ini_set('display_errors', 1); 
set_time_limit ( 0 );
error_reporting(E_ALL); 
require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_test_JFhyexc86xNJjf5rCxnGm7ks00Id6GSvbw');

$pp = number_format(40,2,'.','') * 100;
try {
	
		$account = \Stripe\Account::retrieve();
		$ret = \Stripe\Transfer::create(
		  [
			"amount" => $pp,
			"currency" => "eur",
			"destination" => $account->id
		  ],
		  ["stripe_account" => "acct_1J5WCwLtl8JQt6sv"]
		);
	var_dump($ret);
	 }
	catch (Exception $e) {
	 var_dump($e->getMessage());
	 }


exit;

?>