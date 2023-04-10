<?php

//Spiriteo cost agent


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$list_cost = array();
			
$result = $mysqli->query("SELECT agent_id, sum(seconds) as time from user_credit_history where media != 'email' group by agent_id");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$agent_id = $row['agent_id'];		
				$minutes = $row['time'] / 60;
				
				$result2 = $mysqli->query("SELECT nb_minutes from cost_agents where id_agent = '{$agent_id}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if($row2['nb_minutes']){
					
					$diff = $minutes - $row2['nb_minutes'];
					if($diff > 20){
						var_dump('old -> '.$row2['nb_minutes']);
						$mysqli->query("UPDATE cost_agents set nb_minutes = '{$minutes}' where id_agent='{$agent_id}' ");
						var_dump("UPDATE cost_agents set nb_minutes = '{$minutes}' where id_agent='{$agent_id}' ");	
					}
				}
				
				
				
				
			}
exit;
?>