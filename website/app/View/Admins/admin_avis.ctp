<?php

echo $this->Metronic->titlePage(__('Avis'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Avis'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();

$dbb_r = new DATABASE_CONFIG();
$dbb_route = $dbb_r->default;
$mysqli = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);


if ( isset($_POST["submit_avis"]) ) {

	$expert = $_POST["expert"];
	$nom = $_POST["nom"];
	$etoile = $_POST["etoile"];
	$pourcent = $_POST["pourcent"];
	$date = $_POST["date"];
	$com = $_POST["com"];
	
	if($expert && $nom && $etoile && $com){
		
		//check si user existe
		$nom = addslashes($nom);
		$result_check = $mysqli->query("SELECT * from users where firstname = '{$nom}' and lastname = 'Invite'");
		$row_check = $result_check->fetch_array(MYSQLI_ASSOC);
		$id_client = '';	
		if($row_check['id']){
			$id_client = $row_check['id'];	
		}else{
			$mysqli->query("INSERT INTO `users` ( `firstname`, `lastname`, `pseudo`, `email`, `birthdate`, `address`, `postalcode`, `city`, `sexe`, `country_id`, `domain_id`, `lang_id`, `optin`, `personal_code`, `passwd`, `last_passwd_gen`, `forgotten_password`, `emailConfirm`, `active`, `valid`, `deleted`, `date_add`, `date_upd`, `date_lastconnexion`, `role`, `countries`, `langs`, `agent_status`, `agent_number`, `has_photo`, `has_audio`, `credit`, `siret`, `consult_chat`, `consult_email`, `consult_phone`, `phone_number`, `phone_operator`, `phone_number2`, `creditMail`, `record`, `date_last_activity`, `chat_last_activity`, `limit_credit`, `careers`, `profile`, `status`) VALUES ( '{$nom}', 'Invite', '', '', '0000-00-00', '', '', '', '0', '1', '19', '1', NULL, NULL, '', '0000-00-00 00:00:00', '', '1', '1', '1', '0', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '', '', '', NULL, NULL, NULL, NULL, '', NULL, NULL, NULL, '', '', '', NULL, NULL, '0000-00-00 00:00:00', '0000-00-00 00:00:00', NULL, NULL, NULL, 0)");
			$id_client = $mysqli->insert_id;
		}
		$com = addslashes(utf8_decode($com));
		$mysqli->query("INSERT INTO `reviews` ( `user_id`, `agent_id`, `lang_id`, `content`, `rate`,`pourcent`, `date_add`, `status`) VALUES ( '{$id_client}', '{$expert}', '1', '{$com}', '{$etoile}','{$pourcent}', '{$date}', '1')");
		echo 'Avis posté.';
	}
}
?>


<table width="600">
<form action="/admin/admins/avis" method="post" enctype="multipart/form-data">
<tr>
<td width="20%">Expert</td>
<td width="80%"><select id="expert" name="expert">
<option value="">Choisir...</option>
<?php

$result = $mysqli->query("SELECT * from users where role = 'agent' and pseudo != '' order by pseudo");
while($row = $result->fetch_array(MYSQLI_ASSOC)){
	echo '<option value="'.$row['id'].'">'.$row['pseudo']. '('.$row['agent_number'].', actif : '.$row['active'].')</option>';
}
 ?>
</select></td>
</tr>
<tr>
<td width="20%">Nom cliente</td>
<td width="80%"><input type="text" name="nom" id="nom" /></td>
</tr>
<tr>
<td width="20%">Etoile</td>
<td width="80%"><select id="etoile" name="etoile">
<option value="">Choisir...</option>
<option value="1">1</option>
<option value="2">2</option>
<option value="3">3</option>
<option value="4">4</option>
<option value="5">5</option>
</select></td>
</tr>
	<tr>
<td width="20%">Pourcentage</td>
<td width="80%"><input type="text" name="pourcent" id="pourcent" value="100" /></td>
</tr>
<tr>
<td width="20%">Date</td>
<td width="80%"><input type="text" name="date" id="date" value="<?php echo date('Y-m-d H:i:s'); ?>" /></td>
</tr>
<tr>
<td width="20%">Commentaire</td>
<td width="80%"><textarea name="com" id="com" ></textarea></td>
</tr>
<tr>
<td>&nbsp;</td>
<td><input type="submit" name="submit_avis" /></td>
</tr>

</form>
</table>
<?php
$mysqli->close();
?>