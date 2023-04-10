<?php
        echo $this->Html->script('/theme/default/js/disabledButtonSubmit', array('block' => 'script'));
    ?><section class="slider-small">
		<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Votre numéro de TVA intracommunautaire') ?></h1>
	</section>
    <div class="container">

		<section class="page profile-page mt20 mb40">
			<div class="row">
				<div class="col-sm-12 col-md-9">
					<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">

					<div class="page-header">
						<h2 class="uppercase wow fadeIn" data-wow-delay="0.3s"> <?php echo __('Votre numéro de TVA intracommunautaire') ?></h2>
						<?php
							echo $this->Session->flash();
							/* titre de page */
							echo $this->element('title', array(
					
								'breadcrumb' => array(
									0   =>  array(
										'name'  =>  'Accueil',
										'link'  =>  Router::url('/',true)
									),
									1 => array(
										'name'  =>  '<span class="active">'.__('Votre numéro de TVA intracommunautaire').'</span>',
										'class' => 'active'
									)
								)
							));
						?>

					</div><!--page-header END-->
						
					<div class="row">
				<div class="col-sm-12 col-md-12">
						<p><?php echo __('Irrégularités concernant votre numéro de TVA intracommunautaire.'); ?></p>
						<p><?php echo __('Après contrôle de notre service comptabilité, il apparaît que le numéro de TVA intracommunautaire renseigné par vos soins dans votre compte expert n\'est pas enregistré auprès de votre service des impôts des entreprises. Il est primordial d\'en faire la demande dans les plus brefs délais, votre responsabilité peut être engagée. Cette demande est gratuite et se fait par Email, comme indiqué dans notre courriel envoyé le 8 octobre 2019 et en copie ci-dessous.'); ?></p><br />
						<?php echo $this->Form->create('Agents', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
														  'inputDefaults' => array(
															  'div' => '',
															 
															  'class' => 'form-control2'
														  )
										));
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_0"><input name="data[Agents][choice]" class="form-control" value="0"  type="radio" id="AgentsChoice0" style="clear:both;width:auto;float:left;margin:2px 5px 0 0;height:auto;"><label for="AgentsChoice0" style="float:left;">'.__('Ma société est basée en France').'</label>';
						
						echo '<div id="choice_tva_fr" style="display:none;padding-left:15px;">';
							
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_2"><input name="data[Agents][choice]" class="form-control" value="2"  type="radio" id="AgentsChoice2" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice2" style="width: 90%;">'.__('Ma demande est déjà effectuée auprès de mon SIE - Service des Impôts des entreprises de ma région et j\'attends mon numéro de TVA intracommunautaire valide.').'</label></p>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_3"><input name="data[Agents][choice]" class="form-control" value="3"  type="radio" id="AgentsChoice3" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice3">'.__('Non je n\'ai pas encore fait ma demande.').'</label></p>';
						
						echo '<div id="desc_tva_fr" style="display:none">';
							echo '<p style="clear:both;width:100%;float:left;"><input name="data[Agents][desc]" class="form-control" value="" placeholder="'.__('Merci d\'en indiquer les raisons').'"  type="text" id="AgentsDesc" style="clear:both;width:100%;float:left;"></p>';
						echo '</div>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_4"><input name="data[Agents][choice]" class="form-control" value="4"  type="radio" id="AgentsChoice4" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice4">'.__('Demander un rappel téléphonique de la part de Nicolas').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_8"><input name="data[Agents][choice]" class="form-control" value="8"  type="radio" id="AgentsChoice8" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice8" style="width: 90%;">'.__('Ma demande est bien réalisée auprès de mon centre des impôts et j\'ai relancé celui-ci').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_10"><input name="data[Agents][choice]" class="form-control" value="10"  type="radio" id="AgentsChoice10" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice10" style="width: 90%;">'.__('Ma demande est bien réalisée auprès de mon centre des impôts et mais je n\'ai pas encore relancé celui-ci').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_12"><input name="data[Agents][choice]" class="form-control" value="12"  type="radio" id="AgentsChoice12" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice12" style="width: 90%;">'.__('Mon numéro est désormais validé par mon centre des impôts mais je dois vous envoyer  le document attestant par Email').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_14"><input name="data[Agents][choice]" class="form-control" value="14"  type="radio" id="AgentsChoice14" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice14" style="width: 90%;">'.__('Mon numéro est désormais validé par mon centre des impôts et je vous ai déjà envoyé le document attestant par Email').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_16"><input name="data[Agents][choice]" class="form-control" value="16"  type="radio" id="AgentsChoice16" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice16" style="width: 90%;">'.__('Mon numéro de TVA intracommunautaire est bien valide, je vous en ai fournis la preuve par document').'</label></p>';
					
					
						
						echo '</div>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_1"><input name="data[Agents][choice]" class="form-control" value="1"  type="radio" id="AgentsChoice1" style="clear:both;width:auto;float:left;margin:2px 5px 0 0;height:auto;"><label for="AgentsChoice1" style="float:left;">'.__('Ma société n\'est pas basée en France').'</label></p>';
						
						echo '<div id="choice_tva_notfr" style="display:none;padding-left:15px;">';
							
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_5"><input name="data[Agents][choice]" class="form-control" value="2"  type="radio" id="AgentsChoice5" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice5" style="width: 90%;">'.__('Ma demande est déjà effectuée auprès de mon Service des Impôts des entreprises de ma région et j\'attends mon numéro de TVA intracommunautaire valide.').'</label></p>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_6"><input name="data[Agents][choice]" class="form-control" value="3"  type="radio" id="AgentsChoice6" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice6">'.__('Non je n\'ai pas encore fait ma demande.').'</label></p>';
						
						echo '<div id="desc_tva_notfr" style="display:none">';
							echo '<p style="clear:both;width:100%;float:left;"><input name="data[Agents][desc2]" class="form-control" value="" placeholder="'.__('Merci d\'en indiquer les raisons.').'"  type="text" id="AgentsDesc" style="clear:both;width:100%;float:left;"></p>';
						echo '</div>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_7"><input name="data[Agents][choice]" class="form-control" value="4"  type="radio" id="AgentsChoice7" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice7">'.__('Demander un rappel téléphonique de la part de Nicolas.').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_9"><input name="data[Agents][choice]" class="form-control" value="9"  type="radio" id="AgentsChoice8" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice9" style="width: 90%;">'.__('Ma demande est bien réalisée auprès de mon centre des impôts et j\'ai relancé celui-ci.').'</label></p>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_11"><input name="data[Agents][choice]" class="form-control" value="11"  type="radio" id="AgentsChoice11" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice11" style="width: 90%;">'.__('Ma demande est bien réalisée auprès de mon centre des impôts et mais je n\'ai pas encore relancé celui-ci.').'</label></p>';
						
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_13"><input name="data[Agents][choice]" class="form-control" value="13"  type="radio" id="AgentsChoice13" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice13" style="width: 90%;">'.__('Mon numéro est désormais validé par mon centre des impôts mais je dois vous envoyer  le document attestant par Email.').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_15"><input name="data[Agents][choice]" class="form-control" value="15"  type="radio" id="AgentsChoice15" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice15" style="width: 90%;">'.__('Mon numéro est désormais validé par mon centre des impôts et je vous ai déjà envoyé le document attestant par Email.').'</label></p>';
					
						echo '<p style="clear:both;width:100%;float:left;" id="VAT_choice_17"><input name="data[Agents][choice]" class="form-control" value="17"  type="radio" id="AgentsChoice17" style="clear:both;width:auto;float:left;margin:4px 5px 0 0;height:auto;"><label for="AgentsChoice17" style="width: 90%;">'.__('Mon numéro de TVA intracommunautaire est bien valide, je vous en ai fournis la preuve par document.').'</label></p>';
						
						echo '</div>';
						
							
						echo '<p class="display:inline-block;width:100%;clear:both;float:left;text-align:center">';
						echo '<input class="btn btn-pink btn-pink-modified" type="submit" value="VALIDER" style="margin:0 auto;display:block;">';
						//echo $this->Form->end(array('label' => __('VALIDER'), 'class' => 'btn btn-pink btn-pink-modified' ));
						echo '</p>';
						echo '</form>';
						?>	
						</div></div>	
					<div id="txt_tva_fr" style="margin-top:40px;display:none">
						<?php echo __('<b>COPIE DE NOTRE  EMAIL DU 08/10/2019</b><br /><br />
						Cher(e) Expert,<br />
<br />
Après contrôle de notre service comptabilité, il apparaît que le numéro de TVA intracommunautaire renseigné par vos soins dans votre compte expert n\'est pas enregistré auprès de votre service des impôts des entreprises (SIE) qui vous le transmet automatiquement lors de votre immatriculation.<br />
<br />
Il est primordial et obligatoire de faire votre demande dans les plus brefs délais. A défaut, votre prochain règlement du mois d\'Octobre sera suspendu jusqu\'à obtention de ce numéro, sachant que vous opérez sur la plateforme Spiriteo en nous ayant transmis un numéro de TVA invalide malgré notre alerte vous demandant de bien vérifier la validité de celui-ci auprès de votre centre des impôts, votre responsabilité étant engagée à ce titre.<br />
<br />
L\'enregistrement de ce numéro ne modifie en rien votre rémunération ou contrat avec la plateforme Spiriteo. Simplement, l\'enregistrement de ce numéro de TVA intracommunautaire est une condition requise pour la non facturation de la TVA entre deux pays de l\'Union Européenne.<br />
<br />
Attention : Même si vous n\'êtes pas assujettis à la TVA en France, vous devez malgré tout et obligatoirement obtenir celui-ci pour travailler avec une société en Europe, ceci est absolument indispensable.<br />
<br />
L\'obtention d\'un numéro de TVA intracommunautaire est gratuite et le délai est de 5 à 10 jours selon le centre des impôts. Il faut donc vous y prendre dès maintenant et obtenir ce dernier obligatoirement avant le 21 octobre 2019, date limite pour fourniture auprès de nos services.<br />
<br />
La demande d\'obtention de ce numéro de TVA intracommunautaire se fait par Email auprès de votre centre des impôts des entreprises (SIE) habituel :<br />
Ce numéro de TVA intracommunautaire est basé sur votre numéro Siren habituel ( + deux lettres et deux chiffres ), sa fourniture est donc aisée et rapide dès que la demande est effectuée.<br />
<br />
Pour faire votre demande par Email auprès de votre centre des impôts voici la marche à suivre :<br />
<br />
1/ Connaître l\'email de votre centre des impôts en cliquant ici : https://lannuaire.service-public.fr/navigation/sie<br />
Suivez les 3 étapes en image ci-dessous en bas de cet email pour trouver cette adresse email.<br />
<br />
Pour vous faciliter la tâche voici également un courrier ou lettre type à envoyer dans votre email :<br />
<br />
Je soussigné Madame Monsieur ................................ ( Mettre votre nom )<br />
<br />
Numéro Siret ou Siren : ...............................................<br />
Adresse : ..................................................................... ( Mettre votre adresse )<br />
Téléphone : ...........................................  ( Mettre votre numéro de téléphone )<br />
Par le présent Email je sollicite la délivrance d\'un numéro de TVA intracommunautaire afin de travailler avec la société suivante basée en Irlande.<br />
<br />
Dénomination : GLASSGEN LIMITED<br />
Numéro de société ou " Registered Number " : 649312<br />
Adresse : 1ST FLOOR 9 EXCHANGE PLACE, I.F.S.C. DUBLIN 1 D01X8H2, IRELAND<br />
<br /><br />
Le relation avec cette société sera basée sur la fourniture de " Prestation de service ", aucun produit fini ou physique ne sera acheminé, les prestations étant dématériallisées.<br />
<br />
Dans l\'attente de votre retour, veuillez agréer Madame, Monsieur, mes plus sincères salutations.<br />
<br />
Madame, Monsieur ..................... ( Mettre votre nom )<br />
<br />
Signature : ................................... ( Signer votre lettre )<br />
<br />
<br />
ETAPES POUR CONNAITRE l\'EMAIL DE VOTRE CENTRE DES IMPOTS DES ENTREPRISES ou SIE : https://lannuaire.service-public.fr/navigation/sie<br />
<br />
Etape 1 : Tapez la ville ou commune de votre centre des impôts<br />
<br />
<img src="https://www.spiriteo.com/media/cms_photo/image/Image_impot_1.png" style="display:block;width:100%;height:auto" />
<br />
Etape 2 : Cliquez sur la commune correspondant à votre centre des impôts<br />
<br />
 <img src="https://www.spiriteo.com/media/cms_photo/image/Image_impot_2.png" style="display:block;width:100%;height:auto" />
<br />
Etape 3 : Trouvez l\'email et numéro de téléphone de votre centre des impôts<br />
<br />
 <img src="https://www.spiriteo.com/media/cms_photo/image/Image_impot_3.png" style="display:block;width:100%;height:auto" />
<br />
Lorsque vous aurez obtenu celui-ci notre système interrogera ce site afin de nous assurer que votre numéro de TVA intracommunautaire est désormais valide, donc déclaré par votre centre des impôts. <br />
<br />
Cordialement,<br />
L\'équipe Spiriteo<br />'); ?>
						
					</div>
						
					<div id="txt_tva_notfr" style="margin-top:40px;display:none">
						<?php echo __('<b>COPIE DE NOTRE EMAIL DU 08/10/2019</b><br /><br />
					Cher(e) Expert,<br />
<br />
Après contrôle de notre service comptabilité, il apparaît que le numéro de TVA intracommunautaire renseigné par vos soins dans votre compte expert n\'est pas enregistré auprès du service des impôts des entreprises de votre pays qui vous le transmet normalement automatiquement lors de votre immatriculation.<br />
<br />
Il est primordial et obligatoire de faire votre demande dans les plus brefs délais. A défaut, votre prochain règlement du mois d\'Octobre sera suspendu jusqu\'à obtention de ce numéro, sachant que vous opérez sur la plateforme Spiriteo en nous ayant transmis un numéro de TVA invalide malgré notre alerte vous demandant de bien vérifier la validité de celui-ci auprès de votre centre des impôts, votre responsabilité étant engagée à ce titre.<br />
<br />
L\'enregistrement de ce numéro ne modifie en rien votre rémunération ou contrat avec la plateforme Spiriteo. Simplement, l\'enregistrement de ce numéro de TVA intracommunautaire est une condition requise pour la non facturation de la TVA entre deux pays de l\'Union Européenne.<br />
<br />
Attention : Même si vous n\'êtes pas assujettis à la TVA dans votre pays, vous devez malgré tout et obligatoirement obtenir celui-ci pour travailler avec une société en Europe, ceci est absolument indispensable.<br />
<br />
L\'obtention d\'un numéro de TVA intracommunautaire est gratuite et le délai est de 5 à 10 jours selon le centre des impôts. Il faut donc vous y prendre dès maintenant et obtenir ce dernier obligatoirement avant le 21 octobre 2019, date limite pour fourniture auprès de nos services.<br />
<br />
La demande d\'obtention de ce numéro de TVA intracommunautaire se fait bien souvent par Email auprès de votre centre des impôts des entreprises habituel :<br />
Ce numéro de TVA intracommunautaire est basé sur votre numéro de société habituel, sa fourniture est donc aisée et rapide dès que la demande est effectuée.<br />
<br />
Pour vous faciliter la tâche voici également un courrier ou lettre type à envoyer dans votre email :<br />
<br />
Je soussigné Madame, Monsieur ................................ ( Mettre votre nom )<br />
<br />
Numéro Siret ou Siren : ...............................................<br />
Adresse : ..................................................................... ( Mettre votre adresse )<br />
Téléphone : ...........................................  ( Mettre votre numéro de téléphone )<br />
Par le présent Email je sollicite la délivrance d\'un numéro de TVA intracommunautaire afin de travailler avec la société suivante basée en Irlande.<br />
<br />
Dénomination : GLASSGEN LIMITED<br />
Numéro de société ou " Registered Number " : 649312<br />
Adresse : 1ST FLOOR 9 EXCHANGE PLACE, I.F.S.C. DUBLIN 1 D01X8H2, IRELAND<br />
<br />
Le relation avec cette société sera basée sur la fourniture de " Prestation de service ", aucun produit fini ou physique ne sera acheminé, les prestations étant dématériallisées.<br />
<br />
Dans l\'attente de votre retour, veuillez agréer Madame, Monsieur, mes plus sincères salutations.<br />
<br />
Madame, Monsieur ..................... ( Mettre votre nom )<br />
<br />
Signature : ................................... ( Signer votre lettre )<br />
<br />
<br />
Lorsque vous aurez obtenu celui-ci notre système interrogera le site dont voici le lien, comme vous pouvez le faire vous même, afin de nous assurer que votre numéro de TVA intracommunautaire est désormais valide, donc déclaré par votre centre des impôts : http://ec.europa.eu/taxation_customs/vies/vatResponse.html<br />
<br />
Encore merci de votre retour rapide.<br />
<br />
Cordialement<br />
L\'équipe Spiriteo<br />'); ?>
						

					</div>

				  </div>
					
				</div><!--col-9 END-->
<?php
				echo $this->Frontblock->getRightSidebar();
			?>
				<!--col-md-3 END-->
</div><!--row END-->
</section><!--expert-list END-->

</div>