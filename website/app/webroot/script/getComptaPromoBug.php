<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
mysqli_set_charset( $mysqli, 'utf8' );


$table = array();

$min_date = '2020-06-30 22:00:00';
$max_date = '2020-07-27 21:59:59';

$tx_chf = 0.93;
$tx_dollar = 0.63;

$credit_unsed = 0;
$one_comm = 0;
$multi_comm = 0;

/*$resultorder = $mysqli->query("SELECT * FROM `orders` where `date_add` >= '".$min_date."' and `date_add` <= '".$max_date."' and valid = 1 and voucher_credits > 0");
while($roworder = $resultorder->fetch_array(MYSQLI_ASSOC)){
  $resultcredit = $mysqli->query("SELECT * FROM `user_credits` where `order_id` = '".$roworder['id']."'");
  $rowcredit = $resultcredit->fetch_array(MYSQLI_ASSOC);
  
  if(!$rowcredit)var_dump($roworder);
    
  $resultprice = $mysqli->query("SELECT * FROM `user_credit_prices` where `id_user_credit` = '".$rowcredit['id']."'");
  $rowprice = $resultprice->fetch_array(MYSQLI_ASSOC);
  
  $rowcomm = '';
  
  if($rowprice){
    $resultcomm = $mysqli->query("SELECT * FROM `user_credit_history` where `ca_ids` = '".$rowprice['id']."' and `user_id` = '".$roworder['user_id']."'");
    $rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC); 
    if(!$rowcomm){
      $resultcomm = $mysqli->query("SELECT * FROM `user_credit_history` where `ca_ids` like '%".$rowprice['id']."%' and `user_id` = '".$roworder['user_id']."'");
      $rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC); 
    }
    
    if(!$rowcomm)var_dump($roworder);
    
    if($rowcomm){
     
      $second_price = $roworder['total'] / $roworder['product_credits'];
      $ca = number_format($rowcomm['credits'] * $second_price,2);
      
      $second_price2 = $roworder['total'] / ($roworder['product_credits'] + $roworder['voucher_credits']);
      $ca2 = number_format($rowcomm['credits'] * $second_price2,2);
      
     // var_dump($ca2 . ' -> '.$ca);
      
      $mysqli->query("update `user_credit_history` set `ca`= '".$ca2."' where user_credit_history = '".$rowcomm['user_credit_history']."'");
      
      if($rowcomm['ca_currency'] == 'CHF')      
        $mysqli->query("update `export_coms` set `ca_chf`= '".$ca2."' where user_credit_history_id = '".$rowcomm['user_credit_history']."'");
     if($rowcomm['ca_currency'] == '$')      
        $mysqli->query("update `export_coms` set `ca_dollar`= '".$ca2."' where user_credit_history_id = '".$rowcomm['user_credit_history']."'");
      if($rowcomm['ca_currency'] == '€' || !$rowcomm['ca_currency'])      
        $mysqli->query("update `export_coms` set `ca_euro`= '".$ca2."' where user_credit_history_id = '".$rowcomm['user_credit_history']."'");
      
      //var_dump("update `user_credit_history` set `ca`= '".$ca2."' where user_credit_history = '".$rowcomm['user_credit_history']."'");exit;
    }
    
  }
 
}*/
/*
$resultorder = $mysqli->query("SELECT * FROM `loyalty_credits` where valid = 1 and `date_add` >= '".$min_date."' and `date_add` <= '".$max_date."'");
//var_dump("SELECT * FROM `loyalty_credits` where valid = 1 and `date_add` >= '".$min_date."' and `date_add` <= '".$max_date."'");exit;
while($roworder = $resultorder->fetch_array(MYSQLI_ASSOC)){
  $resultord = $mysqli->query("SELECT * FROM `orders` where `user_id` = '".$roworder['user_id']."' and date_add <= '".$roworder['date_add']."' and valid = 1 limit 1");
  $roword = $resultord->fetch_array(MYSQLI_ASSOC); 
  
  $resultcomm = $mysqli->query("SELECT * FROM `user_credit_history` where `user_id` = '".$roworder['user_id']."' and date_start >= '".$roworder['date_add']."' limit 1");
  $rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC); 
  
  
  if($rowcomm ){
   $second_price = $roword['total'] / ($roword['product_credits'] + 600);
   $ca2 = $rowcomm['credits'] * $second_price;
    
    $mysqli->query("update `user_credit_history` set `ca`= '".$ca2."' where user_credit_history = '".$rowcomm['user_credit_history']."'");
      
      if($rowcomm['ca_currency'] == 'CHF')      
        $mysqli->query("update `export_coms` set `ca_chf`= '".$ca2."' where user_credit_history_id = '".$rowcomm['user_credit_history']."'");
     if($rowcomm['ca_currency'] == '$')      
        $mysqli->query("update `export_coms` set `ca_dollar`= '".$ca2."' where user_credit_history_id = '".$rowcomm['user_credit_history']."'");
      if($rowcomm['ca_currency'] == '€' || !$rowcomm['ca_currency'])      
        $mysqli->query("update `export_coms` set `ca_euro`= '".$ca2."' where user_credit_history_id = '".$rowcomm['user_credit_history']."'");
  }
}*/

?>