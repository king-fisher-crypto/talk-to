<?php

//phpinfo();exit;
//PATCH MIGRATION .com

$new_domain = '';
if(isset($_SERVER) && isset($_SERVER["SERVER_NAME"])){
	switch ($_SERVER["SERVER_NAME"]) {
		case 'www.talkappdev.com':
			$new_domain = 'fr.spiriteo.com';
			break;
		case 'www.spiriteo.be':
			$new_domain = 'be.spiriteo.com';
			break;
		case 'www.spiriteo.ca':
			$new_domain = 'ca.spiriteo.com';
			break;
		case 'lu.spiriteo.com':
			$new_domain = 'www.spiriteo.lu';
			break;
		case 'www.spiriteo.ch':
			$new_domain = 'ch.spiriteo.com';
			break;
	}
	if($new_domain){
		$new_url = 'https://'.$new_domain.$_SERVER["REQUEST_URI"];
		header("Status: 301 Moved Permanently", false, 301);
		header("Location: ".$new_url);
		exit();
	}
}

/*
	if(isset($_SERVER["HTTP_HOST"]) && (!isset($_SERVER["HTTPS"]) || $_SERVER["HTTPS"] != "on") && !substr_count($_SERVER["REQUEST_URI"],'api/'))
	{
		//Tell the browser to redirect to the HTTPS URL.
		echo "rd1:http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		header("Location: http://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"], true, 301);
		//Prevent the rest of the script from executing.
		exit;
	}
*/

if(isset($_SERVER["HTTP_HOST"]) && substr_count($_SERVER["REQUEST_URI"],'/avis-clients-'))
{
    //Tell the browser to redirect to the HTTPS URL.
    header("Location: https://" . $_SERVER["HTTP_HOST"], true, 301);
    //Prevent the rest of the script from executing.
    exit;
}


include('database.php');

$dbb_r = new DATABASE_CONFIG();
$dbb_route = $dbb_r->default;
$mysqli_conf_route = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);

/*if(array_key_exists('REQUEST_URI', $_SERVER)){
	$current_url = $_SERVER['REQUEST_URI'];
	$result_routing_url = $mysqli_conf_route->query("SELECT new,type from redirects where old = '{$current_url}'");
	if($result_routing_url){
		$row_routing_url= $result_routing_url->fetch_array(MYSQLI_ASSOC);

		if($row_routing_url['new']){
			if($row_routing_url['type'] == 301)
				header("Status: 301 Moved Permanently", false, 301);
			if($row_routing_url['type'] == 302)
				header("Status: 301 Found", false, 302);
			header("Location: ".$row_routing_url['new']);
			exit();
		}
	}
}*/
require(__DIR__ . '/Schema/ZumbaRoute.php');

Router::defaultRouteClass('ZumbaRoute');

$routesFile = __DIR__ . '/routes.connect.php';
$routesHash = sha1_file($routesFile);

$cacheFile = TMP . 'routes/routes-' . $routesHash . '.php';
if (file_exists($cacheFile)) {
	include $cacheFile;
} else {
	include $routesFile;

	// Prepare for cache
	foreach (Router::$routes as $i => $route) {
		$route->compile();
	}

	$tmpCacheFile = TMP . 'routes/routes-' . uniqid('tmp-', true) . '.php';
	file_put_contents($tmpCacheFile, '<?php
		Router::$initialized = true;
		Router::$routes = ' . var_export(Router::$routes, true) . ';
	');
	rename($tmpCacheFile, $cacheFile);
}

Router::connectNamed(true);
