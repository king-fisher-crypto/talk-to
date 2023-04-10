<?php
set_time_limit(0);
//Spiriteo CMS


	//$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
var_dump(date('Y-m-d H:i:s'));

$date_bonus = new DateTime("now", new DateTimeZone('UTC') );
     	$annee_bonus = $date_bonus->format('Y');
		$mois_bonus = $date_bonus->format('m');

var_dump($date_bonus->format('Y-m-d H:i:s'));

	
	/*$result = $mysqli->query("SELECT * from carts WHERE date_add < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE carts SET date_add = '$dd' WHERE id = '{$row['id']}'");
		//var_dump("UPDATE carts SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from carts WHERE date_add >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE carts SET date_add = '$dd' WHERE id = '{$row['id']}'");
		//var_dump("UPDATE carts SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from chats WHERE date_start < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_start']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chats SET date_start = '$dd' WHERE id = '{$row['id']}'");
		
		$tab = explode(' ',$row['date_end']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chats SET date_end = '$dd' WHERE id = '{$row['id']}'");
		
		if($row['consult_date_start']){
			$tab = explode(' ',$row['consult_date_start']);
			$tab2 = explode('-', $tab[0]);
			$tab3 = explode(':', $tab[1]);
			
			$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
			$timestamp += 7200; 
			$dd = date('Y-m-d H:i:s',$timestamp);
		
			$mysqli->query("UPDATE chats SET consult_date_start = '$dd' WHERE id = '{$row['id']}'");
		}
		if($row['consult_date_end']){
			$tab = explode(' ',$row['consult_date_end']);
			$tab2 = explode('-', $tab[0]);
			$tab3 = explode(':', $tab[1]);
			
			$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
			$timestamp += 3600; 
			$dd = date('Y-m-d H:i:s',$timestamp);
		
			$mysqli->query("UPDATE chats SET consult_date_end = '$dd' WHERE id = '{$row['id']}'");
		}
	}
	
	$result = $mysqli->query("SELECT * from chats WHERE date_start >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_start']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chats SET date_start = '$dd' WHERE id = '{$row['id']}'");
		
		$tab = explode(' ',$row['date_end']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chats SET date_end = '$dd' WHERE id = '{$row['id']}'");
		
		if($row['consult_date_start']){
			$tab = explode(' ',$row['consult_date_start']);
			$tab2 = explode('-', $tab[0]);
			$tab3 = explode(':', $tab[1]);
			
			$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
			$timestamp += 3600; 
			$dd = date('Y-m-d H:i:s',$timestamp);
		
			$mysqli->query("UPDATE chats SET consult_date_start = '$dd' WHERE id = '{$row['id']}'");
		}
		if($row['consult_date_end']){
			$tab = explode(' ',$row['consult_date_end']);
			$tab2 = explode('-', $tab[0]);
			$tab3 = explode(':', $tab[1]);
			
			$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
			$timestamp += 3600; 
			$dd = date('Y-m-d H:i:s',$timestamp);
		
			$mysqli->query("UPDATE chats SET consult_date_end = '$dd' WHERE id = '{$row['id']}'");
		}
	}*/
	
	/*
	$result = $mysqli->query("SELECT * from chat_events WHERE date_add < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chat_events SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from chat_events WHERE date_add >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chat_events SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from chat_messages WHERE date_add < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chat_messages SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from chat_messages WHERE date_add >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE chat_messages SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from orders WHERE date_add < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE orders SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from orders WHERE date_add >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE orders SET date_add = '$dd' WHERE id = '{$row['id']}'");
	}
	
	$result = $mysqli->query("SELECT * from order_hipaytransactions WHERE date_add < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE order_hipaytransactions SET date_add = '$dd' WHERE cart_id = '{$row['cart_id']}'");
	}
	
	$result = $mysqli->query("SELECT * from order_hipaytransactions WHERE date_add >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE order_hipaytransactions SET date_add = '$dd' WHERE cart_id = '{$row['cart_id']}'");
	}
	
	$result = $mysqli->query("SELECT * from order_paypaltransactions WHERE date_add < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE order_paypaltransactions SET date_add = '$dd' WHERE cart_id = '{$row['cart_id']}'");
	}
	
	$result = $mysqli->query("SELECT * from order_paypaltransactions WHERE date_add >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_add']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE order_paypaltransactions SET date_add = '$dd' WHERE cart_id = '{$row['cart_id']}'");
	}
	
	$result = $mysqli->query("SELECT * from user_credit_history WHERE date_start < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_start']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_history SET date_start = '$dd' WHERE user_credit_history = '{$row['user_credit_history']}'");
		
		$tab = explode(' ',$row['date_end']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_history SET date_end = '$dd' WHERE user_credit_history = '{$row['user_credit_history']}'");
	}
	
	$result = $mysqli->query("SELECT * from user_credit_history WHERE date_start >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_start']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_history SET date_start = '$dd' WHERE user_credit_history = '{$row['user_credit_history']}'");
		
		$tab = explode(' ',$row['date_end']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_history SET date_end = '$dd' WHERE user_credit_history = '{$row['user_credit_history']}'");
	}
	
	$result = $mysqli->query("SELECT * from user_credit_last_histories WHERE date_start < '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_start']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_last_histories SET date_start = '$dd' WHERE user_credit_last_history = '{$row['user_credit_last_history']}'");
		
		$tab = explode(' ',$row['date_end']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 7200; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_last_histories SET date_end = '$dd' WHERE user_credit_last_history = '{$row['user_credit_last_history']}'");
	}
	
	$result = $mysqli->query("SELECT * from user_credit_last_histories WHERE date_start >= '2015-10-25'");
	while($row = $result->fetch_array(MYSQLI_ASSOC)){
		
		$tab = explode(' ',$row['date_start']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_last_histories SET date_start = '$dd' WHERE user_credit_last_history = '{$row['user_credit_last_history']}'");
		
		$tab = explode(' ',$row['date_end']);
		$tab2 = explode('-', $tab[0]);
		$tab3 = explode(':', $tab[1]);
		
		$timestamp = mktime($tab3[0],$tab3[1],$tab3[2],$tab2[1],$tab2[2],$tab2[0]);
		$timestamp += 3600; 
		$dd = date('Y-m-d H:i:s',$timestamp);
	
		$mysqli->query("UPDATE user_credit_last_histories SET date_end = '$dd' WHERE user_credit_last_history = '{$row['user_credit_last_history']}'");
	}
	*/
?>