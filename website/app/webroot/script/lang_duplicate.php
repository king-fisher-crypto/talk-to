<?php

//Spiriteo CMS

	$mysqli = new mysqli($dbb_head['host'], "spiriteo", "m3UFeJ9382rPQ9m8tQPqM4es", "spiriteo");
	
	$result = $mysqli->query("SELECT * from user_country_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['user_countries_id'];
	$lang_id = 12;
	$name = addslashes($row['name']);
	 $mysqli->query("INSERT INTO user_country_langs ( lang_id, user_countries_id, name) VALUES ('{$lang_id}','{$id}','{$name}')");
		
}
	

	/*$result = $mysqli->query("SELECT * from country_langs WHERE id_lang = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['country_id'];
	$lang_id = 12;
	$name = addslashes($row['name']);
	 $mysqli->query("INSERT INTO country_langs ( id_lang, country_id, name) VALUES ('{$lang_id}','{$id}','{$name}')");
		
}*/
	
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
*/

/*$result = $mysqli->query("SELECT * from menu_block_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['menu_block_id'];
	$lang_id = 12;
	$name = addslashes($row['title']);
	
	 $mysqli->query("INSERT INTO menu_block_langs ( menu_block_id, lang_id, title) VALUES ('{$id}','{$lang_id}','{$name}')");
		
}*/


/*$result = $mysqli->query("SELECT * from menu_link_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['menu_link_id'];
	$lang_id = 12;
	$name = addslashes($row['title']);
	
	$link = addslashes($row['link']);
	
	 $mysqli->query("INSERT INTO menu_link_langs ( menu_link_id, lang_id, title, link) VALUES ('{$id}','{$lang_id}','{$name}','{$link}')");
		
}*/

/*$result = $mysqli->query("SELECT * from page_category_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['page_category_id'];
	$lang_id = 12;
	$name = addslashes($row['name']);
	
	 $mysqli->query("INSERT INTO page_category_langs ( page_category_id, lang_id, name) VALUES ('{$id}','{$lang_id}','{$name}')");
		
}*/

/*$result = $mysqli->query("SELECT * from footer_block_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['footer_block_id'];
	$lang_id = 12;
	$name = addslashes($row['title']);
	
	 $mysqli->query("INSERT INTO footer_block_langs ( footer_block_id, lang_id, title) VALUES ('{$id}','{$lang_id}','{$name}')");
		
}*/


/*$result = $mysqli->query("SELECT * from footer_link_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['footer_link_id'];
	$lang_id = 12;
	$name = addslashes($row['title']);
	
	$link = addslashes($row['link']);
	
	 $mysqli->query("INSERT INTO footer_link_langs ( footer_link_id, lang_id, title, link) VALUES ('{$id}','{$lang_id}','{$name}','{$link}')");
		
}*/

/*$result = $mysqli->query("SELECT * from product_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['product_id'];
	$lang_id = 12;
	$name = addslashes($row['name']);
	
	$desc = addslashes($row['description']);
	
	 $mysqli->query("INSERT INTO product_langs ( product_id, lang_id, name, description) VALUES ('{$id}','{$lang_id}','{$name}','{$desc}')");
		
}*/

/*$result = $mysqli->query("SELECT * from horoscope_signs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['sign_id'];
	$lang_id = 8;
	$name = addslashes($row['name']);
	$desc = addslashes($row['info_dates']);
	$link = addslashes($row['link_rewrite']);
	
	 $mysqli->query("INSERT INTO horoscope_signs ( sign_id, lang_id, link_rewrite, name, info_dates) VALUES ('{$id}','{$lang_id}','{$link}','{$name}','{$desc}')");
		
}*/

/*$result = $mysqli->query("SELECT * from horoscope_langs WHERE lang_id = 1");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$id = $row['horoscope_id'];
	$lang_id = 12;
	$name = addslashes($row['sign_id']);
	$desc = addslashes($row['content']);
	
	 $mysqli->query("INSERT INTO horoscope_langs ( horoscope_id, sign_id, lang_id, content) VALUES ('{$id}','{$name}','{$lang_id}','{$desc}')");
		
}*/

//UPDATE  footer_link_langs SET link = replace(link, '/fre', '/frc') where lang_id = 8;

//UPDATE  users SET countries = '1,3,4,5,13' where countries = '1,3,13';

//$mysqli->query("DELETE from country_lang_phone WHERE lang_id = 8 and country_id = 13");
/*$result = $mysqli->query("SELECT * from country_lang_phone WHERE lang_id = 1 and country_id = 5");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	
	$lang_id = 12 ;//$row['country_id'];
	$country_id = 5 ;//$row['country_id'];
	$surtaxed_phone_number = $row['surtaxed_phone_number'];
	$surtaxed_minute_cost = $row['surtaxed_minute_cost'];
	$prepayed_phone_number = $row['prepayed_phone_number'];
	$prepayed_minute_cost = $row['prepayed_minute_cost'];
	$prepayed_second_credit = $row['prepayed_second_credit'];
	$third_phone_number = $row['third_phone_number'];
	$third_minute_cost = $row['third_minute_cost'];
	$mention_legale_num1 = $row['mention_legale_num1'];
	$mention_legale_num2 = $row['mention_legale_num2'];
	$mention_legale_num3 = $row['mention_legale_num3'];
	//var_dump("INSERT INTO country_lang_phone ( country_id, lang_id, surtaxed_phone_number, surtaxed_minute_cost, prepayed_phone_number, prepayed_minute_cost, prepayed_second_credit, third_phone_number, third_minute_cost, mention_legale_num1, mention_legale_num2, mention_legale_num3) VALUES ('{$country_id}','{$lang_id}','{$surtaxed_phone_number}','{$surtaxed_minute_cost}','{$prepayed_phone_number}','{$prepayed_minute_cost}','{$prepayed_second_credit}','{$third_phone_number}','{$third_minute_cost}','{$mention_legale_num1}','{$mention_legale_num2}','{$mention_legale_num3}')");
	$mysqli->query("INSERT INTO country_lang_phone ( country_id, lang_id, surtaxed_phone_number, surtaxed_minute_cost, prepayed_phone_number, prepayed_minute_cost, prepayed_second_credit, third_phone_number, third_minute_cost, mention_legale_num1, mention_legale_num2, mention_legale_num3) VALUES ('{$country_id}','{$lang_id}','{$surtaxed_phone_number}','{$surtaxed_minute_cost}','{$prepayed_phone_number}','{$prepayed_minute_cost}','{$prepayed_second_credit}','{$third_phone_number}','{$third_minute_cost}','{$mention_legale_num1}','{$mention_legale_num2}','{$mention_legale_num3}')");
		
				
}*/



exit;

?>