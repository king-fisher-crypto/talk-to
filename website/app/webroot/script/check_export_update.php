<?php

//Spiriteo cost agent

set_time_limit ( 0 );
		ini_set("memory_limit",-1);


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$list_cost = array();
			
/*$result = $mysqli->query("SELECT * FROM `user_credit_history` where `is_factured` = 1 and date_start <= '2020-05-25 22:00:00' ORDER BY `user_credit_history`.`user_credit_history` ASC");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $row['user_credit_history'];		
				
				$result2 = $mysqli->query("SELECT * from export_coms where user_credit_history_id = '{$id}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if(!$row2){
					echo $id.',
					';
				}
			}*/

$result = $mysqli->query("SELECT * FROM `user_penalities` where `message_id` > 0  and date_add <= '2020-05-26 22:00:00' ORDER BY id ASC");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $row['message_id'];		
				
				$result2 = $mysqli->query("SELECT * from export_coms where media = 'email' and sessionid = '{$id}' and price < '0'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if(!$row2){
					echo $id.',
					';
				}
			}
/*
$list_expert = array();
$result = $mysqli->query("SELECT * FROM `user_credit_history` where `is_factured` = 1 and date_start >= '2020-03-31 22:00:00' and date_start < '2020-04-30 22:00:00' ORDER BY `user_credit_history`.`user_credit_history` ASC");
			while($row = $result->fetch_array(MYSQLI_ASSOC)){
				$id = $row['user_credit_history'];		
				
				if(empty($list_expert[$row['agent_id']]))$list_expert[$row['agent_id']] = 0;
				
				$result2 = $mysqli->query("SELECT * from export_coms where user_credit_history_id = '{$id}' and price >=0");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				if(!$row2){
					var_dump('user credit history missing : '.$row['user_credit_history']);
				}else{
					
					$result3 = $mysqli->query("SELECT * from user_pay where id_user_credit_history = '{$id}'");
					$row3 = $result3->fetch_array(MYSQLI_ASSOC);
					
					if($row3['price'] != $row3['price']){
						var_dump('user credit history price error : '.$row['user_credit_history']);
					}else{
						$list_expert[$row['agent_id']] += $row3['price'];
					}
				}
			}
var_dump($list_expert);*/
exit;
?>