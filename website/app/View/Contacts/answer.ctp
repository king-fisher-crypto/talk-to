<section class="slider-small">
	<h1 class="wow fadeInDown" data-wow-delay="0.5s"><?php echo __('Contact'); ?></h1>
</section>
<div class="container">
	<section class="single-page form-page">
		<div class="content_box mt20 mb40 wow fadeIn" data-wow-delay="0.4s">
    <?php

        /* Block explicatif */
        echo $this->FrontBlock->getPageBlocTexte(32);

    ?>

     <div class="row">
        <?php echo $this->Session->flash(); ?>
                <?php

                    echo $this->Form->create('Contact', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                                              'inputDefaults' => array(
                                                                  'div' => 'form-group',
                                                                  'between' => '<div class="col-lg-6">',
                                                                  'after' => '</div>',
                                                                  'class' => 'form-control'
                                                              )
                    ));

                    echo $this->Form->inputs(array(
                        'message' => array('label' => array('text' => __('Votre message'), 'class' => 'control-label col-lg-3 required'), 'required' => true),
                        'token'   => array('type' => 'hidden', 'value' => $guest['Guest']['answer_token'])
                    ));

                    echo '<p class="pull-right">'.__('Votre adresse ip : ').$this->request->clientIp().'</p>';

                    echo $this->Form->end(array(
                        'label' => 'Envoyer',
                        'class' => 'btn btn-pink btn-pink-modified',
                        'div' => array('class' => false, 'style' => 'margin-top: 10px;')
                    ));
                ?>
            </div>
        </div>
    </section>

</div>
