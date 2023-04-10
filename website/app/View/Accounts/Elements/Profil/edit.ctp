<?php if ($userRole == 'client')
    { ?>
    <div class="div_photo">
        <div class="photo">
    	<img src="https://picsum.photos/200/300" class="square"/>
    	<img src="/theme/black_blue/img/modifier_bleu_disk.svg" class="picto_modif" /> </div> 

        <div class="btn lh24-36 h85c white blue2 up_case" ><?= __("changer la photo") ?></div> 

    </div> 
<?php } ?>

<?php if ($userRole == 'agent')
    { ?>
    <div class="status">
        <DIV class="b lh24-36 txt"><?= __('Quel est votre statut ?') ?></DIV>
        <div class="radio_btns">
    	<label>
    	    <input class="square_radio" type="radio" name="radio">
    	    <span class="btn  b lh42b h80"><?= __('Compte Personnel') ?></span>
    	</label>

    	<label>
    	    <input  class="square_radio" type="radio" name="radio">
    	    <span class="btn multi b lh42b h80"><?= __('Compte Professionnel') . "<br/>" . __('Société'); ?></span>
    	</label>
        </div>
    </div>
<?php } ?>


<?php
/*
$fields = ["lastname" => "nom", "firstname" => "prénom", "pseudo" => "Pseudo visible sur le site",
    "email" => "email", "indicatif_phone" => "indicatif téléphone", "phone_number" => "Numéro De Tel Mobile 1",
    "phone_operator" => "Opérateur téléphonique", "birthdate" => "Date de naissance",
    "sexe" => "sexe",
    "address" => "adresse", "postalcode" => "code postal", "city" => "ville", "country_id" => "Pays de résidence"];
*/
echo $this->Form->create('Account',
	array('action' => 'editAccountCompte',
	    'nobootstrap' => 1,
	    'inputDefaults' => array(
		'label' => false,
		'div' => false
	    ),
	    'class' => '',
	    'default' => 1));


$langs_4_select =  $this->Language->getIconsAndLangsForSelect(1);

//$langs_4_select = [];

foreach ($fields as $field => $label)
    {

    $value = "";
    if (strpos($field, "indicatif_phone")>-1) continue;

    switch ($field)
	{
	
	case"birthdate":
	    $value = $this->Form->inputs(array(
		'birthdate' => array(
		    'separator' => '',
		    'dateFormat' => 'DMY',
		    'empty' => true,
		    'minYear' => date('Y') - 80,
		    'maxYear' => date('Y') - 18,
		)
	    ));
	    break;
	case "sexe":
	    
	     $sex = intval($userData['Account'][$field]);
		   if($sex==1)$sex_value="H";
		   if($sex==2)$sex_value="F";
	    
	    $options = array('H' => 'Homme', 'F' => 'Femme',);
	    /*
	    $attributes = array();
	    $attributes['legend'] = false;
	    $attributes['before'] = "<span class='radio'>";
	    $attributes['after'] = "</span>";
	    $attributes['separator'] = "</span><span  class='radio'>";
	    $attributes['type'] = "radio";
	    $attributes['value'] = "";

	    $value = $this->Form->radio(__("sexe"), $options, $attributes);
	    */
	    $value = $this->Form->input('sexe',
		    array(
		'before' => '<span class="radio">',
		'after' => '</span>',
		'separator' => '</span><span  class="radio">',
		'options' => $options,
		'type' => 'radio',
		"legend" => false,
		"label" => true,
		"value" => $sex_value,
		
	    ));

	    break;
	case"address":
	    $value = $this->Form->input('address',
		    ['type' => 'textarea', 'label' => false, 'placeholder' => '', 'escape' => false,
		'class' => 'address', 'rows' => '2', 'cols' => '40']);
	    break;
	case"indicatif_phone3":
	case"indicatif_phone2":
	case"indicatif_phone":

	    break;
	case"phone_number3":
	case"phone_number2":
	    
	case"phone_number":

	    $value = $this->FrontBlock->getIndicatifTelInputIns();
	    $value .= $this->Form->inputs(array(
		'phone_number' => array(
		    'type' => 'tel',
		    'label' => false,
		    'div' => false,
		    'pattern' => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
		),
	    ));

	    break;
	case"country_id":
	    $value = $this->Form->input('country_id',
		    array('label' => false, 'options' => $select_countries, 'required' => true));

	    break;
	
	case"lang_id":
	    $value = $this->Form->input('lang_id',
		    array('label' => false, 'options' => $langs_4_select, 'required' => true));

	    break;
	case "langs":
	    /*
		    $langs =$userData['Account'][$field];
		    $value = $this->Language->getIconslangs($langs);;
		    $value = $this->Form->input('lang_id',
		    array('label' => false, 'options' => $langs_4_select, 'required' => true, 'selected' => $langs, 'multiple' => true));*/
		$langs =$userData['Account'][$field];
		$value = $this->Language->getIconslangs($langs);;
		
		break;
	default:
	    $value = $values[$field];
	    $value = $this->Form->input($field, array('value' => $value));
	    break;
	}
    //   $value = $values[$field];
    ?>
    <div class="field_bar <?= $field ?>">
        <div class="label"><?= $label ?>
    	<!--    <div class="label"><?= __($label) ?> -->
    	<span class="asterisk">&#65121;</span></div>
        <div class="value"><?= $value ?></div>
    </div>
    <?php
    }


echo $this->Form->end(array('label' => __('VALIDER LES MODIFICATIONS'), 'class' => 'btn xlarge h85b modifier white blue2 up_case',
    'div' => array('class' => 'form-group')));
?>