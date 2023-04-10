<?php
    echo $this->Metronic->titlePage(__('Produits'), __(($this->params['action'] == 'admin_index' ?'Les produits':'Créer un produit')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Produits'), 'classes' => 'icon-euro', 'link' => $this->Html->url(array('controller' => 'products', 'action' => 'index', 'admin' => true))
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

    $form.= $this->Metronic->inputActive('Product', (isset($this->request->data['Product']['active']) ?$this->request->data['Product']['active']:0));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'country_id'                => array('label' => array('text' => __('Pays'), 'class' => 'control-label required'), 'required' => true, 'options' => $select_countries),
        'credits'                   => array('label' => array('text' => __('Credit'), 'class' => 'control-label required'), 'required' => true),
        'tarif'                     => array('label' => array('text' => __('Prix'), 'class' => 'control-label required'), 'required' => true),
		'cout_min'                     => array('label' => array('text' => __('Cout / Minute'), 'class' => 'control-label required'), 'required' => true),
		'economy_pourcent'                     => array('label' => array('text' => __('% Economie'), 'class' => 'control-label required'), 'required' => true),
        'ProductLang.0.lang_id'       => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'required' => true, 'options' =>$lang_options),
        'ProductLang.0.name'          => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true),
        'ProductLang.0.description'   => array('label' => array('text' => __('Description'), 'class' => 'control-label'), 'type' => 'textarea')
    ));

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    $tabs = array(
        0 => array('text'       => __('Produits'),
                   'icon'       => 'icon-list',
                   'content'    => $this->Metronic->getSimpleTable($products, array('product_id' => '#', 'name' => __('Nom'), 'lang_name' => __('Langue'), 'countryLang' => __('Site'), 'date_add' => __('Date de création'),
                                                                                    'tarif' => __('Prix'), 'credits' => __('Crédit'), 'etat' => __('Etat')),
                           function($row, $caller){
                               return $caller->Metronic->getLinkButton(
                                   __('Modifier'),
                                   array('controller' => 'products','action' => 'edit', 'admin' => true, 'id' => $row['product_id']),
                                   'btn blue',
                                   'icon-edit').' '.
                               ($row['active']
                                   ?$caller->Metronic->getLinkButton(
                                       __('Désactiver'),
                                       array('controller' => 'products','action' => 'delete', 'admin' => true, 'id' => $row['product_id']),
                                       'btn red',
                                       'icon-remove',
                                       __('Voulez-vous vraiment désactiver le produit').' "'.$row['name'].'" ?')
                                   :$caller->Metronic->getLinkButton(
                                       __('Activer'),
                                       array('controller' => 'products','action' => 'add', 'admin' => true, 'id' => $row['product_id']),
                                       'btn green',
                                       'icon-plus',
                                       __('Voulez-vous vraiment activer le produit').' "'.$row['name'].'" ?')
                               );
                           }, $this)
        ),
        1 => array('text'       => __('Nouveau produit'),
                   'icon'       => 'icon-plus',
                   'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['Product']))?1:0);