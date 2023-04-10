<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$fichier = 'meta_can_uni.csv';

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
		
		
		
		$id = addslashes($ligne_data[0]);
		
		$meta_title = addslashes($ligne_data[3]);
		$meta_desc = addslashes($ligne_data[4]);
		$meta_keys = addslashes($ligne_data[5]);
		$url = addslashes($ligne_data[6]);
		
		
		//var_dump("SELECT * from page_langs WHERE link_rewrite = '{$url}' and lang_id = 11");
		//$result = $mysqli->query("SELECT * from page_langs WHERE link_rewrite = '{$url}' and lang_id = 11");
		//$row = $result->fetch_array(MYSQLI_ASSOC);

		if($id && $meta_title && $id != '#'){
			//, link_rewrite = '{$url}'
			//var_dump("update page_langs set meta_title = '{$meta_title}',meta_description = '{$meta_desc}',meta_keywords = '{$meta_keys}' WHERE page_id = '{$id}' and lang_id = 12");exit;
			$mysqli->query("update category_langs set meta_title2 = '{$meta_title}',meta_description2 = '{$meta_desc}',meta_keywords2 = '{$meta_keys}' WHERE category_id = '{$id}' and lang_id = 8");
		}else{
			var_dump($ligne_data);	
		}
	}
}

?>