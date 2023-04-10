<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);
//Spiriteo STATS retroactif
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

$mysqli -> set_charset("utf8");

/* Crédits antérieurs  utilisés au cours du mois */

$date_debut = '2020-10-31 23:00:00';
$date_fin = '2020-11-30 22:59:59';

$fact_date_debut = '2020-12-01 00:00:00';
$fact_date_fin = '2020-12-31 22:59:59';
/*
$remuneration = 0;
$remuneration_nofact = 0;
$ca = 0;
$n_line = 0;
$list_agents = array();
$resultcomm = $mysqli->query("SELECT U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids, U.agent_id from  user_credit_history U, user_pay P WHERE U.date_start >= '".$date_debut ."' and U.date_start <= '".$date_fin."' and U.is_factured = 1 and P.id_user_credit_history = U.user_credit_history  order by U.user_credit_history");//and U.agent_id = 2819
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
		
		
		//check if facture
		$resultfact = $mysqli->query("SELECT * from  invoice_agents WHERE date_add >= '".$fact_date_debut."' and date_add <= '".$fact_date_fin."' and user_id = '".$rowcomm['agent_id']."' ");
		$rowfact = $resultfact->fetch_array(MYSQLI_ASSOC);
		
		if(!$rowfact){
			$remuneration_nofact += number_format($rowcomm['price'],2);
		}else{
			$remuneration += number_format($rowcomm['price'],2);
			array_push($list_agents,$rowcomm['agent_id']);
		}
		
		
		
		$n_line ++;
	}
$list_agents = array_unique($list_agents);
sort($list_agents);
//var_dump($list_agents);
var_dump('remuneration no fact: '.number_format($remuneration_nofact,2));
var_dump('remuneration: '.number_format($remuneration,2));
var_dump('nb comms: '.number_format($n_line,0));

$resultfact = $mysqli->query("SELECT * from  invoice_agents WHERE date_add >= '".$fact_date_debut."' and date_add <= '".$fact_date_fin."'");
while($rowfact = $resultfact->fetch_array(MYSQLI_ASSOC)){
	
	if(!in_array($rowfact['user_id'],$list_agents ))
		var_dump($rowfact['user_id']);
	
}*/

$resultcomm = $mysqli->query("SELECT U.user_credit_history, U.type_pay,U.ca as ca_devise,U.ca_currency, P.ca, P.price, U.ca_ids, U.agent_id from  user_credit_history U, user_pay P WHERE U.date_start >= '".$date_debut ."' and U.date_start <= '".$date_fin."' and U.is_factured = 1 and P.id_user_credit_history = U.user_credit_history  order by U.user_credit_history");//and U.agent_id = 2819
	while($rowcomm = $resultcomm->fetch_array(MYSQLI_ASSOC)){
		
		$resultexport = $mysqli->query("SELECT * from   export_coms WHERE user_credit_history_id = '".$rowcomm['user_credit_history']."' and price > 0 ");
        $rowexport = $resultexport->fetch_array(MYSQLI_ASSOC);
		
		if(!$rowexport){
			var_dump('Comms ID : '.$rowcomm['user_credit_history']);
		}else{
			$price_fact = number_format($rowcomm['price'],2);
			$price_export = number_format($rowexport['price'],2);
			
			if($price_fact != $price_export)
				var_dump('Comms ID Price Fail : '.$rowcomm['user_credit_history']);
			
		}
		
	}
?>