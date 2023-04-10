<?php $user = $this->Session->read('Auth.User'); ?>
<div class="chatbox<?php echo ($user['role'] === 'agent' ?' chatbox_agent':' chatbox_client'); ?>" id="chatbox_0">
    <div class="cb_pseudo">
        <div class="action"><?php if( $user['role'] == 'agent'){ ?><div style="display:block;clear:both;float:right;margin-top:0px;" class="cb_notes">mes notes&nbsp;<i class="glyphicon glyphicon-pencil rfloat "></i>&nbsp;&nbsp;<i class="glyphicon glyphicon-resize-full rfloat cb_growup hidden-xs"></i>&nbsp;&nbsp;<i class="glyphicon glyphicon-remove-circle rfloat cb_close"></i></div><?php }else{ ?><i class="glyphicon glyphicon-resize-full rfloat cb_growup hidden-xs"></i>&nbsp;&nbsp;<i class="glyphicon glyphicon-remove-circle rfloat cb_close"></i><?php } ?></div>
        <div class="avatar"><img src="/<?php echo Configure::read('Site.defaultImage'); ?>"></div>
        <p class="name"></p>
        <p class="msg"></p>
    </div>
	
    <div class="cb_time">
        <ul>
            <li class="cb_time_left"><?php

                if($user['role'] === 'client'){
                    echo __('Votre temps sera décompté dès la première réponse de l\'agent');
                }

                ?></li>
        </ul>
    </div>
	<div class="cb_pictures"></div>
    <div class="cb_history">
        <ul>
        </ul>
        <p class="event"></p>
    </div>
	<p class="alert_time" style="padding-left: 5px;padding-top:5px;padding-bottom:5px;color:#ff0000;text-align: center; font-weight:normal;"></p>
    <?php
        echo $this->Form->create('Chat', array(
            'nobootstrap' => 1,
            'class' => 'form-horizontal',
            'default' => 1,
            'inputDefaults' => array(
                'label' => false,
                'div'   => false
            )
        ));


        if($user['role'] === 'agent')
            echo '<div class="time_consult"></div>';


        echo $this->Form->textarea('message', array('label' => false, 'required' => true, 'type' => 'textarea', 'placeholder' => 'Ecrivez votre message… '));


        echo $this->Form->end(array(
            'label' => 'Envoyer',
            'default' => 1,
          //  'after' => '<button type="button" class="btn btn-danger margin_top_5 margin_left_5 cb_close">'. __('Quitter le chat') .'</button></div>',
            'div' => array(),
            'inputDefaults' => array(
                'label' => false,
                'div' => false
            ),
            'class' => false
        ));
        echo '<div class="submit"><button type="button" class="btn btn-danger margin_top_5 cb_close hide_btn_close">'. __('Quitter le chat') .'</button></div>';
    ?>
</div>