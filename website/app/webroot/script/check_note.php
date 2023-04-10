<?php

ini_set('display_errors', 1); 
error_reporting(E_ALL); 

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
$sessions = '';
/*$result = $mysqli->query("SELECT disctinct(callerid),* from call_infos");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	
	$phone_com_title = (substr($row['callerid'], -4));
	$client = 'AUDIO'.(substr($call['CallInfo']['callerid'], -4)*15);
	$result2 = $mysqli->query("SELECT * from call_infos where callerid like '%".$phone_com_title."' and callerid != '".$row['callerid']."'");
	while($row2 = $result2->fetch_array(MYSQLI_ASSOC)){	
		$result3 = $mysqli->query("SELECT * from notes WHERE client = '{$client}'");
		$row3 = $result3->fetch_array(MYSQLI_ASSOC);
		if($row3['id'])
		var_dump($row['id'].' : '.$row['callerid'].' -> '.$row2['callerid'].' => notes id '.$row['id']);
		
	}
}*/

$result = $mysqli->query("SELECT * from notes where id_client = 286");
while($row = $result->fetch_array(MYSQLI_ASSOC)){	
	if($row['callinfo_id']){
		$result3 = $mysqli->query("SELECT * from call_infos WHERE callinfo_id = '{$row['callinfo_id']}'");
		$row3 = $result3->fetch_array(MYSQLI_ASSOC);
		if($row3['callerid']){
			$mysqli->query("update notes set id_client = '".$row3['callerid']."' WHERE id = '{$row['id']}'");	
			//var_dump("update notes set id_client = '".$row3['callerid']."' WHERE id = '{$row['id']}'");exit;
		}
	}
}
exit;
?>