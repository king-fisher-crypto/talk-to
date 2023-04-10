<?php

//Spiriteo CMS

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$result = $mysqli->query("select distinct(U.id), U.credit, (select C.date_add from orders C where C.user_id = U.id and C.date_add <= '2016-10-17 23:59:59' order by C.date_add DESC Limit 1) as date_comp from users U where (select count(*) from orders C where C.user_id = U.id and C.date_add <= '2016-10-17 23:59:59' order by C.date_add DESC Limit 1) != 0 and (select count(*) from orders C where C.user_id = U.id and C.date_add > '2016-10-17 23:59:59' order by C.date_add DESC Limit 1) = 0 and U.credit > 0");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	var_dump($row['id'].' -> '.$row['credit']);
	
	//$mysqli->query("update users set credit_old = '{$row['credit']}' WHERE id = '{$row['id']}'");
	$mysqli->query("update users set credit = '0' WHERE id = '{$row['id']}'");
}
exit;
?>