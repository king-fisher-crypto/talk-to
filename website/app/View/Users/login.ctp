<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Accès à votre espace client Talkappdev'); ?></h1>
</section>
<div class="container">
	<section class="single-page login-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">
		
<?php echo $this->Session->flash(); ?>
<?php if ($nosubscribe): ?>
    <div class="row">

           <div class="col-md-5 col-sm-12">
				<div class="well well-light">
                <h2 class="text-center wow fadeIn" data-wow-delay="0.5s"><?php echo __('Connexion à votre compte'); ?></h2>
                <hr/>
                    <?php
                    echo $this->Form->create('User', array('action' => 'login', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                        'inputDefaults' => array(
                            'div' => 'form-group',
                            'between' => '<div class="col-lg-7">',
                            'after' => '</div>',
                            'class' => 'form-control'
                        )));

                    echo $this->Form->inputs(array(
                            'legend' => false,
                            'compte' => array('type' => 'hidden', 'value' => 'client'),
                            'email'  => array('label' => array('text' => __('Votre E-mail'), 'class' => 'control-label col-lg-5'), 'required' => true),
                            'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label col-lg-5'), 'required' => true))
                    );

?>
                    <div class="form-group">
                        <label class="control-label col-lg-5 col-md-5"></label>
                        <div class="col-lg-<?php echo (isset($colButton) && !empty($colButton) ?$colButton:'5'); ?> col-md-<?php echo (isset($colButton) && !empty($colButton) ?$colButton:'5'); ?>">
                            <?php
                echo $this->Form->button(__('Connexion'),array('type' => 'submit', 'class' => 'btn btn-pink btn-pink-modified'));//btn btn-block btn-connect-popup

                echo __(' ou ');

                echo $this->Html->link(
                    __('S\'inscrire'),
                    array('controller' => 'users', 'action' => 'subscribe'),
                    array('class' => 'btn btn-pink btn-pink-modified')
                );




                    ?>
                        </div>
                    </div>
                    <p class="foget-pass text-center">
                        <?php

                        echo $this->Html->link(__('Mot de passe oublié'), array(
                            'controller' =>    'users',
                            'action'     =>    'passwdforget'

                        ), array(
                            'style'      => ''
                        ));



                        ?>
                    </p>


                </div>
            </div>


    </div>
<?php else:?>
    <div class="row">
       <div class="col-md-3 col-sm-12 hidden-xs"></div>
        <div class="col-md-6 col-sm-12">
			<div class="well well-light">
                <h2 class="text-center wow fadeIn" data-wow-delay="0.5s"><?php echo __('Connexion à votre compte'); ?></h2>
                <hr/>
                    <?php
                        echo $this->Form->create('User', array('action' => 'login', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                                               'inputDefaults' => array(
                                                                   'div' => 'form-group',
                                                                   'between' => '<div class="col-lg-7">',
                                                                   'after' => '</div>',
                                                                   'class' => 'form-control'
                                                               )));

                        echo $this->Form->inputs(array(
                                'legend' => false,
                                'compte' => array('type' => 'hidden', 'value' => 'client'),
                                'email'  => array('label' => array('text' => __('Votre E-mail'), 'class' => 'control-label col-lg-4'), 'required' => true),
                                'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label col-lg-4'), 'required' => true))
                        );
						
						
						//echo '	<div class="form-group">';
						echo '		<div class=" col-lg-12 text-center">';
						echo '			<input class="btn btn-pink btn-pink-modified " type="submit" value="'.__('Se connecter').'">';
						echo '		</div>';
						//echo '	</div>';
						
                    ?>


                    <p class="foget-pass text-center">
                        <?php

                        echo $this->Html->link(__('Mot de passe oublié'), array(
                            'controller' =>    'users',
                            'action'     =>    'passwdforget'

                        ), array(
                            'style'      => ''
                        ));



                        ?>
                    </p>
                    </form>
                </div>
            </div>
            <div class="col-md-3 col-sm-12 hidden-xs"></div>
            <div class="text-bottom-form text-center" style="display:block;float:left;clear:both;text-align:center;width:100%;">
            <!--<a class="pas-links" title="" data-placement="top" data-toggle="tooltip" href="/users/subscribe" data-original-title="Nouveau?">Vous n'êtes pas inscrit ? </a>
            <a class="ins-links" title="" data-placement="top" data-toggle="tooltip" href="/users/subscribe" data-original-title="Creez un compte">Inscrivez vous !</a>-->
            <a class="ins-links" title="" data-placement="top" data-toggle="tooltip" href="/users/subscribe" data-original-title="Creez un compte">Vous n'êtes pas inscrit ? Inscrivez vous !</a><br />
            <span style="display:block;height:9px;"></span>
            </div>
        </div>
<?php endif; ?>
</div></section></div>