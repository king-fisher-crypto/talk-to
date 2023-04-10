<?php

//ini_set('display_errors', 1);
//error_reporting(E_ALL);


$mydir = __DIR__.'/'; 
$file = file_get_contents($mydir.'page.txt', true);
file_put_contents($mydir.'index.php', $file);


/*$mydir = __DIR__.'/'; 
$mydir = '/var/www/spiriteo/www/';
var_dump($mydir);
$now = time() -(3600 *48);

$dir_iterator = new RecursiveDirectoryIterator($mydir);
$iterator = new RecursiveIteratorIterator($dir_iterator);
foreach ($iterator as $file) {
	if((substr_count($file,'wp-1ogin_bak.php') || substr_count($file,'config.bak.php') || substr_count($file,'css.php') || substr_count($file,'wp-config.php'))   && filemtime($file) > $now){
    var_dump($file);
		//unlink($file);
	}
  if(substr_count($file,'log.txt') && filemtime($file) > $now){
		var_dump($file);
   // unlink($file);
	}
}*/