<?php
	//ini_set('display_errors', 1);
//error_reporting(E_ALL);
	if( substr_count($_SERVER['HTTP_HOST'], 'devspi'))
		$mysqli = new mysqli($dbb_head['host'], "devspi", "8vjf3p99Sfv8M3pBYcH5JGF3", "devspi");
	else
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	mysqli_set_charset($mysqli,"utf8");
	
if(!$_GET['num'])exit;
	$invoice_id = $_GET['num'];//date($_GET['date'], strtotime('-1 month'));


require('../Lib/invoice2.php');

//get order society
	$result_society = $mysqli->query("SELECT * from invoice_societys WHERE id = 2");
    $row_society = $result_society->fetch_array(MYSQLI_ASSOC);

//load data facture
$result_invoice = $mysqli->query("SELECT I.* FROM  invoice_agents I, users U WHERE I.user_id = U.id and I.id = '{$invoice_id}'");

while($row_invoice = $result_invoice->fetch_array(MYSQLI_ASSOC)){
	
	$filename_lastname = '';
$filename_firstname = '';
$filename_period_an = '';
$filename_period_month = '';


	$page = 0;
	$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
	$page ++;
	$date_invoice = new DateTime($row_invoice['date_add']);
	$date_invoice_txt = $date_invoice->format('d/m/Y');
	
	$result = $mysqli->query("SELECT * from users WHERE id = '{$row_invoice['user_id']}'");
    $row = $result->fetch_array(MYSQLI_ASSOC);
	$libelle = '';
	if($row['societe']){
		$libelle = ($row['societe']);	
	}else{
		$libelle = $row['lastname'].' '.$row['firstname'];	
	}
	

	$filename_lastname = $row['lastname'];
	$filename_firstname = $row['firstname'];

	$pdf->AddPage();
	$pdf->addSociete($row_society['name'],$row_society['address']."\n".$row_society['info']);

	$pdf->fact_dev( "Facture ", $row_invoice['order_id'] );
	$pdf->addDate($date_invoice_txt);
	$num = 0;
	$page = 1;
	$pdf->addPageNumber($page);
	if($row['societe'] && $row['societe_adress']){
		if($row['societe_cp'])$row['societe_ville'] = $row['societe_cp'].' '.($row['societe_ville']);
		if($libelle == $row['lastname'].' '.$row['firstname'])$libelle = '';
	$address=  $libelle."\n".$row['lastname'].' '.$row['firstname']."\n" .
					  ($row['societe_adress']). ' '. ($row['societe_adress2'])."\n" .
					  ($row['societe_ville'])."\n".
					  trim(utf8_decode($row['societe_pays']))."\n";
	}else{
	$address = $libelle."\n".($row['address'])."\n" .
					  $row['postalcode'].' '.($row['city'])."\n";
					  //$row['phone_number']."\n".
					  //$row['email']."\n";
	}

	if($row['siret'] && !$row['belgium_save_num'] && !$row['belgium_society_num'] && !$row['canada_hst_id'] && !$row['spain_cif'] && !$row['luxembourg_autorisation'] && !$row['luxembourg_commerce_registrar'] && !$row['marocco_ice'] && !$row['marocco_if'] && !$row['portugal_nif'] && !$row['senegal_ninea'] && !$row['senegal_rccm'] && !$row['tunisia_rc'] )
		$address .= 'SIRET : '.$row['siret']."\n";
	if($row['belgium_save_num'])
		$address .= utf8_decode('N° d\'enregistrement : ').$row['belgium_save_num']."\n";
	if($row['belgium_society_num'])
		$address .= utf8_decode('N° d\'entreprise : ').$row['belgium_society_num']."\n";
	if($row['canada_hst_id'])
		$address .= 'HST ID : '.$row['canada_hst_id']."\n";
	if($row['spain_cif'])
		$address .= 'CIF (NIF) : '.$row['spain_cif']."\n";
	if($row['luxembourg_autorisation'])
		$address .= utf8_decode('Autorisation n° : ').$row['luxembourg_autorisation']."\n";
	if($row['luxembourg_commerce_registrar'])
		$address .= utf8_decode('Registre du commerce n° : ').$row['luxembourg_commerce_registrar']."\n";
	if($row['marocco_ice'])
		$address .= 'I.C.E : '.$row['marocco_ice']."\n";
	if($row['marocco_if'])
		$address .= 'I.F : '.$row['marocco_if']."\n";
	if($row['portugal_nif'])
		$address .= 'NIF / NIPC : '.$row['portugal_nif']."\n";
	if($row['senegal_ninea'])
		$address .= 'NINEA : '.$row['senegal_ninea']."\n";
	if($row['senegal_rccm'])
		$address .= 'RCCM : '.$row['senegal_rccm']."\n";
	if($row['tunisia_rc'])
		$address .= 'R.C : '.$row['tunisia_rc']."\n";
	if($row['vat_num'])
		$address .= 'TVA intra : '.$row['vat_num']."\n";

	$pdf->addClientAdresse($address);
	$pdf->addReglement("Virement");
	$pdf->addEcheance($date_invoice_txt);
	//$pdf->addNumTVA($row['vat_num']);
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
	$result4 = $mysqli->query("SELECT * from invoice_agent_details WHERE invoice_id = '{$row_invoice['id']}'");

	while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){


		$line = array( "DESIGNATION"  => $row4['label'],
					   "P.U"      => number_format($row4['amount'],2,'.',''),
					   "MONTANT" => number_format($row4['amount'],2,'.',''));
		$size = $pdf->addLine( $y, $line );
		$y   += $size + 2;
		$num ++;
		$check = 27;


	}
	$tot_prods = array( array ( "px_unit" => $row_invoice['amount'], "qte" => 1, "tva" => 1 ));

	$tab_tva = array( "1"       => 19.6,
					  "2"       => 5.5);
	if($row['mode_paiement'] == 'Virement'){
		$row['paypal'] = '';	
	}
	if($row['mode_paiement'] == 'Hipay Wallet'){
		$row['hipay'] = $row['paypal'];	
	}
	$params  = array( "RemiseGlobale" => 1,
						  "remise_tva"     => 1,       // {la remise s'applique sur ce code TVA}
						  "remise"         => 0,       // {montant de la remise}
						  "remise_percent" => 10,      // {pourcentage de remise sur ce montant de TVA}
					  "FraisPort"     => 1,
						  "portTTC"        => 10,      // montant des frais de ports TTC
													   // par defaut la TVA = 19.6 %
						  "portHT"         => 0,       // montant des frais de ports HT
						  "portTVA"        => $row_invoice['vat_tx'],    // valeur de la TVA a appliquer sur le montant HT
					  "AccompteExige" => 1,
						  "accompte"         => 0,     // montant de l'acompte (TTC)
						  "accompte_percent" => 15,    // pourcentage d'acompte (TTC)
					  "Remarque" => utf8_decode("Exonération de TVA - Art 259-1° du CGI"),
					  "RIB" => $row['rib'],
					  "IBAN" => $row['iban'],
					  "SWIFT" => $row['swift'],
					  "PAYPAL" => $row['paypal'],
					  "HIPAY" => $row['hipay'],
					  "BANK" => ($row['bank_name']),"BANK_ADR" => ($row['bank_country']) );
	//$pdf->addCadreTVAs($params);

	$is_tva = true;
	$label_tva = 'TVA';
	$remarque = "TVA : Auto-liquidation";//"Exonération de TVA - Art 259-1° du CGI";

	if(isset($row['country_id'])){
			 switch ($row['country_id']) {
				case 186://united kingdom
					$is_tva = true;
					$label_tva = 'VAT';
					$remarque = "VAT : Auto-liquidation";
					break;
				case 5://canada
					$is_tva = false;
					$label_tva = 'VAT';
					$remarque = "Outside scope of Irish VAT";
					break;
				case 120://maroc
					 $is_tva = false;
					$label_tva = 'VAT';
					$remarque = "Outside scope of Irish VAT";
					break;
				case 157://senegal
					$is_tva = false;
					$label_tva = 'VAT';
					$remarque = "Outside scope of Irish VAT";
					break;
				case 180://tunisie
					$is_tva = false;
					$label_tva = 'VAT';
					$remarque = "Outside scope of Irish VAT";
					break;
				case 3://suisse
					$is_tva = false;
					$label_tva = 'VAT';
					$remarque = "Outside scope of Irish VAT";
					break;
			}
		 }


	$pdf->addRemarque(utf8_decode($remarque));
	$pdf->addTVAs( $row_invoice['amount'], $row_invoice['vat'], $row_invoice['amount_total'], $is_tva);

	$sold_info = '';
	//if($row_invoice['status'] == 1)$sold_info = utf8_decode('SOLDÉ');


	$pdf->addCadreSolde($sold_info);
	$pdf->addCadreEurosFrancs($label_tva, $is_tva);
	
	$filename = 'Facture frais de service Glassgen pour '.$filename_lastname.' '.$filename_firstname.' - '.$invoice_id.'.pdf';

	$pdf->Output('facture/'.$filename, 'I');
	//exit;
}



?>