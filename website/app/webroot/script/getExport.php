<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );

//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$debut_spiriteo = '2019-07-01 00:00:00';

$period_jour = '2019-07-01 00:00:00';
$period_semaine = '2019-07-01 00:00:00';
$period_month = '2019-07-01 00:00:00';

$max_date = '2019-07-18 23:59:59';

//while($period_jour != $max_date){
	
	$list_period = array();
	
	//periode du jour
	$dx = new DateTime($period_jour);
	$period_jour_begin = $dx->format('Y-m-d 00:00:00');
	$period_jour_end = $dx->format('Y-m-d 23:59:59');
	//array_push($list_period,$period_jour_begin. '_'.$period_jour_end);
	
	//periode semaine
	if(date('w',strtotime($period_jour)) == 1){
		$period_semaine_begin = date('Y-m-d  00:00:00', strtotime($period_jour));
	}else{
		$period_semaine_begin = date('Y-m-d  00:00:00', strtotime('last monday', strtotime($period_jour)));
	}
	
	$period_semaine_end = date('Y-m-d 23:59:59', strtotime('next sunday', strtotime($period_semaine_begin)));
	//array_push($list_period,$period_semaine_begin. '_'.$period_semaine_end);
	
	//periode mois
	$dx = new DateTime($period_jour);
	$period_mois_begin = $dx->format('Y-m-01 00:00:00');
	$dx = new DateTime($period_jour);
	$dx->modify('last day of this month');
	$period_mois_end = $dx->format('Y-m-d 23:59:59');
	array_push($list_period,$period_mois_begin. '_'.$period_mois_end);
	
	//TRAITEMENT STATS AGENT DASHBOARD
		
	$result = $mysqli->query("SELECT * from users WHERE role = 'agent' and id = 13289");//  
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		foreach($list_period as $period){
			$dd = explode('_',$period);
			$min =$dd[0];
			$max =$dd[1];
			
			$dx1 = new DateTime($min);
			$dx2 = new DateTime($max);
			$jdelai=$dx1->diff($dx2)->days; 
			if(!$jdelai)$jdelai = 1;
			
			$note='';$presence='';$decroche='';$transformation='';$tmc='';$tmc_global='';$email='';$tchat='';$tel='';
			
			//check si agent travailler cette periode 
			$result_check = $mysqli->query("SELECT user_credit_last_history from user_credit_last_histories WHERE agent_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."'");
			$row_check = $result_check->fetch_array(MYSQLI_ASSOC);	
			if($row_check['user_credit_last_history']){

				//note
				$result_s = $mysqli->query("SELECT count(*) as nb_review, avg(pourcent) as total_review from reviews WHERE agent_id = '".$row['id']."' and date_add <= '".$max."' and parent_id is NULL");
				$row_s = $result_s->fetch_array(MYSQLI_ASSOC);	

				$n_reviens = $row_s['nb_review'];
				$total_review = $row_s['total_review'];
				if($n_reviens){
					$note = number_format($total_review,1);
				}else{
					$note = 0;	
				}
				
				//presence
				$result_s = $mysqli->query("SELECT * from user_state_history WHERE user_id = '".$row['id']."' and date_add >= '".$min."' and date_add <= '".$max."' order by date_add");
				
				$tranches_co = array();
				$tranche_begin = '';
				$tranche_end = '';
				$in_live = true;
				while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
					if(!$tranche_begin && ($row_s['state'] != 'unavailable')){
						$tranche_begin = $row_s['date_add'];
						$in_live = true;
					}
					
					
					if($row_s['state'] == 'unavailable'){
						if(!$tranche_begin)$tranche_begin = $row_s['date_add'];
						$tranche_end = $row_s['date_add'];
						$in_live = false;
						
					}
					
					if(!$in_live && $tranche_begin && $tranche_end){
						$tt = new stdClass();
						$tt->begin = $tranche_begin;
						$tt->end = $tranche_end;
						array_push($tranches_co,$tt);				
						$tranche_begin = '';
						$tranche_end = '';
					}
				}
				if($in_live && $tranche_begin && !$tranche_end){
						$tt = new stdClass();
						$tt->begin = $tranche_begin;
						$tt->end = date('Y-m-d H:i:s');
						array_push($tranches_co,$tt);				
				}
				
				$tranches = array();
				foreach($tranches_co as $tran){
				
					$result_s = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and date_connexion >= '".$tran->begin."' and date_connexion <= '".$tran->end."' order by id");
					var_dump("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and date_connexion >= '".$tran->begin."' and date_connexion <= '".$tran->end."' order by id");
					$tranche_begin = '';
					$tranche_end = '';
					$in_live = true;
					while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){

						if(!$tranche_begin && ($row_s['tchat'] || $row_s['phone'])){
							$tranche_begin = $row_s['date_connexion'];
							$in_live = true;
						}


						if($row_s['status'] == 'unavailable'){
							if(!$tranche_begin)$tranche_begin = $row_s['date_connexion'];
							if(strtotime($row_s['date_lastactivity']) < strtotime($tran->end))
								$tranche_end = $row_s['date_lastactivity'];
							else
								$tranche_end = $tran->end;
							$in_live = false;
						}

						if(!$row_s['tchat'] && !$row_s['phone'] && $tranche_begin && $in_live){
							$tranche_end = $row_s['date_connexion'];
							$in_live = false;
						}

						if(!$in_live && $tranche_begin && $tranche_end){
							$tt = new stdClass();
							$tt->begin = $tranche_begin;
							$tt->end = $tranche_end;
							array_push($tranches,$tt);				
							$tranche_begin = '';
							$tranche_end = '';
						}
						
						$last_tranche_end = $row_s['date_connexion'];

					}
				
				}
				if($in_live && $tranche_begin && !$tranche_end){
					$tt = new stdClass();
					$tt->begin = $tranche_begin;
					$tt->end = $last_tranche_end;
					array_push($tranches,$tt);				
					$tranche_begin = '';
					$tranche_end = '';
				}
				
				
				$connexion_max = $jdelai * 24 * 60 * 60; //mettre un delta heures a enlever
				$connexion_min = 0;
				
				//var_dump($tranches);
		
				foreach($tranches as $periode){
					/*$result_ss = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and (tchat = 1 OR phone = 1) and date_connexion >= '".$periode->begin."' and date_connexion <= '".$periode->end."'");
					
					while($row_ss = $result_ss->fetch_array(MYSQLI_ASSOC)){
						var_dump(strtotime($row_ss['date_lastactivity']) - strtotime($row_ss['date_connexion']));*/
						$connexion_min += strtotime($periode->end) - strtotime($periode->begin);
					//}
				}

				$d = floor($connexion_min/86400);
				$_d = ($d < 10 ? '0' : '').$d;

				$h = floor(($connexion_min-$d*86400)/3600);
				$_h = ($h < 10 ? '0' : '').$h;

				$m = floor(($connexion_min-($d*86400+$h*3600))/60);
				$_m = ($m < 10 ? '0' : '').$m;

				$s = $connexion_min-($d*86400+$h*3600+$m*60);
				$_s = ($s < 10 ? '0' : '').$s;

				$hd = $_d * 24 + $_h;

				$dd = $hd.'h '.$_m.'min';
		
				$presence = number_format($connexion_min * 100 / $connexion_max,1);
				if($connexion_min)
				$presence_time = $dd;
				else
					$presence_time = '';
				var_dump($dd);exit;
				//decroche
				$agent_number = $row['agent_number'];
				if($agent_number){
					$result_s = $mysqli->query("SELECT * from call_infos WHERE agent = '".$agent_number."' and timestamp >= '".strtotime($min)."' and timestamp <= '".strtotime($max)."'");
					$nb_call_ok = 0;
					$nb_call_nok = 0;
					while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
						if($row_s['accepted'] == 'yes')$nb_call_ok ++;
						if($row_s['accepted'] == 'no'){$nb_call_nok ++;}else{
							if($row_s['accepted'] != 'yes' && $row_s['reason'] == 'NOANSWER')$nb_call_nok ++;
							if($row_s['accepted'] != 'yes' && $row_s['reason'] == 'CANCEL')$nb_call_nok ++;
							if($row_s['accepted'] != 'yes' && $row_s['reason'] == 'BUSY')$nb_call_nok ++;
						}
						
					}
					
					$result_s = $mysqli->query("SELECT * from chats WHERE to_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."'");
					while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
						if($row_s['consult_date_start'])$nb_call_ok ++;
						if(!$row_s['consult_date_start'])$nb_call_nok ++;
					}
					if(($nb_call_ok + $nb_call_nok) > 0)
					$decroche = number_format($nb_call_ok * 100 / ($nb_call_ok + $nb_call_nok),1);
				}

				//transformation
				/*$result_s = $mysqli->query("SELECT * from user_connexion WHERE user_id = '".$row['id']."' and (tchat = 1 OR phone = 1) and date_connexion >= '".$periode->begin."' and date_connexion <= '".$periode->end."'");
				*/
				$nb_consult = 0;
				$nb_visite = 0;
				//while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){*/
				foreach($tranches as $periode){	
					$result_ss = $mysqli->query("SELECT * from agent_views WHERE agent_id = '".$row['id']."' and date_view >= '".$periode->begin."' and date_view < '".$periode->end."'");
					
					while($row_ss = $result_ss->fetch_array(MYSQLI_ASSOC)){
						$nb_visite ++;
					}
			
					$result_ss = $mysqli->query("SELECT * from user_credit_history WHERE agent_id = '".$row['id']."' and date_start >= '".$periode->begin."' and date_start < '".$periode->end."'");
					
					while($row_ss = $result_ss->fetch_array(MYSQLI_ASSOC)){
						$nb_consult ++;
					}
				}
		
		
				if($nb_visite){	
					$transformation = $nb_consult.'_'.$nb_visite;
				}
				//tmc
				$result_s = $mysqli->query("SELECT AVG(seconds) as duree from user_credit_history WHERE agent_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."' and media != 'email'");
				$row_s = $result_s->fetch_array(MYSQLI_ASSOC);
				$duree = $row_s['duree'];
				$tmc = $row_s['duree'];//gmdate("i,s", $row_s['duree']);
					
				//tmc_global
				$result_s = $mysqli->query("SELECT AVG(seconds) as duree from user_credit_history WHERE date_start >= '".$min."' and date_start <= '".$max."' and media != 'email'");
				$row_s = $result_s->fetch_array(MYSQLI_ASSOC);
				$tmc_global = $row_s['duree'];
				
				
				//proportion
				$result_s = $mysqli->query("SELECT media from user_credit_history WHERE agent_id = '".$row['id']."' and date_start >= '".$min."' and date_start <= '".$max."'");
				
				$count_total = 0;
				$count_total_mail = 0;
				$count_total_tchat = 0;
				$count_total_phone = 0;
				
				while($row_s = $result_s->fetch_array(MYSQLI_ASSOC)){
					if($row_s['media'] == 'email') $count_total_mail ++;
					if($row_s['media'] == 'phone') $count_total_phone ++;
					if($row_s['media'] == 'chat') $count_total_tchat ++;
					$count_total ++;
				}
				
				if($count_total){
					$email = number_format($count_total_mail * 100 / $count_total,1);
					$tchat = number_format($count_total_tchat * 100 / $count_total,1);
					$tel = number_format($count_total_phone * 100 / $count_total,1);
				}

				//SAVE
				$result_save = $mysqli->query("SELECT id from agent_stats WHERE user_id = '".$row['id']."' and date_min = '".$min."' and date_max = '".$max."'");
				$row_save = $result_save->fetch_array(MYSQLI_ASSOC);
				if($row_save['id']){
						$mysqli->query("UPDATE agent_stats set note = '{$note}' ,presence = '{$presence}',presence_time = '{$presence_time}',decroche = '{$decroche}',transformation = '{$transformation}',tmc = '{$tmc}',tmc_global = '{$tmc_global}',email = '{$email}',tchat = '{$tchat}',tel = '{$tel}' where id = '{$row_save['id']}'");
					}else{
						$mysqli->query("INSERT INTO agent_stats(user_id, date_min,date_max,note,presence,presence_time,decroche,transformation,tmc,tmc_global,email,tchat,tel) VALUES ('{$row['id']}','{$min}','{$max}','{$note}','{$presence}','{$presence_time}','{$decroche}','{$transformation}','{$tmc}','{$tmc_global}','{$email}','{$tchat}','{$tel}')");
					}
			}
		}
	}
	
//var_dump('ok');exit;
	//ajoute 1 jour
	$dx = new DateTime($period_jour);
	$dx->modify('+ 1 days');
	$period_jour = $dx->format('Y-m-d 00:00:00');
//}
?>