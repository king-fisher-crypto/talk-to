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
    <div class="portlet box green">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Edition du numéro'); ?></div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Phone', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'inputDefaults' => array('class' => 'span8')));

                echo $this->Form->inputs(array(
                    'country_id' => array('type' => 'hidden', 'value' => $country),
                    'lang_id'   => array('type' => 'hidden', 'value' => $lang)
                ));

                //Les inputs du formulaire
                $inputs = array(
                    'surtaxed_phone_number'     => array('label' => array('text' => __('Téléphone surtaxé'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['surtaxed_phone_number']),
                    'surtaxed_minute_cost'      => array('label' => array('text' => __('Coût de la minute surtaxée'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['surtaxed_minute_cost']),
                    'prepayed_phone_number'     => array('label' => array('text' => __('Téléphone prépayé'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['prepayed_phone_number']),
                    'prepayed_minute_cost'      => array('label' => array('text' => __('Coût de la minute prépayée'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['prepayed_minute_cost']),
                    'prepayed_second_credit'    => array('label' => array('text' => __('Nombre de secondes pour un crédit'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['prepayed_second_credit']),

                    'third_phone_number'      => array('label' => array('text' => __('3ème numéro de tél'), 'class' => 'control-label'), 'div' => 'control-group span5','between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['third_phone_number']),
                    'third_minute_cost'       => array('label' => array('text' => __('3ème num, coût minute'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['third_minute_cost']),

                    'mention_legale_num1'            => array('label' => array('text' => __('Mention légale (num. 1)'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['mention_legale_num1']),
                    'mention_legale_num2'            => array('label' => array('text' => __('Mention légale (num. 2)'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['mention_legale_num2']),
                    'mention_legale_num3'            => array('label' => array('text' => __('Mention légale (num. 3)'), 'class' => 'control-label'), 'div' => 'control-group span5', 'between' => '<div class="controls">', 'after' => '</div>', 'value' => $phone['CountryLangPhone']['mention_legale_num3'])
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