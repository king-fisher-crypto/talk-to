<?php
require_once( '../../Config/database.php' );

$db = new DATABASE_CONFIG();
$dbb_route = $db->default;
$mysqli = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);


$id = $_POST['id'];
$status = $_POST['status'];

if(is_numeric($id) ){
	
		
	//get data
	$result = $mysqli->query("SELECT * from user_credit_history WHERE user_credit_history = '{$id}'");
	$row = $result->fetch_array(MYSQLI_ASSOC);
	if($row['user_credit_history']){
		$user_id = $row['user_id'];
		$agent_id = $row['agent_id'];
		$date_start = $row['date_start'];
		$media = $row['media'];
		$resultlast = $mysqli->query("SELECT * from user_credit_last_histories WHERE users_id = '{$user_id}' and agent_id = '{$agent_id}' and date_start = '{$date_start}'");
		$rowlast = $resultlast->fetch_array(MYSQLI_ASSOC);
		
		$id_last = $rowlast['user_credit_last_history'];
		
		
		$mysqli->query("UPDATE user_credit_history set is_sold = '{$status}' where user_credit_history = '{$id}'");	
		$mysqli->query("UPDATE user_credit_last_histories set is_sold = '{$status}' where user_credit_last_history = '{$id_last}'");
			
		
		
		echo json_encode('enregistre');
	}else{
		echo json_encode('erreur');		
	}
}else{
	echo json_encode('erreur');	
}




?>