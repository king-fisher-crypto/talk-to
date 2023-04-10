<?php
	$userData['Account'] = $values;
    
	$indicatif = "";
	foreach ($fields as $field => $label)
	    {
	   //echo"<br>$field = ".$userData['Account'][$field];
	   $value ="";
	   if (strpos($field, "indicatif_phone")>-1) continue;
	    switch($field)
		{
		case "sexe":
		   
		   $sex = intval($userData['Account'][$field]);
		   if($sex==1)$value=__("homme");
		   if($sex==2)$value=__("femme");
		  
		break;
		
		case "indicatif_phone":
		    continue 2;
		break;
		case "lang_id":
		    $lang_id  =$userData['Account'][$field];
		    $value = $this->Language->getIconAndLang($lang_id);
		    
		  //  $value = "français";
		break;
		case "langs":
		    $langs =$userData['Account'][$field];
		    $value = $this->Language->getIconslangs($langs);;
		 
		break;
		case "phone_number":
		if(!empty($userData['Account']["indicatif_phone"])) $prefix ="+".$userData['Account']["indicatif_phone"]." ";
		    	$value = $prefix.$userData['Account'][$field];
		break;
		case "country_id":
		    $value = "France";
		break;
		
		default :
		//$value = $userData['Account'][$field];
		$value = $values[$field];
		break;
		}
	
		
		
		?>
		<div class="field_bar <?= $field ?>">
<!--		    <div class="label"><?= __($label) ?></div>-->
		    <div class="label"><?= $label ?></div>
		    <div class="value blue2 "><?= $value ?></div>
		</div>
	
		<?php
	    }
	?>
	<br/><br/>
	<a class="blue2 edit_profile up_case underline" role="presentation" title="<?= __('Modifier') ?>"><?= __('Modifier') ?></a>