<?php
App::uses('FormHelper', 'View/Helper');

/**
 * BootstrapFormHelper.
 *
 * Applies styling-rules for Bootstrap 3
 *
 * To use it, just save this file in /app/View/Helper/BootstrapFormHelper.php
 * and add the following code to your AppController:
 *   	public $helpers = array(
 *		    'Form' => array(
 *		        'className' => 'BootstrapForm'
 *	  	  	)
 *		);
 *
 * @link https://gist.github.com/Suven/6325905
 */
class BootstrapFormHelper extends FormHelper {

    public function create($model = null, $options = array()) {
        if (isset($options['nobootstrap'])){
            unset($options['nobootstrap']);
            return parent::create($model, $options);
        }
        
        $defaultOptions = array(
            'inputDefaults' => array(
                //'div' => 'form-group',
                'label' => array(
                    'class' => 'col col-md-2 control-label'
                ),
                'wrapInput' => 'col col-md-10',
                'class' => 'form-control'
            ),
            'class' => 'form-horizontal'
        );

        if(!empty($options['inputDefaults'])) {
            $options = array_merge($defaultOptions['inputDefaults'], $options['inputDefaults']);
        } else {
            $options = array_merge($defaultOptions, $options);
        } 
        return parent::create($model, $options);
    }
    
    // Remove this function to show the fieldset & language again
    public function inputs($fields = null, $blacklist = null, $options = array()) {
    	$options = array_merge(array('fieldset' => false), $options);

    	return parent::inputs($fields, $blacklist, $options);
    }
    
    public function submit($caption = null, $options = array()) {
        $defaultOptions = array();
    	if (!isset($options['div']) || $options['div'] != false){
    	    $defaultOptions = array(
                'class' => 'btn btn-primary',
                'before'=> '<div class="col-lg-12 text-center">',
                'after' => '</div>',
                'div'   => 'form-group',
                'href'  => false
            );
            foreach ($defaultOptions AS $k => $v)
                if (isset($options[$k]))
                    $defaultOptions[$k] = $options[$k];

    	}
            
	    return parent::submit($caption, $defaultOptions);
    }

}