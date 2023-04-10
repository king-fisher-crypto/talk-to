<?php

//Spiriteo agent cat

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$result = $mysqli->query("SELECT * from category_user WHERE category_id = '5'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$agent_id = $row['user_id'];

	$mysqli->query("INSERT INTO  category_user (user_id,category_id)  VALUES ('{$agent_id}','27')");
}
exit;
?>