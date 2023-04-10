<?php
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			
$list_cost = array();
			
$result = $mysqli->query("SELECT * from sms_histories where type ='ALERTE EXPERT' order by id desc limit 250");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$result2 = $mysqli->query("SELECT * from  user_state_history where user_id ='".$row['id_agent']."' and date_add > '".$row['date_add']."' and state = 'available' order by date_add limit 1 ");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$result4 = $mysqli->query("SELECT * from  user_state_history where user_id ='".$row['id_agent']."' and date_add < '".$row['date_add']."' and state = 'available' order by date_add desc limit 1");
	$row4 = $result4->fetch_array(MYSQLI_ASSOC);
	
	$result3 = $mysqli->query("SELECT * from  users where id ='".$row['id_agent']."'");
	$row3 = $result3->fetch_array(MYSQLI_ASSOC);
	
	echo $row3['id'].' - '.$row3['pseudo'].' : SMS a '.$row['date_add']. ' connecté a '.$row2['date_add'];
	
	if(!$row2['date_add'])
		echo 'JAMAIS, derniere fois dispo le '.$row4['date_add'];
		
	echo '<br />';
				
}

exit;
?>