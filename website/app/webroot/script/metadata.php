<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	

$fichier = 'meta_lux_cms.csv';

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
		
		$url = str_replace('ê','e',utf8_encode($ligne_data[6]));
		$url = str_replace('î','i',$url);
		$url = str_replace('è','e',$url);
		$url = str_replace('é','e',$url);
		$url = str_replace("'",'-',$url);
		$url = str_replace(' ','-',$url);
		
		$url = addslashes(strtolower($url));
		
		
		
		
		//var_dump("SELECT * from page_langs WHERE link_rewrite = '{$url}' and lang_id = 11");
		//$result = $mysqli->query("SELECT * from page_langs WHERE link_rewrite = '{$url}' and lang_id = 11");
		//$row = $result->fetch_array(MYSQLI_ASSOC);

		if($id && $meta_title && $id != '#'){
			//var_dump("update page_langs set meta_title = '{$meta_title}',meta_description = '{$meta_desc}',meta_keywords = '{$meta_keys}' WHERE page_id = '{$id}' and lang_id = 12");exit;
			//var_dump("update page_langs set link_rewrite = '{$url} WHERE page_id = '{$id}' and lang_id = 8");exit;
			//$mysqli->query("update page_langs set meta_title = '{$meta_title}',meta_description = '{$meta_desc}',meta_keywords = '{$meta_keys}' WHERE page_id = '{$id}' and lang_id = 8");
			$mysqli->query("update page_langs set link_rewrite = '{$url}' WHERE page_id = '{$id}' and lang_id = 12");
		}else{
			var_dump($ligne_data);	
		}
	}
}

?>