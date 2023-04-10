<?php

		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

		header("Content-type: application/csv");
		header("Content-Disposition: attachment; filename=export.csv");
		$fp = fopen('php://output', 'w');
		
		$legend = array(
			utf8_decode('nom'),
			utf8_decode('prénom'),
			utf8_decode('pseudo'),
			utf8_decode('Id'),
			utf8_decode('revenus avec bonus'),
			utf8_decode('bonus'),
		);

		fputcsv($fp, $legend,";");

$list_agents = array(358,3799,7353,8909,331,350,351,355,368,369,379,383,394,397,411,416,419,429,432,448,431,461,457,472,476,481,482,487,496,503,535,559,652,656,722,747,766,857,903,986,994,996,879,1009,1018,1024,1030,916,405,1280,1356,1411,1433,357,818,1493,1497,1889,1893,1994,2044,2046,2162,2255,2263,2286,2341,2344,2398,2400,2504,2645,2769,2902,3067,3356,3406,3435,3731,3961,4303,4478,4728,5023,5295,5554,5604,5687,5716,5766,5809,5841,5851,5871,5956,6045,6061,6183,6285,6358,6443,6537,6559,6665,7123,7158,7204,7238,7323,7358,7436,8118,8171,8201,8628,8826,8954,9084,9611,9656,9712,9713,9777,9780,9828,9904,9943,9993,10037,10279,10280,10441,10612,10781,10790,10814,10930,11035,11336,12114,12539,12701,13194,13371,13609,13634,13889,14056,14346,14383,14461,14578,14627,14655,333,372,401,441,471,473,563,692,790,1490,1514,1526,2093,2566,2578,3303,3310,3751,4160,4222,4816,5615,5803,5885,7492,7530,8610,8895,9015,9336,9695,9914,10085,10196,10603,10744,10901,10923,11027,11194,11299,12088,12168,12210,12281,12331,12429,12530,13116,13289,13426,13538,13710,13921,14215,14265,14287,14414,14549,334,353,352,354,364,361,367,363,371,376,387,393,396,412,422,424,425,427,428,459,480,483,497,502,501,504,510,521,617,734,750,769,764,819,822,852,1000,1005,375,468,389,1739,1900,2014,2253,2514,2519,2535,2555,2682,2817,2821,2842,2988,2996,3606,3825,3995,4098,4150,4251,4263,4302,5014,5785,5804,5846,5905,5922,6081,6105,6139,6797,7392,7393,7424,7474,7621,7772,8054,8347,8418,8517,8799,8837,8871,9072,9114,9258,9491,10109,10877,11734,12113,12266,12567,12580,12693,13372,13539,13823,14020,14042,14377);
						  	  	  
		$period_begin = '2017-01-01 00:00:00';
		$period_end = '2017-12-31 23:59:59';
		
		$session_date_min =  '2017-01-01 00:00:00';
		$session_date_max =  '2017-12-31 23:59:59';
$session_date_min_public =  '2017-01-01 00:00:00';
		$session_date_max_public =  '2017-12-31 23:59:59';

		$result_agent = $mysqli->query("SELECT id,pseudo,lastname,firstname from users WHERE role = 'agent' order by id");
		while($row_agent = $result_agent->fetch_array(MYSQLI_ASSOC)){
		
			$nom = utf8_decode($row_agent['lastname']);
			$prenom = utf8_decode($row_agent['firstname']);
			$pseudo = utf8_decode($row_agent['pseudo']);
			$id = $row_agent['id'];
			
			if(in_array($id,$list_agents)){
			
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
			//	var_dump("SELECT * from bonus_agents WHERE id_agent = '{$row_agent['id']}' and (annee >= '{$annee_min}' AND annee <= '{$annee_max}' ) and (mois >= '{$mois_min}' AND mois <= '{$mois_max}' ) and active = 1 order by id DESC");exit;
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


		/*$resultpenalities = $mysqli->query("SELECT * from user_penalities WHERE user_id = '{$row_agent['id']}' and date_com >= '".$session_date_min."' and date_com <= '".$session_date_max."' and is_factured = 1 order by id ASC");

		while($rowpenality = $resultpenalities->fetch_array(MYSQLI_ASSOC)){
			if($rowpenality['is_factured']){
				$total -= $rowpenality['penality_cost'];
				$total_penality += $rowpenality['penality_cost'];

				if($rowpenality['message_id']){
					$total -= 12;
					$total_penality += 12;
				}
			}
		}*/
			
			$revenu = $total;
		
			
			$row = array($nom,$prenom,$pseudo,$id,$revenu,$total_bonus);

			fputcsv($fp, $row,";");
			}
		}
		fclose($fp);
		exit;	