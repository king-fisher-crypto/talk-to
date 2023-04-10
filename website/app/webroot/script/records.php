<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$fichier = 'all_records.csv';

if ( $file = fopen( $fichier , r ) ) {
	
	$ligne = 1; // compteur de ligne
	$dsatz = array();
	while($tab=fgetcsv($file,1024,';'))
	{
		$champs = count($tab);//nombre de champ dans la ligne en question	
		//echo "<b> Les " . $champs . " champs de la ligne " . $ligne . " sont :</b><br />";
		$ligne ++;
		//affichage de chaque champ de la ligne en question
		$dsatz[$ligne] = array();
	
		for($i=0; $i<$champs; $i ++)
		{
			
			$data = utf8_encode($tab[$i]);
			$data = $tab[$i];
			$data = str_replace('’', "'",$data);
			$dsatz[$ligne][$i] = $data;
		}
	}	
	
//1666 sur last credit
//1692 sur credit
	
	foreach($dsatz as $k => $ligne_data ){
		
		
		
		$dialstart = $ligne_data[0];
		$dialend = $ligne_data[1];
		$startconsult = $ligne_data[2];
		$endconsult = $ligne_data[3];
		$sessionid = $ligne_data[4];
		$callerid = $ligne_data[5];
		$calledid = $ligne_data[6];
		$agentid = $ligne_data[7];
		$clientid = $ligne_data[8];
		
		if($sessionid != 'sessionid'){
		
			//insert call info
			//$mysqli->query("INSERT INTO call_infos (line, sessionid, callerid) VALUES ('{$line}','{$sessionid}','{$callerid}')");
			
			/*$result2 = $mysqli->query("SELECT id from users WHERE agent_number = '{$agentid}'");
			$row2 = $result2->fetch_array(MYSQLI_ASSOC);
			
			$result = $mysqli->query("SELECT * from user_credit_last_histories WHERE date_start = '{$startconsult}' and agent_id = '{$row2[id]}'");
			$row = $result->fetch_array(MYSQLI_ASSOC);
			
			$result1 = $mysqli->query("SELECT * from user_credit_history WHERE date_start = '{$startconsult}' and agent_id = '{$row2[id]}'");
			$row1 = $result1->fetch_array(MYSQLI_ASSOC);
			
			if($row['user_credit_last_history']){
				var_dump('OK');	
				$mysqli->query("update user_credit_last_histories set sessionid = '{$sessionid}' WHERE 	user_credit_last_history < '1666' and user_credit_last_history = '{$row[user_credit_last_history]}'");
				$mysqli->query("update user_credit_history set sessionid = '{$sessionid}' WHERE user_credit_history < '1692' and user_credit_history = '{$row1[user_credit_history]}'");
			}else{
				var_dump('NO '. $dialstart);		
			}*/
			
			$result = $mysqli->query("SELECT callinfo_id from call_infos WHERE sessionid = '{$sessionid}' and line not like '%-%'");
			$row = $result->fetch_array(MYSQLI_ASSOC);
			
			if($row['callinfo_id']){
				$mysqli->query("update call_infos set line = '' WHERE callinfo_id = '{$row['callinfo_id']}'");
			}
			
		}
		
	}
}

?>