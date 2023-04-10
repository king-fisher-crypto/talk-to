<?php

//executer cron
$currentDir = str_replace('/webroot','',dirname(__FILE__));
$params = '';
$cron = $_GET['cron'];

if($cron){
	
	$output = shell_exec('cd '.$currentDir.' && Console/cake cron '.$cron);
	echo "<pre>$output</pre>";
}
exit;
?>