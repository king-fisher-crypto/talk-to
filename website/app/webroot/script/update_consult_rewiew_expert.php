<?php

//Spiriteo EXPERTS

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT * from users where role = 'agent' order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	
	$reviews_avg = '';
	$reviews_nb = 0;
	$consults_nb = 0;
	
	$r = $mysqli->query("SELECT AVG(pourcent) AS avisAVG, count(review_id) as NB FROM reviews WHERE agent_id = ".$row['id']." and status = 1 and parent_id IS NULL");
	$row2 = $r->fetch_array(MYSQLI_ASSOC);
	if($row2['avisAVG']) $reviews_avg = number_format($row2['avisAVG'],1);
	if($row2['NB']) $reviews_nb = $row2['NB'];
	
	$r = $mysqli->query("SELECT count(user_credit_history) as NB FROM user_credit_history WHERE agent_id = ".$row['id']."");
	$row2 = $r->fetch_array(MYSQLI_ASSOC);
	if($row2['NB']) $consults_nb = $row2['NB'];
	//var_dump("UPDATE users set reviews_avg = '".$reviews_avg ."',reviews_nb = '".$reviews_nb ."', consults_nb = '".$consults_nb ."' where id = ".$row['id']." ");
	$mysqli->query("UPDATE users set reviews_avg = '".$reviews_avg ."',reviews_nb = '".$reviews_nb ."', consults_nb = '".$consults_nb ."' where id = ".$row['id']." ");
}
exit;
?>