<?php

//Spiriteo user

ini_set('display_errors', 1); 
error_reporting(E_ALL);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$result = $mysqli->query("SELECT * from users WHERE role = 'agent'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$agent_id = $row['id'];
	$agent_pseudo = addslashes($row['pseudo']);

	$mysqli->query("update agent_pseudos set pseudo = '{$agent_pseudo}' WHERE user_id = '{$agent_id}'");
}
exit;
?>