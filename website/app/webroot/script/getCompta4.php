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


$resultcomm = $mysqli->query("SELECT sum(ca) , ca_currency from  user_credit_history WHERE date_start >= '".$date_debut ."' and date_start <= '".$date_fin."' and is_factured = 1   group by ca_currency");
var_dump("SELECT sum(ca) , ca_currency from  user_credit_history WHERE date_start >= '".$date_debut ."' and date_start <= '".$date_fin."' and is_factured = 1   group by ca_currency");
var_dump("SELECT sum(ca) , ca_currency from user_credit_history WHERE date_start >= '2020-09-30 22:00:00' and date_start <= '2020-10-31 22:59:59' and is_factured = 1 and type_pay = 'pre' group by ca_currency");
var_dump("SELECT sum(ca) , ca_currency from user_credit_history WHERE date_start >= '2020-09-30 22:00:00' and date_start <= '2020-10-31 22:59:59' and is_factured = 1 and type_pay = 'aud' group by ca_currency");
/*
	$rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC);
var_dump($rowcomm);
*/
?>