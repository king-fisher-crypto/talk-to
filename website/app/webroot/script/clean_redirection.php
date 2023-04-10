<?php

//Spiriteo cost agent
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			


//is new comm
$result = $mysqli->query("SELECT * from redirections order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	
	$result2 = $mysqli->query("SELECT * from redirections where id < '{$row['id']}' and ip = '{$row['ip']}' and domain = '{$row['domain']}'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if($row2['ip']){
		$mysqli->query("DELETE FROM redirections where id = '{$row2['id']}'");
	}
	
	
		
}
exit;
?>