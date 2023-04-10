<?php

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT * from messages where archive = 0 and private = 0 and parent_id is null and etat != 3 and date_add >= '2019-05-01 00:00:00'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	$to_id = $row['to_id'];
	
	$result2 = $mysqli->query("SELECT * from messages where parent_id = '{$row['id']}' order by id");
	$etat = 0;
	while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
		$from_id = $row2['from_id'];	
		$date = $row2['date_add']. ' GMT';
		$etat = $row2['etat'];
	};
	
	if($to_id != $from_id && $etat != 3){
		var_dump($row['id'] . ' '.$row['date_add']);
	}
}

?>