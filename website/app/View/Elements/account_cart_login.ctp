<a class="buy_title_content buy_title_content2 mt20 mb10">
								<span class="title_bg">déjà inscrit ?  ></span>
								<span class="title_s  hidden-xs">Connectez-vous à votre compte</span>
								<span class="title_s visible-xs">Cliquez ici et connectez-vous à votre compte</span>
							</a>
<?php

   echo $this->Form->create('User', array('action' => 'login', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                        'inputDefaults' => array(
                            'div' => 'form-group',
                            'between' => '<div class="col-xs-12">',
                            'after' => '</div>',
                            'class' => 'form-control form-control2'
                        )));

  echo $this->Form->inputs(array(
                            'legend' => false,
                            'compte' => array('type' => 'hidden', 'value' => 'client'),
                            'email'  => array('label' => false,'placeholder' => __('E-mail *'), 'required' => true),
                            'passwd' => array('label' => false,'placeholder' => __('Mot de passe *'), 'required' => true))
                    );

?>
				  

<?php
	echo '</form>'; ?>
 <p class="foget-pass">
                        <?php

                        echo $this->Html->link(__('> Mot de passe oublié ?'), array(
                            'controller' =>    'users',
                            'action'     =>    'passwdforget'

                        ), array(
                            'style'      => ''
                        ));



                        ?>
                    </p>
