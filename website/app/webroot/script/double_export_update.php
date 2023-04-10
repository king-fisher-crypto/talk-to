<?php

//Spiriteo cost agent

set_time_limit ( 0 );
		ini_set("memory_limit",-1);


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$list_cost = array();
			
$result = $mysqli->query("SELECT * from export_coms order by user_credit_history_id asc");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $row['user_credit_history_id'];		
				
				$result2 = $mysqli->query("SELECT * from export_coms where user_credit_history_id = '{$id}' and id != '".$row['id']."'");
				while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){
					$mysqli->query("DELETE FROM export_coms where id='{$row2['id']}' ");
				}
			}
exit;
?>