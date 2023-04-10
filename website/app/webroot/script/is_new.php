<?php

//Spiriteo cost agent


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			

//is new order


/*$result = $mysqli->query("SELECT * from orders order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT * from orders where user_id = '{$row['user_id']}' and id < '{$row['id']}'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if(!$row2)
		$mysqli->query("UPDATE orders SET is_new = 1 where id = '{$row['id']}'");
		
}*/

//is new comm
$result = $mysqli->query("SELECT * from user_credit_history order by user_credit_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['user_id'] == 286 || $row['user_id'] == 3630 || $row['user_id'] == 3631 || $row['user_id'] == 3632 || $row['user_id'] == 3633 || $row['user_id'] == 3634 || $row['user_id'] == 3635 || $row['user_id'] == 3636 || $row['user_id'] == 3637 || $row['user_id'] == 3638 ){
		$result2 = $mysqli->query("SELECT * from user_credit_history where phone_number = '{$row['phone_number']}' and user_credit_history < '{$row['user_credit_history']}'");
			
		}else{
			$result2 = $mysqli->query("SELECT * from user_credit_history where user_id = '{$row['user_id']}' and user_credit_history < '{$row['user_credit_history']}'");
		}
	
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	if(!$row2)
		$mysqli->query("UPDATE user_credit_history SET is_new = 1 where user_credit_history = '{$row['user_credit_history']}'");
		
}
exit;
?>