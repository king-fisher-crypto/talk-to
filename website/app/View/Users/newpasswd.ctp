<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Mot de passe oublié'); ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">
        <?php echo $this->Session->flash(); ?>

        <p style="padding-top: 10px;">
            <?php
                if(isset($reInitPass) && $reInitPass){
                    echo __('Votre mot de passe a été réinitialisé.');
                }else {
                    echo __('Créez votre nouveau mot de passe.');
                }
            ?>
        </p>
        <?php
            if((isset($reInitPass) && !$reInitPass) || !isset($reInitPass)){
                echo $this->Form->create('User', array('action' => 'newpasswd', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                    'inputDefaults' => array(
                        'div' => 'form-group',
                        'between' => '<div class="col-lg-6">',
                        'after' => '</div>',
                        'class' => 'form-control'
                    )));

                echo $this->Form->inputs(array(
                        'legend' => false,
                        'passwd'  => array('label' => array('text' => __('Nouveau mot de passe'), 'class' => 'control-label col-lg-4'), 'required' => true),
                        'passwd2'  => array('label' => array('text' => __('Confirmez votre mot de passe'), 'class' => 'control-label col-lg-4'), 'required' => true, 'type' => 'password'),
                        'forgotten_password'  => array('type' => 'hidden')
                    )
                );

                echo $this->Form->submit(__('Valider'),array('class' => 'btn btn-pink btn-pink-modified'));
            }
        ?>
        <div style="clear:both"></div>
		</div>
    </section>
 </div>