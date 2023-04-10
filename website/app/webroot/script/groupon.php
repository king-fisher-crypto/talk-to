<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$fichier = 'groupon.csv';

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
	

	
	foreach($dsatz as $k => $ligne_data ){
		
		
		
		$code = $ligne_data[0];
		$titre = $ligne_data[1];
		$debut = str_replace('  ',' ',$ligne_data[2]);
		$fin = str_replace('  ',' ',$ligne_data[3]);
		$forfait = $ligne_data[8];
		
		$credit = 0;
		
		if(substr_count($forfait,30)){
			$credit = 1800;	
		}
		if(substr_count($forfait,60)){
			$credit = 3600;	
		}
		if(substr_count($forfait,120)){
			$credit = 7200;	
		}
		
		$dds = explode(' ',$debut);
		$dds2 = explode('/',$dds[0]);
		
		$date_start =  $dds2[2].'-'.$dds2[1].'-'.$dds2[0].' '.$dds[1].':00';
		
		$ddf = explode(' ',$fin);
		$ddf2 = explode('/',$ddf[0]);
		
		$date_end =  $ddf2[2].'-'.$ddf2[1].'-'.$ddf2[0].' '.$ddf[1].':00';
		
		
		$result = $mysqli->query("SELECT * from vouchers WHERE code = '{$code}' ");
		$row = $result->fetch_array(MYSQLI_ASSOC);
		
		if(!$row['code'] && $code != 'CODE'){
		
			//var_dump("INSERT INTO vouchers (code, validity_start, validity_end, title, credit, amount, percent, population, product_ids, country_ids, buy_only, number_use, number_use_by_user, active) VALUES ('{$code}','{$date_start}','{$date_end}','{$titre}','{$credit}','0.00','0','','all','all','1','1','1','1')");
			$mysqli->query("INSERT INTO vouchers (code, validity_start, validity_end, title, credit, amount, percent, population, product_ids, country_ids, buy_only, number_use, number_use_by_user, active) VALUES ('{$code}','{$date_start}','{$date_end}','{$titre}','{$credit}','0.00','0','','all','all','1','1','1','1')");
		}else{
			var_dump($code);	
		}
	}
}

?>