<?php
    echo $this->Metronic->titlePage(__('Produits'),__('Editer "'.$product['ProductLang'][0]['name'].'"'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Produit'), 'classes' => 'icon-euro', 'link' => $this->Html->url(array('controller' => 'products', 'action' => 'index', 'admin' => true))
        )
    ));
    echo $this->Session->flash();

    $form = $this->Form->create('Product', array('nobootstrap' => 1,'class' => 'form-horizontal panel-contenu', 'default' => 1,
                                                 'inputDefaults' => array(
                                                     'div' => 'control-group',
                                                     'between' => '<div class="controls">',
                                                     'after' => '</div>'
                                                 )
    ));

    $form.= $this->Metronic->inputActive('Product', ((isset($this->request->data['Product']['active']) && $this->request->data['Product']['active'] == 1) || $product['Product']['active'] == 1 ?1:0));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'country_id'                => array('label' => array('text' => __('Pays'), 'class' => 'control-label required'), 'required' => true, 'options' => $select_countries, 'selected' => $product['Product']['country_id']),
        'credits'                   => array('label' => array('text' => __('Credit'), 'class' => 'control-label required'), 'required' => true, 'value' => $product['Product']['credits']),
        'tarif'                     => array('label' => array('text' => __('Prix'), 'class' => 'control-label required'), 'required' => true, 'value' => $product['Product']['tarif']),
		'cout_min'                     => array('label' => array('text' => __('Cout / Minute'), 'class' => 'control-label required'), 'required' => true, 'value' => $product['Product']['cout_min']),
		'economy_pourcent'                     => array('label' => array('text' => __('% Economie'), 'class' => 'control-label required'), 'required' => true, 'value' => $product['Product']['economy_pourcent'])
    ));

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    $tabs = array();
    //L'onglet du produit
    $tabs[] = array(
        'text'      => __('Le produit'),
        'icon'      => 'icon-euro',
        'content'   => $form
    );
    //On crée les onglets pour chaque langue
    foreach($langs as $id => $val){
        array_push($tabs, array(
            'text'      => __($val[array_keys($val)[0]]),
            'icon'      => 'lang_flags lang_'.array_keys($val)[0],
            'content'   => $this->Metronic->formProductLang($product['Product']['id'],$id,(isset($langDatas[$id])?$langDatas[$id]:array()))
        ));
    }

    echo $this->Metronic->getTabs($tabs);
                            
                            

