<?php

ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$sessions = '';

$result = $mysqli->query("SELECT * from notes where birthday != ''");
$n_result = 0;
$n_homme = 0;
$n_femme = 0;
$n_date = 0;
$date = array();
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	
	
	if($row['sexe']){
		if($row['sexe'] == 'H')$n_homme ++;
		if($row['sexe'] == 'F')$n_femme ++;
		$n_result ++;
	}
	
	$dd = explode('-',$row['birthday']);
	if($dd[0] != '0000'){
		$n_date ++;
		if(empty($date[$dd[0]]))$date[$dd[0]] = 0;

		$date[$dd[0]] ++;
	}
	
}
ksort($date);
var_dump($n_result);
var_dump('H ->'.$n_homme);
var_dump('F ->'.$n_femme);
var_dump($n_date);
var_dump($date);
exit;
?>