<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
mysqli_set_charset( $mysqli, 'utf8' );
/*header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="export_offer_ca.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
 
// send the column headers
fputcsv($file, array('Date', 'Session ID', 'Agent_ID', 'CA', 'Currency' ));
*/

$table = array();

$min_date = '2019-05-31 22:00:00';
$max_date = '2020-03-31 22:00:00';

$tx_chf = 0.946185;
$tx_dollar = 0.66074;

$credit_unsed = 0;
$one_comm = 0;
$multi_comm = 0;

$resultorder = $mysqli->query("SELECT * FROM `orders` where `date_add` >= '".$min_date."' and `date_add` <= '".$max_date."' and valid = 1 and voucher_credits > 0");
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
  }
  if(!$rowcomm){
      $resultcomm = $mysqli->query("SELECT * FROM `user_credit_history` where `user_id` = '".$roworder['user_id']."' and date_start >= '".$roworder['date_add']."' limit 1");
      $rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC); 
  }
  
  $second_price = $roworder['total'] / $roworder['product_credits'];
  $ca = $roworder['voucher_credits'] * $second_price;
  
  if(!$rowcomm)$credit_unsed += $roworder['voucher_credits'];
   if($rowcomm['credits'] <  ($roworder['voucher_credits'] + $roworder['product_credits'])){
    $multi_comm ++;
    $ca = $rowcomm['credits'] * $second_price;
     $ca_expert = 0;
     //var_dump($rowcomm['date_start'].' expert:'.$rowcomm['agent_id'].' -> CA : '.$ca . ' '.$roworder['currency']);
   /* $line = array($rowcomm['date_start'], $rowcomm['sessionid'],$rowcomm['agent_id'],$ca,utf8_decode($roworder['currency']));
		fputcsv($file, $line);*/
    
    if($roworder['currency'] == '$')$ca = $ca * $tx_dollar;
    if($roworder['currency'] == 'CHF')$ca = $ca * $tx_chf;
    $dx = new DateTime($rowcomm['date_start']);
		$date_ex = $dx->format('Ym');
    if(!$table[$date_ex])$table[$date_ex] = array();
    if(!$table[$date_ex][$rowcomm['agent_id']])$table[$date_ex][$rowcomm['agent_id']] = 0;
     
    $resultpay = $mysqli->query("SELECT * FROM `user_pay` where `id_user_credit_history` = '".$rowcomm['user_credit_history']."'");
    $rowpay = $resultpay->fetch_array(MYSQLI_ASSOC); 
     if($rowpay){
       $resultcat = $mysqli->query("SELECT * FROM `costs` where `id` = '".$rowpay['order_cat_index']."'");
       $rowcat = $resultcat->fetch_array(MYSQLI_ASSOC); 
       $rem_expert = $rowcat['cost'] / 60;
       $ca_expert = $roworder['voucher_credits'] * $rem_expert;
     }
    if($ca_expert)
    $table[$date_ex][$rowcomm['agent_id']] += $ca - $ca_expert;
    
    $resultcomm2 = $mysqli->query("SELECT * FROM `user_credit_history` where `user_id` = '".$roworder['user_id']."' and date_start >= '".$roworder['date_add']."' and user_credit_history > '".$rowcomm['user_credit_history']."' limit 1");
      $rowcomm2 = $resultcomm2->fetch_array(MYSQLI_ASSOC); 
$ca_expert = 0;
     $ca = ($roworder['voucher_credits'] - $rowcomm['credits']) * $second_price;
    // var_dump($rowcomm2['date_start'].' expert:'.$rowcomm2['agent_id'].' -> CA : '.$ca . ' '.$roworder['currency']);
   /* $line = array($rowcomm2['date_start'], $rowcomm2['sessionid'],$rowcomm2['agent_id'],$ca,utf8_decode($roworder['currency']));
		fputcsv($file, $line);*/
    
    if($roworder['currency'] == '$')$ca = $ca * $tx_dollar;
    if($roworder['currency'] == 'CHF')$ca = $ca * $tx_chf;
    $dx = new DateTime($rowcomm2['date_start']);
		$date_ex = $dx->format('Ym');
    if(!$table[$date_ex])$table[$date_ex] = array();
    if(!$table[$date_ex][$rowcomm2['agent_id']])$table[$date_ex][$rowcomm2['agent_id']] = 0;
     
     $resultpay = $mysqli->query("SELECT * FROM `user_pay` where `id_user_credit_history` = '".$rowcomm2['user_credit_history']."'");
    $rowpay = $resultpay->fetch_array(MYSQLI_ASSOC); 
     if($rowpay){
       $resultcat = $mysqli->query("SELECT * FROM `costs` where `id` = '".$rowpay['order_cat_index']."'");
       $rowcat = $resultcat->fetch_array(MYSQLI_ASSOC); 
       $rem_expert = $rowcat['cost'] / 60;
       $ca_expert = ($roworder['voucher_credits'] - $rowcomm['credits']) * $rem_expert;
     }
    if($ca_expert)
    $table[$date_ex][$rowcomm['agent_id']] += $ca - $ca_expert;
    
    
    
  }else{
      $one_comm ++;
    //var_dump($rowcomm['date_start'].' expert:'.$rowcomm['agent_id'].' -> CA : '.$ca . ' '.$roworder['currency']);
   /* $line = array($rowcomm['date_start'], $rowcomm['sessionid'],$rowcomm['agent_id'],$ca,utf8_decode($roworder['currency']));
		fputcsv($file, $line);*/
    $ca_expert = 0;
    if($roworder['currency'] == '$')$ca = $ca * $tx_dollar;
    if($roworder['currency'] == 'CHF')$ca = $ca * $tx_chf;
    $dx = new DateTime($rowcomm['date_start']);
		$date_ex = $dx->format('Ym');
    if(!$table[$date_ex])$table[$date_ex] = array();
    if(!$table[$date_ex][$rowcomm['agent_id']])$table[$date_ex][$rowcomm['agent_id']] = 0;
    
    $resultpay = $mysqli->query("SELECT * FROM `user_pay` where `id_user_credit_history` = '".$rowcomm['user_credit_history']."'");
    $rowpay = $resultpay->fetch_array(MYSQLI_ASSOC); 
     if($rowpay){
       $resultcat = $mysqli->query("SELECT * FROM `costs` where `id` = '".$rowpay['order_cat_index']."'");
       $rowcat = $resultcat->fetch_array(MYSQLI_ASSOC); 
       $rem_expert = $rowcat['cost'] / 60;
       $ca_expert = $roworder['voucher_credits'] * $rem_expert;
     }
    if($ca_expert)
    $table[$date_ex][$rowcomm['agent_id']] += $ca - $ca_expert;
   }
  if($rowcomm['credits'] ==  ($roworder['voucher_credits'] + $roworder['product_credits']) && $rowcomm['media'] != 'email'){
  /*var_dump($rowcomm);
  var_dump($roworder);
    var_dump($rowpay);
    var_dump($ca);
    var_dump($ca_expert);
  exit;*/
  }
}

//meme combat loyalties
$table = array();
$resultorder = $mysqli->query("SELECT * FROM `loyalty_credits` where valid = 1 and `date_add` >= '".$min_date."' and `date_add` <= '".$max_date."'");
//var_dump("SELECT * FROM `loyalty_credits` where valid = 1 and `date_add` >= '".$min_date."' and `date_add` <= '".$max_date."'");exit;
while($roworder = $resultorder->fetch_array(MYSQLI_ASSOC)){
  $resultord = $mysqli->query("SELECT * FROM `orders` where `user_id` = '".$roworder['user_id']."' and date_add <= '".$roworder['date_add']."' and valid = 1 limit 1");
  $roword = $resultord->fetch_array(MYSQLI_ASSOC); 
  
  $resultcomm = $mysqli->query("SELECT * FROM `user_credit_history` where `user_id` = '".$roworder['user_id']."' and date_start >= '".$roworder['date_add']."' limit 1");
  $rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC); 
  
  $second_price = $roword['total'] / 600;
  $ca = 600 * $second_price;
  

  // $line = array($rowcomm['date_start'], $rowcomm['sessionid'],$rowcomm['agent_id'],$ca,utf8_decode($roword['currency']));
	//	fputcsv($file, $line);
  if($rowcomm ){
    $ca_expert = 0;
  if($roword['currency'] == '$')$ca = $ca * $tx_dollar;
    if($roword['currency'] == 'CHF')$ca = $ca * $tx_chf;
    $dx = new DateTime($rowcomm['date_start']);
		$date_ex = $dx->format('Ym');
    if(!$table[$date_ex])$table[$date_ex] = array();
    if(!$table[$date_ex][$rowcomm['agent_id']])$table[$date_ex][$rowcomm['agent_id']] = 0;
    
    $resultpay = $mysqli->query("SELECT * FROM `user_pay` where `id_user_credit_history` = '".$rowcomm['user_credit_history']."'");
    $rowpay = $resultpay->fetch_array(MYSQLI_ASSOC); 
     if($rowpay){
       $resultcat = $mysqli->query("SELECT * FROM `costs` where `id` = '".$rowpay['order_cat_index']."'");
       $rowcat = $resultcat->fetch_array(MYSQLI_ASSOC); 
       $rem_expert = $rowcat['cost'] / 60;
       $ca_expert = 600 * $rem_expert;
     }
    if($ca_expert)
    $table[$date_ex][$rowcomm['agent_id']] += $ca - $ca_expert;
    
   // if($rowcomm['agent_id'] == 16266)var_dump($rowcomm);
  //if($rowcomm['agent_id'] == 16266)var_dump($roworder);
  }
}
  
/*var_dump('Unsed = '.$credit_unsed);
var_dump('one_comm = '.$one_comm);
var_dump('multi_comm = '.$multi_comm);
echo 'end';*/
ksort($table);
//var_dump($table);

$total_ca = 0;
$date_voucher = '2020-03-30 22:00:00';
foreach($table as $key => $tab){
 
  if($key >= 201906 && $key <= 202003){
    
    $fact_month = '';
    if($key == 201906) $fact_month = '2019-07-%';
    if($key == 201907) $fact_month = '2019-08-%';
    if($key == 201908) $fact_month = '2019-09-%';
    if($key == 201909) $fact_month = '2019-10-%';
    if($key == 201910) $fact_month = '2019-11-%';
    if($key == 201911) $fact_month = '2019-12-%';
    if($key == 201912) $fact_month = '2020-01-%';
    if($key == 202001) $fact_month = '2020-02-%';
    if($key == 202002) $fact_month = '2020-03-%';
    if($key == 202003) $fact_month = '2020-04-%';
   
    foreach($tab as $idagent => $ca){
       $resultfact = $mysqli->query("SELECT * FROM `invoice_agents` where `user_id` = '".$idagent."' and date_add like '".$fact_month."' and status = 1");
       $rowfact = $resultfact->fetch_array(MYSQLI_ASSOC); 
      if($rowfact){
        $total_ca += number_format($ca,2,'.','');
       // $mysqli->query("INSERT INTO `invoice_voucher_agents` (user_id,invoice_id,amount,date_add,status) VALUES ('".$idagent."','".$rowfact['id']."','".number_format($ca,2,'.','')."','".$date_voucher."','1')");
        var_dump("INSERT INTO `invoice_voucher_agents` (user_id,invoice_id,amount,date_add,status) VALUES ('".$idagent."','".$rowfact['id']."','".number_format($ca,2,'.','')."','".$date_voucher."','1')");
      }
    }
    
  }
  
}
var_dump($total_ca);
/*ordonner le tableau par MYSQLI_OPT_SSL_VERIFY_SERVER_CERT
  
  look si mois suivant y a une facture expert status 1
  
  enregistrer avoir 
  $data['InvoiceVoucherAgent']['user_id']= $invoice['InvoiceAgent']['user_id'];
					$data['InvoiceVoucherAgent']['invoice_id']= $invoice['InvoiceAgent']['id'];
					$data['InvoiceVoucherAgent']['amount']= $invoice['InvoiceAgent']['amount_total'];
					$data['InvoiceVoucherAgent']['date_add']= date('Y-m-d H:i:s');
					$data['InvoiceVoucherAgent']['status']= 1;*/

?>