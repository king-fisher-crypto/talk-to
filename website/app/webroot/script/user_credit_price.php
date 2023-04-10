<?php

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT credit,id from users WHERE role = 'client' and credit > 0 order by id");

while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT * from user_credits WHERE users_id = '{$row['id']}' and product_id > 0 order by id desc limit 1");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$price = 0;
	
	$result3 = $mysqli->query("SELECT * from products WHERE id = '{$row2['product_id']}' ");
	$row3 = $result3->fetch_array(MYSQLI_ASSOC);
	
	$devise = 1;
	
	
	
	if($row3['country_id'] == 13){
		$devise = 0.67;
	}
	
	$price = number_format($row3['cout_min'],2) / 60;
	
	$mysqli->query("INSERT INTO user_credit_prices(id_user_credit, user_id, price, seconds, seconds_left,date_add,date_upd,status) VALUES ('{$row2['id']}','{$row['id']}','{$price}','{$row2['credits']}','{$row['credit']}',NOW(),NOW(),'0')");
		
}

?>