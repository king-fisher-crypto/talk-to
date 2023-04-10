<?php echo $this->Html->script('/theme/default/js/inputDateEmpty', array('block' => 'script')); ?>
<?php if(isset($valid)): //Valid est censé existé donc si c'est pas le cas on renvoie rien?>
    <?php if($valid): //Compte valid?>
        <?php if(!isset($userData)): ?>
            <?php echo __('Erreur dans la récupération de vos données'); ?>
        <?php else : ?>
            <?php echo $this->Form->inputs(array(
                'legend' => false,
                'firstname' => array(
                    'label' => array('text' => __('Prénom ou pseudo'), 'class' => 'control-label col-lg-4 required'),
                    'before' => '<h3 class="tabs-heading">'.__('Informations générales').'</h3>',
                    'required' => true
                ),
                'lastname' => array(
                    'label' => array('text' => __('Nom'), 'class' => 'control-label col-lg-4 norequired'),
					'after' => '<span class="help">'.__('Non visible de vos experts').'</span>',
                ),
                'birthdate' => array(
                    'label'         =>   array('text' => __('Date de naissance'), 'class' => 'control-label col-lg-4 norequired'),
                    'dateFormat'    =>   'DMY',
                    'empty'         =>   true,
                    'minYear'       =>   date('Y') - 80,
                    'maxYear'       =>   date('Y') - 18,
                    'div'           =>   'form-group form-inline col-xs-12 col-lg-12 col-md-12 select_date_min',
                )
            )); ?>
        <?php endif; ?>
    <?php else: //Compte pas valid?>
        <?php echo $this->Form->inputs(array(
            'legend' => false,
            'firstname' => array(
                'label' => array('text' => __('Prénom'), 'class' => 'control-label col-lg-4 required'),
                'before' => '<fieldset><legend>'.__('Informations générales').'</legend></fieldset>',
                'required' => true
            ),
            'lastname' => array(
                'label' => array('text' => __('Nom'), 'class' => 'control-label col-lg-4 norequired'),
				'after' => '<p>'.__('Non visible de vos experts').'</p>',
            ),
            'birthdate' => array(
                'label'         =>   array('text' => __('Date de naissance'), 'class' => 'control-label col-lg-4 norequired'),
                'dateFormat'    =>   'DMY',
                'empty'         =>   true,
                'minYear'       =>   date('Y') - 80,
                'maxYear'       =>   date('Y') - 18,
                'div'           =>   'form-group form-inline col-xs-12 col-lg-12 col-md-12 select_date_min',
            )
        )); ?>
    <?php endif; ?>
<?php endif; ?>