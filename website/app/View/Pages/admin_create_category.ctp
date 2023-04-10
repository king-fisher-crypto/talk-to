<?php
    echo $this->Metronic->titlePage(__('Pages'),__('Catégories des pages CMS'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Categories CMS'), 'classes' => 'icon-sitemap', 'link' => $this->Html->url(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    $form = $this->Form->create('PageCategory', array('nobootstrap' => 1, 'class' => 'form-horizontal panel-contenu', 'default' => 1,
                                                   'inputDefaults' => array(
                                                       'div' => 'control-group',
                                                       'between' => '<div class="controls">',
                                                       'after' => '</div>'
                                                   )
    ));

    $form.= $this->Metronic->inputCheckbox('PageCategory','Active', 'Active', 'active');
    $form.= $this->Metronic->inputCheckbox('PageCategory','Footer', 'Footer', 'footer');

    $form.= $this->Form->inputs(array(
        'PageCategoryLang.0.lang_id'    => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'required' => true, 'options' => $lang_options),
        'PageCategoryLang.0.name'       => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true)
    ));

    $form.= $this->Form->end(array(
        'label' => __('Créer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    $tabs = array(
        0 => array('text'       => __('Catégories CMS'),
                   'icon'       => 'icon-list',
                   'content'    => $this->Metronic->getSimpleTable($pagesCat, array('page_category_id' => '#', 'name' => __('Nom'), 'lang_name' => __('Langue'), 'etat' => __('Etat'), 'footer' => __('Dans le footer'), 'count' => __('Nombre de pages')),
                           function($row, $caller){
                               $html = '<div class="btn-group margin-left">';
                               $html.= '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
                               $html.= '<ul class="dropdown-menu pull-right">';
                               $html.= '<li>';
                               $html.= $this->Form->create('PageLang', array('url' => array('controller' => 'pages', 'action' => 'list', 'admin' => true), 'nobootstrap' => 1,'class' => 'dropdown-form', 'default' => 1));
                               $html.= $this->Form->input('category', array('type' => 'hidden', 'value' => $row['page_category_id']));
                               $html.= $this->Form->input('search', array('type' => 'hidden'));
                               $html.= '<button type="submit" class="btn green"><i class="icon-zoom-in"></i> '.__('Voir les pages').'</button></form>';
                               $html.= '</li>';
                               $html.= '<li>';
                               $html.= $caller->Metronic->getLinkButton(
                                   __('Modifier'),
                                   array('controller' => 'pages','action' => 'edit_category', 'admin' => true, 'id' => $row['page_category_id']),
                                   'btn blue',
                                   'icon-edit');
                               $html.= '</li>';
                               if($row['page_category_id'] != Configure::read('Site.catBlocTexteID')){
                                   $html.= '<li>';
                                   if($row['active'])
                                       $html.= $caller->Metronic->getLinkButton(
                                           __('Désactiver'),
                                           array('controller' => 'pages','action' => 'delete_category', 'admin' => true, 'id' => $row['page_category_id']),
                                           'btn red',
                                           'icon-remove',
                                           __('Voulez-vous vraiment désactiver la catégorie ').$row['name'].' ?');
                                   else
                                       $html.= $caller->Metronic->getLinkButton(
                                           __('Activer'),
                                           array('controller' => 'pages','action' => 'add_category', 'admin' => true, 'id' => $row['page_category_id']),
                                           'btn green',
                                           'icon-plus',
                                           __('Voulez-vous vraiment activer la catégorie ').$row['name'].' ?');
                                   $html.= '</li>';
                               }
                               $html.= '</ul>';
                               $html.= '</div>';
                               return $html;
                               /*return $this->Form->create('PageLang', array('url' => array('controller' => 'pages', 'action' => 'list', 'admin' => true), 'nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1)).
                                        $this->Form->input('category', array('type' => 'hidden', 'value' => $row['page_category_id'])).
                                        $this->Form->input('search', array('type' => 'hidden')).
                                        '<button type="submit" class="btn green"><i class="icon-zoom-in"></i> '.__('Voir les pages').'</button></form>'.
                                   $caller->Metronic->getLinkButton(
                                       __('Modifier'),
                                       array('controller' => 'pages','action' => 'edit_category', 'admin' => true, 'id' => $row['page_category_id']),
                                       'btn blue',
                                       'icon-edit').' '.
                                   ($row['page_category_id'] == Configure::read('Site.catBlocTexteID')
                                       ?''
                                       :($row['active']
                                           ?$caller->Metronic->getLinkButton(
                                               __('Désactiver'),
                                               array('controller' => 'pages','action' => 'delete_category', 'admin' => true, 'id' => $row['page_category_id']),
                                               'btn red',
                                               'icon-remove',
                                               __('Voulez-vous vraiment désactiver la catégorie ').$row['name'].' ?')
                                           :$caller->Metronic->getLinkButton(
                                               __('Activer'),
                                               array('controller' => 'pages','action' => 'add_category', 'admin' => true, 'id' => $row['page_category_id']),
                                               'btn green',
                                               'icon-plus',
                                               __('Voulez-vous vraiment activer la catégorie ').$row['name'].' ?')
                                       )
                                   );*/
                           }, $this)
        ),
        1 => array('text'       => __('Nouvelle catégorie'),
                   'icon'       => 'icon-plus',
                   'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create_category' || isset($this->request->data['PageCategory']))?1:0);
                            
                            

