<?php

//Spiriteo CA
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set("memory_limit",-1);
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$result = $mysqli->query("SELECT * from invoice_agents order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
  	$society_name = '';
	$society_address = '';
	$society_postalcode = '';
	$society_city = '';
	$society_country = '';
	$society_num = '';
	
	$result2 = $mysqli->query("SELECT * from users where id = '".$row['user_id']."'");
	$row2 = $result2->fetch_array(MYSQLI_ASSOC);
	
	
	
	if($row2['societe']){
		$society_name = ($row2['societe'])."\n".$row2['lastname'].' '.$row2['firstname'];	
	}else{
		$society_name = $row2['lastname'].' '.$row2['firstname'];	
	}
	
	if($row2['societe'] && $row2['societe_adress']){
		
		$result3 = $mysqli->query("SELECT * from user_country_langs where user_countries_id = '".$row2['societe_pays']."' and lang_id = 1");
		$row3 = $result3->fetch_array(MYSQLI_ASSOC);
		
		$society_address = $row2['societe_adress'].' '.$row2['societe_adress2'];
		$society_postalcode = $row2['societe_cp'];
		$society_city = $row2['societe_ville'];
		
		if($row3)
			$society_country = $row3['name'];
		
	}else{
		
		$result3 = $mysqli->query("SELECT * from user_country_langs where user_countries_id = '".$row2['country_id']."' and lang_id = 1");
		$row3 = $result3->fetch_array(MYSQLI_ASSOC);
		
		$society_address = $row2['address'];
		$society_postalcode = $row2['postalcode'];
		$society_city = $row2['city'];
		if($row3)
			$society_country = $row3['name'];
	}
	
	
	if($row2['siret'] && !$row2['belgium_save_num'] && !$row2['belgium_society_num'] && !$row2['canada_id_hst'] && !$row2['spain_cif'] && !$row2['luxembourg_autorisation'] && !$row2['luxembourg_commerce_registrar'] && !$row2['marocco_ice'] && !$row2['marocco_if'] && !$row2['portugal_nif'] && !$row2['senegal_ninea'] && !$row2['senegal_rccm'] && !$row2['tunisia_rc'] )
	$society_num .= 'SIRET : '.$row2['siret']."\n";
if($row2['belgium_save_num'])
	$society_num .= utf8_decode('recording N° : ').$row2['belgium_save_num']."\n";
if($row2['belgium_society_num'])
	$society_num .= utf8_decode('Society N° : ').$row2['belgium_society_num']."\n";
if($row2['canada_id_hst'])
	$society_num .= 'HST ID : '.$row2['canada_id_hst']."\n";
if($row2['spain_cif'])
	$society_num .= 'CIF (NIF) : '.$row2['spain_cif']."\n";
if($row2['luxembourg_autorisation'])
	$society_num .= utf8_decode('Authorization n° : ').$row2['luxembourg_autorisation']."\n";
if($row2['luxembourg_commerce_registrar'])
	$society_num .= utf8_decode('The commercial register n° : ').$row2['luxembourg_commerce_registrar']."\n";
if($row2['marocco_ice'])
	$society_num .= 'I.C.E : '.$row2['marocco_ice']."\n";
if($row2['marocco_if'])
	$society_num .= 'I.F : '.$row2['marocco_if']."\n";
if($row2['portugal_nif'])
	$society_num .= 'NIF / NIPC : '.$row2['portugal_nif']."\n";
if($row2['senegal_ninea'])
	$society_num .= 'NINEA : '.$row2['senegal_ninea']."\n";
if($row2['senegal_rccm'])
	$society_num .= 'RCCM : '.$row2['senegal_rccm']."\n";
if($row2['tunisia_rc'])
	$society_num .= 'R.C : '.$row2['tunisia_rc']."\n";
if($row2['vat_num'])
	$society_num .= 'VAT : '.$row2['vat_num']."\n";
	
	$mysqli->query("UPDATE invoice_agents set society_name = '".addslashes($society_name) ."',society_address = '".addslashes($society_address) ."',society_postalcode = '".addslashes($society_postalcode) ."',society_city = '".addslashes($society_city) ."',society_country = '".addslashes($society_country) ."',society_num = '".addslashes($society_num) ."' where id = ".$row['id']." ");
	
}
exit;
?>