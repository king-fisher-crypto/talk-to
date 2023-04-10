<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="export_comm_ca.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
 
// send the column headers
fputcsv($file, array('Date', 'Session ID', 'media', 'type','seconds', 'CA','CA currency','Order 1','Voucher 1','Currency 1','price seconds 1','nb seconds 1', 'Order 2','Voucher 2','Currency 2','price seconds 2','nb seconds 2','CA euros','remuneration','fees' ));


$debut_spiriteo = '2020-09-30 23:00:00';
$max_date = '2020-10-31 22:59:59';



	$resultcomm = $mysqli->query("SELECT * from  user_credit_history WHERE is_factured = '1' and ca > 0 and date_start >= '".$debut_spiriteo."' and date_start <= '".$max_date."' order by date_start");
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
    
    //get order
    $order_1 = '';
    $currency_1= '';
    $price_1 = '';
    $order_2 = '';
    $currency_2 = '';
    $price_2 = '';
    $nb_1 = '';
    $nb_2 = '';
    $voucher_1 = '';
    $voucher_2 = '';
    
    $resultpay = $mysqli->query("SELECT * from user_pay WHERE id_user_credit_history = '{$rowcomm['user_credit_history']}'");
    $rowpay = $resultpay->fetch_array(MYSQLI_ASSOC);
    
    $ca_expert = $rowpay['ca'];
    $rem_expert = $rowpay['price'];
    $fees = $rowpay['ca'] - $rowpay['price'];
    
    $cut_id = @unserialize($rowcomm['ca_ids']);
    if(is_array($cut_id)){
      $n = 1;
      foreach($cut_id as $cut){
        
        $resultprice = $mysqli->query("SELECT * from user_credit_prices WHERE id = '{$cut['id']}'");
        $rowprice = $resultprice->fetch_array(MYSQLI_ASSOC);
        $resultcredit = $mysqli->query("SELECT * from user_credits WHERE id = '{$rowprice['id_user_credit']}'");
        $rowcredit = $resultcredit->fetch_array(MYSQLI_ASSOC);
        $resultorder = $mysqli->query("SELECT * from  orders WHERE id = '{$rowcredit['order_id']}'");
        $roworder = $resultorder->fetch_array(MYSQLI_ASSOC);
       
        if($n < 2){
          $order_1 = $roworder['total'];
          $currency_1= $roworder['currency'];
          $price_1 = $rowprice['price'];
          $nb_1 = $cut['seconds'];
          $voucher_1 = $roworder['voucher_credits'];
        }
        if($n == 2){
          $order_2 = $roworder['total'];
          $currency_2= $roworder['currency'];
          $price_2 = $rowprice['price'];
          $nb_2 = $cut['seconds'];
          $voucher_2 = $roworder['voucher_credits'];
        }
        
        $n++;
        if($n> 2)
        break;
      }
      
    }else{
      $cut_id = explode('_',$rowcomm['ca_ids']);
      $n = 1;
      foreach($cut_id as $caid){
        $resultprice = $mysqli->query("SELECT * from user_credit_prices WHERE id = '{$caid}'");
        $rowprice = $resultprice->fetch_array(MYSQLI_ASSOC);
        $resultcredit = $mysqli->query("SELECT * from user_credits WHERE id = '{$rowprice['id_user_credit']}'");
        $rowcredit = $resultcredit->fetch_array(MYSQLI_ASSOC);
        $resultorder = $mysqli->query("SELECT * from  orders WHERE id = '{$rowcredit['order_id']}'");
        $roworder = $resultorder->fetch_array(MYSQLI_ASSOC);

        if($n < 2){
          $order_1 = $roworder['total'];
          $currency_1= $roworder['currency'];
          $price_1 = $rowprice['price'];
          $nb_1 = '';
          $voucher_1 = $roworder['voucher_credits'];
        }
        if($n == 2){
          $order_2 = $roworder['total'];
          $currency_2= $roworder['currency'];
          $price_2 = $rowprice['price'];
          $nb_2 = '';
          $voucher_2 = $roworder['voucher_credits'];
        }
        $n++;
        if($n> 2)
        break;
      }
    }
    
		$line = array($rowcomm['date_start'], $rowcomm['sessionid'],$rowcomm['media'],$rowcomm['type_pay'],$rowcomm['credits'],$rowcomm['ca'],$rowcomm['ca_currency'],$order_1,$voucher_1,$currency_1,$price_1,$nb_1,$order_2,$voucher_2,$currency_2,$price_2,$nb_2,$ca_expert,$rem_expert,$fees);
		fputcsv($file, $line);

	}
?>