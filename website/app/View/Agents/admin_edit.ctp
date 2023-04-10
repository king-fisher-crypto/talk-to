<?php
echo $this->Html->script('/theme/default/js/crop/agent_admin_photo', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/crop/jquery.color', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/crop/jquery.Jcrop.min', array('block' => 'script'));
    //echo $this->Html->script('/theme/default/js/subscribe_agent', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/inputDateEmpty', array('block' => 'script'));

    echo $this->Html->css('/theme/default/css/crop/jquery.Jcrop.min', array('block' => 'css'));

echo $this->Metronic->titlePage(__('Agents'),__('Edition d\'un agent'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Agents'),
        'classes' => 'icon-user-md',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
    ),
    2 => array(
        'text' => (!isset($agent['User']['pseudo']) && empty($agent['User']['pseudo'])?__('Agent'):$agent['User']['pseudo']),
        'classes' => 'icon-zoom-in',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id']))
    ),
    3 => array(
        'text' => __('Edition'),
        'classes' => 'icon-edit-sign',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $agent['User']['id']))
    )
));
echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="span8">
        <div class="portlet box blue">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Editer l\'agent'); ?></div>
            </div>
            <div class="portlet-body form">
                <?php
                    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,'enctype' => 'multipart/form-data', 'inputDefaults' => array('class' => 'span10')));
                    echo '<h3 class="form-section">'.__('Informations de l\'agent').'</h3>';

                    //Les inputs du formulaire
                    $inputs = array(
                        'firstname' => array('label' => array('text' => __('Prénom'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'lastname' => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'pseudo' => array('label' => array('text' => __('Pseudo'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'email' => array('label' => array('text' => __('Email'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'passwd2' => array('label' => array('text' => __('Confirmation mot de passe'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'password'),
                        'phone_number' => array(
                            'label'     => array('text' => __('Numéro de téléphone'), 'class' => 'control-label required'),
                            'placeholder' => 'Ex : 33XXXXXXXXX ou 0XXXXXXXXX',
                            'required'  => true,
                            'div' => 'control-group span6',
                            'between' => '<div class="controls">',
                            'after' => '</div>',
                            'type'      => 'tel',
                            'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
                        ),
						'phone_number2' => array(
                            'label'     => array('text' => __('Numéro de téléphone 2'), 'class' => 'control-label'),
                            'placeholder' => 'Ex : 33XXXXXXXXX ou 0XXXXXXXXX',
                            'required'  => false,
                            'div' => 'control-group span6',
                            'between' => '<div class="controls">',
                            'after' => '</div>',
                            'type'      => 'tel',
                            'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
                        ),
                        'phone_mobile' => array(
                            'label'     => array('text' => __('Numéro mobile'), 'class' => 'control-label'),
                            'placeholder' => 'Ex : 33XXXXXXXXX ou 0XXXXXXXXX',
                            'required'  => false,
                            'div' => 'control-group span6',
                            'between' => '<div class="controls">',
                            'after' => '</div>',
                            'type'      => 'tel',
                            'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
                        ),
                        'phone_api_use' => array(
                            'label'     => array('text' => __('Numéro utilisé'), 'class' => 'control-label'),
                            'placeholder' => 'Ex : 33XXXXXXXXX ou 0XXXXXXXXX',
                            'required'  => false,
                            'div' => 'control-group span6',
                            'between' => '<div class="controls">',
                            'after' => '</div>',
                            'type'      => 'tel',
                            'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
                        ),
                        'creditMail' => array('label' => array('text' => __('Nombre de crédits pour un mail'), 'class' => 'control-label'),
                                              'div' => 'control-group span6',
                                              'between' => '<div class="controls">',
                                              'after' => '<p>'.__('Si sans valeur alors par défaut : ').Configure::read('Site.creditPourUnMail').' '.__('crédits').'</p></div>'
                        ),
                        'address' => array('label' => array('text' => __('Adresse'), 'class' => 'control-label'), 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'postalcode' => array('label' => array('text' => __('Code postal'), 'class' => 'control-label'), 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'city' => array('label' => array('text' => __('Ville'), 'class' => 'control-label'), 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'country_id' => array('label' => array('text' => __('Pays de résidence'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_countries),

						'siret' => array('label' => array('text' => __('Siret'), 'class' => 'control-label'), 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'society_type_id' => array('label' => array('text' => __('Statut'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_society_type),

						'societe_statut' => array('label' => array('text' => __('Statut autre'), 'class' => 'control-label'), 'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'societe' => array('label' => array('text' => __('Societé'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'societe_adress' => array('label' => array('text' => __('Societé adresse'), 'class' => 'control-label'), 'autocomplete' => 'off','div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'societe_adress2' => array('label' => array('text' => __('Societé adresse2'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'societe_cp' => array('label' => array('text' => __('Societé Code postal'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'societe_ville' => array('label' => array('text' => __('Societé ville'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'societe_pays' => array('label' => array('text' => __('Societé Pays'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_countries),
						'vat_num_spirit' => array('label' => array('text' => __('N° TVA Intracommunautaire'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
                        'invoice_vat_id' => array('label' => array('text' => __('TVA Type'), 'class' => 'control-label required'), 'required' => false, 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_invoice_vat),

						'vat_num_proof' => array('label' => array('text' => __('Preuve TVA intra fourni'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => array(0 => 'Non', 1=>'Oui')),

						'belgium_save_num' => array('label' => array('text' => __('Belgique num enregistrement'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'belgium_society_num' => array('label' => array('text' => __('Belgique num société'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'canada_id_hst' => array('label' => array('text' => __('Canada HST ID'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'spain_cif' => array('label' => array('text' => __('Espagne CIF'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'luxembourg_autorisation' => array('label' => array('text' => __('Luxembourg autorisation'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'luxembourg_commerce_registrar' => array('label' => array('text' => __('Luxembourg registre commerce'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'marocco_ice' => array('label' => array('text' => __('Maroc ICE'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'marocco_if' => array('label' => array('text' => __('Maroc IF'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'portugal_nif' => array('label' => array('text' => __('Portugal NIF'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'senegal_ninea' => array('label' => array('text' => __('Senegal NINEA'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'senegal_rccm' => array('label' => array('text' => __('Senegal RCCM'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
						'tunisia_rc' => array('label' => array('text' => __('Tunise RC'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),

						'stripe_account' => array('label' => array('text' => __('ID compte Stripe'), 'class' => 'control-label'),'autocomplete' => 'off', 'div' => 'control-group span6', 'between' => '<div class="controls">', 'after' => '</div>'),
				    );

					echo $this->Metronic->inputsAdminEdit($inputs);
					?>


						<?php

		if(!isset($univers) || empty($univers)) $univers = array();
		if(!isset($langs) || empty($langs)) $langs = array();
		if(!isset($countries) || empty($countries)) $countries = array();
		$boucleFirst = floor(count($category_langs)/2);
    	$boucleSecond = count($category_langs) - $boucleFirst;
		$nomModel = 'Agent';
		?>
		<div class="row-fluid">
			<div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
				<label class="col-sm-12 col-md-4 control-label" for="" style="margin-top:-5px;"><?php echo __('Langues'); ?></label>
				<div class="col-sm-12 col-md-8">
					<?php
					foreach ($select_langs as $k => $lang){
						if($k != 8 && $k != 10 && $k != 11 && $k != 12)
						echo '<div class="checkbox checkbox-inline" style="display:inline-block;margin-left: 10px;margin-right: 10px;"><label for="'. $nomModel .'Langs'.$k.'"><input type="checkbox" name="data['. $nomModel .'][langs][]" '. (in_array($k, $langs) || $k == 1?'checked':'') .' value='.$k.' id="'. $nomModel .'Langs'.$k.'"/><span></span><i class="lang_flags lang_'.key($lang).' " data-toggle="tooltip" data-original-title="'. $lang[key($lang)] .'" title="'. $lang[key($lang)] .'"></i></label></div>';
					}
					?>
				</div>
			</div>
		</div>
		<div class="row-fluid">
			<div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
		        <label class="col-sm-12 col-md-4 control-label "  style="margin-top:-5px;" for="<?php echo $nomModel; ?>Countries"><?php echo __('Domains'); ?> </label>
				<div class="col-sm-12 col-md-8">
					<?php
					$tickedFlag = array(1,3,4,5,13);
					$tickedRead = array(1,3,4,5,13);
						foreach ($select_countries_sites as $id => $country){

							$checked = (in_array($id, $countries)?'checked':'');
							$readonly = '';
							if(in_array($id,$tickedFlag)) $checked = 'checked';
							if(in_array($id,$tickedRead)) $readonly = 'onclick="return false"';

							echo '<div class="checkbox checkbox-inline" style="display:inline-block;margin-left: 10px;margin-right: 10px;"><label for="'. $nomModel .'Countries'.$id.'"><input type="checkbox" name="data['. $nomModel .'][countries][]" '. $checked .' '.$readonly.' value='.$id.' id="'. $nomModel .'Countries'.$id.'"/><span></span><i class="country_flags country_'.$id.' " data-original-title="'. __($country) .'" data-toggle="tooltip" title="'. __($country) .'"></i></label></div>';
						}
					?>

				</div>
			</div>
		</div>
		<div class="row-fluid">
			 <div class="form-group">
        <label  style="margin-top:-5px;height:50px;" for="<?php echo $nomModel; ?>Categories" class="col-sm-12 col-md-4 control-label "><?php echo __('Univers'); ?></label>
        <div class="col-sm-12 col-md-8">
            <?php
            $i = 0;
            foreach ($category_langs as $k => $val){
                //Si catégorie "Accueil";
              //  if($k == 1) continue;

                echo '<div class="checkbox" style="display:inline-block;margin-left: 10px;margin-right: 10px;">';
                echo '<label class="norequired" for="'. $nomModel .'Categories'. $k .'"><input type="checkbox" name="data['. $nomModel .'][categories][]" '. (in_array($k, $univers)?'checked':'') .' value='.$k.' id="'. $nomModel .'Categories'.$k.'"/>';
                echo ''. __($val) .'</label>';
                echo '</div>';
               // unset($category_langs[$k]);
               // if(++$i == $boucleFirst) break;
            }
            ?>
            <?php
           /* $i = 0;
            foreach ($category_langs as $k => $val){
                //Si catégorie "Accueil";
                if($k == 1) continue;

                echo '<div class="checkbox" style="display:inline-block;margin-left: 10px;margin-right: 10px;">';
                echo '<label class="norequired" for="'. $nomModel .'Categories'. $k .'"><input type="checkbox" name="data['. $nomModel .'][categories][]" '. (in_array($k, $univers)?'checked':'') .' value='.$k.' id="'. $nomModel .'Categories'.$k.'"/>';
                echo ''. __($val) .'</label>';
                echo '</div>';
                unset($category_langs[$k]);
                if(++$i == $boucleSecond) break;
            }*/
            ?>
        </div>
    </div>
		</div>





				<br /><br />

                    <div class="row-fluid">
                    <div class="control-group span6">
<label class="control-label" for="AgentMailPrice">Prix du mail</label>
<div class="controls">
<input id="AgentMailPrice" class="span10" type="number" value="12" name="data[Agent][mail_price]">
</div>
</div>
 </div><div class="row-fluid">
                    <div class="control-group span12">
<label class="control-label" for="AgentOrderCat">Catégorie rémunération</label>
<div class="controls">
<input id="AgentOrderCat" class="span10" type="number" value="<?php echo $agent['User']['order_cat']; ?>" name="data[Agent][order_cat]">
	<p style="font-size:9px;">Catégorie calculé => <?=$id_cost ?> -> <?=$nb_minutes ?> min</p>
<p style="font-size:9px;">
Rémunération 1 de 0 à 9999 min : 21.00 €/heure soit 0.35 €/min HT
<br >
Rémunération 2 de 10 000 à 49 999 min: 22.20 €/heure soit 0.37 €/min HT
<br>
Rémunération 3 de 50 000 à 99 999 min : 24.60 €/heure soit 0.41 €/min HT
<br>
Rémunération 4 au delà de 100 000 min : 27.00 €/heure soit 0.45 €/min HT
<br>
Rémunération 5 : Redirection vers portable expert: 19.20 €/heure soit 0.32 €/min HT
<br>
Rémunération 6 : pour possible éventualité de rémunération différente à venir
<br>
</p>
</div>
</div>
</div>



                  <div class="row-fluid">
                  <div class="control-group span6">
<label class="control-label" type="select" for="AgentFlagNew">Nbr jours Nouveau</label>
<div class="controls">
<select id="AgentFlagNew" class="span10" name="data[Agent][flag_new]">
<option value=""> </option>
<option <?php if($agent['User']['flag_new'] == 30) echo 'selected="selected"'; ?> value="30">30</option>
<option <?php if($agent['User']['flag_new'] == 45) echo 'selected="selected"'; ?> value="45">45</option>
<option <?php if($agent['User']['flag_new'] == 60) echo 'selected="selected"'; ?> value="60">60</option>
<option <?php if($agent['User']['flag_new'] == 75) echo 'selected="selected"'; ?> value="75">75</option>
</select>
</div>
</div>
<div class="control-group span6">
<label class="control-label" for="AgentDateNew">A partir du</label>
<div class="controls">
<input id="AgentDateNew" class="span10" type="text" value="<?php echo $agent['User']['date_new']; ?>" name="data[Agent][date_new]">
</div>
</div>
<div class="row-fluid">
<div class="control-group span6">
<label class="control-label" for="AgentNbConsult">Nb consult ajouté</label>
<div class="controls">
<input id="AgentNbConsult" class="span10" type="text" value="<?php echo $agent['User']['nb_consult_ajoute']; ?>" name="data[Agent][nb_consult_ajoute]">
</div>
</div>
 </div>
<fieldset ><legend>Recevoir les alertes Spiriteo :</legend>
					  <div class="row-fluid">
<div class="control-group span6">
<label class="control-label" for="AgentAlertMail">Par email</label>
<div class="controls">
<input id="AgentSubscribeMail"  type="hidden" value="<?php echo $agent['User']['subscribe_mail']; ?>" name="data[Agent][subscribe_mail]">
<input id="AgentAlertMail" class="span10" type="text" value="<?php echo $agent['User']['alert_mail']; ?>" name="data[Agent][alert_mail]"><br />
	<p style="font-size:9px;">Communication email non répondu<br />Alerte présence agents VS clients</p>
</div>
</div>
</div>
	<div class="row-fluid">
<div class="control-group span6">
<label class="control-label" for="AgentAlertNight">Par email la nuit</label>
<div class="controls">
<input id="AgentAlertNight" class="span10" type="text" value="<?php echo $agent['User']['alert_night']; ?>" name="data[Agent][alert_night]">
	<br /><p style="font-size:9px;">Alerte présence agents VS clients</p>
</div>
</div>
</div>
<div class="row-fluid">
<div class="control-group span6">
<label class="control-label" for="AgentAlertPhone">Par appel téléphonique</label>
<div class="controls">
<input id="AgentAlertPhone" class="span10" type="text" value="<?php echo $agent['User']['alert_phone']; ?>" name="data[Agent][alert_phone]">
	<br /><p style="font-size:9px;">Communication tchat commence</p>
</div>
</div>
</div>
<div class="row-fluid">
<div class="control-group span6">
<label class="control-label" for="AgentAlertSMS">Par SMS</label>
<div class="controls">
<input id="AgentAlertSMS" class="span10" type="text" value="<?php echo $agent['User']['alert_sms']; ?>" name="data[Agent][alert_sms]">
	<br /><p style="font-size:9px;">Communication email non répondu<br />Alerte présence agents VS clients</p>
</div>
</div>
</div>
	<hr >
</fieldset>

 </div>

      <div class="col-lg-7">
            <div class="photo_agent preview"><?php

				if(empty($agent['User']['agent_number'])){
					$filePhoto = new File(Configure::read('Site.pathInscriptionMedia').'/'.$agent['User']['id'].'/'.$agent['User']['id'].'_listing.jpg');
					$pathPhoto = $filePhoto->name;
					echo $this->Html->image('/'.Configure::read('Site.pathInscriptionMedia').'/'.$agent['User']['id'].'/'.$agent['User']['id'].'_listing.jpg');
				}
				elseif($agent['User']['agent_number']) {
					$filePhoto = new File(Configure::read('Site.pathPhoto').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$agent['User']['agent_number'].'_listing.jpg');
					$pathPhoto = $filePhoto->name;
					echo $this->Html->image('/'.Configure::read('Site.pathPhoto').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$pathPhoto);
				}else{
					echo $this->Html->image('/'.Configure::read('Site.defaultImage'), array('id' => 'previewCrop'));
				}

			 	 ?></div>
            <br/>
            <input type="file" url="<?php echo $this->Html->url(array('controller' => 'users', 'action' => 'modalPhotoAgent'));?>" name="data[Agent][photo]" accept="image/*" id="UserPhoto"/>
            <input type="hidden" name="data[Agent][crop][x]" id="UserCropX"/>
            <input type="hidden" name="data[Agent][crop][y]" id="UserCropY"/>
            <input type="hidden" name="data[Agent][crop][h]" id="UserCropH"/>
            <input type="hidden" name="data[Agent][crop][w]" id="UserCropW"/>

        </div>


        <br/><br/>

        <?php
        echo $this->Form->inputs(array(
            'texte' => array(
                'label' => array(
                    'text' => __('Présentation'),
                    'class' => 'control-label col-lg-12 ',
			'style' => 'width:100% !important;text-align:left'
                ),
                'required' => true,
                'type' => 'textarea',
                'after' => '</div>',
                'value' => html_entity_decode($agent['User']['texte']),
                'between' => '<div class="col-lg-offset-3 col-lg-7">'
            ),
            'lang_id' => array('type' => 'hidden', 'value' => $agent['User']['lang_id'])
        ));

		 ?>

		 <br/><br/>

        <?php
        echo $this->Form->inputs(array(
            'absence' => array(
                'label' => array(
                    'text' => __('Infos absence ( Visible par l’expert dans son BO ) '),
                    'class' => 'control-label col-lg-12 ',
					'style' => 'width:100% !important;text-align:left'
                ),
                'required' => false,
                'type' => 'textarea',
                'after' => '</div>',
                'value' => html_entity_decode($agent['User']['absence']),
                'between' => '<div class=" col-lg-12">'
            )

        ));

		 ?>

		<br/><br/>

				<?php


                    echo $this->Form->end(array(
                        'label' => __('Enregistrer'),
                        'class' => 'btn blue',
                        'div' => array('class' => 'controls')
                    ));
                ?>
            </div>
        </div>
    </div>
    <div class="span4">
        <div class="portlet box yellow"><div class="portlet-title">
                <div class="caption"><?php echo __('Modifier le statut de l\'agent'); ?></div>
            </div>
            <div class="portlet-body form">
                <?php
                    $badge = '';
                    $labelLink = '';
                    switch ($agent['User']['agent_status']){
                        case 'busy' :
                            $badge = 'warning';
                            $badgeLabel = __('Occupé');
                            break;
                        case 'available' :
                            $badge = 'success';
                            $badgeLabel = __('Disponible');
                            $linkLabel = __('Indisponible');
                            $linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
                            break;
                        case 'unavailable' :
                            $badge = 'danger';
                            $badgeLabel = __('Indisponible');
                            $linkLabel = __('Disponible');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
                            break;
                    }

                    echo '<p>'.__('Le statut actuel de l\'expert : ').'<span class="badge badge-'.$badge.'">'.$badgeLabel.'</span></p>';
                    if($agent['User']['agent_status'] !== 'busy'):

                        echo '<p>'.__('Modifier le statut en : ');

                        echo $this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'change_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        ).'</p>';
                    else : ?>
                        <?php echo '<p>'.__('Vous ne pouvez pas modifier son statut pour le moment.').'</p>'; ?>
                <?php endif; ?>

                <?php

					echo '<p><span style="width:200px;display:block">'.__('Consult Tel : ');
					$badge = '';
                    $labelLink = '';
                    switch ($agent['User']['consult_phone']){
                        case 1 :
                            $badge = 'danger';
                            $badgeLabel = __('Désactiver');
							$linkLabel = __('Désactiver');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
							echo '<strong>'.__('actif ').'</strong>';
                            break;
                        case 0 :
							$badge = 'success';
                            $badgeLabel = __('Activer');
                            $linkLabel = __('Activer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
							echo '<strong>'.__('inactif ').'</strong>';
                            break;
						case -1 :
							$badge = 'success';
                            $badgeLabel = __('Activer');
                            $linkLabel = __('Activer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
							echo '<strong>'.__('bloqué ').'</strong>';
                    }
					echo '</span>';
                        echo $this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'admin_consult_phone_modify_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        );
						switch ($agent['User']['consult_phone']){
                        case 1 :
						case 0 :
                            $badge = 'danger';
                            $badgeLabel = __('Bloquer');
							$linkLabel = __('Bloquer');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
                            break;
						case -1 :
                            $badge = 'success';
                            $badgeLabel = __('Débloquer');
                            $linkLabel = __('Débloquer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
                            break;
                    }
						echo ' '.$this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'consult_phone_change_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        ).'</p>';
                ?>
                <?php
					echo '<p><span style="width:200px;display:block">'.__('Consult Mail : ');
					$badge = '';
                    $labelLink = '';
                    switch ($agent['User']['consult_email']){
                        case 1 :
                            $badge = 'danger';
                            $badgeLabel = __('Désactiver');
							$linkLabel = __('Désactiver');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
							echo '<strong>'.__('actif ').'</strong>';
                            break;
                        case 0 :
							$badge = 'success';
                            $badgeLabel = __('Activer');
                            $linkLabel = __('Activer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
							echo '<strong>'.__('inactif ').'</strong>';
                            break;
						case -1 :
							echo '<strong>'.__('bloqué ').'</strong>';
							$badge = 'success';
                            $badgeLabel = __('Activer');
                            $linkLabel = __('Activer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
                            break;
                    }
					echo '</span>';
                        echo $this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'consult_mail_modify_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        );
						switch ($agent['User']['consult_email']){
                        case 1 :
						case 0 :
                            $badge = 'danger';
                            $badgeLabel = __('Bloquer');
							$linkLabel = __('Bloquer');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
                            break;
						case -1 :
                            $badge = 'success';
                            $badgeLabel = __('Débloquer');
                            $linkLabel = __('Débloquer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
                            break;
                    }

                        echo ' '.$this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'consult_mail_change_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        ).'</p>';
                ?>
                <?php
					echo '<p><span style="width:200px;display:block">'.__('Consult Chat : ');
					$badge = '';
                    $labelLink = '';
                    switch ($agent['User']['consult_chat']){
                        case 1 :
                            $badge = 'danger';
                            $badgeLabel = __('Désactiver');
							$linkLabel = __('Désactiver');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
							echo '<strong>'.__('actif ').'</strong>';
                            break;
                        case 0 :
							$badge = 'success';
                            $badgeLabel = __('Activer');
                            $linkLabel = __('Activer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
							echo '<strong>'.__('inactif ').'</strong>';
                            break;
						case -1 :
							echo '<strong>'.__('bloqué ').'</strong>';
							$badge = 'success';
                            $badgeLabel = __('Activer');
                            $linkLabel = __('Activer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
                            break;
                    }
					echo '</span>';
                        echo $this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'consult_chat_modify_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        );
						switch ($agent['User']['consult_chat']){
                        case 1 :
						case 0 :
                            $badge = 'danger';
                            $badgeLabel = __('Bloquer');
							$linkLabel = __('Bloquer');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
                            break;
						case -1 :
							$badge = 'success';
                            $badgeLabel = __('Débloquer');
                            $linkLabel = __('Débloquer');
                            $linkClass = 'btn green';
                            $linkIcon = 'icon-check';
                            break;
                    }

                        echo ' '.$this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'consult_chat_change_status', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        ).'</p>';
					echo '<br /><br />'.'<p><span style="width:200px;display:block">'.__('Débloquer un agent').'</span>';
						$badge = 'danger';
                            $badgeLabel = __('Débugger');
							$linkLabel = __('Débugger');
							$linkClass = 'btn red';
                            $linkIcon = 'icon-remove';
					echo $this->Metronic->getLinkButton(
                                $linkLabel,
                                array('controller' => 'agents', 'action' => 'consult_debug', 'admin' =>true, 'id' => $agent['User']['id']),
                                $linkClass,
                                $linkIcon
                        ).'</p>';
                ?>
            </div>
        </div>
    </div>
</div>
