<?php
/**
 * Export to PHP Array plugin for PHPMyAdmin
 * @version 0.2b
 */

//
// verification que toutes les comms existe en factu experts
//

ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");

$result = $mysqli->query("SELECT * FROM `export_coms` where price = 0 and date_start >= '2021-03-31 22:00:00'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT * from user_pay where id_user_credit_history = '".$row['user_credit_history_id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$mysqli->query("update `export_coms` set price = '".$row2['price']."', ca_euro = '".$row2['ca']."' where id = '".$row['id']."'");
}
var_dump('ok');exit;



//check doublons
$result = $mysqli->query("SELECT P.ca,P.price, C.user_credit_history,C.sessionid, C.is_factured from user_credit_history C, user_pay P where C.date_start >= '2021-02-28 23:00:00' and P.id_user_credit_history = C.user_credit_history and C.is_factured = 1 order by user_credit_history ");

while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT count(user_credit_history_id) as nb from export_coms where user_credit_history_id = '".$row['user_credit_history']."' and price > 0");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	if($row2['nb'] > 1){
		var_dump($row['sessionid']);
	}
	
	/*if($row2){
		var_dump("UPDATE export_coms set ca_euro = '".$row['ca'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");
		$mysqli->query("UPDATE export_coms set ca_euro = '".$row['ca'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");
	}*/
	//$mysqli->query("UPDATE export_coms set ca_euro = '".$row['ca'] ."', price = '".$row['price'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");
	//var_dump("UPDATE export_coms set ca_euro = '".$row['ca'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");exit;
}

var_dump('ok');exit;

$result = $mysqli->query("SELECT P.ca,P.price, C.user_credit_history, C.is_factured from user_credit_history C, user_pay P where C.date_start >= '2021-02-28 23:00:00' and P.id_user_credit_history = C.user_credit_history and C.is_factured = 1 order by user_credit_history ");

while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from export_coms where user_credit_history_id = '".$row['user_credit_history']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	if(!$row2){
		var_dump($row['user_credit_history']);
	}
	
	/*if($row2){
		var_dump("UPDATE export_coms set ca_euro = '".$row['ca'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");
		$mysqli->query("UPDATE export_coms set ca_euro = '".$row['ca'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");
	}*/
	//$mysqli->query("UPDATE export_coms set ca_euro = '".$row['ca'] ."', price = '".$row['price'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");
	//var_dump("UPDATE export_coms set ca_euro = '".$row['ca'] ."' where user_credit_history_id = '{$row['user_credit_history']}'");exit;
}

exit;

/*$result = $mysqli->query("SELECT * from export_coms where date_start >= '2019-02-28 05:00:00'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from user_pay_v2 where id_user_credit_history = '".$row['user_credit_history_id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	if(!$row2){
		$mysqli->query("UPDATE export_coms set ca_euro = '0', ca_chf = '0',ca_dollar = '0',price = '0' where user_credit_history_id = '{$row['user_credit_history_id']}'");
	}
	
}*/



/*$result = $mysqli->query("SELECT * FROM `user_credit_history` where date_start >= '2020-12-31 23:00:00' and date_start <= '2021-01-31 22:59:59' and agent_id = 20418 and is_factured = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from export_coms where user_credit_history_id = '".$row['user_credit_history']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	if(!$row2){
		var_dump($row['user_credit_history']);
	}
}*/


/*$result = $mysqli->query("SELECT * FROM `export_coms` where credits < 0 and ca_euro > 0");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	
	$price = $row['price'] * -1;
	$ca_euro = 0;
	$ca_chf = 0;
	$ca_dollar = 0;
	
	$mysqli->query("update `export_coms` set price = '".$price."',ca_euro = '".$ca_euro."',ca_chf = '".$ca_chf."',ca_dollar = '".$ca_dollar."'  where id = '".$row['id']."'");
}*/

$result = $mysqli->query("SELECT * FROM `export_coms` where credits < 0 and price = 12");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	
	$price = $row['price'] * -1;
	$ca_euro = $row['ca_euro'] * -1;
	$ca_chf = $row['ca_chf'] * -1;
	$ca_dollar = $row['ca_dollar'] * -1;
	
	$mysqli->query("update `export_coms` set price = '".$price."',ca_euro = '".$ca_euro."',ca_chf = '".$ca_chf."',ca_dollar = '".$ca_dollar."'  where id = '".$row['id']."'");
}