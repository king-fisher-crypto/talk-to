<?php

//Glassgen Comptabilité
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");

$utc_dec = array(
	'0101' => 1,'0102' => 1,'0103' => 1,'0104' => 1,'0105' => 1,'0106' => 1,'0107' => 1,'0108' => 1,'0109' => 1,'0110' => 1,'0111' => 1,'0112' => 1,'0113' => 1,'0114' => 1,'0115' => 1,'0116' => 1,'0117' => 1,'0118' => 1,'0119' => 1,'0120' => 1,'0121' => 1,'0122' => 1,'0123' => 1,'0124' => 1,'0125' => 1,'0126' => 1,'0127' => 1,'0128' => 1,'0129' => 1,'0130' => 1,'0131' => 1,'0201' => 1,'0202' => 1,'0203' => 1,'0204' => 1,'0205' => 1,'0206' => 1,'0207' => 1,'0208' => 1,'0209' => 1,'0210' => 1,'0211' => 1,'0212' => 1,'0213' => 1,'0214' => 1,'0215' => 1,'0216' => 1,'0217' => 1,'0218' => 1,'0219' => 1,'0220' => 1,'0221' => 1,'0222' => 1,'0223' => 1,'0224' => 1,'0225' => 1,'0226' => 1,'0227' => 1,'0228' => 1,'0229' => 1,'0301' => 1,'0302' => 1,'0303' => 1,'0304' => 1,'0305' => 1,'0306' => 1,'0307' => 1,'0308' => 1,'0309' => 1,'0310' => 1,'0311' => 1,'0312' => 1,'0313' => 1,'0314' => 1,'0315' => 1,'0316' => 1,'0317' => 1,'0318' => 1,'0319' => 1,'0320' => 1,'0321' => 1,'0322' => 1,'0323' => 1,'0324' => 1,'0325' => 1,'0326' => 1,'0327' => 1,'0328' => 1,'0329' => 2,'0330' => 2,'0331' => 2,'0401' => 2,'0402' => 2,'0403' => 2,'0404' => 2,'0405' => 2,'0406' => 2,'0407' => 2,'0408' => 2,'0409' => 2,'0410' => 2,'0411' => 2,'0412' => 2,'0413' => 2,'0414' => 2,'0415' => 2,'0416' => 2,'0417' => 2,'0418' => 2,'0419' => 2,'0420' => 2,'0421' => 2,'0422' => 2,'0423' => 2,'0424' => 2,'0425' => 2,'0426' => 2,'0427' => 2,'0428' => 2,'0429' => 2,'0430' => 2,'0501' => 2,'0502' => 2,'0503' => 2,'0504' => 2,'0505' => 2,'0506' => 2,'0507' => 2,'0508' => 2,'0509' => 2,'0510' => 2,'0511' => 2,'0512' => 2,'0513' => 2,'0514' => 2,'0515' => 2,'0516' => 2,'0517' => 2,'0518' => 2,'0519' => 2,'0520' => 2,'0521' => 2,'0522' => 2,'0523' => 2,'0524' => 2,'0525' => 2,'0526' => 2,'0527' => 2,'0528' => 2,'0529' => 2,'0530' => 2,'0531' => 2,'0601' => 2,'0602' => 2,'0603' => 2,'0604' => 2,'0605' => 2,'0606' => 2,'0607' => 2,'0608' => 2,'0609' => 2,'0610' => 2,'0611' => 2,'0612' => 2,'0613' => 2,'0614' => 2,'0615' => 2,'0616' => 2,'0617' => 2,'0618' => 2,'0619' => 2,'0620' => 2,'0621' => 2,'0622' => 2,'0623' => 2,'0624' => 2,'0625' => 2,'0626' => 2,'0627' => 2,'0628' => 2,'0629' => 2,'0630' => 2,'0701' => 2,'0702' => 2,'0703' => 2,'0704' => 2,'0705' => 2,'0706' => 2,'0707' => 2,'0708' => 2,'0709' => 2,'0710' => 2,'0711' => 2,'0712' => 2,'0713' => 2,'0714' => 2,'0715' => 2,'0716' => 2,'0717' => 2,'0718' => 2,'0719' => 2,'0720' => 2,'0721' => 2,'0722' => 2,'0723' => 2,'0724' => 2,'0725' => 2,'0726' => 2,'0727' => 2,'0728' => 2,'0729' => 2,'0730' => 2,'0731' => 2,'0801' => 2,'0802' => 2,'0803' => 2,'0804' => 2,'0805' => 2,'0806' => 2,'0807' => 2,'0808' => 2,'0809' => 2,'0810' => 2,'0811' => 2,'0812' => 2,'0813' => 2,'0814' => 2,'0815' => 2,'0816' => 2,'0817' => 2,'0818' => 2,'0819' => 2,'0820' => 2,'0821' => 2,'0822' => 2,'0823' => 2,'0824' => 2,'0825' => 2,'0826' => 2,'0827' => 2,'0828' => 2,'0829' => 2,'0830' => 2,'0831' => 2,'0901' => 2,'0902' => 2,'0903' => 2,'0904' => 2,'0905' => 2,'0906' => 2,'0907' => 2,'0908' => 2,'0909' => 2,'0910' => 2,'0911' => 2,'0912' => 2,'0913' => 2,'0914' => 2,'0915' => 2,'0916' => 2,'0917' => 2,'0918' => 2,'0919' => 2,'0920' => 2,'0921' => 2,'0922' => 2,'0923' => 2,'0924' => 2,'0925' => 2,'0926' => 2,'0927' => 2,'0928' => 2,'0929' => 2,'0930' => 2,'1001' => 2,'1002' => 2,'1003' => 2,'1004' => 2,'1005' => 2,'1006' => 2,'1007' => 2,'1008' => 2,'1009' => 2,'1010' => 2,'1011' => 2,'1012' => 2,'1013' => 2,'1014' => 2,'1015' => 2,'1016' => 2,'1017' => 2,'1018' => 2,'1019' => 2,'1020' => 2,'1021' => 2,'1022' => 2,'1023' => 2,'1024' => 2,'1025' => 2,'1026' => 1,'1027' => 1,'1028' => 1,'1029' => 1,'1030' => 1,'1031' => 1,'1101' => 1,'1102' => 1,'1103' => 1,'1104' => 1,'1105' => 1,'1106' => 1,'1107' => 1,'1108' => 1,'1109' => 1,'1110' => 1,'1111' => 1,'1112' => 1,'1113' => 1,'1114' => 1,'1115' => 1,'1116' => 1,'1117' => 1,'1118' => 1,'1119' => 1,'1120' => 1,'1121' => 1,'1122' => 1,'1123' => 1,'1124' => 1,'1125' => 1,'1126' => 1,'1127' => 1,'1128' => 1,'1129' => 1,'1130' => 1,'1201' => 1,'1202' => 1,'1203' => 1,'1204' => 1,'1205' => 1,'1206' => 1,'1207' => 1,'1208' => 1,'1209' => 1,'1210' => 1,'1211' => 1,'1212' => 1,'1213' => 1,'1214' => 1,'1215' => 1,'1216' => 1,'1217' => 1,'1218' => 1,'1219' => 1,'1220' => 1,'1221' => 1,'1222' => 1,'1223' => 1,'1224' => 1,'1225' => 1,'1226' => 1,'1227' => 1,'1228' => 1,'1229' => 1,'1230' => 1,'1231' => 1
	);
$month = '';
if(isset($_GET) && isset($_GET['month'])){
	$month = $_GET['month'];
}
if(isset($_GET) && isset($_GET['year'])){
	$year = $_GET['year'];
}
if(isset($_GET) && isset($_GET['month2'])){
	$month2 = $_GET['month2'];
}
if(isset($_GET) && isset($_GET['year2'])){
	$year2 = $_GET['year2'];
}
if(!$month){
	echo 'Merci de renseigner un mois';
	exit;
}
if(!$year){
	echo 'Merci de renseigner une année';
	exit;
}

$dx = new DateTime($year.'-'.$month.'-01 00:00:00');
$dx->modify('- '.$utc_dec[$dx->format('md')].' hour');
$date_debut = $dx->format('Y-m-d H:i:s');

$dx = new DateTime($year2.'-'.$month2.'-02 23:59:59');
$dx->modify('last day of this month');
$dx->modify('- '.$utc_dec[$dx->format('md')].' hour');
$date_fin = $dx->format('Y-m-d H:i:s');

$dx = new DateTime($year.'-'.$month.'-01 00:00:00');
$dx->modify('+1 month');
$fact_date_debut = $dx->format('Y-m-01 H:i:s');

$dx = new DateTime($year2.'-'.$month2.'-02 23:59:59');
$dx->modify('+1 month');
$dx->modify('last day of this month');
$dx->modify('- '.$utc_dec[$dx->format('md')].' hour');
$fact_date_fin = $dx->format('Y-m-d H:i:s');

/*
//scan credit acheter et non utilise
$total = 0;
$result_order = $mysqli->query("SELECT * from  orders where valid > 0 and date_add >= '".$date_debut."' and date_add <= '".$date_fin."' ");
while($row_order = $result_order->fetch_array(MYSQLI_ASSOC)){
	$result_credit = $mysqli->query("SELECT * from  user_credits where order_id = '".$row_order['id']."' ");
	$row_credit = $result_credit->fetch_array(MYSQLI_ASSOC);
	if($row_credit){
		$result_price = $mysqli->query("SELECT * from  user_credit_prices where id_user_credit = '".$row_credit['id']."' ");
		$row_price = $result_price->fetch_array(MYSQLI_ASSOC);
		
		if($row_price){
			if(!$row_price['status']){
				if($row_price['price_euros'])
				$total += $row_price['price_euros'] * $row_price['seconds_left'];
				else
					var_dump('pas de prix euros -> '.$row_price['id_user_credit']);
			}
		}else{
			var_dump('pas de credit price -> '.$row_credit['id']);
		}
		
		
		
		
	}
	
	
}
var_dump($total);*/

var_dump("SELECT U.agent_id, U.sessionid,U.date_start,  U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids from  user_credit_history U, user_pay_v2 P WHERE U.date_start >= '".$date_debut."' and U.date_start <= '".$date_fin."' and U.is_sold = 0 and P.id_user_credit_history = U.user_credit_history order by U.user_credit_history")

//credit consommé utilisé en comm et non payé au expert sur l exercice
/*$ca_not_paid = 0;
$rem_not_paid = 0;
$resultcomm = $mysqli->query("SELECT U.agent_id, U.sessionid,U.date_start,  U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids from  user_credit_history U, user_pay_v2 P WHERE U.date_start >= '".$date_debut."' and U.date_start <= '".$date_fin."' and U.is_factured = 1 and U.is_sold = 0 and P.id_user_credit_history = U.user_credit_history order by U.user_credit_history");
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
		
		$result_fact = $mysqli->query("SELECT * from  invoice_agents where user_id = '".$rowcomm['agent_id']."' and date_min <= '".$rowcomm['date_start']."' and date_max >= '".$rowcomm['date_start']."' ");
		$row_fact = $result_fact->fetch_array(MYSQLI_ASSOC);
		
		if(!$row_fact){
			var_dump($rowcomm);
			$ca_not_paid += $rowcomm['ca'];
			$rem_not_paid += $rowcomm['price'];
		}
	}

var_dump($ca_not_paid);
var_dump($rem_not_paid);*/
?>