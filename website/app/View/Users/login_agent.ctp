<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Connexion'); ?></h1>
</section>
<div class="container">
	<section class="single-page login-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">
        	<?php echo $this->Session->flash(); ?>
            <div class="row">
            	<div class="col-md-6 col-sm-12">
					<div class="well well-light">
                    	<h2 class="text-center wow fadeIn" data-wow-delay="0.5s"><?php echo __('Connexion à votre compte expert'); ?></h2>
                        <hr/>
                            <?php
                                echo $this->Form->create('User', array('action' => 'login', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                                                       'inputDefaults' => array(
                                                                           'div' => 'form-group',
                                                                           'between' => '<div class="col-lg-8">',
                                                                           'after' => '</div>',
                                                                           'class' => 'form-control'
                                                                       )));
            
                                echo $this->Form->inputs(array(
                                        'legend' => false,
                                        'compte'  => array('type' => 'hidden', 'value' => 'agent'),
                                        'email'  => array('label' => array('text' => __('Votre E-mail'), 'class' => 'control-label col-lg-4'), 'required' => true),
                                        'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label col-lg-4'), 'required' => true))
                                );
                        
                               // echo $this->Form->submit(__('Se connecter'), array('class' => 'btn btn-block btn-connect-popup'));
            
                            ?>
                           <div class="text-center">
                            <input class="btn btn-pink btn-pink-modified" type="submit" value="<?php echo __('Se connecter') ?>">
                            </div>
                           <p class="foget-pass text-center">
                                <?php
            
                                echo $this->Html->link(__('Mot de passe oublié'), array(
                                    'controller' =>    'users',
                                    'action'     =>    'passwdforget',
                                    '?'          =>    array(
                                        'compte' => 'agent'
                                    )
                                ), array(
                                    'style'      => ''
                                ));
            
                                ?>
                            </p>
                     </div>
        		</div>
                <div class="col-md-6 col-sm-12">
                	<div class="well well-light">
                    <?php
                        $page = $this->FrontBlock->getPageBlocTexte(130);
                        if($page !== false){
                            echo $page;
                        }
                    ?>
                    </div>
                </div>
            </div>
		</div>
    </section>
</div>