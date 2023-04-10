<?php

//Spiriteo EXPERTS
ini_set("memory_limit",-1);
set_time_limit ( 0 );
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");


$result = $mysqli->query("SELECT * from users where role = 'agent' order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	
	$name = strtolower(str_replace(' ','-',$row['pseudo']));
	$number = $row['agent_number'];
	$url = '/agents/'.$name.'-'.$number;
	
	if($number){
	
		$url_fr_old = '/fre'.$url;
		$url_fr_new = 'https://fr.spiriteo.com/fre/agents-en-ligne/'.$name.'-'.$number;
		$mysqli->query("INSERT INTO  redirects (type,old,new) VALUES ('301','".$url_fr_old."','".$url_fr_new."')");

		$url_fr_old = '/frb'.$url;
		$url_fr_new = 'https://be.spiriteo.com/frb/agents-en-ligne/'.$name.'-'.$number;
		$mysqli->query("INSERT INTO  redirects (type,old,new) VALUES ('301','".$url_fr_old."','".$url_fr_new."')");

		$url_fr_old = '/frc'.$url;
		$url_fr_new = 'https://ca.spiriteo.com/frc/agents-en-ligne/'.$name.'-'.$number;
		$mysqli->query("INSERT INTO  redirects (type,old,new) VALUES ('301','".$url_fr_old."','".$url_fr_new."')");

		$url_fr_old = '/frl'.$url;
		$url_fr_new = 'https://lu.spiriteo.com/frl/agents-en-ligne/'.$name.'-'.$number;
		$mysqli->query("INSERT INTO  redirects (type,old,new) VALUES ('301','".$url_fr_old."','".$url_fr_new."')");

		$url_fr_old = '/frs'.$url;
		$url_fr_new = 'https://ch.spiriteo.com/frs/agents-en-ligne/'.$name.'-'.$number;
		$mysqli->query("INSERT INTO  redirects (type,old,new) VALUES ('301','".$url_fr_old."','".$url_fr_new."')");
	}
}
exit;
?>