<?php
//exit;
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$data = \Stripe\Account::update(
						  'acct_1Fm3DkIUCh1ZjfcD',
						  [
							 'country' => 'ES',
							  
							  
							/*  'company' => [
								'address' => [
									'city' => $data['city'],
									'country' => $data['country_company'],
									'line1' => $data['line1'],
									'line2' => $data['line2'],
									'postal_code' => $data['postal_code'],
									'state' => ''
								],	
								'vat_id' => $data['vat_id'],
								'tax_id' => $data['tax_id']
							],
							  'external_account' => [
								'object' => 'bank_account',
								'country' => 'FR',
								'currency' => 'EUR',
								'account_number' => 'FR7630003014600005007653004',
							],*/
						  ]
						);
	var_dump($data);




$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' and stripe_account != '' order by id");
while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){


try {
	$account = \Stripe\Account::retrieve($rowagent['stripe_account']);
	$persons = \Stripe\Account::allPersons(
				  'acct_1GHw9VBwM1eRq5je',
				  ['limit' => 3]
				);
	$person = $persons->data[0];
	//var_dump($person->id);
		\Stripe\Account::updatePerson(
		  $rowagent['stripe_account'],
		  $person->id,
		  ['relationship' => ['representative' => true]]
		);
	
	/*$data = \Stripe\Account::update(
						  'acct_1GHw9VBwM1eRq5je',
						  [
							 'email' => $data['person_email'],
							  
							  
							  'company' => [
								'address' => [
									'city' => $data['city'],
									'country' => $data['country_company'],
									'line1' => $data['line1'],
									'line2' => $data['line2'],
									'postal_code' => $data['postal_code'],
									'state' => ''
								],	
								'vat_id' => $data['vat_id'],
								'tax_id' => $data['tax_id']
							],
							  'external_account' => [
								'object' => 'bank_account',
								'country' => 'FR',
								'currency' => 'EUR',
								'account_number' => 'FR7630003014600005007653004',
							],
						  ]
						);
	var_dump($data);*/
 }
	catch (Exception $e) {
	 var_dump($e->getMessage());
	 }

}
			
exit;

?>