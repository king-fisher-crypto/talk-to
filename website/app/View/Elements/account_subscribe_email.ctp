<?php

    echo $this->Form->create('User', array('action' => 'subscribe_email', 'nobootstrap' => 1,'class' => 'form-horizontal form_cards_email',
        'default' => 1,
                                             'inputDefaults' => array(
                                                 'div' => 'form-group',
                                                 'between' => '<div class="col-lg-6">',
                                                 'after' => '</div>',
                                                 'class' => 'form-control'
                                             )
    ));



?>

				  <div class="form-group wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-12">
						<p class="card_emailform_title"><?php echo __('Pour lire la suite de votre tirage personnalisé, précisez votre mail*') ?> </p>
				      <input type="email" class="form-control form-control2" id="UserEmailSubscribe"  name="data[User][email_subscribe]" placeholder="<?php echo __('Votre Email') ?> *" required >
				    </div>
				  </div>

				  
				  <div class="form-group mt20 wow fadeIn" data-wow-delay="0.4s">
				    <div class="col-sm-12 col-md-offset-0 col-md-12" style="text-align:center">
                    	<input class="btn btn-pink btn-2-gold btn-read-card-txt" type="submit" value="<?php echo __('Lire mon interprétation') ?>">
				    </div>
				 </div>

<?php
	echo '</form>';
