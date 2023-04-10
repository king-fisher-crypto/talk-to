<?php
ini_set('display_errors', 1); 
set_time_limit ( 0 );
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

try {
	
		$account = \Stripe\Account::retrieve('acct_1ErTvdCdLeItFzjq');
	var_dump($account->settings->payouts);
		
	 }
	catch (Exception $e) {
	 var_dump($e->getMessage());
	 }



exit;

?>