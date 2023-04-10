<?php


$dir = '/var/www/spiriteo/www/';//irname(__FILE__);

ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$tranche_min = 0;
			$tranche_max = 8;




$date_add = '2019-12-24 08:03:20';
$timezone = 'America/Toronto';

//$date_script = '2019-12-24 11:00:00';

$mail_date = "2019-12-24 03:03:20";//CakeTime::format(Tools::dateZoneUser($timezone,$date_add),'%Y-%m-%d %H:%M:%S');
$check_date = "2019-12-24 09:00:00";//CakeTime::format(Tools::dateZoneUser($timezone,$date_script),'%Y-%m-%d %H:%M:%S');






if($timezone == 'Europe/Paris'){
							$time_mail = 6;
						}else{
							$time_mail = 6;
							if($timezone == 'America/Toronto'){
								$dateTimezoneUser1 = new DateTimeZone('Europe/Paris');
								$dateTimeUser = new DateTime($mail_date);
								$off = $dateTimezoneUser1->getOffset($dateTimeUser);
								$time_plus = 	($off / 60 ) / 60  ;
								if($time_plus < 2)$time_mail = 7;//rester sur 6 heure dec
								//else
									//$time_mail = 13;
							}
						}

$datehour = new DateTime($check_date);
						$heure_check = $datehour->format('H');

						$night_send = 0;
						if($heure_check >= $tranche_min && $heure_check <= $tranche_max){
							$night_send = 1;
						}
var_dump($night_send);
						$datehour = new DateTime($mail_date);
						$datehour->modify('+ '.$time_mail.' hour');
						$heure_check = $datehour->format('H');

						/*$night = 0;
						if($heure_check >= $tranche_min && $heure_check < $tranche_max){
							$night = 1;
						}*/

						//add hour night
						$add_hour = 0;
						$cumul_hour = 0;
						$cut  = 0;
						//if($night){
							$dx = new DateTime($mail_date);
							for($nnn = 1; $nnn <= 8;$nnn++){
								$dx->modify('+1 hour');
								$hour = $dx->format('H');
								if($hour >= $tranche_min && $hour <= $tranche_max){
									$add_hour ++;
									$cut = 1;
								}else{
									if(!$cut)
									$cumul_hour ++;
								}
							}

						if($add_hour >= 2 )$add_hour = $add_hour+1;//bordure des heure de nuit
						//}
						//var_dump($add_hour);
						//var_dump($cumul_hour);
						if($add_hour > 0 && $cumul_hour > 0 && $cumul_hour < 6){
							$add_hour = $add_hour + (6 - $cumul_hour);
						}

						$datetime1 = new DateTime($mail_date);
						$date_pour_mail = $datetime1->format('d-m-Y H').'h'.$datetime1->format('i').'min'.$datetime1->format('s').'s';

						
						$datetime1->modify('+ '.$add_hour.' hour');
						$mail_date = $datetime1->format('Y-m-d H:i:s');
						$mail_date_penality = $datetime1->format('Y-m-d H:i:s');
						

						$datetime1 = new DateTime($mail_date);
						$datetime2 = new DateTime($check_date);	
						$date_comp = $datetime1->format('YmdHis');

						$datetime3 = new DateTime($mail_date_penality);
						$date_comp_penality = $datetime3->format('YmdHis');

						$interval = $datetime1->diff($datetime2);
						$diff_heure = $interval->format('%H');
						$diff_jour  = $interval->format('%D');
						$diff_minute  = $interval->format('%I');
						$diff_second  = $datetime2->getTimestamp() - $datetime1->getTimestamp();
						$dx = new DateTime($check_date);
						$nb_hour = -2;
						$dx->modify($nb_hour.' hour');
						$date_sms = $dx->format('YmdHis');
						$dx = new DateTime($check_date);
						$nb_hour = -3;
						$dx->modify($nb_hour.' hour');
						$date_agent = $dx->format('YmdHis');

						//calcul date mail perdu
						$dx = new DateTime($check_date);
						$heure_check = $dx->format('H');
						$night = 0;



						if($heure_check >= $tranche_min && $heure_check < $tranche_max){
							$night = 1;
						}



						$dx = new DateTime($check_date);
						$nb_hour = -6;
						$dx->modify($nb_hour.' hour');
						$date_admin = $dx->format('YmdHis');

var_dump($date_sms .'>='. $date_comp);
if($date_sms >= $date_comp) echo ' SEND SMS';
echo'<br />';echo'<br />';
var_dump($date_agent .'>='. $date_comp);
if($date_agent >= $date_comp) echo ' SEND EMAIL';
var_dump($diff_heure);
echo'<br />';echo'<br />';
var_dump($date_admin .'>='. $date_comp_penality);
if($date_admin >= $date_comp_penality) echo ' SEND PENALITY';
exit;
?>