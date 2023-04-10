<?php
$path = dirname(__FILE__);
$path = substr($path, 0, -10);
include($path.'/auth/db-connect.php');
$ap_tracking = 'ap_ref_tracking';
$datetime = date("Y-m-d H:i:s");

//IF AFFILIATE ID IS PRESENT
if(isset($_COOKIE[$ap_tracking])){
	session_start();

	//GET COMMISSION LEVEL
	$get_dc = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT default_commission FROM ap_settings WHERE id=1"));
	$default_commission = $get_dc['default_commission'];
	
	//IS SALE VOLUME COMMISSIONS ON
	$get_sv = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT sv_on FROM ap_other_commissions WHERE id=1"));
	$sv_on = $get_sv['sv_on'];
	
	if($sv_on=='1'){
	$get_cl = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT percentage FROM ap_commission_settings WHERE $sale_amount BETWEEN sales_from AND sales_to"));
	$comission = $get_cl['percentage'];
	}
	
	$sessionid = session_id();
	$ip_user = getenv('HTTP_CLIENT_IP')?:getenv('HTTP_X_FORWARDED_FOR')?:getenv('HTTP_X_FORWARDED')?:getenv('HTTP_FORWARDED_FOR')?:getenv('HTTP_FORWARDED')?:getenv('REMOTE_ADDR');
	$get_nb_pay = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT count(id) as nb FROM ap_earnings WHERE sessionid = '{$sessionid}'"));
	$nb_pay = $get_nb_pay['nb'] + 1;
	
	//COMMISSION OVERRIDE
	$comission = $commission;
	
	//SET DEFAULT COMMISSION IF NO OTHER VALUE HAS BEEN SET
	if($comission==''){$comission = $default_commission;}
	
	//RECURRING COMMISSIONS
	if(isset($recurring)){
		$recurring_period = strtolower($recurring);
		$recurring_amount = $recurring_fee;
		if($recurring_period==''){$recurring_period='monthly';}
		if($recurring_fee==''){$recurring_fee = $default_commission;}
	}else{
		$recurring_period = 'Non-recurring';
		$recurring_fee = '0';
	}
	
	//RECORD SALE
	$affiliate_id = $_COOKIE[$ap_tracking];
	$percentage = $comission / 100;
	$net_earnings = $sale_amount * $percentage;
	$datetime = date("Y-m-d H:i:s");

	//CHECK FOR VALID AFFILIATE
	$get_affiliate = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT balance FROM ap_members WHERE id=$affiliate_id"));
	$affiliate_balance = $get_affiliate['balance'];

	if(isset($affiliate_balance)){
		//PREVENT DUPLICATE SALES
		if($is_new_affiliate)$is_new_affiliate = 1; else $is_new_affiliate = 0;
		
		if($_SESSION['ap_sale_hit']=='1' && $affiliate_id == $_SESSION['saved_affiliate'] && $net_earnings == $_SESSION['saved_net']){}else{
		$stmt = $mysqli->prepare("INSERT INTO ap_earnings (affiliate_id, product, comission, sale_amount, net_earnings, recurring, recurring_fee, is_new, sessionid, IP, nb_pay, mode_payment, datetime) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
		$stmt->bind_param('sssssssssssss', $affiliate_id, $product, $comission, $sale_amount, $net_earnings, $recurring_period, $recurring_fee, $is_new_affiliate, $sessionid, $ip_user, $nb_pay, $mode_payment_affiliate, $datetime);
		$stmt->execute();
		$transaction_id = $stmt->insert_id;
		$stmt->close();
		//UPDATE AFFILIATE BALANCE
		$updated_balance = $affiliate_balance + $net_earnings;
		$update_one = $mysqli->prepare("UPDATE ap_members SET balance = ? WHERE id=$affiliate_id"); 
		$update_one->bind_param('s', $updated_balance);
		$update_one->execute();
		$update_one->close();	
			
		}
	}
	
	//MULT-TIER COMMISSIONS
	$get_mt = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT mt_on FROM ap_other_commissions WHERE id=1"));
	$mt_on = $get_mt['mt_on'];
	if($mt_on=='1'){
		$levels = 10;
		//FOR EACH LEVEL RUN
		$get_sponsor = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT sponsor FROM ap_members WHERE id=$affiliate_id"));
		$sponsor = $get_sponsor['sponsor'];
		for ($loop = 2 ; $loop < $levels; $loop++){ 
			
			//CHECK FOR AVAILABLE SPONSOR
			if($sponsor!='0'){
				
				//GET LEVEL PERCENTAGE
				$gp = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT * FROM ap_other_commissions WHERE id=1"));
				$p = $gp['tier'.$loop.'']; 
				$sc = $p / 100;
				$se = $sale_amount * $sc;
				
				//GET SPONSOR BALANCE
				$gb = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT balance FROM ap_members WHERE id=$sponsor"));
				$ob = $gb['balance']; 
				$nb = $ob + $se;
				$update_one = $mysqli->prepare("UPDATE ap_members SET balance = ? WHERE id=$sponsor"); 
				$update_one->bind_param('s', $nb);
				$update_one->execute();
				$update_one->close();

					//INSERT TRANSACTION HISTORY
					$stmt = $mysqli->prepare("INSERT INTO ap_multi_tier_transactions (affiliate_id, transaction_id, tier, commission, mt_earnings, datetime) VALUES (?, ?, ?, ?, ?, ?)");
					$stmt->bind_param('ssssss', $sponsor, $transaction_id, $loop, $p, $se, $datetime);
					$stmt->execute();
					$stmt->close();
			}else{
				break 1;
			}
			//GET NEXT SPONSOR FOR NEXT LOOP
			$gs = mysqli_fetch_assoc(mysqli_query($mysqli, "SELECT sponsor FROM ap_members WHERE id=$sponsor"));
			$sponsor = $gs['sponsor'];
		}
	}
	
	$_SESSION['ap_sale_hit'] = '1';
	$_SESSION['saved_affiliate'] = $affiliate_id;
	$_SESSION['saved_net'] = $net_earnings;
	
}

if(isset($_POST['reset'])){
unset($_SESSION['ap_sale_hit']);
unset($_SESSION['saved_affiliate']);
unset($_SESSION['saved_net']);
//header("Refresh:0");
}
?>