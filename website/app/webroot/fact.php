<?php
	session_start();
	if(isset($_GET['idagent']))$_SESSION['fact_agent'] = $_GET['idagent'];
	if(isset($_GET['fact_min']))$_SESSION['fact_min'] = $_GET['fact_min'];
	if(isset($_GET['fact_max']))$_SESSION['fact_max'] = $_GET['fact_max'];
	if(!isset($_SESSION['fact_agent']))exit;
	
	if( substr_count($_SERVER['HTTP_HOST'], 'devspi'))
		$mysqli = new mysqli($dbb_head['host'], "devspi", "8vjf3p99Sfv8M3pBYcH5JGF3", "devspi");
	else
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
	$result = $mysqli->query("SELECT * from users WHERE id = '{$_SESSION['fact_agent']}'");
    $row = $result->fetch_array(MYSQLI_ASSOC);
	
	if(!$row['id'])exit;

$session_date_min = $_SESSION['fact_min'];
$session_date_max = $_SESSION['fact_max'];

$cut_date = explode( ' ',$session_date_min);

/*$utc_dec = 1;
$cut = explode('-',$cut_date[0] );
$mois_comp = $cut[1];
if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
	$utc_dec = 2;*/
$listing_utcdec = array(
	'0101' => 1,'0102' => 1,'0103' => 1,'0104' => 1,'0105' => 1,'0106' => 1,'0107' => 1,'0108' => 1,'0109' => 1,'0110' => 1,'0111' => 1,'0112' => 1,'0113' => 1,'0114' => 1,'0115' => 1,'0116' => 1,'0117' => 1,'0118' => 1,'0119' => 1,'0120' => 1,'0121' => 1,'0122' => 1,'0123' => 1,'0124' => 1,'0125' => 1,'0126' => 1,'0127' => 1,'0128' => 1,'0129' => 1,'0130' => 1,'0131' => 1,'0201' => 1,'0202' => 1,'0203' => 1,'0204' => 1,'0205' => 1,'0206' => 1,'0207' => 1,'0208' => 1,'0209' => 1,'0210' => 1,'0211' => 1,'0212' => 1,'0213' => 1,'0214' => 1,'0215' => 1,'0216' => 1,'0217' => 1,'0218' => 1,'0219' => 1,'0220' => 1,'0221' => 1,'0222' => 1,'0223' => 1,'0224' => 1,'0225' => 1,'0226' => 1,'0227' => 1,'0228' => 1,'0229' => 1,'0301' => 1,'0302' => 1,'0303' => 1,'0304' => 1,'0305' => 1,'0306' => 1,'0307' => 1,'0308' => 1,'0309' => 1,'0310' => 1,'0311' => 1,'0312' => 1,'0313' => 1,'0314' => 1,'0315' => 1,'0316' => 1,'0317' => 1,'0318' => 1,'0319' => 1,'0320' => 1,'0321' => 1,'0322' => 1,'0323' => 1,'0324' => 1,'0325' => 1,'0326' => 1,'0327' => 1,'0328' => 1,'0329' => 2,'0330' => 2,'0331' => 2,'0401' => 2,'0402' => 2,'0403' => 2,'0404' => 2,'0405' => 2,'0406' => 2,'0407' => 2,'0408' => 2,'0409' => 2,'0410' => 2,'0411' => 2,'0412' => 2,'0413' => 2,'0414' => 2,'0415' => 2,'0416' => 2,'0417' => 2,'0418' => 2,'0419' => 2,'0420' => 2,'0421' => 2,'0422' => 2,'0423' => 2,'0424' => 2,'0425' => 2,'0426' => 2,'0427' => 2,'0428' => 2,'0429' => 2,'0430' => 2,'0501' => 2,'0502' => 2,'0503' => 2,'0504' => 2,'0505' => 2,'0506' => 2,'0507' => 2,'0508' => 2,'0509' => 2,'0510' => 2,'0511' => 2,'0512' => 2,'0513' => 2,'0514' => 2,'0515' => 2,'0516' => 2,'0517' => 2,'0518' => 2,'0519' => 2,'0520' => 2,'0521' => 2,'0522' => 2,'0523' => 2,'0524' => 2,'0525' => 2,'0526' => 2,'0527' => 2,'0528' => 2,'0529' => 2,'0530' => 2,'0531' => 2,'0601' => 2,'0602' => 2,'0603' => 2,'0604' => 2,'0605' => 2,'0606' => 2,'0607' => 2,'0608' => 2,'0609' => 2,'0610' => 2,'0611' => 2,'0612' => 2,'0613' => 2,'0614' => 2,'0615' => 2,'0616' => 2,'0617' => 2,'0618' => 2,'0619' => 2,'0620' => 2,'0621' => 2,'0622' => 2,'0623' => 2,'0624' => 2,'0625' => 2,'0626' => 2,'0627' => 2,'0628' => 2,'0629' => 2,'0630' => 2,'0701' => 2,'0702' => 2,'0703' => 2,'0704' => 2,'0705' => 2,'0706' => 2,'0707' => 2,'0708' => 2,'0709' => 2,'0710' => 2,'0711' => 2,'0712' => 2,'0713' => 2,'0714' => 2,'0715' => 2,'0716' => 2,'0717' => 2,'0718' => 2,'0719' => 2,'0720' => 2,'0721' => 2,'0722' => 2,'0723' => 2,'0724' => 2,'0725' => 2,'0726' => 2,'0727' => 2,'0728' => 2,'0729' => 2,'0730' => 2,'0731' => 2,'0801' => 2,'0802' => 2,'0803' => 2,'0804' => 2,'0805' => 2,'0806' => 2,'0807' => 2,'0808' => 2,'0809' => 2,'0810' => 2,'0811' => 2,'0812' => 2,'0813' => 2,'0814' => 2,'0815' => 2,'0816' => 2,'0817' => 2,'0818' => 2,'0819' => 2,'0820' => 2,'0821' => 2,'0822' => 2,'0823' => 2,'0824' => 2,'0825' => 2,'0826' => 2,'0827' => 2,'0828' => 2,'0829' => 2,'0830' => 2,'0831' => 2,'0901' => 2,'0902' => 2,'0903' => 2,'0904' => 2,'0905' => 2,'0906' => 2,'0907' => 2,'0908' => 2,'0909' => 2,'0910' => 2,'0911' => 2,'0912' => 2,'0913' => 2,'0914' => 2,'0915' => 2,'0916' => 2,'0917' => 2,'0918' => 2,'0919' => 2,'0920' => 2,'0921' => 2,'0922' => 2,'0923' => 2,'0924' => 2,'0925' => 2,'0926' => 2,'0927' => 2,'0928' => 2,'0929' => 2,'0930' => 2,'1001' => 2,'1002' => 2,'1003' => 2,'1004' => 2,'1005' => 2,'1006' => 2,'1007' => 2,'1008' => 2,'1009' => 2,'1010' => 2,'1011' => 2,'1012' => 2,'1013' => 2,'1014' => 2,'1015' => 2,'1016' => 2,'1017' => 2,'1018' => 2,'1019' => 2,'1020' => 2,'1021' => 2,'1022' => 2,'1023' => 2,'1024' => 2,'1025' => 2,'1026' => 2,'1027' => 1,'1028' => 1,'1029' => 1,'1030' => 1,'1031' => 1,'1101' => 1,'1102' => 1,'1103' => 1,'1104' => 1,'1105' => 1,'1106' => 1,'1107' => 1,'1108' => 1,'1109' => 1,'1110' => 1,'1111' => 1,'1112' => 1,'1113' => 1,'1114' => 1,'1115' => 1,'1116' => 1,'1117' => 1,'1118' => 1,'1119' => 1,'1120' => 1,'1121' => 1,'1122' => 1,'1123' => 1,'1124' => 1,'1125' => 1,'1126' => 1,'1127' => 1,'1128' => 1,'1129' => 1,'1130' => 1,'1201' => 1,'1202' => 1,'1203' => 1,'1204' => 1,'1205' => 1,'1206' => 1,'1207' => 1,'1208' => 1,'1209' => 1,'1210' => 1,'1211' => 1,'1212' => 1,'1213' => 1,'1214' => 1,'1215' => 1,'1216' => 1,'1217' => 1,'1218' => 1,'1219' => 1,'1220' => 1,'1221' => 1,'1222' => 1,'1223' => 1,'1224' => 1,'1225' => 1,'1226' => 1,'1227' => 1,'1228' => 1,'1229' => 1,'1230' => 1,'1231' => 1
	);

$dmin = new DateTime($session_date_min);
$dmax = new DateTime($session_date_max);
$date_filename = clone($dmin);
$session_date_min_public =  $dmin->format('Y-m-d H:i:s'); 
$session_date_max_public =  $dmax->format('Y-m-d H:i:s'); 

//$cut = explode('-',$session_date_min );
				$datecomp = $cut[0].$cut[1].$cut[2];
//var_dump($datecomp);				
				//if($datecomp >= '20190228')
				$dmin->modify('-'.$listing_utcdec[$dmin->format('md')].' hour');
				//else
				//$dmin->modify('-0 hour');
//var_dump($dmin->format('Y-m-d H:i:s'));
$dmax->modify('-'.$listing_utcdec[$dmax->format('md')].' hour');
$session_date_min =  $dmin->format('Y-m-d H:i:s'); 
$session_date_max =  $dmax->format('Y-m-d H:i:s'); 

if($session_date_max == '2019-03-31 22:59:59')$session_date_max = '2019-03-31 21:59:59';
if($session_date_max == '2019-04-30 22:59:59')$session_date_max = '2019-04-30 21:59:59';
if($session_date_min == '2018-12-31 23:00:00')$session_date_min = '2019-01-01 00:00:00';
if($session_date_min == '2019-02-28 22:00:00')$session_date_min = '2019-02-28 23:00:00';
if($session_date_min == '2018-12-31 22:00:00')$session_date_min = '2019-01-01 00:00:00';
if($session_date_min == '2019-01-31 22:00:00')$session_date_min = '2019-02-01 00:00:00';
if($session_date_min == '2019-01-31 23:00:00')$session_date_min = '2019-02-01 00:00:00';
if($session_date_max == '2019-01-31 21:59:59')$session_date_max = '2019-01-31 23:59:59';
if($session_date_max == '2019-02-28 21:59:59')$session_date_max = '2019-02-28 22:59:59';

//var_dump($session_date_min);

require('../Lib/invoice.php');
$filename_lastname = '';
$filename_firstname = '';
$filename_period_an = '';
$filename_period_month = '';
$libelle = '';
if($row['societe']){
	$libelle = utf8_decode($row['societe']);	
}else{
	$libelle = $row['lastname'].' '.$row['firstname'];	
}
$filename_lastname = $row['lastname'];
$filename_firstname = $row['firstname'];
$filename_period_an = $date_filename->format('Y');
$filename_period_month = $date_filename->format('m');
$page = 1;
$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
if($row['societe'] && $row['societe_adress']){
	if($row['societe_cp'])$row['societe_ville'] = $row['societe_cp'].' '.$row['societe_ville'];
$pdf->addSociete( $libelle,
				 $row['lastname'].' '.$row['firstname']."\n" .
                  utf8_decode($row['societe_adress']). ' '. utf8_decode($row['societe_adress2'])."\n" .
				  utf8_decode($row['societe_ville'])."\n".
                  trim($row['societe_pays'])."\n".'SIRET : '.$row['siret']."\n");
}else{
$pdf->addSociete( $libelle,
                  utf8_decode($row['address'])."\n" .
                  $row['postalcode'].' '.utf8_decode($row['city'])."\n".
                  $row['phone_number']."\n".
				  $row['email']."\n".
				  'SIRET : '.$row['siret']."\n");
}
				  
$pdf->fact_dev( "Facture ", " " );
$pdf->addDate( date('d/m/Y'));
//$pdf->addClient("CL".$_SESSION['fact_agent']);
$num = 0;
$new_list = array();
$result4 = $mysqli->query("SELECT C.agent_id,C.expert_number,C.is_mobile, C.user_credit_history,C.media,C.is_factured,C.user_id, C.seconds,C.date_start , P.price, P.order_cat_index, P.mail_price_index, C.is_sold from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$_SESSION['fact_agent']}' and C.date_start >= '{$session_date_min}' and C.date_start <= '{$session_date_max}' and P.id_user_credit_history = C.user_credit_history and is_factured = 1");
while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
	$num++;	
	$new_list[$row4['date_start']] = $row4;
}
$result_refund = $mysqli->query("SELECT * from user_penalities WHERE date_add >= '{$session_date_min}' and date_add <= '{$session_date_max}' and is_factured = 1 and user_id = '{$_SESSION['fact_agent']}' and message_id > 0");
while($row_refund = $result_refund->fetch_array(MYSQLI_ASSOC)){ 
	$num++;
	$result2 = $mysqli->query("SELECT * from user_credit_history where sessionid = '{$row_refund['message_id']}'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	$row2['date_start'] = $row_refund['date_add'] ;
	$row2['price'] = -12 ;
	$new_list[$row2['date_start']] = $row2;
}

ksort($new_list);
$historiqueComs = array();
foreach($new_list as $cc){
	array_push($historiqueComs,$cc);
}

$page = ceil($num / 30);
$pdf->addPageNumber($page);
$pdf->addClientAdresse("Zconnect Limited\nNO. 5\n17/F BONHAM TRADE CENTRE 50\nBONHAM STRAND SHEUNG WAN\nHONG KONG\ncontact@talkappdev.com");
$pdf->addReglement("Virement");
$pdf->addEcheance(" ");
//$pdf->addNumTVA(" ");
$pdf->addReference(utf8_decode("Facturation de l'agent ".$row['pseudo']." pour la période du ".$session_date_min_public." au ".$session_date_max_public.""));
$cols=array( "DESIGNATION"  => 92,
			"MODE"  => 20,
			"DUREE / MIN"  => 28,
             "P.U / MIN"      => 24,
             "MONTANT" => 26
              );
$pdf->addCols( $cols);
$cols=array( "DESIGNATION"  => "L",
"MODE"  => "C",
"DUREE / MIN"  => "R",
             "P.U / MIN"      => "R",
             "MONTANT" => "R");
$pdf->addLineFormat( $cols);

$exist_sold = 0;
$is_all_sold = 1;
$y    = 109;
$num = 0;
$total = 0;
$page_actual = 1;
/*$result4 = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$_SESSION['fact_agent']}' and C.date_start >= '{$session_date_min}' and C.date_start <= '{$session_date_max}' and P.id_user_credit_history = C.user_credit_history");

while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){*/
foreach($historiqueComs as $row4){
	
	
	if($row4['is_sold']){
		$exist_sold = 1;
	}else{
		$is_all_sold = 0;
	}
	$seconds = $row4['seconds'];
	$order_cat = $row4['order_cat_index'];
	$mail_price = $row4['mail_price_index'];
	$media = $row4['media'];
	$is_factured = $row4['is_factured'];
	$montant = $row4['price'];
	$date_com = $row4['date_start'];
	
	$ddate = new DateTime($date_com);
	$ddate->modify('+1 hour');
	$date_com =  $ddate->format('Y-m-d H:i:s'); 
	
	
	$user_id = $row4['user_id'];
	$price = number_format($row4['price'], 2);
	$total += $price;
	$result2 = $mysqli->query("SELECT firstname from users where id = '{$user_id}'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$pu = 0;
	switch ($order_cat) {
		case 0:
			$pu = 0;
			break;
		case 1:
			$pu = 0.35;//21€ / heure 
			break;
		case 2:
			$pu = 0.37;//22.20€ / heure
			break;
		case 3:
			$pu = 0.41;//24.6€ / heure
			break;
		case 4:
			$pu = 0.45;//27€ / heure 
			break;
		case 5:
			$pu = 0.32;//19.20€ / heure 
			break;
		case 6:
			$pu = 0;//XX€ / heure 
			break;
	}
	
	if($is_factured){
		switch ($media) {
			case 'phone':
				$pu = $pu;
				break;
			case 'chat':
				$pu = $pu;
				break;
			case 'email':
				$pu = $mail_price;
				break;
		}
	}
	
	if($row4['is_mobile']){
						$result_costphone = $mysqli->query("SELECT * from cost_phones order by id");
						while($row_costphone = $result_costphone->fetch_array(MYSQLI_ASSOC)){
							if(substr($row4['expert_number'],0,strlen($row_costphone['indicatif'])) == $row_costphone['indicatif'])
								$pu = $pu - $row_costphone['cost'];
						}
					}
	
	$media = str_replace('phone','téléphone',$media);
	
	if(substr_count($row2['firstname'],'AUDIOTEL'))
		$row2['firstname'] = 'AUDIOTEL';
	
	$designation = $date_com. ' ' .$media .' avec '. utf8_encode($row2['firstname']) ;
	if($price < 0)$designation .= ' annulé';
	$heures = intval($seconds/ 60 / 60);
	$minutes = intval(($seconds % 3600) / 60);
	$secondes =intval((($seconds % 3600) % 60));
	
	$duree = $heures.'h '.$minutes . 'm '.$secondes.'s';

	$line = array( "DESIGNATION"  => utf8_decode($designation),
	"MODE"  => utf8_decode($media),
	"DUREE / MIN"  => $duree,
				   "P.U / MIN"      => $pu,
				   "MONTANT" => $price);
	$size = $pdf->addLine( $y, $line );
	$y   += $size + 2;
 	$num ++;
	$check = 27;
	if($page_actual > 1)$check = 43;
		
	
	if($num > $check){
		$page_actual ++;
		$pdf->addPage();
		$page++;
		$num = 0;
		$y    = 29;
		
		$cols=array( "DESIGNATION"  => 92,
			"MODE"  => 20,
			"DUREE / MIN"  => 28,
             "P.U / MIN"      => 24,
             "MONTANT" => 26
              );
		$pdf->addCols2( $cols);
		$cols=array( "DESIGNATION"  => "L",
		"MODE"  => "C",
		"DUREE / MIN"  => "R",
					 "P.U / MIN"      => "R",
					 "MONTANT" => "R");
		$pdf->addLineFormat( $cols);

	}
}

$tabdate = explode(' ',$session_date_min_public);
$tabdatec = explode('-',$tabdate[0]);
$annee_min = $tabdatec[0];
$mois_min = $tabdatec[1];
$tabdate = explode(' ',$session_date_max_public);
$tabdatec = explode('-',$tabdate[0]);
$annee_max = $tabdatec[0];
$mois_max = $tabdatec[1];

//$annee = date('Y');
//$mois = date('m');

$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$_SESSION['fact_agent']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= {$mois_min} AND mois <= {$mois_max} ) and paid = 1 order by id ASC");
while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
	
	$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
	$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
	$num ++;
	if($page_actual > 1)$check = 43;
		
	
	if($num > $check){
		$page_actual ++;
		$pdf->addPage();
		$page++;
		$num = 0;
		$y    = 29;
		
		$cols=array( "DESIGNATION"  => 92,
			"MODE"  => 20,
			"DUREE / MIN"  => 28,
             "P.U / MIN"      => 24,
             "MONTANT" => 26
              );
		$pdf->addCols2( $cols);
		$cols=array( "DESIGNATION"  => "L",
		"MODE"  => "C",
		"DUREE / MIN"  => "R",
					 "P.U / MIN"      => "R",
					 "MONTANT" => "R");
		$pdf->addLineFormat( $cols);

	}
	
	$prime_montant = $rowbonusagent['paid_amount'];
	if(!$prime_montant)$prime_montant = $rowbonus['amount'];
	
	$line = array( "DESIGNATION"  => utf8_decode($rowbonus['name']. ' '.$rowbonusagent['mois'].'/'.$rowbonusagent['annee']),
	"MODE"  => 'BONUS',
	"DUREE / MIN"  => $rowbonus['bearing'],
				   "P.U / MIN"      => number_format($prime_montant,2),
				   "MONTANT" => number_format($prime_montant,2));
	$size = $pdf->addLine( $y, $line );
	$total += $prime_montant;
	$y   += $size + 2;
}

$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$_SESSION['fact_agent']}' and is_recup = 1 and status <= 4");
while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){
	$num ++;
	if($page_actual > 1)$check = 43;
		
	
	if($num > $check){
		$page_actual ++;
		$pdf->addPage();
		$page++;
		$num = 0;
		$y    = 29;
		
		$cols=array( "DESIGNATION"  => 92,
			"MODE"  => 20,
			"DUREE / MIN"  => 28,
             "P.U / MIN"      => 24,
             "MONTANT" => 26
              );
		$pdf->addCols2( $cols);
		$cols=array( "DESIGNATION"  => "L",
		"MODE"  => "C",
		"DUREE / MIN"  => "R",
					 "P.U / MIN"      => "R",
					 "MONTANT" => "R");
		$pdf->addLineFormat( $cols);

	}
	$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$session_date_min}' and date_start <= '{$session_date_max}'");
	$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
	$mt = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];
	$line = array( "DESIGNATION"  => utf8_decode('Prime mensuelle parrainage '.$mois_min.'/'.$annee_min),
	"MODE"  => 'BONUS',
	"DUREE / MIN"  => '',//$rowcomm['total'],
				   "P.U / MIN"      => '',//number_format($rowsponsoragent['bonus'],3),
				   "MONTANT" => number_format($mt,2));
	$size = $pdf->addLine( $y, $line );
	$total += $mt;
	$y   += $size + 2;
	//break;
}


//ecriture facturation special
$resultfacturation = $mysqli->query("SELECT * from user_orders WHERE user_id = '{$_SESSION['fact_agent']}' and date_ecriture >= '".$session_date_min."' and date_ecriture <= '".$session_date_max."' order by id ASC");

while($rowfacturation = $resultfacturation->fetch_array(MYSQLI_ASSOC)){
	$num ++;
	if($page_actual > 1)$check = 43;
		
	
	if($num > $check){
		$page_actual ++;
		$pdf->addPage();
		$page++;
		$num = 0;
		$y    = 29;
		
		$cols=array( "DESIGNATION"  => 92,
			"MODE"  => 20,
			"DUREE / MIN"  => 28,
             "P.U / MIN"      => 24,
             "MONTANT" => 26
              );
		$pdf->addCols2( $cols);
		$cols=array( "DESIGNATION"  => "L",
		"MODE"  => "C",
		"DUREE / MIN"  => "R",
					 "P.U / MIN"      => "R",
					 "MONTANT" => "R");
		$pdf->addLineFormat( $cols);

	}
	$line = array( "DESIGNATION"  => $rowfacturation['label'],
	"MODE"  => '',
	"DUREE / MIN"  => '',
				   "P.U / MIN"      => number_format($rowfacturation['amount'],2),
				   "MONTANT" => number_format($rowfacturation['amount'],2));
	$size = $pdf->addLine( $y, $line );
	$total += $rowfacturation['amount'];
	$y   += $size + 2;
	
	if($rowfacturation['is_sold']){
		$exist_sold = 1;
	}else{
		$is_all_sold = 0;
	}
}

//ecriture communication perdu
/*$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$_SESSION['fact_agent']}' and date_com >= '".$session_date_min."' and date_com <= '".$session_date_max."' and is_factured = 1 order by id ASC");

while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
	
	if($rowpenality['is_factured']){

		$num ++;
		if($page_actual > 1)$check = 43;


		if($num > $check){
			$page_actual ++;
			$pdf->addPage();
			$page++;
			$num = 0;
			$y    = 29;

			$cols=array( "DESIGNATION"  => 92,
				"MODE"  => 20,
				"DUREE / MIN"  => 28,
				 "P.U / MIN"      => 24,
				 "MONTANT" => 26
				  );
			$pdf->addCols2( $cols);
			$cols=array( "DESIGNATION"  => "L",
			"MODE"  => "C",
			"DUREE / MIN"  => "R",
						 "P.U / MIN"      => "R",
						 "MONTANT" => "R");
			$pdf->addLineFormat( $cols);

		}

		$penality = '';
		if($rowpenality['tchat_id']){
			$penality = 'Tchat perdu le '.$rowpenality['date_com'];
		}
		if($rowpenality['message_id']){
			$penality = 'Email non répondu le '.$rowpenality['date_com'];
		}
		if($rowpenality['callinfo_id']){
			$penality = 'Appel perdu le '.$rowpenality['date_com'];
		}

		$line = array( "DESIGNATION"  => utf8_decode($penality),
		"MODE"  => '',
		"DUREE / MIN"  => '',
					   "P.U / MIN"      => "-".number_format($rowpenality['penality_cost'],2),
					   "MONTANT" => "-".number_format($rowpenality['penality_cost'],2));
		$size = $pdf->addLine( $y, $line );
		$total -= $rowpenality['penality_cost'];
		$y   += $size + 2;

	}
}
*/



$tot_prods = array( array ( "px_unit" => $total, "qte" => 1, "tva" => 1 ));
					
$tab_tva = array( "1"       => 19.6,
                  "2"       => 5.5);
if($row['mode_paiement'] == 'Virement'){
	$row['paypal'] = '';	
}
if($row['mode_paiement'] == 'Hipay Wallet'){
	$row['hipay'] = $row['paypal'];	
}
$params  = array( "RemiseGlobale" => 1,
                      "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
                      "remise"         => 0,       // {montant de la remise}
                      "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
                  "FraisPort"     => 1,
                      "portTTC"        => 10,      // montant des frais de ports TTC
                                                   // par defaut la TVA = 19.6 %
                      "portHT"         => 0,       // montant des frais de ports HT
                      "portTVA"        => 19.6,    // valeur de la TVA a appliquer sur le montant HT
                  "AccompteExige" => 1,
                      "accompte"         => 0,     // montant de l'acompte (TTC)
                      "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
                  "Remarque" => utf8_decode("Exonération de TVA - Art 259-1° du CGI"),
				  "RIB" => $row['rib'],
				  "IBAN" => $row['iban'],
				  "SWIFT" => $row['swift'],
				  "PAYPAL" => $row['paypal'],
				  "HIPAY" => $row['hipay'],
				  "BANK" => utf8_decode($row['bank_name']),"BANK_ADR" => utf8_decode($row['bank_country']) );
$pdf->addCadreTVAs($params);
$pdf->addTVAs( $params, $tab_tva, $tot_prods);

$sold_info = '';
if($exist_sold && !$is_all_sold)$sold_info = utf8_decode('SOLDÉ PARTIELLEMENT');
if($exist_sold && $is_all_sold)$sold_info = utf8_decode('SOLDÉ');

$filename_period_an = $date_filename->format('Y');
$filename_period_month = $date_filename->format('m');
$list_month = array(
'01' => 'Janvier',
	'02' => 'Fevrier',
	'03' => 'Mars',
	'04' => 'Avril',
	'05' => 'Mai',
	'06' => 'Juin',
	'07' => 'Juillet',
	'08' => 'Aout',
	'09' => 'Septembre',
	'10' => 'Octobre',
	'11' => 'Novembre',
	'12' => 'Decembre',

);
$filename_period_month = $list_month[$filename_period_month];

$filename = 'Facture '.$filename_lastname.' '.$filename_firstname. ' '.$filename_period_month. ' '.$filename_period_an.'.pdf';
	
$pdf->addCadreSolde($sold_info);
$pdf->addCadreEurosFrancs();
$pdf->Output($filename, 'I');

?>