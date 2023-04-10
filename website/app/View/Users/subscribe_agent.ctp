<?php
    echo $this->Html->script('/theme/default/js/crop/agent_photo', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/crop/jquery.color', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/crop/jquery.Jcrop.min', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/subscribe_agent', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/inputDateEmpty', array('block' => 'script'));
	echo $this->Html->script('https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js', array('block' => 'script'));
    echo $this->Html->css('/theme/default/css/crop/jquery.Jcrop.min', array('block' => 'css'));

?>
<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Devenir Expert'); ?></h1>
</section>
<div class="container ">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">

        <?php
            //$page = $this->FrontBlock->getPageBlocTexte(142);


            if($page_block !== false){
                echo $page_block;
            }
            echo $this->Session->flash();
        ?>

        <?php
        if(!isset($inscription)){
            $requestData = array(
                'email' => 'email_subscribe',
                'presentation' => 'texte',
                'sexe' => 'sexe',
                'consult' => 'consult',
                'univers' => 'categories',
                'langs' => 'langs',
                'countries' => 'countries',
            );
            foreach ($requestData as $k => $val){
                if(isset($this->request->data['User'][$val])) $requestData[$k] = $this->request->data['User'][$val];
                else $requestData[$k] = '';
            }
        ?>

                    <?php
                    echo $this->Form->create('User', array('action' => 'subscribe_agent', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
                        'inputDefaults' => array(
							'div' => 'form-group mt20 wow fadeIn animated',
                            'class' => 'form-control'
                        )
                    ));
					echo '<div class="row">';
					echo '<h2 class="text-center wow fadeIn animated" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('Mon Compte') .'</h2>
<hr><div class="col-sm-12 col-md-8 col-md-offset-2">';
                    echo $this->element('agent_compte', array('inputs' => array('nameEmail' => 'email_subscribe', 'namePasswd' => 'passwd_subscribe'), 'inscription' => true, 'email' => $requestData['email']));
					echo '</div></div>';
					echo '<h2 class="text-center wow fadeIn animated" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'. __('Informations personnelles') .'</h2>
						  <hr>';
				   echo '<div class="row"><div class="col-sm-12 col-md-8 col-md-offset-2">';
                   echo $this->element('agent_infos_subscribe', array('nomModel' => 'User', 'sexe' => $requestData['sexe'], 'subscribe' => true));
					echo '</div></div>';
					
					 echo '<h2 class="text-center wow fadeIn animated" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.__('Options Experts').'</h2>
						<hr>
						<div class="row">
						<div class="">';
					
                   echo $this->element('agent_options',array('nomModel' => 'User', 'langs' => $requestData['langs'], 'univers' => $requestData['univers'], 'consult' => $requestData['consult'], 'countries' => $requestData['countries']));
					
					echo '</div></div>';
					
					echo '<h2 class="text-center wow fadeIn animated" data-wow-delay="0.5s" style="visibility: visible;-webkit-animation-delay: 0.5s; -moz-animation-delay: 0.5s; animation-delay: 0.5s;">'.__('Présentation').'</h2>
						<hr>
						<div class="row">
						<div class=""><div class="col-sm-12 col-md-8 col-md-offset-2">';
					
                    echo $this->element('agent_media', array('nomModel' => 'User', 'inscription' => true));
					
					echo '</div></div></div>';
					echo '<div class="row"><div class=""><div class="col-sm-12 col-md-8 col-md-offset-2">';
                  echo $this->element('agent_presentations', array('commentaireOn' => true, 'lang_id' => $this->Session->read('Config.id_lang'), 'presentation' => $requestData['presentation']));

                     echo $this->Form->inputs(array(
                        'careers' => array(
                            'label' => array(
                                'text' => __('Parcours professionnel').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '<p>'.__('Ne sera pas diffusé sur le site').'</p></div>',
                            'type' => 'textarea',
                            'between' => '<div class="col-sm-12 col-md-8">'
                        ),
                        'profile' => array(
                            'label' => array(
                                'text' => __('Votre profil').' <span class="star-condition">*</span>',
                                'class' => 'col-sm-12 col-md-4 control-label required'
                            ),
                            'required' => true,
                            'after' => '<p>'.__('Ne sera pas diffusé sur le site').'</p></div>',
                            'type' => 'textarea',
                            'between' => '<div class="col-sm-12 col-md-8">'
                        )
                    ));
					echo '</div></div></div>';
                    echo $this->Form->end(array(
                        'label' => __('S\'inscrire'),
                        'class' => 'btn btn-pink btn-pink-modified',
                        'href'  => $this->Html->url(array('controller' => 'users', 'action' => 'modalLoading')),
						'before' => '<div class="col-sm-12 col-md-8 col-md-offset-2"><div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
<div class="col-sm-12 col-md-offset-4 col-md-8">',
'after' => '</div></div></div>'
                    ));
                    ?>
        <?php
        }else {
			header('Location: /users/subscribe_agent_merci');
 					exit();
           // echo __('Votre inscription est enregistrée. Vous allez recevoir un email dans quelques instants pour confirmer votre inscription.');
        }
        ?>
        <div style="clear:both"></div>
		</div>
    </section>
 </div>
