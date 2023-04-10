<?php
    echo '<h3>'.__('Votre réponse').'</h3>';
    echo $this->Form->create('Admin', array('action' => 'admin_mails','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                           'inputDefaults' => array(
                                               'div' => 'form-group',
                                               'between' => '<div class="span8">',
                                               'after' => '</div>',
                                               'class' => 'form-control span8'
                                           )
    ));

    echo $this->Form->inputs(array(
        'mail_id' => array('type' => 'hidden', 'value' => $idMail),
        'content' => array('label' => array('text' => __('Votre message'), 'class' => 'control-label span3  required'), 'required' => true, 'type' => 'textarea', 'class' => 'tinymce', 'style'=> 'width:100%')
    ));

    echo $this->Form->end(array(
        'label' => 'Répondre',
        'class' => 'btn',
        'div' => array('class' => 'margin-tp margin-bt span10 offset2')
    ));