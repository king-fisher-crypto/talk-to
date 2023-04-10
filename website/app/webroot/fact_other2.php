<?php
	session_start();
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
	if(!isset($_GET['id']))exit;
	if( substr_count($_SERVER['HTTP_HOST'], 'devspi'))
		$mysqli = new mysqli($dbb_head['host'], "devspi", "8vjf3p99Sfv8M3pBYcH5JGF3", "devspi");
	else
		$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	mysqli_set_charset($mysqli,"utf8");

	//load data facture
	$result_invoice = $mysqli->query("SELECT * FROM  invoice_others WHERE id = '{$_GET['id']}'");
	$row_invoice = $result_invoice->fetch_array(MYSQLI_ASSOC);


	//get order society
	$result_society = $mysqli->query("SELECT * from invoice_societys WHERE id = ".$row_invoice['society_id']);
    $row_society = $result_society->fetch_array(MYSQLI_ASSOC);


	$result = $mysqli->query("SELECT * from invoice_customers WHERE id = '{$row_invoice['customer_id']}'");
    $row = $result->fetch_array(MYSQLI_ASSOC);
	


require('../Lib/invoice4.php');

$libelle = $row['name'];	
$filename_libelle = $libelle;

$date_invoice = new DateTime($row_invoice['date_order']);
$date_due = new DateTime($row_invoice['date_due']);
$date_invoice_txt = $date_invoice->format('d/m/Y');
$date_due_txt = $date_due->format('d/m/Y');

$page = 1;
$pdf = new PDF_Invoice( 'P', 'mm', 'A4' );
$pdf->AddPage();
if($row_invoice['society_id'] == 2)
	$pdf->addLogo('https://www.glassgen.com/img/logo.jpg');
else
	$pdf->addLogo('https://www.zconnect-worldwide.com/img/logo.jpg');


$pdf->addSociete($row_society['name'],$row_society['address']."\n".$row_society['info']);

$pdf->addFactureInfo( $row_invoice['order_id'], $date_invoice_txt, $row['customer'], $row['phone'], $row['mail'], $row['id'], $row['info'],$date_due_txt );

$num = 0;
$page = 1;

$address = $libelle."\n".$row['address']."\n";

$pdf->addClientAdresse($address);

$cols=array("Pos."  => 15, 
			"Item"  => 100,
			"Qty"      => 25,
             "Unit price"      => 24,
             "Total" => 26
              );
$pdf->addCols( $cols);

$cols=array( "Pos."  => "L",
             "Item"      => "L",
             "Qty"      => "R",
             "Unit price"      => "R",
             "Total" => "R");
$pdf->addLineFormat( $cols);

	  
$y    = 109;
$result4 = $mysqli->query("SELECT * from invoice_other_details WHERE invoice_id = '{$row_invoice['id']}' order by id");
$page_actual = 1;
$num = 109;
$num_h = 0;
$n_prod = 1;
$amount = 0;
while($row4 = $result4->fetch_array(MYSQLI_ASSOC)){
	
	$qty = $row4['qty'];
	$amount += $row4['amount'] * $qty;
	$line = array( 
					"Pos."  => $n_prod,
					"Item"  => $row4['label'],
					"Qty"  => $qty,
				   "Unit price"      => number_format($row4['amount'],4,'.',','),
				   "Total" => number_format($row4['amount'] * $qty,4,'.',','));
	$size = $pdf->addLine( $y, $line );
	$n_prod ++;
	$y   += $size + 5;
 	$num +=$y;
	$num_h +=$size + 5;
	$check = 125;
	//if($page_actual > 1)$check = 200;
		
	
	if($num_h > $check){
		$page_actual ++;
		$pdf->addPage();
		$page++;
		$num = 0;
		$num_h = 0;
		$y    = 29;
		
		$cols=array("Pos."  => 15, 
			"Item"  => 100,
			"Qty"      => 25,
             "Unit price"      => 24,
             "Total" => 26
              );
		$pdf->addCols2( $cols);
		$cols=array( "Pos."  => "L",
             "Item"      => "L",
             "Qty"      => "R",
             "Unit price"      => "R",
             "Total" => "R");
		$pdf->addLineFormat( $cols);

	}
}

$size = $pdf->addColEnd($y);
$y   = $size + 5;


$is_tva = true;
if($row_invoice['vat_tx'])
	$label_tva = 'VAT ('.$row_invoice['vat_tx'].'%)';
else
	$label_tva = 'VAT';

if($row_invoice['vat']){
	$is_tva = true;
}

$pdf->addCadreTotal($y,$label_tva, $is_tva, number_format($row_invoice['deposit'],2,'.',','),number_format($amount,2,'.',','), number_format($row_invoice['vat'],2,'.',','), number_format($row_invoice['amount_total'],2,'.',','));

$remarque = $row_invoice['remarque'];
$remarque = nl2br($remarque);
$remarque = str_replace('€','$euro$',$remarque);
$conditions = $row_invoice['conditions'];
$conditions = nl2br($conditions);
$conditions = str_replace('€','$euro$',$conditions);
$pdf->addRemarque($conditions,$remarque,$y);

$pdf->Footer(utf8_decode('Zconnect Limited - N°5, 17/F, Strand 50, 50 Bonham Strand, Sheung Wan, HONG KONG.
Email:  contact@zconnect-worldwide.com - https://www.zconnect-worldwide.com    
Banking details: HK and Shanghai Banking Corp Ltd - EUR: BIC HSBCHKHH - Account number 848718219838
'));

$filename = 'Invoice - '.$row_invoice['order_id'].' pour '.$filename_libelle.'.pdf';
$pdf->Output($filename, 'I');

?>