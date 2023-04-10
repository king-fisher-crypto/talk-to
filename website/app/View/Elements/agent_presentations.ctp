<?php
    if(!isset($presentation)) $presentation = '';

    if(isset($lang_id)){
        echo $this->Form->inputs(array(
            'texte' => array(
                'label' => array(
                    'text' => __('Présentation') .' <span class="star-condition">*</span>',
                    'class' => 'col-sm-12 col-md-4 control-label required'
                ),
                'required' => true,
                'type' => 'textarea',
                'after' => (isset($commentaireOn) && ($commentaireOn))?'<p>'.__('Sera diffusé sur le site').'</p></div>':'',
                'value' => $presentation,
                'between' => '<div class="col-sm-12 col-md-8">'
            ),
            'lang_id' => array('type' => 'hidden', 'value' => $lang_id)
        ));
    }