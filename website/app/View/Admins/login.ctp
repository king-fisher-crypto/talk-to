<?php echo $this->Session->flash(); ?>
<div class="admin-login">
        <?php
        echo $this->Form->create('User', array('action' => 'login', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
            'inputDefaults' => array(
                'div' => 'control-group',
                'between' => '<div class="controls">',
                'after' => '</div>'
            )));

        echo $this->Form->inputs(array(
                'legend' => false,
                'email'  => array('label' => array('text' => __('Votre E-mail'), 'class' => 'control-label'), 'required' => true),
                'passwd' => array('label' => array('text' => __('Mot de passe'), 'class' => 'control-label'), 'required' => true),
                'compte'  => array('type' => 'hidden', 'value' => 'admin'))
        );

        echo $this->Form->end(array(
            'label' => __('Connexion'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        )); ?>
</div>