<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

$mysqli -> set_charset("utf8");

$date_debut = '2020-09-30 22:00:00';
$date_fin = '2020-10-31 22:59:59';

$fact_date_debut = '2020-11-01 00:00:00';
$fact_date_fin = '2020-11-31 22:59:59';

$ca = 0;


$resultfact = $mysqli->query("SELECT * from  invoice_agents WHERE date_add >= '".$fact_date_debut."' and date_add <= '".$fact_date_fin."' order by id");
var_dump("SELECT * from  invoice_agents WHERE date_add >= '".$fact_date_debut."' and date_add <= '".$fact_date_fin."' and date_min <= '".$date_debut."' order by id");
while($rowfact = $resultfact->fetch_array(MYSQLI_ASSOC)){
	
	$resultcomm = $mysqli->query("SELECT U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids from  user_credit_history U, user_pay P WHERE U.date_start >= '".$rowfact['date_min']."' and U.date_start < '".$rowfact['date_max']."' and U.is_factured = 1 and U.agent_id = '".$rowfact['user_id']."' and P.id_user_credit_history = U.user_credit_history order by U.user_credit_history");
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
		$ca += $rowcomm['ca'];
	}
}
var_dump('CA facturé : '.number_format($ca,2));

?>