<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

$mysqli -> set_charset("utf8");


//CA enregistré en database ( basé sur experts facturé + facture fusioné )

$date_debut = '2020-09-30 22:00:00';
$date_fin = '2020-10-31 22:59:59';

$fact_date_debut = '2020-11-01 00:00:00';
$fact_date_fin = '2020-11-31 22:59:59';

$CA = 0;


$resultfact = $mysqli->query("SELECT * from  invoice_agents WHERE date_add >= '".$fact_date_debut."' and date_add <= '".$fact_date_fin."' order by id");
while($rowfact = $resultfact->fetch_array(MYSQLI_ASSOC)){
	$CA += $rowfact['ca'];
	
	
}
var_dump('CA : '.number_format($CA,2));


?>