<?php
    echo $this->Html->script('/theme/default/js/inputDateEmpty', array('block' => 'script'));
	echo $this->Html->script('/theme/default/js/survey2', array('block' => 'script'));
	echo $this->Html->script('https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('block' => 'script'));

?>
<style>

.content_cms_custom table {
	width:80%;
}
.content_cms_custom table td{
	border:1px solid #ccc;
	padding:10px;
}
.content_cms_custom p {
	line-height:1.5em;
}
</style>
<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Questionnaire préalable de candidature'); ?></h1>
</section>
<div class="container ">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">

        <?php
            echo $this->Session->flash();

			echo $this->Form->create('Agents', array('action' => 'survey_agent', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
                        'inputDefaults' => array(
							'div' => 'form-group mt20 wow fadeIn animated',
                            'class' => 'form-control'
                        )
                    ));

			 echo $this->Form->inputs(array(
                        'lastname' => array(
                            'label' => array(
                                'text' => __('Nom').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['lastname']
                        ),
                        'firstname' => array(
                            'label' => array(
                                'text' => __('Prénom').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['firstname']
                        ),
				 		'pseudo' => array(
                            'label' => array(
                                'text' => __('Pseudo composé').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['pseudo']
                        ),
				 		'address' => array(
                            'label' => array(
                                'text' => __('Adresse').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['address']
                        ),
				 		'postalcode' => array(
                            'label' => array(
                                'text' => __('Code Postal').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['postalcode']
                        ),
				 		'city' => array(
                            'label' => array(
                                'text' => __('Ville').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['city']
                        ),
				 		'country' => array(
                            'label' => array(
                                'text' => __('Pays').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['country']
                        ),
				 		'email' => array(
                            'label' => array(
                                'text' => __('Email').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['email']
                        ),
				 		'phone_number' => array(
                            'label' => array(
                                'text' => __('Tél fixe +indicatif').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['phone_number']
                        ),
				 		'phone_number2' => array(
                            'label' => array(
                                'text' => __('Tél portable +indicatif').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''//$agent['User']['phone_number2']
                        )

                    ));


					echo '<div class="text-justify mt40">'.$this->FrontBlock->getPageBlocTexte(441).'</div>';

					echo '<div class="text-justify mt40">'.$this->FrontBlock->getPageBlocTexte(442).'</div>';

					echo '<h2 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('QUALIFICATIONS & EXPÉRIENCE') .'</h2>';
					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('1. Avez-vous déjà travaillé par téléphone ? ( consultation agents ou autre )') .'</h3>';
					echo $this->Form->inputs(array(
                        'work_phone' => array(
                            'label' => array(
                                'text' => __('Oui ou Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'work_phone_time' => array(
                            'label' => array(
                                'text' => __('Combien de temps et dans quelle activité ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        )
                    ));
					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('2. Exercez-vous une autre activité professionnelle ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'activity' => array(
                            'label' => array(
                                'text' => __('Oui ou Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'activity_name' => array(
                            'label' => array(
                                'text' => __('Si oui, laquelle ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'activity_hour' => array(
                            'label' => array(
                                'text' => __("Nombre d'heures approximatif par jour, semaine ou mois").'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        )
                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('3. Depuis combien d’années pratiquez-vous les arts divinatoires ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'work_year' => array(
                            'label' => array(
                                'text' => __('Nombre d\'année(s)').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
						 'work_details' => array(
                            'label' => array(
                                'text' => __("Résumez succinctement votre expérience dans les arts divinatoires auprès du public, mois, années, fréquences journalières").' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'textarea',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        )
                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('4. Avez-vous votre propre site Internet ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'own_website' => array(
                            'label' => array(
                                'text' => __('Oui ou Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'own_website_name' => array(
                            'label' => array(
                                'text' => __('Si oui, mon site Internet est :').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        )
                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('5. Avez-vous travaillé pour d’autres plateformes ou cabinets de agents ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'other_company' => array(
                            'label' => array(
                                'text' => __('Oui ou Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        )

                    ));
					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('Si oui lesquels ? ') .'</h3>';

					echo '<fieldset class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><h4>'. __('Site 1 :') .'</h4>';
					echo $this->Form->inputs(array(
                        'site1_name' => array(
                            'label' => array(
                                'text' => __('Nom').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site1_during' => array(
                            'label' => array(
                                'text' => __('Durée').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site1_period' => array(
                            'label' => array(
                                'text' => __('Période').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site1_work' => array(
                            'label' => array(
                                'text' => __('Travaillez-vous toujours avec ? Oui / Non ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site1_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site1_profil' => array(
                            'label' => array(
                                'text' => __("Lien de votre page profil expert si toujours actif ").'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                        'site1_profil_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                    ));

					echo '</fieldset>';
					echo '<fieldset class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><h4>'. __('Site 2 :') .'</h4>';
					echo $this->Form->inputs(array(
                        'site2_name' => array(
                            'label' => array(
                                'text' => __('Nom').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site2_during' => array(
                            'label' => array(
                                'text' => __('Durée').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site2_period' => array(
                            'label' => array(
                                'text' => __('Période').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site2_work' => array(
                            'label' => array(
                                'text' => __('Travaillez-vous toujours avec ? Oui / Non ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site2_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site2_profil' => array(
                            'label' => array(
                                'text' => __("Lien de votre page profil expert si toujours actif ").'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                        'site2_profil_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                    ));

					echo '</fieldset>';
					echo '<fieldset class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><h4>'. __('Site 3 :') .'</h4>';
					echo $this->Form->inputs(array(
                        'site3_name' => array(
                            'label' => array(
                                'text' => __('Nom').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site3_during' => array(
                            'label' => array(
                                'text' => __('Durée').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site3_period' => array(
                            'label' => array(
                                'text' => __('Période').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site3_work' => array(
                            'label' => array(
                                'text' => __('Travaillez-vous toujours avec ? Oui / Non ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site3_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site3_profil' => array(
                            'label' => array(
                                'text' => __("Lien de votre page profil expert si toujours actif ").'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                        'site3_profil_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                    ));

					echo '</fieldset>';
					echo '<fieldset class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><h4>'. __('Site 4 :') .'</h4>';
					echo $this->Form->inputs(array(
                        'site4_name' => array(
                            'label' => array(
                                'text' => __('Nom').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site4_during' => array(
                            'label' => array(
                                'text' => __('Durée').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site4_period' => array(
                            'label' => array(
                                'text' => __('Période').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site4_work' => array(
                            'label' => array(
                                'text' => __('Travaillez-vous toujours avec ? Oui / Non ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site4_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site4_profil' => array(
                            'label' => array(
                                'text' => __("Lien de votre page profil expert si toujours actif ").'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                        'site4_profil_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                    ));

					echo '</fieldset>';
					echo '<fieldset class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;"><h4>'. __('Site 5 :') .'</h4>';
					echo $this->Form->inputs(array(
                        'site5_name' => array(
                            'label' => array(
                                'text' => __('Nom').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site5_during' => array(
                            'label' => array(
                                'text' => __('Durée').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site5_period' => array(
                            'label' => array(
                                'text' => __('Période').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site5_work' => array(
                            'label' => array(
                                'text' => __('Travaillez-vous toujours avec ? Oui / Non ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site5_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'site5_profil' => array(
                            'label' => array(
                                'text' => __("Lien de votre page profil expert si toujours actif ").'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                        'site5_profil_reason' => array(
                            'label' => array(
                                'text' => __('Si non, pourquoi ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
                            'value' => ''
                        ),
                    ));

					echo '</fieldset>';

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('6. Indiquez Votre ou vos spécialité(s) ? Astrologie | Cartomancie | Magnétiseur | Médiumnité | agents | Numérologie |  Tarologie | Coaching | Divination | Interprétation des rêves | autre(s)') .'</h3>';
					echo $this->Form->inputs(array(
                        'categories' => array(
                            'label' => array(
                                'text' => __('Précisez ').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'textarea',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),

                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('7. Indiquez les Langues que vous pratiquez couramment :  Français | Anglais | Allemand | Italien | Espagnol | Portugais | autres') .'</h3>';
					echo $this->Form->inputs(array(
                        'langs' => array(
                            'label' => array(
                                'text' => __('Précisez ').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'textarea',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),

                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('8. Ecrivez-vous parfaitement le français pour les consultations par Email ou Tchat ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'good_french' => array(
                            'label' => array(
                                'text' => __('Oui - Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),

                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('9. Comment qualifiez vous vos connaissances informatique-internet sur une échelle de 0 à 10') .'</h3>';
					echo $this->Form->inputs(array(
                        'good_infomatic' => array(
                            'label' => array(
                                'text' => __('Votre note :').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
							'placeholder' => 'Très mauvaise  <-  1  2  3  4  5  6  7  8  9  10  ->  Excellente',
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),

                    ));

					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('10. Avec quelle(s) méthodes(s) de consultation souhaitez-vous travailler ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'work_by_phone' => array(
                            'label' => array(
                                'text' => __('Par téléphone : Oui - Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'work_by_phone_reason' => array(
                            'label' => array(
                                'text' => __('Si non pour quelle raison ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
						'work_by_email' => array(
                            'label' => array(
                                'text' => __('Par email : Oui - Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'work_by_email_reason' => array(
                            'label' => array(
                                'text' => __('Si non pour quelle raison ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
						'work_by_tchat' => array(
                            'label' => array(
                                'text' => __('Par tchat : Oui - Non ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'work_by_tchat_reason' => array(
                            'label' => array(
                                'text' => __('Si non pour quelle raison ?').'',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                    ));
					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('11. Pourquoi avez vous choisi Spiriteo.com ?') .'</h3>';
					echo $this->Form->inputs(array(
                        'why_us' => array(
                            'label' => array(
                                'text' => __('Votre raison :').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),

                    ));
					echo '<h2 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('PLAGES HORAIRES DE DISPONIBILITÉ ET DE PRÉSENCE') .'</h2>';

					echo '<p>Vous trouverez ci-dessous un paragraphe nous permettant d’évaluer votre temps de présence sur Spiriteo. Nous savons que ces horaires seront approximatifs et que vous pourrez être parfois présent, parfois absent aux horaires indiqués, mais cela nous permet néanmoins de nous positionner sur votre présence.</p>';


					echo '<h3 class="text-center wow fadeIn animated mt40" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('Je serai disponible pour effectuer des consultations') .'</h3>';
					echo $this->Form->inputs(array(
                        'hours' => array(
                            'label' => array(
                                'text' => __('Environ :').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
							'placeholder' => 'heures par jour',
                            'required' => true,
                            'after' => '<p class="small">( Indiquez la moyenne d’heures que vous pensez pouvoir pratiquer par jour )</p></div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                        'hours_do' => array(
                            'label' => array(
                                'text' => __('Est-ce le nombre d\'heures minimum, moyen ou maximum que vous effectuerez ?').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => true,
                            'after' => '</div>',
                            'type' => 'text',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
						'hours_comment' => array(
                            'label' => array(
                                'text' => __('Commentaires sur ma présence').' ',
                                'class' => 'col-sm-12 col-md-4 control-label'
                            ),
                            'required' => false,
                            'after' => '</div>',
                            'type' => 'textarea',
                            'between' => '<div class="col-sm-12 col-md-8">',
							'value' => ''
                        ),
                    ));

					echo '<p class="mt40">Dans le tableau ci-dessous, cliquez sur les tranches horaires pendant lesquelles vous pensez pouvoir être disponible.</p>';
					echo '<p class="mt20">(Pour rappel vos horaires sont totalement libres) </p>';

					?>
					<table id="planning">
						<thead>
							<tr>
								<th></th>
								<?php for($i=0; $i<24; $i++): ?>
									<th colspan="2" class="date_column"><?php echo $i."h"; ?></th>
								<?php endfor; ?>

							</tr>
						</thead>
						<tbody>
							<?php
							$intervalle = array(
								0 => 'Lundi',
								1 => 'Mardi',
								2 => 'Mercredi',
								3 => 'Jeudi',
								4 => 'Vendredi',
								5 => 'Samedi',
								6 => 'Dimanche'
							);

							for($i=0; $i<=6; $i++): //Pour chaque jour du planning?>
								<tr<?php echo (($i%2==0)?' class="alternate"':''); ?> date="<?php echo $intervalle[$i]; ?>">
									<td class="date_label"><?php echo __($intervalle[$i]); ?></td>
										<?php for($a=0; $a<24; $a++): //Pour chaque heure du jour?>
											<td class="date_checkbox"><?php echo $this->Form->input('planning-'.$i.'-'.$a.'-0', array('type'=> 'hidden','hiddenField' => false, 'h' => $a, 'm' => 0)); ?></td>
											<td class="date_checkbox"><?php echo $this->Form->input('planning-'.$i.'-'.$a.'-30', array('type'=> 'hidden','hiddenField' => false, 'h' => $a, 'm' => 30)); ?></td>
										<?php endfor; ?>
								</tr>
							<?php endfor; ?>
						</tbody>
					</table>
					<div class="content_cms_custom">
					<?php

					echo '<div class="text-justify mt40">'.$this->FrontBlock->getPageBlocTexte(443).'</div>';

					echo '<div class="text-justify mt40">'.$this->FrontBlock->getPageBlocTexte(444).'</div>';
					?>
					</div>
					<?php
					echo '<input type="hidden" name="data[Agents][survey]" value="'.$survey['Survey']['id'].'"  />';
					echo $this->Form->end(array(
                        'label' => __('Valider'),
                        'class' => 'btn btn-pink btn-pink-modified btn-survey',
                        'before' => '<div class="col-sm-12 col-md-8 col-md-offset-2"><div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
<div class="col-sm-12 col-md-offset-4 col-md-8">',
'after' => '</div></div></div>'
                    ));
                    ?>

        <div style="clear:both"></div>
		</div>
    </section>
 </div>
