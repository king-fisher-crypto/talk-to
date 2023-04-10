<?php

//Glassgen Comptabilité
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli -> set_charset("utf8");

$fees_boi = 17;

echo 'user_id;societe;pays;num tva;preuve;status'.'<br />';
$result = $mysqli->query("SELECT * FROM `invoice_agents` where date_add >= '2020-05-01 00:00:00' group by user_id");// and order_id = 2842
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$result2 = $mysqli->query("SELECT * FROM `users` where id = '".$row['user_id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	$user_id = $row['user_id'];
	$societe = $row['society_name'];
	$num_tva = $row2['vat_num'];
	$pays = '';
	$preuve = $row2['vat_num_proof'];
	$status = '';
	
	if($row2['societe_pays']){
		$result3 = $mysqli->query("SELECT * FROM `user_country_langs` where user_countries_id = '".$row2['societe_pays']."' and lang_id = 1");
		$row3 = $result3->fetch_array(MYSQLI_ASSOC);
		$pays = $row3['name'];
	}else{
		$result3 = $mysqli->query("SELECT * FROM `user_country_langs` where user_countries_id = '".$row2['country_id']."' and lang_id = 1");
		$row3 = $result3->fetch_array(MYSQLI_ASSOC);
		$pays = $row3['name'];
	}
	
	
	if($num_tva){
		$resultat = '';
				$tva = str_replace(' ','',$num_tva);
				$iso_code = substr($tva,0,2);
				$vat = substr($tva,2);
				$url = 'http://ec.europa.eu/taxation_customs/vies/vatResponse.html?locale=FR&memberStateCode='.
				strtoupper($iso_code).'&number='.$vat.'&traderName=';
				ini_set('default_socket_timeout', 3);
				for ($i = 0; $i < 3; $i++) {
					if ($line = @file_get_contents($url)) {
						if (strstr($line, 'TVA valide')) {
							ini_restore('default_socket_timeout');
							$resultat = 'valide';
						}
						if (strstr($line, 'TVA invalide')) {
							ini_restore('default_socket_timeout');
							$resultat = 'invalide';
						}
						if (strstr($line, 'demandes trop nombreuses')) {
							ini_restore('default_socket_timeout');
							$resultat = 'Numero non verifie';
						}
						if( !$resultat ) $resultat = 'invalide';
					}
				}
				ini_restore('default_socket_timeout');
		$status = $resultat;
	}
	
	
	echo $user_id.';'.$societe.';'.$pays.';'.$num_tva.';'.$preuve.';'.$status.'<br />';
	
	
}

?>