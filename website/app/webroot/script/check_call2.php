<?php

//Spiriteo CMS
ini_set('display_errors', 1);
error_reporting(E_ALL);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$sessions = '';
$result = $mysqli->query("SELECT * from call_infos WHERE agent != '' and time_end != '' and callinfo_id > 290000");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from user_credit_last_histories WHERE sessionid like '{$row['sessionid']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
		if(!$row2['agent_id']){
			$row['diff'] = $row['time_end'] - $row['time_start'];
			$row['timestamp'] = date('Y-m-d H:i:s',$row['timestamp']);
			$row['time_start'] = date('Y-m-d H:i:s',$row['time_start']);
			$row['time_end'] = date('Y-m-d H:i:s',$row['time_end']);
			var_dump($row);
			$sessions .= $row['sessionid'].'<br />';
		}
			
}

exit;
?>