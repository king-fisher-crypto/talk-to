<?php

//Spiriteo CMS

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$sessions = '';
$result = $mysqli->query("SELECT * from call_infos WHERE agent!= '' and time_end != '' ");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from user_credit_last_histories WHERE sessionid like '{$row['sessionid']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
		if(!$row2['agent_id']){
			$row['diff'] = $row['time_end'] - $row['time_start'];
			$row['timestamp'] = date('Y-m-d H:i:s',$row['timestamp']);
			$row['time_start'] = date('Y-m-d H:i:s',$row['time_start']);
			$row['time_end'] = date('Y-m-d H:i:s',$row['time_end']);
			//var_dump($row);
			$sessions .= $row['sessionid'].'<br />';
		}
			
}

if($sessions){

	$to = 'system@web-sigle.fr;degrefinance@gmail.com';
	$subject = 'URGENT CALL BUG';

	$headers = "From: Spiriteo <contact@web-sigle.fr>\r\n";
	$headers .= "Reply-To: contact@web-sigle.fr\r\n";
	//$headers .= "CC: degrefinance@gmail.com\r\n";
	$headers .= "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$message = '<html><body>';
	$message .= '<h1>Appels a debuguer :</h1><br />';
	$message .= $sessions;
	$message .= '</body></html>';

	mail($to, $subject, $message, $headers);
}
exit;
?>