<?php


$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$result = $mysqli->query("SELECT * from redirects order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if(substr_count($row['old'], '/fre/' )){
		$domain_id =19;
	}
	if(substr_count($row['old'], '/frb/' )){
		$domain_id = 11;
	}
	if(substr_count($row['old'], '/frs/' )){
		$domain_id = 13;
	}
	if(substr_count($row['old'], '/frl/' )){
		$domain_id = 22;
	}
	if(substr_count($row['old'], '/frc/' )){
		$domain_id = 29;
	}
	
	$mysqli->query("UPDATE redirects set domain_id = '".$domain_id."' where id = '".$row['id']."'");
	
}

?>