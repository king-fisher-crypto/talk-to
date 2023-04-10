<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

// Processing may take a while
//set_time_limit(0);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");



$date_debut = '2015-06-01';
$max_date = '2019-04-01';

while($date_debut != $max_date){
	$datedebut = $date_debut;
	$dd = new DateTime($date_debut);
	$dd->modify('-1 day');

	$date = $dd->format('Y-m-01');
	$datecomp = $dd->format('Ym01');
	$period = $dd->format('Y-m');
	$mois_comp = $dd->format('m');
	$datemin = $date.' 00:00:00';
	$dd->modify('last day of this month');
	$datemax = $dd->format('Y-m-d').' 23:59:59';

	$session_date_min_public =  $datemin; 
	$session_date_max_public =  $datemax; 

	$utc_dec = 1;
	if($mois_comp == '04' || $mois_comp == '05' || $mois_comp == '06' || $mois_comp == '07' || $mois_comp == '08' || $mois_comp == '09' || $mois_comp == '10')
		$utc_dec = 2;

	$dmin = new DateTime($datemin);
	$dmax = new DateTime($datemax);
	if($datecomp >= '20190228'){
		$dmin->modify('-'.$utc_dec.' hour');
		$dmax->modify('-'.$utc_dec.' hour');
	}

	$session_date_min =  $dmin->format('Y-m-d H:i:s'); 
	$session_date_max =  $dmax->format('Y-m-d H:i:s'); 

	if($session_date_max == '2019-03-31 22:59:59')$session_date_max = '2019-03-31 21:59:59';



	$result_agent = $mysqli->query("SELECT id from users WHERE role = 'agent' order by id");
	while($row_agent = $result_agent->fetch_array(MYSQLI_ASSOC)){

		$result_comm = $mysqli->query("SELECT C.agent_id, C.user_credit_history,C.is_sold,C.media,C.is_factured,C.user_id, C.seconds,C.date_start , P.price, P.order_cat_index, P.mail_price_index from user_credit_history C, users U, user_pay P WHERE C.agent_id = U.id and U.id = '{$row_agent['id']}' and C.date_start >= '{$session_date_min}' and C.date_start <= '{$session_date_max}' and P.id_user_credit_history = C.user_credit_history");
		$total = 0;
		$total_comm = 0;
		$total_penality = 0;
		$total_bonus = 0;

		while($row_comm = $result_comm->fetch_array(MYSQLI_ASSOC)){
			if($row_comm['is_factured']){
				$total += $row_comm['price'];
				$total_comm += $row_comm['price'];
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


		$resultbonusagent = $mysqli->query("SELECT * from bonus_agents WHERE id_agent = '{$row_agent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1 order by id DESC");
		while($rowbonusagent = $resultbonusagent->fetch_array(MYSQLI_ASSOC)){

			$resultbonus = $mysqli->query("SELECT * from bonuses where id = '{$rowbonusagent['id_bonus']}'");
			$rowbonus = $resultbonus->fetch_array(MYSQLI_ASSOC);
			$total += $rowbonus['amount'];
			$total_bonus += $rowbonus['amount'];
		}


		$resultsponsoragent = $mysqli->query("SELECT * from sponsorships WHERE user_id = '{$row_agent['id']}' and is_recup = 1 and status <= 4");
		while($rowsponsoragent = $resultsponsoragent->fetch_array(MYSQLI_ASSOC)){

			$resultcomm = $mysqli->query("SELECT sum(credits) as total from user_credit_history where user_id = '{$rowsponsoragent['id_customer']}' and is_factured = 1 and date_start >= '{$session_date_min}' and date_start <= '{$session_date_max}'");
			$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
			$mt = $rowsponsoragent['bonus'] / 60 * $rowcomm['total'];
			$total += $mt;
			$total_bonus += $mt;
		}


		$resultfacturation = $mysqli->query("SELECT * from user_orders WHERE user_id = '{$row_agent['id']}' and date_ecriture >= '".$session_date_min."' and date_ecriture <= '".$session_date_max."' order by id ASC");

		while($rowfacturation = $resultfacturation->fetch_array(MYSQLI_ASSOC)){
			$total += $rowfacturation['amount'];
		}


		$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$row_agent['id']}' and date_com >= '".$session_date_min."' and date_com <= '".$session_date_max."' and is_factured = 1 order by id ASC");

		while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
			if($rowpenality['is_factured']){
				$total -= $rowpenality['penality_cost'];
				$total_penality += $rowpenality['penality_cost'];

				if($rowpenality['message_id']){
					$total -= 12;
					$total_penality += 12;
				}
			}
		}

		if($total > 0){
			$mysqli->query("INSERT INTO user_invoices(user_id, date_min, date_max, period, total_comm,total_bonus,total_penality,total_amount) VALUES ('{$row_agent['id']}','{$session_date_min}','{$session_date_max}','{$period}','{$total_comm}','{$total_bonus}','{$total_penality}','{$total}')");
		}
	}

	$new = new DateTime($datedebut);
	$new->modify('+1 month');
	$date_debut = $new->format('Y-m-01');
}


?>