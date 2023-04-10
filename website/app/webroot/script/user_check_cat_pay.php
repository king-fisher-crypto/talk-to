<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT * from cost_agents");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$min_agent = $row['nb_minutes'];
	$id_agent = $row['id_agent'];
	
	$result2 = $mysqli->query("SELECT sum(seconds) as nb from user_credit_history where agent_id=".$id_agent." and media != 'email' and is_factured = 1 ");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	$nb_seconds = $row2['nb'];
	
	$nb_minute_comp = number_format($nb_seconds / 60,2);
	
	if($nb_minute_comp != $min_agent){
		echo $id_agent.' : '. $min_agent. ' pour ca calculé -> '.$nb_minute_comp.'<br />';
	}
}
?>