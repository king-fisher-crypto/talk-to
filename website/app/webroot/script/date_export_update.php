<?php

//Spiriteo cost agent

set_time_limit ( 0 );
		ini_set("memory_limit",-1);


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$list_cost = array();
			
$result = $mysqli->query("SELECT * from export_coms order by user_credit_history_id desc");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $row['user_credit_history_id'];		
				
				$result2 = $mysqli->query("SELECT date_start, date_end from user_credit_history where user_credit_history = '{$id}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if($row2['date_start']){

						$mysqli->query("UPDATE export_coms set date_start = '{$row2['date_start']}',date_end = '{$row2['date_end']}' where id='{$row['id']}' ");

				}
				
				
				
				
			}
exit;
?>