<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
echo '<h1>Panier moyen</h1>';

echo '<table width="100%"><tr><td width="50%">';

echo '<h2>TOTAL  ( achat pour inscrit + audiotel )</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id in(29,22,13,11,19)  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
		}
		if($row_order && $row_order['nb']){
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$result = $mysqli->query("SELECT phone_number,avg(credits) as moy,count(credits) as nb from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3630,3631,3632,3633,3634,3635,3636,3637,3638) group by phone_number");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$list_panier_client[$row['phone_number']] = $row['moy'];
	$list_nb_panier_client[$row['phone_number']] = $row['nb'];
}

$nb_clients = count($list_panier_client)-154;
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;

echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>TOTAL  ( achat pour inscrit )</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' ");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
		}
		$list_nb_panier_client[$row['id']] = $row_order['nb'];
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>FRANCE</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id not in(29,22,13,11) order by id");//19
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
		}
		$list_nb_panier_client[$row['id']] = $row_order['nb'];
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>BELGIQUE</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 11 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
		}
		$list_nb_panier_client[$row['id']] = $row_order['nb'];
	}
}

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3631,3632) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3631 || $row['users_id'] == 3632 || ($row['users_id'] == 286 && $row['called_number'] == '90755456')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
		$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}
	
$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . (str_replace(',','.',$moy) * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>BELGIQUE client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 11 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>BELGIQUE audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3631,3632) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3631 || $row['users_id'] == 3632 || ($row['users_id'] == 286 && $row['called_number'] == '90755456')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
		$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}
	
$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;


echo '<h2>SUISSE</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 13 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}
$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3630) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3630 || ($row['users_id'] == 286 && $row['called_number'] == '901801885')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
		$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];

	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>SUISSE client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 13 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>SUISSE audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3630) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3630 || ($row['users_id'] == 286 && $row['called_number'] == '901801885')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
		$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];

	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>LUXEMBOURG</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 22 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3633) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3633 || ($row['users_id'] == 286 && $row['called_number'] == '90128222')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>LUXEMBOURG client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 22 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>LUXEMBOURG audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3633) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3633 || ($row['users_id'] == 286 && $row['called_number'] == '90128222')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>CANADA</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 29 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}
$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3634,3635,3636,3637,3638) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3634 || $row['users_id'] == 3635 || $row['users_id'] == 3636 || $row['users_id'] == 3637 || $row['users_id'] == 3638 || ($row['users_id'] == 286 && $row['called_number'] == '19007884466') || ($row['users_id'] == 286 && $row['called_number'] == '4466')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>CANADA client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 29 order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>CANADA audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3634,3635,3636,3637,3638) group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3634 || $row['users_id'] == 3635 || $row['users_id'] == 3636 || $row['users_id'] == 3637 || $row['users_id'] == 3638 || ($row['users_id'] == 286 && $row['called_number'] == '19007884466') || ($row['users_id'] == 286 && $row['called_number'] == '4466')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '</td>';
echo '<td width="50%">';

echo '<h2>DERNIERE ANNEE ( > 22/05/2017 ) TOTAL  ( achat pour inscrit + audiotel )</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id in(29,22,13,11,19) and date_add >= '2017-05-22 00:00:00' order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$result = $mysqli->query("SELECT phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3630,3631,3632,3633,3634,3635,3636,3637,3638)  and date_start >= '2017-05-22 00:00:00'  group by phone_number");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	$list_panier_client[$row['phone_number']] = $row['moy'];
	$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
}

$nb_clients = count($list_panier_client)-154;
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>TOTAL  ( achat pour inscrit )</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client'  and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' ");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>FRANCE</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id not in(29,22,13,11) and date_add >= '2017-05-22 00:00:00'  order by id");//19
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>BELGIQUE</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 11 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3631,3632) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3631 || $row['users_id'] == 3632 || ($row['users_id'] == 286 && $row['called_number'] == '90755456')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}
	
$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . (str_replace(',','.',$moy) * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>BELGIQUE client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 11 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>BELGIQUE audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3631,3632) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3631 || $row['users_id'] == 3632 || ($row['users_id'] == 286 && $row['called_number'] == '90755456')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}
	
$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;


echo '<h2>SUISSE</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 13 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}
$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3630) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3630 || ($row['users_id'] == 286 && $row['called_number'] == '901801885')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>SUISSE client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 13 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>SUISSE audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3630) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3630 || ($row['users_id'] == 286 && $row['called_number'] == '901801885')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>LUXEMBOURG</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 22 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy, count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy, count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3633) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3633 || ($row['users_id'] == 286 && $row['called_number'] == '90128222')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>LUXEMBOURG client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 22 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb  from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>LUXEMBOURG audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();

$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy , count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3633) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3633 || ($row['users_id'] == 286 && $row['called_number'] == '90128222')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;

echo '<h2>CANADA</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 29 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}
$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy , count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3634,3635,3636,3637,3638) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3634 || $row['users_id'] == 3635 || $row['users_id'] == 3636 || $row['users_id'] == 3637 || $row['users_id'] == 3638 || ($row['users_id'] == 286 && $row['called_number'] == '19007884466') || ($row['users_id'] == 286 && $row['called_number'] == '4466')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>CANADA client prepayé / achat credits</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT id, valid, date_add from users WHERE role = 'client' and domain_id = 29 and date_add >= '2017-05-22 00:00:00'  order by id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	if($row['valid']){
		
		
		$result_order = $mysqli->query("SELECT avg(product_credits) as moy , count(product_credits) as nb   from orders WHERE valid = '1' and user_id = '".$row['id']."' order by date_add desc limit 1");
		$row_order = $result_order->fetch_array(MYSQLI_ASSOC);
		
		if($row_order && $row_order['moy']){
			$list_panier_client[$row['id']] = $row_order['moy'];
			$list_nb_panier_client[$row['id']] = $row_order['nb'];
		}
	}
}

$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '<h2>CANADA audiotel</h2>';
$list_panier_client = array();
$list_nb_panier_client = array();
$result = $mysqli->query("SELECT users_id, date_start,called_number,phone_number,avg(credits) as moy , count(credits) as nb  from user_credit_last_histories WHERE media = 'phone' and users_id IN (286,3634,3635,3636,3637,3638) and date_start >= '2017-05-22 00:00:00'  group by phone_number order by date_start desc");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	if($row['users_id'] == 3634 || $row['users_id'] == 3635 || $row['users_id'] == 3636 || $row['users_id'] == 3637 || $row['users_id'] == 3638 || ($row['users_id'] == 286 && $row['called_number'] == '19007884466') || ($row['users_id'] == 286 && $row['called_number'] == '4466')){
		$list_panier_client[$row['phone_number']] = $row['moy'];
$list_nb_panier_client[$row['phone_number']] = $row_order['nb'];
	}
}


$nb_clients = count($list_panier_client);
$credits = array_sum($list_panier_client);
$moy = number_format($credits / $nb_clients,0,'','');
$nbs = array_sum($list_nb_panier_client);
$moy_nb = number_format($nbs / $nb_clients,2,'.',' ');
if($moy_nb < 1)$moy_nb = 1.00;
echo 'Nb clients '.$nb_clients;
echo '</br >';
echo 'Panier moy: '.$moy. ' credits soit ' . ($moy * 0.03).'€';
echo '</br >';
echo 'Paniers par client (moyenne ): '.$moy_nb;
echo '</td></tr></table>';