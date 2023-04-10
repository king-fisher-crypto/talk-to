<?php

//Spiriteo EXPERTS
ini_set("memory_limit",-1);
set_time_limit ( 0 );
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT * from users where role = 'agent' order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	
	$result1 = $mysqli->query("SELECT * from  invoice_vats where country_id = '{$row['country_id']}' and society_type_id = '{$row['society_type_id']}' order by id limit 1");
	$row1 = $result1->fetch_array(MYSQLI_ASSOC);
	
	
	$mysqli->query("UPDATE users set invoice_vat_id = '{$row1['id']}' where id='{$row['id']}'");
}
exit;
?>