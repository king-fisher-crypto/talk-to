<?php
exit;
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


//creer compte associé
$result = $mysqli->query("SELECT * from users WHERE role='agent' and active = 1 and id = 14549");// and stripe_account is NULL 
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$dob = explode('-',$row['birthdate']);
	$cpt_country = '';
	switch ($row['country_id']) {
		case 1:
			$cpt_country = 'FR';//France
			break;
		case 2:
			$cpt_country = 'BE';//Belgique
			break;
		case 3:
			$cpt_country = 'CH';//Suisse
			break;
		case 4:
			$cpt_country = 'LU';//Luxembourg
			break;
		case 60:
			$cpt_country = 'ES';//Espagne
			break;
		case 145:
			$cpt_country = 'PT';//Portugal
			break;
	}
	
	switch ($row['country_id']) {
		case 1:
			$iso_country = 'FR';//France
			break;
		case 2:
			$iso_country = 'BE';//Belgique
			break;
		case 3:
			$iso_country = 'CH';//Suisse
			break;
		case 4:
			$iso_country = 'LU';//Luxembourg
			break;
		case 5:
			$iso_country = 'CA';//Canada
			break;
		case 60:
			$iso_country = 'ES';//Espagne
			break;
		case 120:
			$iso_country = 'MA';//Morocco
			break;
		case 145:
			$iso_country = 'PT';//Portugal
			break;
		case 157:
			$iso_country = 'SN';//Senegal
			break;
		case 180:
			$iso_country = 'TN';//Tunisia
			break;
	}
	$iso_country_societe = '';
	switch (strtolower($row['societe_pays'])) {
		case 'france':
			$iso_country_societe = 'FR';//France
			break;
		case 'belgique':
			$iso_country_societe = 'BE';//Belgique
			break;
		case 'suisse':
			$iso_country_societe = 'CH';//Suisse
			break;
		case 'luxembourg':
			$iso_country_societe = 'LU';//Luxembourg
			break;
		case 'espagne':
			$iso_country_societe = 'ES';//Espagne
			break;
		case 'portugal':
			$iso_country_societe = 'PT';//Portugal
			break;
		case 'bulgarie':
			$iso_country_societe = 'BG';//Portugal
			break;
		case 'canada':
			$iso_country_societe = 'CA';//Portugal
			break;
	}
	
	
	if(!$iso_country || !$cpt_country){
		$iso_country_societe = 'FR';
		$iso_country = 'FR';
		$cpt_country = 'FR';
		$row['postalcode'] = '33000';
		$row['societe_cp'] = '30000';
	}
	
	$result_ip = $mysqli->query("SELECT IP from user_ips WHERE user_id='".$row['id']."' order by id desc limit 1");
	$row_ip = $result_ip->fetch_array(MYSQLI_ASSOC);
	if(!$row['societe']){
		$row['societe'] = $row['firstname'].' '.$row['lastname'];
		$row['societe_ville'] = $row['city'];
		$row['societe_adress'] =$row['address'];
		$row['societe_adress2'] ='';
		$row['societe_cp'] = $row['postalcode'];
	} 
	if(!$iso_country_societe) $iso_country_societe = $cpt_country;
	if(!$row_ip['IP']) $row_ip['IP'] = '90.76.78.149';
	if($cpt_country){
		$data = array();
		$data['city'] = $row['societe_ville'];
		$data['line1'] = $row['societe_adress'];
		$data['line2'] = $row['societe_adress2'];
		$data['postal_code'] = $row['societe_cp'];
		$data['name'] = $row['societe'];
		$data['vat_id'] = $row['vat_num'];
		$data['tax_id'] = $row['siret'];
		$data['date'] = time();
		$data['ip'] = $row_ip['IP'];
		$data['country'] = $cpt_country;
		$data['country_company'] = $iso_country_societe;
		$data['account_number'] = '';//str_replace(' ','',$row['iban']);

		$data['person_country'] = $iso_country;
		$data['person_line1'] = $row['address'];
		$data['person_postal_code'] = $row['postalcode'];
		$data['person_city'] = $row['city'];
		$data['person_email'] = $row['email'];
		$data['person_first_name'] = $row['firstname'];
		$data['person_last_name'] = $row['lastname'];
		$data['day'] = $dob[2];
		$data['month'] = $dob[1];
		$data['year'] = $dob[0];


		var_dump($data);
	
		try {
			if($data['account_number']){
				$acct = \Stripe\Account::create([
					'business_type' => 'company',
					'country' => $data['country'],
					'type' => 'custom',
					'default_currency' => 'EUR',
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
						'name' => $data['name'],
						'vat_id' => $data['vat_id'],
						'tax_id' => $data['tax_id']
					],
					'tos_acceptance' => [
						'date' => $data['date'],
						'ip' => $data['ip'],
					],
					'external_account' => [
						'object' => 'bank_account',
						'country' => $data['country'],
						'currency' => 'EUR',
						'account_number' => $data['account_number'],
					],
					'settings' => [
						'payouts' => [
							'schedule' => [
								'interval' => 'manual',
							],	
						],
					],

				]);
			}else{
				$acct = \Stripe\Account::create([
					'business_type' => 'company',
					'country' => $data['country'],
					'type' => 'custom',
					'default_currency' => 'EUR',
					'email' => $data['person_email'],
					'requested_capabilities' => [
								'transfers',
							  ],
					'company' => [
						'address' => [
							'city' => $data['city'],
							'country' => $data['country_company'],
							'line1' => $data['line1'],
							'line2' => $data['line2'],
							'postal_code' => $data['postal_code'],
							'state' => ''
						],	
						'name' => $data['name'],
						'vat_id' => $data['vat_id'],
						'tax_id' => $data['tax_id']
					],
					'tos_acceptance' => [
						'date' => $data['date'],
						'ip' => $data['ip'],
					],
					'settings' => [
								'payouts' => [
									'schedule' => [
										'interval' => 'manual',
									],	
								],
							],
				]);
			}
			\Stripe\Account::createPerson(
			  $acct->id,
			  [
				'address' => [
						'city' => $data['person_city'],
						'country' => $data['person_country'],
						'line1' => $data['person_line1'],
						'line2' => '',
						'postal_code' => $data['person_postal_code'],
						'state' => '',
					],	
				'email' => $data['person_email'],
				'first_name' => $data['person_first_name'],
				'last_name' => $data['person_last_name'],
				'dob'=> [
						'day' => $data['day'],
						'month' => $data['month'],
						'year' => $data['year'],
				],
				 'relationship' => [
						'owner' => true,
								 'director' => true,
							 	 'representative' => true,
								 'percent_ownership' => 100,
					],	
			  ]
			);
			\Stripe\Account::update(
			  $acct->id,
			  [
				'company' => [
					'directors_provided' => true
				],
			  ]
			);
			
			//integre le num 
			$mysqli->query("UPDATE users set stripe_account = '".$acct->id."'  WHERE id='".$row['id']."'");
			var_dump("UPDATE users set stripe_account = '".$acct->id."'  WHERE id='".$row['id']."'");
		}
		catch (Exception $e) {
			 var_dump($row['id'].' => '.$e->getMessage());
		}
		
	}
}
exit;

?>