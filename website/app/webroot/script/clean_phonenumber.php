<?php

//Spiriteo CMS

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$sessions = '';
$result = $mysqli->query("SELECT * from call_infos");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from user_credit_last_histories WHERE sessionid = '{$row['sessionid']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if($row2['user_credit_last_history'])
	$mysqli->query("update  user_credit_last_histories set phone_number = '{$row['callerid']}' WHERE user_credit_last_history = '{$row2['user_credit_last_history']}'");
	
	$result3 = $mysqli->query("SELECT * from user_credit_history WHERE sessionid = '{$row['sessionid']}'");
    $row3 = $result3->fetch_array(MYSQLI_ASSOC);
	if($row3['user_credit_history'])
	$mysqli->query("update  user_credit_history set phone_number = '{$row['callerid']}' WHERE user_credit_history = '{$row3['user_credit_history']}'");		
}
exit;
?>