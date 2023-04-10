<?php

//Spiriteo cost agent

set_time_limit ( 0 );
		ini_set("memory_limit",-1);


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$list_cost = array();
			
$result = $mysqli->query("SELECT * from export_coms order by id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $row['user_credit_history_id'];		
				
				$result2 = $mysqli->query("SELECT * from user_pay where id_user_credit_history = '{$id}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if($row2['order_cat_index']){
					$result3 = $mysqli->query("SELECT * from costs where id = '{$row2['order_cat_index']}'");
					$row3 = $result3->fetch_array(MYSQLI_ASSOC);
					
					$tx_minute = $row3['cost'];
					$tx_second = $tx_minute / 60;

					if($tx_minute > 0){
						$mysqli->query("UPDATE export_coms set tx_minute = '{$tx_minute}',tx_second = '{$tx_second}' where id='{$row['id']}' ");

					}
				}
				
				
				
				
			}
exit;
?>