<?php
exit;
ini_set('display_errors', 1); 
set_time_limit ( 0 );
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");



try {
							$charge = \Stripe\Charge::create([
									  'amount' => '4675',
									  'currency' => 'eur',
									  'source' => 'src_1GdzcjLTMHGldfQIIb50JhmM',
									]);
	
							var_dump($charge);	
							
							} catch (\Stripe\Error\Base $e) {
var_dump($e);
							}

exit;

?>