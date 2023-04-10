<?php

//Spiriteo cost agent
ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
			


//is new comm
$result = $mysqli->query("SELECT * from user_credit_history where user_credit_history < 38886 and domain_id = 0 order by user_credit_history");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	//if($row['media'] == 'phone'){
	
	/*	if($row['user_id'] == 286 || $row['user_id'] == 3630 || $row['user_id'] == 3631 || $row['user_id'] == 3632 || $row['user_id'] == 3633 || $row['user_id'] == 3634 || $row['user_id'] == 3635 || $row['user_id'] == 3636 || $row['user_id'] == 3637 || $row['user_id'] == 3638 ){
			switch ($row['user_id']) {
					case '286':
						$domainid = '19';
						break;
					case '3630':
						$domainid = '13';
						break;
					case '3631':
						$domainid = '11';
						break;
					case '3632':
						$domainid = '11';
						break;
					case '3633':
						$domainid = '22';
						break;
					case '3634':
						$domainid = '29';
						break;
					case '3635':
						$domainid = '29';
						break;
					case '3636':
						$domainid = '29';
						break;
					case '3637':
						$domainid = '29';
						break;
					case '3638':
						$domainid = '19';
						break;
				}
				$type = 'aud';
			}else{
				$result2 = $mysqli->query("SELECT * from users where id = '{$row['user_id']}'");
				$row2 = $result2->fetch_array(MYSQLI_ASSOC);
				$domainid = $row['domain_id'];
				$type = 'pre';
			}*/
	//}else{
		$result2 = $mysqli->query("SELECT * from users where id = '{$row['user_id']}'");
			$row2 = $result2->fetch_array(MYSQLI_ASSOC);
			$domainid = $row2['domain_id'];
			$type = 'pre';
		$mysqli->query("UPDATE user_credit_history SET type_pay = '{$type}', domain_id = '{$domainid}' where user_credit_history = '{$row['user_credit_history']}'");
	//}
	
	
		
}
exit;
?>