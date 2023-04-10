<?php
//exit;
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

require '../../Lib/stripe/init.php';
\Stripe\Stripe::setApiKey('sk_live_A3RxMDZA0tYljaejGG9BDf4U00CX9fXkQs');

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
$mysqli->set_charset("utf8");


$resultagent = $mysqli->query("SELECT * from users WHERE role = 'agent' and stripe_account != '' and active = 1 order by id");
while($rowagent = $resultagent->fetch_array(MYSQLI_ASSOC)){


try {
	$account = \Stripe\Account::retrieve($rowagent['stripe_account']);
	
	$is_same = true;
	$bank = $account->external_accounts;
	if($bank && is_object($bank)){
		if($bank->data){
		$last4 = $bank->data[0]->last4;
			/*if($id){
				$bank_account = $account->external_accounts->retrieve($id);
				var_dump($bank_account);
			}*/
			if($last4){
				$iban = str_replace(' ','',$rowagent['iban']);
				$iban_last = substr($iban,-4,4);
				if($last4 != $iban_last)$is_same = false;
			}
		}else{
			$is_same = false;
		}
	}else{
		$is_same = false;
	}
	if(!$is_same){
		var_dump($rowagent['id']. ' -> '.$rowagent['stripe_account']);
		$cpt_country = '';
		
		switch (strtolower($rowagent['bank_country'])) {
					case 'allemagne':
						$cpt_country = 'DE';//Allemagne
						break;
					case 'france':
						$cpt_country = 'FR';//France
						break;
					case 'belgique':
						$cpt_country = 'BE';//Belgique
						break;
					case 'suisse':
						$cpt_country = 'CH';//Suisse
						break;
					case 'luxembourg':
						$cpt_country = 'LU';//Luxembourg
						break;
					case 'espagne':
						$cpt_country = 'ES';//Espagne
						break;
					case 'portugal':
						$cpt_country = 'PT';//Portugal
						break;
					case 'bulgarie':
						$cpt_country = 'BG';//Portugal
						break;
				}
		if(!$cpt_country){
			switch ($rowagent['country_id']) {
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
		}
		if($cpt_country){
		$data = \Stripe\Account::update(
						  $rowagent['stripe_account'],
						  [
							  'external_account' => [
								'object' => 'bank_account',
								'country' => $cpt_country,
								'currency' => 'EUR',
								'account_number' => $rowagent['iban'],
							],
						  ]
						);
		}
	//var_dump($data);
		
		
	}
	

	
	
 }
	catch (Exception $e) {
	 var_dump($e->getMessage());
	 }

}
			
exit;

?>