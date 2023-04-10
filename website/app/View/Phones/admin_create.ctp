<?php
    echo $this->Metronic->titlePage(__('Contenu'),__('Les numéros'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Numéro'),
            'classes' => 'icon-bell',
            'link' => $this->Html->url(array('controller' => 'phones', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Ajout d\'un numéro'); ?></div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Phone', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array('class' => 'span8')));

                //Les inputs du formulaire
                $inputs = array(
                    'country_id'                => array('label' => array('text' => __('Pays'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_countries),
                    'lang_id'                   => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'required' => true, 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'options' => $select_langs),
                    'surtaxed_phone_number'     => array('label' => array('text' => __('Téléphone surtaxé'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>'),
                    'surtaxed_minute_cost'      => array('label' => array('text' => __('Coût de la minute surtaxée'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>'),
                    'prepayed_phone_number'     => array('label' => array('text' => __('Téléphone prépayé'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>'),
                    'prepayed_minute_cost'      => array('label' => array('text' => __('Coût de la minute prépayée'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>'),
                    'prepayed_second_credit'   => array('label' => array('text' => __('Nombre de secondes pour un crédit'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>'),

                    'mention_legale_num1'      => array('label' => array('text' => __('Mention légale (num. 1)'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>'),
                    'mention_legale_num2'       => array('label' => array('text' => __('Mention légale (num. 2)'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>')
                );

                echo $this->Metronic->inputsAdminEdit($inputs);

                echo $this->Form->end(array(
                    'label' => __('Enregistrer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>