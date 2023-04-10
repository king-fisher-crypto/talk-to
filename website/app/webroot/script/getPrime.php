<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );

//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="all_prime.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
 
// send the column headers
fputcsv($file, array('Date', 'ID agent', 'Prenom', 'Nom', 'Pseudo','Prime', 'Montant'));


$debut_spiriteo = '2015-06';
$max_date = '2018-11';
$period_month = '2018-09';

while($period_month != $max_date){
	
	$dx = new DateTime($period_month);
	$dx->modify('+1 month');
	$period_month = $dx->format('Y-m');
	
	$dx->modify('last day of this month');
	$period_month_end = $dx->format('Y-m-d 23:59:59');
	$period_month_start = $dx->format('Y-m-01 00:00:01');
	
	$dd = explode('-',$period_month);
	
	$annee_min = $dd[0];
	$annee_max = $dd[0];
	$mois_min = $dd[1];
	$mois_max = $dd[1];
	
	$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' order by id");
	while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
		
		$bonus = '';
		$bonus_montant = 0;
		$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$rowagent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1 order by id DESC");
		while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){
			$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
			$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
			$bonus = $rowbonus['name']. ' '.$rowbonusagent['mois'].'/'.$rowbonusagent['annee'];
			$bonus_montant += number_format($rowbonus['amount'],2);
			break;
		}
		
		if($bonus_montant){
			$line = array($period_month, $rowagent['id'], $rowagent['firstname'], $rowagent['lastname'], $rowagent['pseudo'],$bonus,$bonus_montant);
			fputcsv($file, $line);
		}
		
		$bonus = '';
		$bonus_montant = 0;
		$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$rowagent['id']}' and is_recup = 1 and status <= 4");
		while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){

			$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$period_month_start}' and date_start <= '{$period_month_end}'");
			$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
			$bonus_montant = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];
			
			$bonus = 'Prime mensuelle parrainage '.$mois_min.'/'.$annee_min;

			break;
		}
		if($bonus_montant){
			$line = array($period_month, $rowagent['id'], $rowagent['firstname'], $rowagent['lastname'], $rowagent['pseudo'],$bonus,$bonus_montant);
			fputcsv($file, $line);
		}

	}
}
?>