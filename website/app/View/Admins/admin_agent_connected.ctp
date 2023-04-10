<?php

echo $this->Metronic->titlePage(__('Agent connecté'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Agent connecté'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();

$dbb_r = new DATABASE_CONFIG();
$dbb_route = $dbb_r->default;
$mysqli = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);


if ( isset($_POST["submit_agent"]) ) {

	$expert = $_POST["expert"];
	$laps = $_POST["laps"];
	$time = $_POST["time"];
	
	if($expert && $laps && $time){
		
		$mysqli->query("INSERT INTO `user_connected` ( `agent_id`, `time_laps`, `call_during`) VALUES ( '{$expert}', '{$laps}', '{$time}')");
		echo 'Enregistrement ok.';
	}
}

if(isset($_GET['del'])){
	$mysqli->query("DELETE FROM `user_connected` where agent_id = '{$_GET['del']}'");
	echo 'Suppression ok.';
}
?>


<table width="600">
<form action="/admin/admins/agent_connected" method="post" enctype="multipart/form-data">
<tr>
<td width="20%">Expert</td>
<td width="80%"><select id="expert" name="expert">
<option value="">Choisir...</option>
<?php

$result = $mysqli->query("SELECT * from users where role = 'agent' and pseudo != '' order by pseudo");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	echo '<option value="'.$row['id'].'">'.$row['pseudo']. '('.$row['id'].')</option>';
}


 ?>
</select></td>
</tr>
<tr>
<td width="20%">connection toutes les (secondes )</td>
<td width="80%"><input type="text" name="laps" id="laps" /></td>
</tr>
<tr>
<td width="20%">temps de connection (secondes )</td>
<td width="80%"><input type="text" name="time" id="time" /></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit_agent" /></td>
</tr>

</form>
</table>

<br /><br />
Liste des agents auto connecté/en call
<table width="600" border="1">
<thead><th>Agent</th><th>connection toutes les</th><th>temps de connection</th><th>action</th></thead>
<?php

$result = $mysqli->query("SELECT U.id, C.time_laps, C.call_during, U.pseudo from user_connected C, users U where C.agent_id = U.id");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	echo '<tr><td>'.$row['pseudo'].' ('.$row['id'].')</td><td>'.$row['time_laps'].' sec.</td><td>'.$row['call_during'].' sec.</td><td><a href="/admin/admins/agent_connected?del='.$row['id'].'">supprimer</a></td></tr>';
}
 ?>
</table>
