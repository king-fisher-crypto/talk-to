<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');


$result = $mysqli->query("SELECT * FROM invoice_agents where date_add >= '2020-07-01 01:00:18' ORDER BY id ASC");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
  $result2 = $mysqli->query("SELECT * FROM invoice_agents_bk where date_add >= '2020-07-01 01:00:18'and user_id = ".$row['user_id']);
  $row2 = $result2->fetch_array(MYSQLI_ASSOC);
  
  if($row['paid_total'] != $row2['paid_total']){
    
    $delta = $row['paid_total'] - $row2['paid_total'];
    $result3 = $mysqli->query("SELECT * FROM users where id = ".$row['user_id']);
    $row3 = $result3->fetch_array(MYSQLI_ASSOC);
    $stripe = $row3['stripe_account'];
    
     $is_available_stripe = true;
      $countrie_stripe = array(1,2,3,4,60,145);
      if($row3['societe_pays'] && !in_array($row3['societe_pays'],$countrie_stripe))$is_available_stripe = false;
			if(!$row3['societe_pays'] && !in_array($row3['country_id'],$countrie_stripe))$is_available_stripe = false;
    
    if($is_available_stripe ){
    var_dump($row3['pseudo'].' - '.$row3['id']. ' ' . $stripe. ' -> '.$delta);
      $pp = number_format($delta,2,'.','') * 100;
try {
										
											$transfer = \Stripe\Transfer::create([
											  "amount" => $pp,
											  "currency" => "eur",
											  "destination" => $stripe,
											]);
																			}
								   catch (Exception $e) {
									 var_dump($e->getMessage());
									
									} 
    }
  }
  
}
exit;
?>