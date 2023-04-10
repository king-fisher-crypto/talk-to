<?php

//Spiriteo CMS

$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$fichier = 'desc_cms.csv';

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
		
		
		
		$id = $ligne_data[0];
		
		if(is_numeric($id)){
		
			$meta_title_1 = addslashes($ligne_data[3]);//france
			$meta_title_8 = addslashes($ligne_data[5]);//canada
			$meta_title_10 = addslashes($ligne_data[7]);//suisse
			$meta_title_11 = addslashes($ligne_data[9]);//belge
			$meta_title_12 = addslashes($ligne_data[11]);//luxembourg
			//var_dump("update page_langs set meta_description = '{$meta_title_1}' WHERE page_id = '{$id}' and lang_id = 1");
			$mysqli->query("update page_langs set meta_description = '{$meta_title_1}' WHERE page_id = '{$id}' and lang_id = 1");
			//var_dump("update page_langs set meta_description = '{$meta_title_8}' WHERE page_id = '{$id}' and lang_id = 8");
			$mysqli->query("update page_langs set meta_description = '{$meta_title_8}' WHERE page_id = '{$id}' and lang_id = 8");
			//var_dump("update page_langs set meta_description = '{$meta_title_10}' WHERE page_id = '{$id}' and lang_id = 10");
			$mysqli->query("update page_langs set meta_description = '{$meta_title_10}' WHERE page_id = '{$id}' and lang_id = 10");
			//var_dump("update page_langs set meta_description = '{$meta_title_11}' WHERE page_id = '{$id}' and lang_id = 11");
			$mysqli->query("update page_langs set meta_description = '{$meta_title_11}' WHERE page_id = '{$id}' and lang_id = 11");
			//var_dump("update category_langs set meta_description = '{$meta_title_12}' WHERE page_id = '{$id}' and lang_id = 12");
			$mysqli->query("update page_langs set meta_description = '{$meta_title_12}' WHERE page_id = '{$id}' and lang_id = 12");
			//exit;
		}
	}
}

?>