<?php
    echo $this->Metronic->titlePage(__('Catégories'),__(($this->params['action'] == 'admin_list' ?'Les catégories':'Créer une nouvelle catégorie')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Catégories'), 'classes' => 'icon-sitemap', 'link' => $this->Html->url(array('controller' => 'category', 'action' => 'list', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    $form = $this->Form->create('Category', array('nobootstrap' => 1,'class' => 'form-horizontal panel-contenu', 'default' => 1,
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class' => 'span10',
            'after' => '</div>'
        )
    ));

    $form.= '<div class="row-fluid"><div class="span4">';

    $form.= $this->Metronic->inputActive('Category', (isset($this->request->data['Category']['active']) ?$this->request->data['Category']['active']:0));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'CategoryLang.0.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'),'required' => true),
        'CategoryLang.0.lang_id'            => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $lang_options),
		'CategoryLang.0.cat_rewrite'       => array('label' => array('text' => __('Parent Lien réécrit'), 'class' => 'control-label')),
		'CategoryLang.0.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label')),
		
		'CategoryLang.0.meta_title2'         => array('label' => array('text' => __('Méta titre category'), 'class' => 'control-label'), 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaTitle').'</p></div>'),
        'CategoryLang.0.meta_keywords2'      => array('label' => array('text' => __('Méta mots-clés category'), 'class' => 'control-label'), 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaKeywords').'</p></div>'),
        'CategoryLang.0.meta_description2'   => array('label' => array('text' => __('Méta description category'), 'class' => 'control-label'), 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaDescription').'</p></div>')
    ));

/*
'CategoryLang.0.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label'), 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaTitle').'</p></div>'),
        'CategoryLang.0.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaKeywords').'</p></div>'),
        'CategoryLang.0.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'after' => '<p class="label label-info">'.__('Nombre de caractère recommandé : ').Configure::read('Site.lengthMetaDescription').'</p></div>'),
*/


    $form.= '</div><div class="tinymce">';

    $form.= $this->Form->input('CategoryLang.0.description', array('type' => 'textarea', 'tinymce' => true, 'label' => false, 'div' => false, 'between' => false, 'after' => false));

    $form.= '</div></div>';

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue save',
        'div' => array('class' => 'controls')
    ));

    $tabs = array(
        0 => array('text'       => __('Catégories'),
            'icon'       => 'icon-list',
            'content'    => $this->Metronic->getSimpleTable($categories, array('category_id' => '#', 'name' => __('Nom'), 'lang_name' => __('Langue'), 'agents' => __('Nombre d\'agents'), 'date_add' => __('Date d\'ajout'), 'etat' => __('Etat')),
                    function($row, $caller){
                        return $caller->Metronic->getLinkButton(
                            __('Modifier'),
                            array('controller' => 'category','action' => 'edit', 'admin' => true, 'id' => $row['category_id']),
                            'btn blue',
                            'icon-edit').' '.
                        ($row['active']
                            ?$caller->Metronic->getLinkButton(
                                __('Désactiver'),
                                array('controller' => 'category','action' => 'delete', 'admin' => true, 'id' => $row['category_id']),
                                'btn red',
                                'icon-remove',
                                __('Voulez-vous vraiment désactiver la catégorie ').$row['name'].' ?')
                            :$caller->Metronic->getLinkButton(
                                __('Activer'),
                                array('controller' => 'category','action' => 'add', 'admin' => true, 'id' => $row['category_id']),
                                'btn green',
                                'icon-plus',
                                __('Voulez-vous vraiment activer la catégorie ').$row['name'].' ?')
                        );
                    }, $this)
        ),
        1 => array('text'       => __('Nouvelle catégorie'),
            'icon'       => 'icon-plus',
            'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['Category']))?1:0);
                            
                            

