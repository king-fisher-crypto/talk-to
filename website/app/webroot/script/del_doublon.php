<?php

//Spiriteo clean communication duplicate
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$list_duplicate = array();
$result = $mysqli->query("SELECT * from user_credit_last_histories where sessionid != '' order by user_credit_last_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from user_credit_last_histories WHERE sessionid = '{$row['sessionid']}' and user_credit_last_history != '{$row['user_credit_last_history']}' and media = '{$row['media']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
		if($row2['user_credit_last_history'] && !in_array($row['sessionid'],$list_duplicate)){
			var_dump($row['sessionid']);
			var_dump("delete from user_credit_last_histories WHERE user_credit_last_history = '{$row2['user_credit_last_history']}'");
			$mysqli->query("delete from user_credit_last_histories WHERE user_credit_last_history = '{$row2['user_credit_last_history']}'");
			array_push($list_duplicate,$row['sessionid']); 
		}
}

$list_duplicate = array();
$result = $mysqli->query("SELECT * from user_credit_history where sessionid != '' order by user_credit_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from user_credit_history WHERE sessionid = '{$row['sessionid']}' and user_credit_history != '{$row['user_credit_history']}' and media = '{$row['media']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
		if($row2['user_credit_history'] && !in_array($row['sessionid'],$list_duplicate)){
			var_dump($row['sessionid']);
			var_dump("delete from user_credit_history WHERE user_credit_history = '{$row2['user_credit_history']}'");
			$mysqli->query("delete from user_credit_history WHERE user_credit_history = '{$row2['user_credit_history']}'");
			array_push($list_duplicate,$row['sessionid']); 
		}
}

$list_duplicate = array();
$result = $mysqli->query("SELECT * from user_pay where id_user_credit_history != '' order by id_user_credit_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$result2 = $mysqli->query("SELECT * from user_pay WHERE id_user_credit_history = '{$row['id_user_credit_history']}' and id_user_pay != '{$row['id_user_pay']}'");
    $row2 = $result2->fetch_array(MYSQLI_ASSOC);
		if($row2['id_user_pay'] && !in_array($row['id_user_credit_history'],$list_duplicate)){
			var_dump($row['id_user_credit_history']);
			var_dump("delete from user_pay WHERE id_user_pay = '{$row2['id_user_pay']}'");
			$mysqli->query("delete from user_pay WHERE id_user_pay = '{$row2['id_user_pay']}'");
			array_push($list_duplicate,$row['id_user_credit_history']); 
		}
}

exit;
?>