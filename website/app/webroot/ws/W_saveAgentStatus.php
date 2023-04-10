<?php
require_once( '../../Config/database.php' );

$db = new DATABASE_CONFIG();
$dbb_route = $db->default;
$mysqli = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);

$status		 	= explode('_',$_POST['status']);

if(is_array($status)){
	
	$statut = $status['0'];
	$id = $status['1'];
	$mysqli->query("UPDATE users set status = '{$statut}' where id = '{$id}'");
}

$retour = array();

echo json_encode($retour);

?>