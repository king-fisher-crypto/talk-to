<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
/*$result = $mysqli->query("SELECT * from page_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$page_id = $row['page_id'];
	$lang_id = 10;
	$name = addslashes($row['name']);
	$meta_title = addslashes($row['meta_title']);
	$meta_description = addslashes($row['meta_description']);
	$meta_keywords = addslashes($row['meta_keywords']);
	$content = addslashes($row['content']);
	$link_rewrite = addslashes($row['link_rewrite']);
	
	
	 $mysqli->query("INSERT INTO page_langs ( page_id, lang_id, name, meta_title, meta_description, meta_keywords, content, link_rewrite ) VALUES ('{$page_id}','{$lang_id}','{$name}','{$meta_title}','{$meta_description}','{$meta_keywords}','{$content}','{$link_rewrite}')");
		
}



exit;*/

$fichier = 'belgique.csv';

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
		
		
		
		$nom = addslashes($ligne_data[0]);
		
		//$content = nl2br(htmlentities(addslashes(utf8_decode($ligne_data[5]))));
		$meta_title = addslashes(utf8_decode($ligne_data[3]));
		$meta_desc = addslashes(utf8_decode($ligne_data[5]));
		$meta_keys = addslashes(utf8_decode($ligne_data[7]));
		$new_url = nl2br(htmlentities(addslashes($ligne_data[11])));
		$content = nl2br(htmlentities(addslashes($ligne_data[9])));
		
		
		$url = addslashes($ligne_data[10]);
		var_dump("SELECT * from page_langs WHERE link_rewrite = '{$url}' and lang_id = 11");
		$result = $mysqli->query("SELECT * from page_langs WHERE link_rewrite = '{$url}' and lang_id = 11");
		$row = $result->fetch_array(MYSQLI_ASSOC);

		if($row['page_id'] && $content){
			//var_dump("update page_langs set content = '{$content}',meta_title = '{$meta_title}',meta_description = '{$meta_desc}',meta_keywords = '{$meta_keys}' WHERE page_id = '{$row['page_id']}' and lang_id = 11");exit;
			//$mysqli->query("update page_langs set content = '{$content}',meta_title = '{$meta_title}',meta_description = '{$meta_desc}',meta_keywords = '{$meta_keys}' WHERE page_id = '{$row['page_id']}' and lang_id = 10");
			//$mysqli->query("update page_langs set link_rewrite = '{$new_url}' WHERE page_id = '{$row['page_id']}' and lang_id = 11");
		}else{
			var_dump($ligne_data);	
		}
	}
}

?>