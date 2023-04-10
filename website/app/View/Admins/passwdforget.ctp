<?php echo $this->Session->flash(); ?>
<div class="admin-login">
    <h4 class="label-forget"><?php echo __('Mot de passe oublié ?') ?></h4>
    <?php if(!isset($timePass) && !isset($emailValid)) :
        echo $this->Form->create('User', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                               'inputDefaults' => array(
                                                   'div' => 'control-group',
                                                   'between' => '<div class="controls">',
                                                   'after' => '</div>'
                                               )));

        echo $this->Form->inputs(array(
                'legend' => false,
                'email'  => array('label' => array('text' => __('Votre E-mail'), 'class' => 'control-label required'), 'required' => true))
        );

        echo $this->Form->end(array(
            'label' => __('Valider'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));
        endif;
    ?>
</div>