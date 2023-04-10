<?
/* ===========================================
		Langauge Support  
   	========================================= */
	if(isset($_SESSION['language'])){$language = $_SESSION['language'];}	
	
	//DEFAULT LANGUAGE
	if(empty($language)){$language='en'; $_SESSION['language'] = 'en';}
	include($language.'.php'); 