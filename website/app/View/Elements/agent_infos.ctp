<?php

    if(!isset($sexe)) $sexe = 0;

    echo $this->Form->inputs(array(
            'pseudo' => array('label' => array('text' => __('Pseudo composé').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'), 'required' => true, 'between' => '<div class="col-sm-12 col-md-8">','after'=>'</div>'))
    );
    ?>
        <div class="form-group mt20 wow fadeIn animated">
            <?php
            
            echo $this->Form->input('firstname', array(
                'label' => array('text' => __('Noms').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'),
                'required' => true,
                'div' => false,
				'placeholder' => __('Prénoms'),
                'between' => '<div class="col-sm-12 col-md-4">',
				'after'=>'</div>'
            ));

            echo $this->Form->input('lastname', array(
                'required' => true,
				'label' => array('text' => __('&nbsp;'), 'class' => 'col-sm-12 col-md-4 control-label required hidden-md hidden-lg'),
                'div' => false,
				'placeholder' => __('Noms'),
                'between' => '<div class="col-sm-12 col-md-4">'
				,'after'=>'</div>'
            ));
			if(isset($subscribe)) echo '<div class="col-sm-12 col-md-offset-4 col-md-8">'.__('Vos prénoms et noms ne seront pas diffusés sur le site').'</div>';
            ?>
        </div>
        <div class="form-group mt20 wow fadeIn animated">
            <label for="UserSexe" class="col-sm-12 col-md-4 control-label required"><?php echo __('Sexe').' <span class="star-condition">*</span>'; ?></label>
            <div class="col-sm-6 col-md-2"><div class="radio"><label class="" for="<?php echo $nomModel; ?>Sexe1">
                <input name="data[<?php echo $nomModel; ?>][sexe]" <?php echo ($sexe == 1?'checked':''); ?> required="required" id="<?php echo $nomModel; ?>Sexe1" value=1 type="radio"><span></span>
                <?php echo __('Homme'); ?></label>
             </div></div>
              <div class="col-sm-6 col-md-2"><div class="radio"><label class="" for="<?php echo $nomModel; ?>Sexe2">
                <input name="data[<?php echo $nomModel; ?>][sexe]" <?php echo ($sexe == 2?'checked':''); ?> required="required" id="<?php echo $nomModel; ?>Sexe2" value=2 type="radio"><span></span>
                <?php echo __('Femme'); ?></label>
            </div></div>
        </div>
    <?php
    echo $this->Form->inputs(array(
            'birthdate' => array(
                'label'         =>  array('text' => __('Date de naissance').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'),
                'dateFormat'    =>  'DMY',
                'minYear'       =>  date('Y') - 99,
                'maxYear'       =>  date('Y') - 18,
                'empty'         =>  (isset($emptyBirthdate) ?$emptyBirthdate:true),
                'required'      =>  true,
				'between' =>'<div class="col-sm-12 col-md-8 select_dd">',
				'after' => '</div>'
            )
    ));

if(isset($subscribe)) echo '<div class="col-sm-12 col-md-offset-4 col-md-8"><span class="help">'.__('Vos consultations clients vous seront transmises sur ce premier numéro de téléphone :').'</span></div>';
    echo $this->Form->inputs(array(
            'phone_number' => array(
                'label'     => array('text' => __('Numéro de téléphone fixe uniquement').' <span class="star-condition">*</span>', 'class' => 'control-label col-xs-12 col-md-4 col-lg-4 required'),
                'required'  => true,
                'type'      => 'tel',
                'between'    => '<div class="col-xs-4 col-md-3 pr0">'.$this->FrontBlock->getIndicatifTelInputIns(true).'</div><div class="col-xs-8 col-md-5">',
				'after'    => '</div>'
               // 'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
            )
    ));


echo $this->Form->inputs(array(
    'phone_operator' => array(
        'label'         =>  array('text' => __('Opérateur téléphonique').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'),
        'between'        =>  '<div class="col-sm-12 col-md-8">',
		'after'			=> '</div>',
        'required'      =>  true
    )
));
echo '<div class="col-sm-12 col-md-offset-4 col-md-8"><span class="help">'.__('Ce 2ème numéro de téléphone est réservé aux tâches secondaires, vous ne recevrez pas d\'appels client sur celui-ci.').'</span></div>';
//if(isset($subscribe)) echo '<p class="col-lg-offset-3 legend_no_marginbtom">'.__('Ce 2ème numéro de téléphone est réservé aux tâches secondaires, vous ne recevrez pas d\'appels client sur celui-ci.').'</p>';
    echo $this->Form->inputs(array(
            'phone_number2' => array(
                'label'     => array('text' => __('Numéro de téléphone fixe secondaire'), 'class' => 'control-label col-xs-12 col-md-4 col-lg-4'),
                'required'  => false,
                'type'      => 'tel',
                'between'    => '<div class="col-xs-4 col-md-3 pr0">'.$this->FrontBlock->getIndicatifTelInputIns(false, false, false, 'indicatif_phone2').'</div><div class="col-xs-8 col-md-5">',
                'after'		=> '</div>',
				//'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
            )
    ));
echo $this->Form->inputs(array(
    'phone_operator2' => array(
        'label'         =>  array('text' => __('Opérateur téléphonique').' ', 'class' => 'col-sm-12 col-md-4 control-label '),
        'between'        =>  '<div class="col-sm-12 col-md-8">',
		'after'			=> '</div>',
        'required'      =>  false
    )
));
 echo $this->Form->inputs(array(
            'phone_mobile' => array(
                'label'     => array('text' => __('Numéro de téléphone mobile'), 'class' => 'control-label col-xs-12 col-md-4 col-lg-4'),
                'required'  => false,
                'type'      => 'tel',
                'between'    => '<div class="col-xs-4 col-md-3 pr0">'.$this->FrontBlock->getIndicatifTelInputIns(false, false, false, 'indicatif_mobile').'</div><div class="col-xs-8 col-md-5">',
                'after'		=> '</div>',
				//'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
            )
    ));
echo $this->Form->inputs(array(
    'phone_operator3' => array(
        'label'         =>  array('text' => __('Opérateur téléphonique').' ', 'class' => 'col-sm-12 col-md-4 control-label '),
        'between'        =>  '<div class="col-sm-12 col-md-8">',
		'after'			=> '</div>',
        'required'      =>  false
    )
));
	
	if(!isset($selected_countries))$selected_countries = array();

    echo $this->Form->inputs(array(
            'country_id'   => array(
                'label' => array('text' => __('Pays de résidence').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'),
                'options' => $select_countries,
                'required' => true,
				'between' => '<div class="col-sm-12 col-md-8">',
				'after' => '</div>',
				'selected' => $selected_countries,
				'class' => 'form-control country_choice country_address'
            ),
            'address' => array('label' => array('text' => __('Adresse').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'), 'required' => true,'between' => '<div class="col-sm-12 col-md-8">',
				'after' => '</div>'))
    ); ?>
        <div class="form-group mt20 wow fadeIn animated">
            <?php
            echo $this->Form->input('postalcode', array(
                'label' => array('text' => __('Code postal').' <span class="star-condition">*</span>', 'class' => 'control-label col-md-4 required'),
                'div' => false,
                'required'  => true,
                'between' => '<div class="col-md-3">',
				'after' => '</div>',
            ));

            echo $this->Form->input('city', array(
                'label' => array('text' => __('Ville').' <span class="star-condition">*</span>', 'class' => 'control-label col-md-1', 'style' => 'padding-left:12px'),
                'div' => false,
                'required'  => true,
                'between' => '<div class="col-md-4">',
				'after' => '</div>',
            ));
            ?>
        </div>
    <?php
    echo $this->Form->input('siret', array('label' => array('text' => __('Numéro Entreprise').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'), 'required' => true , 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '<span class="help">'. __('(ex: Rcs, Siret, Siren, Insee, IDE, Banque Carrefour des Entreprises, Company registration number)').'</span></div>'));

	 echo $this->Form->input('society_type_id', array('label' => array('text' => __('Statut').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label required'), 'required' => true ,  'options' => $select_society_types, 'selected' => $selected_society_types, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>','class' => 'form-control status_choice'));
	
	echo $this->Form->input('societe_statut', array('label' => array('text' => __('Autre... (précisez)').' ', 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false , 'between' => '<div class="col-sm-12 col-md-8">',  'after'=> '</span></div>'));
	echo $this->Form->input('invoice_vat_id', array('label' => array('text' => __('Mon assujettissement TVA dans mon pays').' <span class="star-condition">*</span>', 'class' => 'col-sm-12 col-md-4 control-label society-info'), 'required' => true , 'between' => '<div class="col-sm-12 col-md-8 society-info">', 'after'=> '<p class="help"></p></div>','options' => $select_invoice_vat,'class' => 'form-control vat_choice'));
	echo $this->Form->input('vat_num', array('label' => array('text' => __('N° TVA Intracommunautaire').' ', 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false , 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '<p class="help">si Européén</p></div>'));
	

	?>
 <div class="form-group mt20 wow fadeIn animated society-info col-lg-12">
    <?php
	echo $this->Form->input('societe', array('label' => array('text' => __('Société si facturation différente du Nom-Prénom'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	 echo $this->Form->input('societe_adress', array('label' => array('text' => __('Adresse société'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	 echo $this->Form->input('societe_adress2', array('label' => array('text' => __('Adresse (suite)'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	 echo $this->Form->input('societe_cp', array('label' => array('text' => __('Code postal société'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	 echo $this->Form->input('societe_ville', array('label' => array('text' => __('Ville société'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	 echo $this->Form->input('societe_pays', array('label' => array('text' => __('Pays société'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>','options' => $select_countries_society,'selected' => $selected_countries_society, 'class' => 'form-control country_choice country_society'));
	 
	 
	 if(isset($donnees['User']['country_id'])){
		 switch ($donnees['User']['country_id']) {
			case 2:
				echo $this->Form->input('belgium_save_num', array('label' => array('text' => __('N° d\'enregistrement'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	 			echo $this->Form->input('belgium_society_num', array('label' => array('text' => __('N° d\'entreprise '), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 5:
				echo $this->Form->input('canada_id_hst', array('label' => array('text' => __('HST ID'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 60:
				echo $this->Form->input('spain_cif', array('label' => array('text' => __('CIF (NIF)'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 4:
				echo $this->Form->input('luxembourg_autorisation', array('label' => array('text' => __('Autorisation n°'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
	 			echo $this->Form->input('luxembourg_commerce_registrar', array('label' => array('text' => __('Registre du commerce n°'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 120:
				 echo $this->Form->input('marocco_ice', array('label' => array('text' => __('I.C.E'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
	 			echo $this->Form->input('marocco_if', array('label' => array('text' => __('I.F'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 145:
				echo $this->Form->input('portugal_nif', array('label' => array('text' => __('NIF / NIPC'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 157:
				echo $this->Form->input('senegal_ninea', array('label' => array('text' => __('NINEA'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
	 			echo $this->Form->input('senegal_rccm', array('label' => array('text' => __('RCCM'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
			case 180:
				echo $this->Form->input('tunisia_rc', array('label' => array('text' => __('R.C'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8 ">', 'after'=> '</div>'));
				break;
		}
	 }
	 
	 
	 
	 
	 echo $this->Form->input('rib', array('label' => array('text' => __('RIB'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	echo $this->Form->input('bank_name', array('label' => array('text' => __('Nom de votre banque'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	echo $this->Form->input('bank_address', array('label' => array('text' => __('Adresse de votre banque'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	echo $this->Form->input('bank_country', array('label' => array('text' => __('Pays de votre banque'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	echo $this->Form->input('iban', array('label' => array('text' => __('IBAN'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	echo $this->Form->input('swift', array('label' => array('text' => __('BIC ou SWIFT'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	//echo $this->Form->input('paypal', array('label' => array('text' => __('Email compte Paypal'), 'class' => 'col-sm-12 col-md-4 control-label '), 'required' => false, 'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'));
	echo $this->Form->inputs(array(
            'mode_paiement'   => array(
                'label' => array('text' => __('Mode de paiement souhaité'), 'class' => 'col-sm-12 col-md-4 control-label'),
                'options' => array('Virement' => 'Virement'),//,'Paypal' => 'Paypal'
                'required' => true,
				'between' => '<div class="col-sm-12 col-md-8">', 'after'=> '</div>'
            ))
    ); 
	?>
</div>


