<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php

        if ($compte === 'client')
            echo __('Mot de passe oublié (client)');
        else
            echo __('Mot de passe oublié (expert)');


        ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">
        <?php echo $this->Session->flash(); ?>

        <p style="padding-top: 10px;text-align:center">
            <?php
            if(isset($timePass) && !$timePass){
                echo __('Vous ne pourrez initialiser votre mot de passe que %s min après votre dernière réinitialisation.', Configure::read('Site.timeMinPass')/60);
            }else if(isset($emailValid)){
                if ($emailValid)
                    echo __('Vous allez recevoir un mail avec un lien pour la réinitialisation de votre mot de passe. Si vous ne recevez pas celui-ci pensez à regarder dans vos spams.');
                else
                    echo __('Nous n\'avons pas trouvé de compte avec cet identifiant. Veuillez réessayer.');
            }else {
                if ($compte === 'client')
                    echo __('Saisissez l\'adresse mail de votre compte client pour réinitialiser votre mot de passe.');
                else
                    echo __('Saisissez l\'adresse mail de votre compte expert pour réinitialiser votre mot de passe.');
            }
            ?>
        </p>
        <div class="col-md-3 col-sm-12 hidden-xs"></div>
		<div class="col-md-6 col-sm-12">
        <?php
        if(isset($timePass) || (isset($emailValid) && !$emailValid) || (!isset($timePass) && !isset($emailValid))){
            echo $this->Form->create('User', array('action' => 'passwdforget', 'nobootstrap' => 1,'class' => '', 'default' => 1,
                'inputDefaults' => array(
                    'class' => 'form-control'
                )));

            echo '<div class="form-group">'.$this->Form->inputs(array(
                    'legend' => false,
                    'email'  => array('label' => array('text' => __('Votre E-mail'), 'class' => 'control-label'), 'required' => true),
                    'compte' => array('type' => 'hidden', 'value' => $compte)
                )
            ).'</div>';

            echo $this->Form->submit(__('Valider'),array('class' => 'btn btn-pink btn-pink-modified'));
        }
        ?>
        </form>
        </div>
        <div class="col-md-3 col-sm-12 hidden-xs"></div>
        <div style="clear:both"></div>
		</div>
    </section>
 </div>
