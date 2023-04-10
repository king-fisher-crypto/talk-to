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
header('Content-Disposition: attachment; filename="all_stripe_payouts.csv"');
 
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

$cond_year = 2020;
$cond_month = 03;


$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' and stripe_account != '' order by id");
while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){
	
	try {
	
		//$account = \Stripe\Account::retrieve('acct_1GAZcqGgy0meznqF');
		$payouts = \Stripe\Payout::all(['limit' => 10], ['stripe_account' => $rowagent['stripe_account']]);

		
	 }
	catch (Exception $e) {
	 //var_dump($e->getMessage());
	 }
	
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
	
	if($payouts){
		
		foreach($payouts as $payout){
			$timecreated = $payout['created'];
			$dt = new DateTime('@' . $timecreated);
			
			if($dt->format('m') == $cond_month && $dt->format('Y') == $cond_year && $payout['status'] != 'failed'){
				$paid = $payout['amount'] / 100;
				$resultinvoice = $mysqli->query("SELECT * from  invoice_agents WHERE user_id = '".$rowagent['id']."' and date_add like '".$dt->format('Y').'-'.$dt->format('m')."-%'");
				$rowinvoice = $resultinvoice->fetch_array(MYSQLI_ASSOC);
				$num_fact = $rowinvoice['id'];
				if(!$num_fact){
					$resultinvoice = $mysqli->query("SELECT * from  invoice_agents WHERE user_id = '".$rowagent['id']."' and paid_total_valid ='".$paid."'");
					$rowinvoice = $resultinvoice->fetch_array(MYSQLI_ASSOC);
					$num_fact = $rowinvoice['id'];
				}
				
				$line = array($dt->format('Y'), $list_month[$dt->format('m')],$rowagent['id'],$rowagent['pseudo'],$rowagent['lastname'].' '.$rowagent['firstname'],$address.' '.$postalcode.' '.$city,$country, $rowagent['siret'],$paid,$payout['currency'], $payout['status'],$num_fact );
				fputcsv($file, $line);
			}
		}
	}	
	
}
exit;

?>