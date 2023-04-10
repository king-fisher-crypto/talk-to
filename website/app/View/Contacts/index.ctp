<script src='https://www.google.com/recaptcha/api.js'></script>
<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Page contact de Spiriteo'); ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">

    <?php
	
	$idlang = $this->Session->read('Config.id_lang');
			$parts = explode('.', $_SERVER['SERVER_NAME']);
			if(sizeof($parts)) $extension = end($parts); else $extension = '';
			if($idlang == 1){
				if($extension == 'ca')$idlang=8;	
				//if($extension == 'ch')$idlang=10;
				//if($extension == 'be')$idlang=11;
				if($extension == 'lu')$idlang=12;
			}
	
    /* Block explicatif */
    echo $this->FrontBlock->getPageBlocTextebyLang(32,$idlang);

    ?><div class="row">
	<div class="col-lg-12">
    
        <?php echo $this->Session->flash(); ?>
                <?php
			//(isset($guest) && !$guest ?'discussion':'subscribe')
                    echo $this->Form->create('Support', array('action' => 'submit_message', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,  'enctype' => 'multipart/form-data',
                        'inputDefaults' => array(
                            'div' => 'form-group',
                            'between' => '<div class="col-lg-6">',
                            'after' => '</div>',
                            'class' => 'form-control'
                        )
                    ));
		
					echo $this->Form->inputs(array(
                            'service' => array('label' => array('text' => __('Pour *'), 'class' => 'control-label col-lg-3 required'), 'options' => $list_services,  'required' => true)
                        ));
		
					echo $this->Form->inputs(array(
                            'title' => array('label' => array('text' => __('Titre *'), 'class' => 'control-label col-lg-3 required'), 'required' => true)
                        ));

                    if(isset($guest) && !$guest)
                        echo $this->Form->inputs(array(
                            'message' => array('label' => array('text' => __('Votre message *'), 'class' => 'control-label col-lg-3 required'), 'required' => true, 'type' => 'textarea')
                        ));
                    else{
                        echo $this->Form->inputs(array(
                            'nom' => array('label' => array('text' => __('Nom *'),'class' => 'control-label col-lg-3 required'), 'required' => true),
                            'prenom' => array('label' => array('text' => __('Prénom *'),'class' => 'control-label col-lg-3 required'), 'required' => true),
                            'email' => array('label' => array('text' => __('Votre E-mail *'), 'class' => 'control-label col-lg-3 required'),'required' => true),
                            'message' => array('label' => array('text' => __('Votre message *'), 'class' => 'control-label col-lg-3 required'), 'required' => true, 'type' => 'textarea')
                        ));

                        //echo '<p class="pull-right">'.__('Votre adresse ip : ').$this->request->clientIp().'</p>';
                    }
					?>
					<div class="form-group">
						<label for="SupportAttachment" class="control-label col-lg-3 norequired ">Joindre mes fichiers</label>
						<div class="col-lg-6">
							<input type="file" name="data[Support][attachment]" multiple="multiple">
						</div>
					</div>
					<div class="row">
						<div class="col-lg-3"></div>
						<div class="col-lg-6">
							<div class="g-recaptcha" data-sitekey="6LdQPR0UAAAAANL3lfsdxx7qQPJbBHi965Ak8tRr"></div>
						</div>
					</div>
					<?=$this->Form->end(array(
						'label' => 'Envoyer',
						'class' => 'btn btn-pink btn-pink-modified',
						'div' => array('class' => false, 'style' => 'margin-top: 10px;')
					));?>
				</div>
			</div>
        </div>
    </section>
</div>