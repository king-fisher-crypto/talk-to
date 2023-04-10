<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$fichier = 'trans_oppose.csv';

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
		
		
		
		$date = $ligne_data[3];
		$heure = $ligne_data[4];
		$tr = strtoupper($ligne_data[6]);
		
		
		$dds2 = explode('/',$date);
		
		$date_upd =  $dds2[2].'-'.$dds2[1].'-'.$dds2[0].' '.$heure;
		
		if($tr){
			
			$result = $mysqli->query("SELECT * from order_hipaytransactions WHERE transaction = '{$tr}' ");
			$row = $result->fetch_array(MYSQLI_ASSOC);

			if($row['order_id']){

				$mysqli->query("UPDATE order_hipaytransactions set date_upd='{$date_upd}' WHERE order_id = '{$row['order_id']}' ");
				$mysqli->query("UPDATE orders set valid=2 WHERE id = '{$row['order_id']}' ");
			}
		}
	}
}

?>