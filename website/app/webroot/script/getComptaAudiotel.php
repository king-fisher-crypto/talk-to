<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
mysqli_set_charset( $mysqli, 'utf8' );


$debut_spiriteo = '2019-05-31 22:00:00';
$max_date = '2020-03-31 21:59:59';

$tx_chf = 0.93;
$tx_dollar = 0.64;

$ca_total = 0;
$list_ca_total = array();

	$resultcomm = $mysqli->query("SELECT * from  user_credit_history WHERE is_factured = '1' and type_pay = 'aud' and ca > 0 and date_start >= '".$debut_spiriteo."' and date_start <= '".$max_date."' order by date_start");
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
    
      
    $resultpay = $mysqli->query("SELECT * from user_pay WHERE id_user_credit_history = '{$rowcomm['user_credit_history']}'");
    $rowpay = $resultpay->fetch_array(MYSQLI_ASSOC);
    
    //if($rowpay['ca'] > 0){
      $ca_paid = number_format($rowpay['ca'],2,'.','');
   // }else{
       if($rowcomm['ca_currency'] == '$')$ca_total = number_format($rowcomm['ca'] * $tx_dollar,2,'.','');
      if($rowcomm['ca_currency'] == 'CHF')$ca_total = number_format($rowcomm['ca'] * $tx_chf,2,'.','');
      if($rowcomm['ca_currency'] == '€' || !$rowcomm['ca_currency'] )$ca_total = number_format($rowcomm['ca'],2,'.','');
      
   // }
   // $rem_total += number_format($rowpay['price'],2,'.','');
    
    if(!$ca_total[$rowcomm['agent_id']])$ca_total[$rowcomm['agent_id']] = 0;
    $diff = $ca_total - $ca_paid;
    if($diff < 0) $diff = $diff * -1;
    $list_ca_total[$rowcomm['agent_id']] += $diff/2;
	}

var_dump($list_ca_total);
$total = 0;
$n = 7000;
foreach($list_ca_total as $user_id => $credit){
  if($credit > 99 ){
    if($n > 0)$credit = $credit + 100;
    $total += $credit;
    $mysqli->query("INSERT INTO invoice_voucher_agents(user_id,invoice_id,amount,date_add,status) VALUES ('".$user_id."','-1','".$credit."','2020-07-01 00:00:00','1')");
    $n -=100;
  }
}
var_dump($total);

?>