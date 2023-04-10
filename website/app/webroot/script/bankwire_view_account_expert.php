<?php
ini_set("memory_limit",-1);
set_time_limit ( 0 );
ini_set('display_errors', 1);
error_reporting(E_ALL);

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

//Spiriteo Export Payouts
$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");

header('Content-type: text/csv');
header('Content-Disposition: attachment; filename="all_bankwire_payouts.csv"');
 
// do not cache the file
header('Pragma: no-cache');
header('Expires: 0');
 
// create a file pointer connected to the output stream
$file = fopen('php://output', 'w');
 
// send the column headers
fputcsv($file, array('Year','Month', 'ID agent', 'Pseudo agent', 'Name Agent', 'Address Agent','Pays', 'Siret', 'Amount', 'Currency', 'Paid', 'No Facture'));

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
	'12' => 'Decembre'
);

$dt = new DateTime('2020-02-01');

$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' and stripe_account = '' order by id");
while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
	
	$resultinvoice = $mysqli->query("SELECT * from  invoice_agents WHERE user_id = '".$rowagent['id']."' and date_add like '".$dt->format('Y').'-'.$dt->format('m')."-%' and status = 1");
	$rowinvoice = $resultinvoice->fetch_array(MYSQLI_ASSOC);
	
	
	if($rowinvoice){
		$num_fact = $rowinvoice['id'];
		
		$address = $rowagent['societe_adress']. ' '.$rowagent['societe_adress2'];
		if(!$address) $address = $rowagent['address'];

		$postalcode = $rowagent['societe_cp'];
		if(!$postalcode) $postalcode = $rowagent['postalcode'];

		$city = $rowagent['societe_ville'];
		if(!$city) $city = $rowagent['city'];

		$country = $rowagent['societe_pays'];
		if(!$country){
			$resultcountry = $mysqli->query("SELECT * from user_country_langs WHERE lang_id = 1 and user_countries_id = ".$rowagent['country_id']);
			$rowcountry= $resultcountry->fetch_array(MYSQLI_ASSOC);
			$country = $rowcountry['name'];
		}
	
		$paid = $rowinvoice['paid_total'];
		$line = array($dt->format('Y'), $list_month[$dt->format('m')],$rowagent['id'],$rowagent['pseudo'],$rowagent['lastname'].' '.$rowagent['firstname'],$address.' '.$postalcode.' '.$city,$country, $rowagent['siret'],$paid,'eur', $rowinvoice['status'],$num_fact );
		fputcsv($file, $line);
	}	
	
}
exit;

?>