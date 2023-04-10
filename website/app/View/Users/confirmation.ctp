<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Devenir membre'); ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">
			<?php echo $this->Session->flash(); ?>
			<?php
            if($confirmation)
                if ($role == 'client')
                    echo __('Votre compte est activé. Vous pouvez dès à présent vous connecter.');
                else
                    echo __('Votre compte est confirmé. Vous recevrez un e-mail lorsqu\'un administrateur aura activé votre compte.');
            else
                echo __('Suite à une erreur, l\'activation de votre compte a échoué');

        ?>
		</div>
    </section>
 </div>