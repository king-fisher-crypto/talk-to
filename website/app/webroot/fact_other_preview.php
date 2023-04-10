<?php
	session_start();

	if(!isset($_SESSION['fact_other']))exit;
	if( substr_count($_SERVER['HTTP_HOST'], 'devspi'))
		$mysqli = new mysqli($dbb_head['host'], "devspi", "8vjf3p99Sfv8M3pBYcH5JGF3", "devspi");
	else
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	mysqli_set_charset($mysqli,"utf8");

	//load data facture
	$row_invoice = $_SESSION['fact_other']['Invoices'];


	//get order society
	$result_society = $mysqli->query("SELECT * from invoice_societys WHERE id = ".$row_invoice['society_id']);
    $row_society = $result_society->fetch_array(MYSQLI_ASSOC);


	$result = $mysqli->query("SELECT * from invoice_customers WHERE id = '{$row_invoice['customer_id']}'");
    $row = $result->fetch_array(MYSQLI_ASSOC);
	


require('../Lib/invoice3.php');

$libelle = $row['name'];	
$filename_libelle = $libelle;

if(strlen($_SESSION['fact_other']['date_order']) == 10 && substr_count($_SESSION['fact_other']['date_order'],'-') )
					$dd = explode('-',$_SESSION['fact_other']['date_order']);
				else
					$dd = explode('-',date('d-m-Y'));
$row_invoice['date_order'] = $dd[2].'-'.$dd[1].'-'.$dd[0].' 12:00:00';
$date_invoice = new DateTime($row_invoice['date_order']);
$date_due = new DateTime($row_invoice['date_due']);
$date_invoice_txt = $date_invoice->format('d/m/Y');


$page = 1;
$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
$pdf->addSociete($row_society['name'],$row_society['address']."\n".$row_society['info']);
				  
$pdf->fact_dev( "Facture ", $row_invoice['order_id'] );
$pdf->addDate($date_invoice_txt);
$num = 0;
$page = 1;
$pdf->addPageNumber($page);
$address = $libelle."\n".$row['address']."\n";

if($row['info'])
	$address .= $row['info']."\n";

$pdf->addClientAdresse($address);
$pdf->addReglement($row_invoice['mode']);
$pdf->addEcheance($date_due->format('d/m/Y'));
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
$row_invoice['amount'] = 0;
$page_actual = 1;
$num = 0;
$num_h = 0;
for($nn=1;$nn<=5;$nn++){
	if($_SESSION['fact_other']['Invoices']['ProductName'.$nn]){
		$_SESSION['fact_other']['Invoices']['ProductPrice'.$nn] = str_replace(',','.',$_SESSION['fact_other']['Invoices']['ProductPrice'.$nn]);
		$line = array( "DESIGNATION"  => $_SESSION['fact_other']['Invoices']['ProductName'.$nn],
					   "P.U"      => number_format($_SESSION['fact_other']['Invoices']['ProductPrice'.$nn],2,'.',''),
					   "MONTANT" => number_format($_SESSION['fact_other']['Invoices']['ProductPrice'.$nn],2,'.',''));
		$size = $pdf->addLine( $y, $line );
		$y   += $size + 5;
		$num ++;
		$row_invoice['amount'] += $_SESSION['fact_other']['Invoices']['ProductPrice'.$nn];
		$num +=$y;
	$num_h +=$size + 5;
	$check = 125;
	//if($page_actual > 1)$check = 550;
		
	
	if($num_h > $check){
		$page_actual ++;
		$pdf->addPage();
		$page++;
		$num = 0;
		$y    = 29;
		
		$cols=array( "DESIGNATION"  => 92,
             "P.U / MIN"      => 24,
             "MONTANT" => 26
              );
		$pdf->addCols2( $cols);
		$cols=array( "DESIGNATION"  => "L",
					 "P.U / MIN"      => "R",
					 "MONTANT" => "R");
		$pdf->addLineFormat( $cols);

	}
	}
}
$vat = 0;

if($row_invoice['vat_tx']){
	$vat = $row_invoice['amount'] * number_format($row_invoice['vat_tx'],2,'.','') / 100;
	$row_invoice['vat'] = number_format($vat,2,'.','');
	
}
$row_invoice['amount_total'] = $row_invoice['amount'] + $row_invoice['vat'];					

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
                  "Remarque" => utf8_decode("Exonération de TVA - Art 259-1° du CGI") );
//$pdf->addCadreTVAs($params);

$is_tva = true;
if($row_invoice['vat_tx'])
	$label_tva = 'TVA ('.$row_invoice['vat_tx'].'%)';
else
	$label_tva = 'TVA';
$remarque = $row_invoice['remarque'];
if($row_invoice['vat']){
	$is_tva = true;
}

$remarque = nl2br($remarque);
$remarque = str_replace('€','$euro$',$remarque);
$pdf->addRemarque($remarque);


$pdf->addTVAs( $row_invoice['amount'], $row_invoice['vat'], $row_invoice['amount_total'], $is_tva, $row_invoice['deposit']);


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

$filename = 'Facture Glassgen - '.$row_invoice['order_id'].' pour '.$filename_libelle.'.pdf';
	
$pdf->addCadreSolde($sold_info);
$pdf->addCadreEurosFrancs($label_tva, $is_tva, $row_invoice['deposit']);
$pdf->Output($filename, 'I');

?>