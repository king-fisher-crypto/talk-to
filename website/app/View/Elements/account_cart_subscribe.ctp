<a class="buy_title_content buy_title_content1 mt20 mb10">
								<span class="title_bg">Nouvel inscrit ?  ></span>
								<span class="title_s hidden-xs">Complétez le formulaire ci-dessous</span>
								<span class="title_s visible-xs">Cliquez ici et complétez le formulaire ci-dessous</span>
							</a>
<?php

    echo $this->Form->create('User', array('action' => 'subscribe', 'nobootstrap' => 1,'class' => 'form-horizontal',
        'default' => 1,
                                             'inputDefaults' => array(
                                                 'div' => 'form-group',
                                                 'between' => '<div class="col-xs-12">',
                                                 'after' => '</div>',
                                                 'class' => 'form-control'
                                             )
    ));



?>
				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-12">
				      <input type="text" class="form-control form-control2" id="UserFirstname" name="data[User][firstname]" placeholder="<?php echo __('Votre prénom ou pseudo') ?> *" required value="<?php echo $firstname; ?>">
				    </div>
				  </div>

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-12">
				      <input type="email" class="form-control form-control2" id="UserEmailSubscribe"  name="data[User][email_subscribe]" placeholder="<?php echo __('Email') ?> *" required value="<?php if(isset($sponsor_email) && $sponsor_email && !$email) echo $sponsor_email; else echo $email; ?>">
				    </div>
				  </div>
				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-12">
				      <input type="password" class="form-control form-control2" id="UserPasswdSubscribe" name="data[User][passwd_subscribe]" placeholder="<?php echo __('Mot de passe') ?> * (8 caractères min)" required>
				    </div>
				  </div>

				  

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-12">
				      <select class="form-control form-control2" id="UserCountryId" name="data[User][country_id]" required placeholder="Pays *">
						  <?php 
						  	foreach( $select_countries as $key => $opt){
								$selected = '';
								if($key == $country)$selected = 'selected';
								echo '<option value="'.$key.'" '.$selected.'>'.$opt.'</option>';
								
							}?>
						</select>
				    </div>
				  </div>
					
                    
                   

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-offset-0 col-md-12">
				      <div class="checkbox checkbox2">
				        <label>
				          <input type="checkbox" id="UserOptin" value="1" name="data[User][optin]">  <span></span>Je souhaite recevoir les offres exclusives de Spiriteo
				        </label>
				      </div>

				      <div class="checkbox checkbox2">
				        <label>
				          <input type="checkbox" id="UserCgu" value="1" name="data[User][cgu]">  <span></span>J'ai lu et j'approuve sans réserve <?php echo $this->FrontBlock->getPageLink(1, array('target' => '_blank', 'class' => 'nx_openinlightbox', 'style' => 'text-decoration:underline'), __('les conditions générales d\'utilisation')) ?>
				        </label>
				      </div>
						
				    </div>
				  </div>
				  <div class="form-group mt20 wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-offset-0 col-md-12" style="text-align:center">
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
						
				    </div>
				 </div>

<?php
	echo '</form>';
