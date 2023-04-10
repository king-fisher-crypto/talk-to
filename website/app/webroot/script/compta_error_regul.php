<?php

//Glassgen Comptabilité
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");

$fees_boi = 17;

$list_tva_fail = array(28380,24915,51628,59578,64426,21982,9179,13289,23313,25646,28833,31671,32046,32106,40429,42069,42423,44820,46548,47439,48465,49375,49985,54081,60934,62366,64286);
$new_tva = 23;

//echo 'diff rem;Difference entre CA et Fees Glaasgen + Rem experts;mode;num fact;date fact;societe;date debut;date fin;Old CA;Old Fees;Old TVA;Old Fees TTC;Old Paid Expert ;Old Rem Expert;CA Rectif;TVA Rectif;Fees Rectif;Rem Experts Rectif;CA Total;Fees Total HT;TVA Total;Fees Total TTC;Total Paid Experts;Total Rem Experts;Diff ca;diff fees;;CA Rectif;TVA Rectif;Fees Rectif;Rem Experts Rectif;'.'<br />';

$list_expert_ca = array();
$list_expert_fees = array();
$list_expert_fees_ttc = array();
$list_expert_tva = array();
$list_expert_rem = array();

$result = $mysqli->query("SELECT * FROM `invoice_agents` where date_add >= '2020-05-01 00:00:00'  and status != 10   order by order_id");//  and order_id = 1847 
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if(!isset($list_expert_ca[$row['user_id']]))$list_expert_ca[$row['user_id']] = 0;
	if(!isset($list_expert_fees[$row['user_id']]))$list_expert_fees[$row['user_id']] = 0;
	if(!isset($list_expert_fees_ttc[$row['user_id']]))$list_expert_fees_ttc[$row['user_id']] = 0;
	if(!isset($list_expert_rem[$row['user_id']]))$list_expert_rem[$row['user_id']] = 0;
	if(!isset($list_expert_tva[$row['user_id']]))$list_expert_tva[$row['user_id']] = 0;
	
	$date_com_min = $row['date_min'];
	$date_com_max = $row['date_max'];
	$date_create  = $row['date_add'];
	$dx = new DateTime($date_create);
	$dx->modify('-1 month');
	$date_fact = $dx->format('Y-m');
	
	$mode = $row['payment_mode'];
	if(!$mode){
		if(!$row['paid_total_valid'])
			$mode = 'bankwire';
		else
			$mode = 'stripe';
	}
	
	$old_ca 		= $row['ca'];
	$old_paid_total = $row['paid_total'];
	$old_paid 		= $row['paid'] + $row['bonus'] - $row['penality'] + $row['bonus'] + $row['sponsor'];
	$old_fees 		= $row['amount'];
	$old_tva 	    = $row['vat'];
	$old_fees_ttc 		= $row['amount_total'];
	
	$result2 = $mysqli->query("SELECT * FROM `invoice_agents2` where id = '".$row['id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$ca 			= $row2['ca'];
	$fees 			= $row2['amount'];
	$tva 	    	= $row2['vat'];
	$fees_ttc 			= $row2['amount_total'];
	$paid 			= $row2['paid'] + $row2['bonus'] - $row2['penality'] + $row2['bonus'] + $row2['sponsor'];
	$paid_total 	= $row2['paid_total'];
	
	$diff_ca = $ca-$old_ca;
	$diff_rem = $paid_total-$old_paid_total;
	$diff_fees = $fees-$old_fees;
	
	$rectif_ca 		= 0;
	$rectif_fees 	= 0;
	$rectif_tva 	= 0;
	$rectif_paid_total 	= 0;
	
	//var_dump('old_ca ->'.$old_ca);
	//var_dump('old_tva ->'.$old_tva);
	//var_dump('old_fees ->'.$old_fees);
	//var_dump('old_paid_total ->'.$old_paid_total);
	
	$rectif_ca 		= $ca - $old_ca;
	$rectif_fees 	= $fees  - $old_fees;
	$rectif_tva 	= $tva - $old_tva;
	$rectif_fees_ttc 	= $fees_ttc  - $old_fees_ttc;
	$rectif_paid_total 	= $paid_total - $old_paid_total;
	$rectif_paid 	= $paid - $old_paid;
	
	$rectif_ca2 		= 0;
	$rectif_fees2 	= 0;
	$rectif_tva2 	= 0;
	$rectif_fees_ttc2 	= 0;
	$rectif_paid_total2 	= 0;
	
	$is_tva = 0;
	if(!$tva && in_array($row['user_id'],$list_tva_fail ))$is_tva = 1;
	if($row['vat_tx'] && $row['vat_tx'] != 23)$is_tva = 1;
	
	
	
	if($is_tva || ($tva &&  $row['order_id'] <= 2699 )){//
		$vat = 0 ;
		$r_amount = number_format($rectif_ca - $rectif_paid,2,'.','');
		//if($is_tva){
			$r_vat = $rectif_fees * 23 / 100;
			$r_vat = number_format($r_vat,2,'.','');
			$r_paid_total = $rectif_paid_total;
		/*}else{
			$r_fees = $rectif_ca - $rectif_paid;
			$r_vat = $r_fees * 23 / 100;
			$r_vat = number_format($r_vat,2,'.','');
			$r_paid_total = $rectif_paid_total - $r_vat;
		}*/
		
	
		$r_amount_total = number_format($rectif_ca - $r_paid_total + $r_vat,2,'.','');
		$rectif_tva2 	= $r_vat;// - $old_tva;
		$rectif_ca2 		= $rectif_ca;// - $old_ca;
		$rectif_fees2 	=  $rectif_fees;//   $r_amount;// - $old_fees;
		$rectif_paid_total2 	= $r_paid_total;// - $old_paid_total;
		$rectif_fees_ttc2 	=  $rectif_fees_ttc;
		
		/*$rectif_ca 		= $ca - $old_ca;
		$rectif_fees 	= $fees  - $old_fees;
		$rectif_tva 	= $tva - $old_tva;
		$rectif_paid_total 	= $paid_total - $old_paid_total;*/
	}else{
		$rectif_tva2 	= $rectif_tva;
		$rectif_ca2 		= $rectif_ca;
		$rectif_fees2 	= $rectif_fees;
		$rectif_fees_ttc2 	= $rectif_fees_ttc;
		$rectif_paid_total2 	= $rectif_paid_total;
	}
	
	//var_dump($rectif_ca);
	//var_dump($rectif_tva);
	//var_dump($rectif_fees);
	//var_dump($rectif_paid_total);
	
	
	/*echo ''.number_format($diff_rem,2,',','').';;'.$mode.';'.$row['order_id'].';'.$date_fact.';'.$row['society_name'].';'.$date_com_min.';'.$date_com_max.';'.number_format($old_ca,2,',','') .';'.number_format($old_fees,2,',','').';'.number_format($old_tva,2,',','') .';'.number_format($old_fees_ttc,2,',','').';'.number_format($old_paid,2,',','').';'.number_format($old_paid_total,2,',','') .';'.number_format($rectif_ca,2,',','').';'.number_format($rectif_tva,2,',','').';'.number_format($rectif_fees,2,',','').';'.number_format($rectif_paid_total,2,',','').';'.number_format($ca,2,',','') .';'.number_format($fees,2,',','').';'.number_format($tva,2,',','') .';'.number_format($fees_ttc,2,',','').';'.number_format($paid,2,',','').';'.number_format($paid_total,2,',','').';'.number_format($diff_ca,2,',','').';'.number_format($diff_fees,2,',','').';'.';'.number_format($rectif_ca2,2,',','').';'.number_format($rectif_tva2,2,',','').';'.number_format($rectif_fees2,2,',','').';'.number_format($rectif_paid_total2,2,',','').'<br />';*/

	if($rectif_ca2)
	$list_expert_ca[$row['user_id']] += $rectif_ca2;
	if($rectif_fees2)
	$list_expert_fees[$row['user_id']] += $rectif_fees2;
	if($rectif_fees_ttc2)
	$list_expert_fees_ttc[$row['user_id']] += $rectif_fees_ttc2;
	if($rectif_tva2)
	$list_expert_tva[$row['user_id']] += $rectif_tva2;
	if($rectif_paid_total2)
	$list_expert_rem[$row['user_id']] += $rectif_paid_total2;

}

echo 'ID expert;societe;ca;fees;tva;fees_ttc;rem'.'<br />';

foreach($list_expert_ca as $user_id => $ca){
	
	$fees = $list_expert_fees[$user_id];
	$fees_ttc = $list_expert_fees_ttc[$user_id];
	$tva = $list_expert_tva[$user_id];
	$rem = $list_expert_rem[$user_id];
	
	
	$result2 = $mysqli->query("SELECT * FROM `invoice_agents2` where user_id = '".$user_id."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	echo $user_id.';'.$row2['society_name'].';'.number_format($ca,2,',','').';'.number_format($fees,2,',','').';'.number_format($tva,2,',','').';'.number_format($fees_ttc,2,',','').';'.number_format($rem,2,',','').'<br />';
	
}

?>