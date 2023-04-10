<?php
set_time_limit ( 0 );
		ini_set("memory_limit",-1);
//ini_set('display_errors', 1); 
//error_reporting(E_ALL); 

	if(isset($_GET['c']))$id_coms = base64_decode($_GET['c']);
	if(!isset($id_coms))exit;

	$list_com = explode('#',$id_coms);
	$id_com_min = $list_com[0];
	$id_com_max = $list_com[1];
	if(!$id_com_min)exit;
	if(!$id_com_max)exit;

	if($id_com_min > $id_com_max){
		$tmp_min = $id_com_min;
		$id_com_min = $id_com_max;
		$id_com_max = $tmp_min;
	}

	if( substr_count($_SERVER['HTTP_HOST'], 'devspi'))
		$mysqli = new mysqli($dbb_head['host'], "devspi", "8vjf3p99Sfv8M3pBYcH5JGF3", "devspi");
	else
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	mysqli_set_charset($mysqli,"utf8");
	$result = $mysqli->query("SELECT * from invoice_accounts WHERE 	user_credit_last_history = '{$id_com_max}'");
    $row = $result->fetch_array(MYSQLI_ASSOC);
	
	if(!$row['id'])exit;

	$result_agent = $mysqli->query("SELECT * from users WHERE id = '{$row['agent_id']}'");
    $row_agent = $result_agent->fetch_array(MYSQLI_ASSOC);


	if($row['vat_id']){
		$result_vat = $mysqli->query("SELECT * from invoice_vats WHERE id = '{$row['vat_id']}'");
		$row_vat = $result_vat->fetch_array(MYSQLI_ASSOC);
		$vat_rate = $row_vat['rate'];
		$vat_description = $row_vat['description'];
		$show_vat_num = $row_vat['show_vat_num'];
		$show_siret = $row_vat['show_siret'];
	}else{
		$vat_rate = 0;
		$vat_description = '';
		$show_vat_num = 1;
		$show_siret = 1;
	}

	require('../Lib/invoice_account.php');
	$libelle_top = utf8_decode('Facture émise par Glassgen Ltd au nom de :')."\n";
	if($row_agent['societe']){
		$libelle = $row_agent['societe'];	
	}else{
		$libelle = $row_agent['lastname'].' '.$row_agent['firstname'];	
	}

//boucle sur toutes les factures
$page = 0;
$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$result = $mysqli->query("SELECT * from invoice_accounts WHERE 	user_credit_last_history >= '{$id_com_min}' and user_credit_last_history <= '{$id_com_max}' and agent_id = '{$row['agent_id']}'");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
$page ++;

		$result_user = $mysqli->query("SELECT * from users WHERE id = '{$row['user_id']}'");
		$row_user = $result_user->fetch_array(MYSQLI_ASSOC);

		$result_user_country = $mysqli->query("SELECT * from user_country_langs WHERE user_countries_id = '{$row_user['country_id']}' and lang_id =1");
		$row_user_country = $result_user_country->fetch_array(MYSQLI_ASSOC);


	$date_invoice = new DateTime($row['date_add']);
	$date_invoice_txt = $date_invoice->format('d/m/Y');
$address = '';
	
	
	$pdf->AddPage();
	if($row_agent['societe'] && $row_agent['societe_adress']){
	if($row_agent['societe_cp'] && !$row_agent['societe_ville'])$row_agent['societe_ville'] = $row_agent['societe_cp'].' '.($row_agent['societe_ville']);
	$address .= $row_agent['lastname'].' '.$row_agent['firstname']."\n" .
                  ($row_agent['societe_adress']). ' '. ($row_agent['societe_adress2'])."\n" .
				  ($row_agent['societe_ville'])."\n".
                  trim($row['societe_pays'])."\n";
}else{
$address .= utf8_decode($row_agent['address'])."\n" .
                  $row_agent['postalcode'].' '.($row_agent['city'])."\n".
                  $row_agent['phone_number']."\n";
				 // $row_agent['email']."\n";
}

if($row_agent['belgium_save_num'])
	$address .= utf8_decode('N° d\'enregistrement : ').$row_agent['belgium_save_num']."\n";
if($row_agent['belgium_society_num'])
	$address .= utf8_decode('N° d\'entreprise : ').$row_agent['belgium_society_num']."\n";
if($row_agent['canada_hst_id'])
	$address .= 'HST ID : '.$row_agent['canada_hst_id']."\n";
if($row_agent['spain_cif'])
	$address .= 'CIF (NIF) : '.$row_agent['spain_cif']."\n";
if($row_agent['luxembourg_autorisation'])
	$address .= 'Autorisation n° : '.$row_agent['luxembourg_autorisation']."\n";
if($row_agent['luxembourg_commerce_registrar'])
	$address .= 'Registre du commerce n° : '.$row_agent['luxembourg_commerce_registrar']."\n";
if($row_agent['marocco_ice'])
	$address .= 'I.C.E : '.$row_agent['marocco_ice']."\n";
if($row_agent['marocco_if'])
	$address .= 'I.F : '.$row_agent['marocco_if']."\n";
if($row_agent['portugal_nif'])
	$address .= 'NIF / NIPC : '.$row_agent['portugal_nif']."\n";
if($row_agent['senegal_ninea'])
	$address .= 'NINEA : '.$row_agent['senegal_ninea']."\n";
if($row_agent['senegal_rccm'])
	$address .= 'RCCM : '.$row_agent['senegal_rccm']."\n";
if($row_agent['tunisia_rc'])
	$address .= 'R.C : '.$row_agent['tunisia_rc']."\n";


if($show_siret)$address .= 'SIRET : '.$row_agent['siret']."\n";
if($show_vat_num)$address .= 'TVA intra : '.$row_agent['vat_num']."\n";
$pdf->addSociete( $libelle_top,$libelle,$address);

	$pdf->fact_dev( "Facture ", "SP".str_pad($row['order_id'], 8, "0", STR_PAD_LEFT) );
	$pdf->addDate($date_invoice_txt);
	$num = 0;
	$page = 1;
	$pdf->addPageNumber($page);
	
	if($row_user['firstname'] == 'AUDIOTEL Belgique fixe')$row_user['firstname'] = 'AUDIOTEL Belgique';
if($row_user['firstname'] == 'AUDIOTEL Belgique Mob')$row_user['firstname'] = 'AUDIOTEL Belgique';
if($row_user['firstname'] == 'AUDIOTEL Canada fixe')$row_user['firstname'] = 'AUDIOTEL Canada';
if($row_user['firstname'] == 'AUDIOTEL Canada mobile Bell')$row_user['firstname'] = 'AUDIOTEL Canada';
if($row_user['firstname'] == 'AUDIOTEL Canada mobile Telus')$row_user['firstname'] = 'AUDIOTEL Canada';
if($row_user['firstname'] == 'AUDIOTEL Canada mobile Videotron')$row_user['firstname'] = 'AUDIOTEL Canada';
if($row_user['firstname'] == 'AUDIOTEL Canada mobile Rogers')$row_user['firstname'] = 'AUDIOTEL Canada';
	
	$pdf->addClientAdresse( $row_user['firstname']);//."\n".$row_user_country['name']."\n"

	$pdf->addReglement("Virement");
	$pdf->addEcheance($date_invoice_txt);
	//if($show_vat_num)
	//$pdf->addNumTVA($row_agent['vat_num']);

	$pdf->addReference(" ");
	$cols=array( "DESIGNATION"  => 140,
				 "P.U"      => 24,
				 "MONTANT" => 26
				  );
	$pdf->addCols( $cols);
	$cols=array( "DESIGNATION"  => "L",
				 "P.U"      => "R",
				 "MONTANT" => "R");
	$pdf->addLineFormat( $cols);
	$y    = 109;


		$line = array( "DESIGNATION"  => utf8_decode($row['product']),
					   "P.U"      => number_format($row['amount'],2,'.',''),
					   "MONTANT" => number_format($row['amount'],2,'.',''));
		$size = $pdf->addLine( $y, $line );
		$y   += $size + 2;
		$num ++;
		$check = 27;


	$tot_prods = array( array ( "px_unit" => $row['amount'], "qte" => 1, "tva" => 1 ));
	/*					
	$tab_tva = array( "1"       => 19.6,
					  "2"       => 5.5);
	$params  = array( "RemiseGlobale" => 1,
						  "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
						  "remise"         => 0,       // {montant de la remise}
						  "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
					  "FraisPort"     => 1,
						  "portTTC"        => 10,      // montant des frais de ports TTC
													   // par defaut la TVA = 19.6 %
						  "portHT"         => 0,       // montant des frais de ports HT
						  "portTVA"        => $vat_rate,    // valeur de la TVA a appliquer sur le montant HT
					  "AccompteExige" => 1,
						  "accompte"         => 0,     // montant de l'acompte (TTC)
						  "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
					  "Remarque" => $vat_description,
					  "RIB" => $row['rib'],
					  "IBAN" => $row['iban'],
					  "SWIFT" => $row['swift'],
					  "PAYPAL" => $row['paypal'],
					  "HIPAY" => $row['hipay'],
					  "BANK" => utf8_decode($row['bank_name']),"BANK_ADR" => utf8_decode($row['bank_country']) );
	$pdf->addCadreTVAs($params);*/
	$pdf->addRemarque($vat_description);
	switch ($row['currency']) {
							case '€':
								$currency = 'EUR';
								break;
							case '$':
								$currency = '$';
								break;
							case 'CHF':
								$currency = 'CHF';
								break;
						}


	$pdf->addTVAs( $row['amount'], $row['vat_amount'], $row['total_amount'],$currency);
	$pdf->addCadreSolde($sold_info);
	$pdf->addCadreEurosFrancs($row['vat_tx']);
}

$filename = 'Factures.pdf';
$pdf->Output($filename, 'I');

?>