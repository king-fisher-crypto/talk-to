<?php
echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
          <br />
        </div>
        <div class="portlet-body form custom-form-sh">
            <?php
            echo $this->Form->create('Rooms', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array('class' => 'span10')));
            echo '<h3 class="form-section">'.__('Add new room').'</h3>';

            //Les inputs du formulaire
            $inputs = array(
                'title' => array('label' => array('text' => __('Title'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'slug' => array('label' => array('text' => __('Slug'), 'class' => 'control-label'), 'required' => true, 'autocomplete'=> off, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '<label id="message" style="color: Red; display: none">Special Characters and space not allowed</label></div>'),
                'no_of_invites' => array('label' => array('text' => __('No. of invites'),  'class' => 'control-label'),'required' => true, 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>'),
                'date_start' => array('label' => array('text' => __('Date Start'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'date'),
                'date_end' => array('label' => array('text' => __('Date End'), 'class' => 'control-label'), 'div' => 'control-group span4', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'date'),
                'role' => array('label' => array('text' => __('Handle By'), 'class' => 'control-label role_option_field custom-input roleinput-cls'), 'div' => 'control-group span4 custom-input', 'between' => '<div class="controls">', 'after' => '</div>', 'type' => 'select', 'options'=>array('1'=>'Admin','2'=>'Moderator')),
                'user_id' => array('label' => array('text' => __('Select Moderator'), 'class' => 'control-label custom-input '), 'div' => 'control-group span4 custom-input custom-user-id-cls custom-handler', 'between' => '<div class="controls user_id_input ">', 'after' => '</div>', 'type' => 'select', 'options'=>$getagents),
                
            );//protege avec code admin level

            echo $this->Metronic->inputsAdminEdit($inputs);

            //echo $this->Form->input();
?>
        <div class="cls">
          <div class="overflow-container-cs">
            <?php

              echo $this->Form->input('invited_users', array(
                  'label' => 'Invite Users',
                  'type' => 'select',
                  'multiple' => 'checkbox',
                  'options' => $getclients,
                  //'selected' => $selectedWarnings
                ));
            ?>
          </div>
        </div>

<?php


            echo $this->Form->end(array(
                'label' => __('Save'),
                'class' => 'btn ',
                'div' => array('class' => 'controls')
            ));
            ?>
            <br/><br/>
        </div>
    </div>
</div>


