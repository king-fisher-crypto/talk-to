<?php echo $this->Session->flash(); ?>
<div class="admin-login">
    <h4 class="label-forget"><?php echo __('Mot de passe oublié') ?></h4>
    <?php
        echo $this->Form->create('User', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                               'inputDefaults' => array(
                                                   'div' => 'control-group',
                                                   'between' => '<div class="controls">',
                                                   'after' => '</div>'
                                               )));

        echo $this->Form->inputs(array(
                'legend' => false,
                'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label required'), 'required' => true),
                'passwd2' => array('label' => array('text' => __('Confirmez votre mot de passe'), 'class' => 'control-label required'), 'required' => true, 'type' => 'password'),
                'forgotten_password'  => array('type' => 'hidden'))
        );

        echo $this->Form->end(array(
            'label' => __('Valider'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>