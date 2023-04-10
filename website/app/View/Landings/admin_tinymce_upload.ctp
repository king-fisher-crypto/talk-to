<?php

    //include('/webroot/theme/default/js/tinymce/plugins/filemanager/dialog.php');
    //App::import('Vendor/Noox', 'Api.php');

    /*echo $this->Html->css('/assets/plugins/bootstrap/css/bootstrap.min.css');
    echo $this->Html->css('/assets/plugins/bootstrap/css/bootstrap-responsive.min.css');
    echo $this->Html->css('/assets/plugins/font-awesome/css/font-awesome.min.css');
    echo $this->Html->css('/assets/css/style-metro.css');
    echo $this->Html->css('/assets/css/style.css');
    echo $this->Html->css('/assets/css/style-responsive.css');
    echo $this->Html->css('/assets/css/themes/default.css');
    echo $this->Html->css('/assets/plugins/uniform/css/uniform.default.css');
    echo $this->Html->css('/assets/plugins/select2/select2_metro.css');
    echo $this->Html->css('/assets/css/pages/login.css');*/
    echo $this->Html->css('/theme/default/css/admin');

    echo $this->Session->flash();

    echo $this->Form->create('Tinymce', array('nobootstrap' => 1,'class' => 'form-horizontal span4 panel-contenu', 'default' => 1, 'enctype' => 'multipart/form-data',
                                              'inputDefaults' => array(
                                                  'div' => 'control-group',
                                                  'between' => '<div class="controls">',
                                                  'after' => '</div>'
                                              )));

    echo $this->Form->inputs(array(
        'legend' => false,
        'photo'     => array(
            'label' => array('text' => __('Photo (.jpg .png .gif)'),'class' => 'control-label required'),
            'required' => true,
            'type' => 'file'
        )
    ));

    echo $this->Form->end(array(
        'label' => __('OK'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

if(isset($saveImage) && $saveImage){
    echo $this->Html->script('/theme/default/js/tinymcePopup');
}