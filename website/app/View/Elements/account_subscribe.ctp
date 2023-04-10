<?php

    echo $this->Form->create('User', array('action' => 'subscribe', 'nobootstrap' => 1,'class' => 'form-horizontal',
      //  'novalidate' => "novalidate",
        'default' => 1,
                                             'inputDefaults' => array(
                                                 'div' => 'form-group',
                                                 'between' => '<div class="col-lg-6">',
                                                 'after' => '</div>',
                                                 'class' => 'form-control'
                                             )
    ));

	/*echo '<div class="row">
<div class="col-sm-12 col-md-8 col-md-offset-2">';*/


?>
				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <label for="" class="col-sm-12 col-md-4 control-label" style="padding-left: 10px;"><?php echo __('Votre prénom ou pseudo') ?> <span class="star-condition">*</span></label>
				    <div class="col-sm-12 col-md-7">
				      <input type="text" class="form-control" id="UserFirstname" name="data[User][firstname]" placeholder="" required value="<?php echo $firstname; ?>">
				    </div>
				  </div>

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Email') ?> <span class="star-condition">*</span></label>
				    <div class="col-sm-12 col-md-7">
				      <input type="email" class="form-control" id="UserEmailSubscribe"  name="data[User][email_subscribe]" placeholder="" required value="<?php if(isset($sponsor_email) && $sponsor_email && !$email) echo $sponsor_email; else	echo $email; ?>">
				    </div>
				  </div>
		<?php if(!$sponsor_id && !$sponsor_user_id){ ?>
				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Confirmez votre Email') ?> <span class="star-condition">*</span></label>
				    <div class="col-sm-12 col-md-7">
				      <input type="text" class="form-control" id="UserEmail2" name="data[User][email2]" placeholder="" required value="<?php echo $email2; ?>">
				    </div>
				  </div> 
<?php } ?>
				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Mot de passe') ?> <span class="star-condition">*</span></label>
				    <div class="col-sm-12 col-md-7">
				      <input type="password" class="form-control" id="UserPasswdSubscribe" name="data[User][passwd_subscribe]" placeholder="" required>
				      <span class="help"><?php echo __('(8 caractères min)') ?></span>
				    </div>
				  </div>

				  <!-- <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <label for="" class="col-sm-12 col-md-4 control-label"><?php echo __('Confirmez votre mot de passe') ?> <span class="star-condition">*</span></label>
				    <div class="col-sm-12 col-md-8">
				      <input type="password" class="form-control" id="UserPasswd2" name="data[User][passwd2]" placeholder="" required>
				    </div>
				  </div> -->

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <label for="" class="col-sm-12 col-md-4 control-label">Pays <span class="star-condition">*</span></label>
				    <div class="col-sm-12 col-md-7">
				      <select class="form-control" id="UserCountryId" name="data[User][country_id]" required>
						  <?php 
						  	foreach( $select_countries as $key => $opt){
								$selected = '';
								if($key == $country)$selected = 'selected';
								echo '<option value="'.$key.'" '.$selected.'>'.$opt.'</option>';
								
							}?>
						</select>
				    </div>
				  </div>
					
                    
                    <?php
					/*echo $this->Form->inputs(array(
						'country_id'    => array('label' => array('text' => __('Pays'), 'class' => 'control-label col-xs-12 col-md-4 col-lg-4'), 'options' => $select_countries, 'required'  => true, 'selected' => $selected_countries),
						'phone_number' => array(
							'label'     => array('text' => __('Numéro de téléphone'), 'class' => 'form-control ind-form'),
							'type'      => 'tel',
							//'between'    => '<div class="col-lg-2 col-md-2">'.$this->FrontBlock->getIndicatifTelInput(false, false, 'form-control no-width').'</div><div class="col-lg-4">',
							'between'    => '<div class="col-xs-4 col-lg-3 col-md-3">'.$this->FrontBlock->getIndicatifTelInputIns(false, false).'</div><div class="col-xs-8 col-md-3 col-lg-3">',
						  //  'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
						)
					));*/
					?>
                    

				  <!--<div class="form-group wow fadeIn" data-wow-delay="0.4s">
				  	<label for="UserPhoneNumber" class="control-label col-xs-12 col-md-4 col-lg-4">Numéro de téléphone</label>
				  	<div class="col-xs-4 col-md-2 pr0">
                    	<span class="ind_plus">+</span>
                    	<?php echo $this->FrontBlock->getIndicatifTelInputIns(false, false, 'form-control ind-form'); ?>
				  	</div>
				  	<div class="col-xs-8 col-md-6">
				  		<input class="form-control" type="tel" id="UserPhoneNumber" name="data[User][phone_number]">
				  	</div>
				  </div>-->

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-offset-4 col-md-7">
				      <div class="checkbox">
				        <label>
				          <input type="checkbox" id="UserOptin" value="1" name="data[User][optin]">  <span></span>Je souhaite recevoir les offres exclusives de Spiriteo
				        </label>
				      </div>

				      <div class="checkbox">
				        <label>
				          <input type="checkbox" id="UserCgu" value="1" name="data[User][cgu]">  <span></span>J'ai lu et j'approuve sans réserve <?php echo $this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => 'nx_openinlightbox', 'style' => 'text-decoration:underline'), __('les conditions générales d\'utilisation')) ?>
				        </label>
				      </div>

				    </div>
				  </div>
				  <div class="form-group mt20 wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-offset-4 col-md-7" style="text-align:center">
						<?php
							
							if(isset($source_ins)){
								echo '<input type="hidden" id="UserSourceIns"  name="data[User][source_ins]" placeholder="" value="'.$source_ins.'">';
							}						
							if(isset($sponsor_id)){
								echo '<input type="hidden" id="UserSponsorId"  name="data[User][sponsor_id]" placeholder="" value="'.$sponsor_id.'">';
							}
							if(isset($sponsor_user_id)){
								echo '<input type="hidden" id="UserSponsorUserId"  name="data[User][sponsor_user_id]" placeholder="" value="'.$sponsor_user_id.'">';
							}
						?>
						
						
                    	<input class="btn btn-pink btn-pink-modified" type="submit" value="S'inscrire">
				    </div>
				 </div>

<?php


   /* echo $this->element('account_compte', array('inputs' => array('nameEmail' => 'email_subscribe', 'namePasswd' => 'passwd_subscribe'), 'inscription' => true, 'email' => (isset($this->request->data['User']['email_subscribe']) ?$this->request->data['User']['email_subscribe']:'')));

    echo $this->Form->inputs(array(
        'country_id'    => array('label' => array('text' => __('Pays'), 'class' => 'control-label col-lg-4 required'), 'options' => $select_countries, 'required'  => true, 'selected' => $selected_countries),
        'phone_number' => array(
            'label'     => array('text' => __('Numéro de téléphone'), 'class' => 'control-label col-xs-12 col-md-4 col-lg-4 norequired'),
            'type'      => 'tel',
            //'between'    => '<div class="col-lg-2 col-md-2">'.$this->FrontBlock->getIndicatifTelInput(false, false, 'form-control no-width').'</div><div class="col-lg-4">',
            'between'    => '<div class="col-xs-4 col-lg-3 col-md-3">'.$this->FrontBlock->getIndicatifTelInputIns(false, false).'</div><div class="col-xs-8 col-md-3 col-lg-3">',
          //  'pattern'   => '^((\+\d{1,3}(-| )?\(?\d\)?(-| )?\d{1,5})|(\(?\d{2,6}\)?))(-| )?(\d{3,4})(-| )?(\d{4})(( x| ext)\d{1,5}){0,1}$'
        )
    ));


echo '<div style="position:relative;">
        <p class="obli" style="text-align:right"><span style="font-weight:bold; color:#e32">*</span> '.__('Champs obligatoires').'</p>
    </div>';

    echo $this->Form->inputs(array(
            'optin'  => array('label' => __('Je souhaite recevoir les offres exclusives de ').Configure::read('Site.name'), 'type' => 'checkbox', 'value' => 1, 'class' => false, 'between' => false, 'after' => false, 'div' => array('class' => 'checkbox col-lg-offset-3')),
            'cgu'    => array('label' => __('J\'ai lu et j\'approuve sans réserve les').' '.$this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => 'nx_openinlightbox', 'style' => 'text-decoration:underline'), __('conditions générales d\'utilisation')), 'type' => 'checkbox', 'between' => false, 'after' => false, 'value' => 1, 'required' => true, 'class' => false, 'div' => array('class' => 'checkbox col-lg-offset-3'))
        )
    );*/

//</div></div>
	echo '</form>';
