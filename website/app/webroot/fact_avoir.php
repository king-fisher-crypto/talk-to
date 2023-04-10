<?php
	session_start();
	if(isset($_GET['id']))$_SESSION['fact_id'] = $_GET['id'];
	if(!isset($_SESSION['fact_id']))exit;
	
	if( substr_count($_SERVER['HTTP_HOST'], 'devspi'))
		$mysqli = new mysqli($dbb_head['host'], "devspi", "8vjf3p99Sfv8M3pBYcH5JGF3", "devspi");
	else
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	mysqli_set_charset($mysqli,"utf8");

	$result_fact = $mysqli->query("SELECT * from  invoice_voucher_agents WHERE id = '{$_SESSION['fact_id']}'");
    $row_invoice = $result_fact->fetch_array(MYSQLI_ASSOC);

$result_fact2 = $mysqli->query("SELECT * from  invoice_agents WHERE id = '{$row_invoice['invoice_id']}'");
    $row_invoice2 = $result_fact2->fetch_array(MYSQLI_ASSOC);

	//get order society
	$result_society = $mysqli->query("SELECT * from invoice_societys WHERE id = 2");
    $row_society = $result_society->fetch_array(MYSQLI_ASSOC);


	$result = $mysqli->query("SELECT * from users WHERE id = '{$row_invoice['user_id']}'");
    $row = $result->fetch_array(MYSQLI_ASSOC);
	
	if(!$row['id'])exit;

$dmin = new DateTime($row_invoice['date_add']);
	$date_filename = clone($dmin);

require('../Lib/invoice2_us.php');
$filename_lastname = '';
$filename_firstname = '';
$filename_period_an = '';
$filename_period_month = '';
$libelle = '';
if($row['societe']){
	$libelle = ($row['societe']);	
}else{
	$libelle = $row['lastname'].' '.$row['firstname'];	
}
$filename_lastname = $row['lastname'];
$filename_firstname = $row['firstname'];
$filename_period_an = $date_filename->format('Y');
$filename_period_month = $date_filename->format('m');


$date_invoice = new DateTime($row_invoice['date_add']);
$date_invoice_txt = $date_invoice->format('d/m/Y');

$page = 1;
$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
$pdf->addSociete($row_society['name'],$row_society['address']."\n".$row_society['info']);
				  
$pdf->fact_dev( "CREDIT NOTE ", $row_invoice['id'] );
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
	if($row_invoice2['order_id']){
    $line = array( "DESIGNATION"  => utf8_decode('Avoir sur facture numéro '.$row_invoice2['order_id']),
				   "P.U"      => number_format($row_invoice['amount'],2,'.',''),
				   "MONTANT" => number_format($row_invoice['amount'],2,'.',''));
		$size = $pdf->addLine( $y, $line );
	$y   += $size + 2;
 	$num ++;
  }else{
   if($row_invoice['invoice_id'] == -1){
    	$line = array( "DESIGNATION"  => utf8_decode('Over charge from period 06/2019 to 03/2020'),
				   "P.U"      => number_format($row_invoice['amount'],2,'.',''),
				   "MONTANT" => number_format($row_invoice['amount'],2,'.',''));
		$size = $pdf->addLine( $y, $line );
		$y   += $size + 2;
		$num ++;
   }else{
		if($row_invoice['invoice_id'] == -2){
			$result_line = $mysqli->query("SELECT * from  invoice_voucher_agent_details WHERE invoice_voucher_id = '{$row_invoice['id']}'");
   			while($row_line = $result_line->fetch_array(MYSQLI_ASSOC)){
				$line = array( "DESIGNATION"  => utf8_decode('To adjust IN '.$row_line['invoice_order_id'].', being unit price overbilled at '.$row_line['old_amount'].' EUR instead of  '.$row_line['new_amount'].' EUR'),
				   "P.U"      => number_format($row_line['unit_price'],2,'.',''),
				   "MONTANT" => number_format($row_line['unit_price'],2,'.',''));
				$size = $pdf->addLine( $y, $line );
				$y   += $size + 2;
				$num ++;
			}
			
		}
		
		
		
	}
  }
	
	
	$check = 27;
	
	
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
$is_tva = false;
if(ceil($row_invoice['vat']) > 0)$is_tva = true;
$vat_tx = 0;
if($is_tva)$vat_tx = 23;
$pdf->addRemarque(utf8_decode($remarque));
$pdf->addTVAs( $row_invoice['amount'], $row_invoice['vat'], $row_invoice['amount_total'], $is_tva,$vat_tx );

$sold_info = '';
//if($row_invoice['status'] == 1)$sold_info = utf8_decode('SOLDÉ');

$filename_period_an = $date_filename->format('Y');
$filename_period_month = $date_filename->format('m');
$list_month = array(
'01' => 'Janvier',
	'02' => 'Fevrier',
	'03' => 'Mars',
	'04' => 'Avril',
	'05' => 'Mai',
	'06' => 'Juin',
	'07' => 'Juillet',
	'08' => 'Aout',
	'09' => 'Septembre',
	'10' => 'Octobre',
	'11' => 'Novembre',
	'12' => 'Decembre',

);
$filename_period_month = $list_month[$filename_period_month];

$filename = 'Credit note Glassgen for '.$filename_lastname.' '.$filename_firstname. ' '.$filename_period_month. ' '.$filename_period_an.'.pdf';
	
$pdf->addCadreSolde($sold_info);
$pdf->addCadreEurosFrancs($label_tva, $is_tva,$vat_tx );
$pdf->Output($filename, 'I');

?>