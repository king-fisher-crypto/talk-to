<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

//Spiriteo Export Payouts
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

	
	try {
	
		$account = \Stripe\Account::retrieve('acct_1Fm3DkIUCh1ZjfcD');
		var_dump($account);
		//var_dump(implode(' ,',$account->requirements->currently_due));
		
	 }
	catch (Exception $e) {
	 var_dump($e->getMessage());
	 }
	
	
exit;

?>