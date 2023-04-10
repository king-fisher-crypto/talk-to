<?php
    echo $this->Metronic->titlePage(__('Pages'),__(($this->params['action'] == 'admin_list' ?'Les pages':'Créer une nouvelle page')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Pages'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'pages', 'action' => 'list', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    //Formulaire de création d'une page---------------------------------------------------------------------------------------------------------------

    $form = $this->Form->create('Page', array('nobootstrap' => 1,'class' => 'panel-contenu', 'default' => 1,
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class' => 'span10',
            'after' => '</div>'
        )));

    $form.= '<div class="row-fluid"><div class="span4"><div class="form-horizontal">';

    $form.= $this->Metronic->inputActive('Page', (isset($this->request->data['Page']['active']) ?$this->request->data['Page']['active']:0));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'page_category_id'              => array('label' => array('text' => __('Catégorie'), 'class' => 'control-label required'), 'required' => true, 'options' => $cat_options, 'empty' => __('Choisissez')),
        'PageLang.0.name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true),
        'PageLang.0.meta_title'         => array('label' => array('text' => __('Méta titre'), 'class' => 'control-label required'), 'required' => true, 'type' => 'text', 'after' => '<p class="label label-info" style="white-space:normal">'.__('Nombre de caractères recommandé : ').Configure::read('Site.lengthMetaTitle').'</p></div>'),
        'PageLang.0.lang_id'            => array('label' => array('text' => __('Langue'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $lang_options),
        'PageLang.0.link_rewrite'       => array('label' => array('text' => __('Lien réécrit'), 'class' => 'control-label')),
        'PageLang.0.meta_keywords'      => array('label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'), 'after' => '<p class="label label-info" style="white-space:normal">'.__('Nombre de caractères recommandé : ').Configure::read('Site.lengthMetaKeywords').'</p></div>'),
        'PageLang.0.meta_description'   => array('label' => array('text' => __('Méta description'), 'class' => 'control-label'), 'after' => '<p class="label label-info" style="white-space:normal">'.__('Nombre de caractères recommandé : ').Configure::read('Site.lengthMetaDescription').'</p></div>')
    ));

    $form.= '</div></div><div class="span8"><div class="tinymce">';

    $form.= $this->Form->input('PageLang.0.content', array('type' => 'textarea', 'tinymce' => true, 'label' => false, 'div' => false, 'between' => false, 'after' => false));

    $form.= '</div></div></div>';

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    //Listing des pages---------------------------------------------------------------------------------------------------------------------------------------

    $html = '<div class="row-fluid">';
    $html.= '<div class="portlet box blue">';
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les pages CMS'). '</div>';
    $html.= '<div class="pull-right">';
    $html.= '<span class="label-search">'. __('Recherche') .'</span>';
    $html.= $this->Form->create('PageLang', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    $html.= $this->Form->input('title', array('class' => 'input-mini  margin-left margin-right', 'type' => 'text', 'label' => __('Titre').' :', 'div' => false));
    $html.= $this->Form->input('search', array('type' => 'hidden'));
    $html.= '<input class="btn green" type="submit" value="Ok">';
    $html.= '</form>';
    $html.= $this->Form->create('PageLang', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    $html.= $this->Form->input('category', array('class' => 'margin-left margin-right', 'options' => $cat_options, 'empty' => '------', 'label' => __('Catégorie').' :', 'div' => false));
    $html.= $this->Form->input('search', array('type' => 'hidden'));
    $html.= '<input class="btn green" type="submit" value="Ok">';
    $html.= '</form>';
    $html.= '</div></div>';
    $html.= '<div class="portlet-body">';

    if(empty($pages)) :
        $html.= __('Pas de page');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>'.$this->Paginator->sort('PageLang.page_id', '#').'</th>';
        $html.= '<th>'.$this->Paginator->sort('PageLang.name', __('Nom')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('Page.page_category_id', __('Catégorie')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('Page.active', __('Etat')).'</th>';
        $html.= '<th>'.__('Langue').'</th>';
        $html.= '<th></th>';
        $html.= '</tr></thead><tbody>';

        foreach($pages as $page) :

            $html.= '<tr>';
            $html.= '<td>'. $page['page_id'] .'</td>';
            $html.= '<td>'. $page['name'] .'</td>';
            $html.= '<td>'. $page['category_name'] .'</td>';
            $html.= '<td>'. $page['etat'] .'</td>';
            $html.= '<td>'. $page['langs'] .'</td>';
            $html.= '<td>';
            $html.= '<div class="btn-group margin-left">';
            $html.= '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
            $html.= '<ul class="dropdown-menu pull-right">';
            if($page['active'] == 1 && !$page['hidden_page']){
                $html.= '<li>';
				$html.= '<a href="'.Configure::read('Site.baseUrlFull').$page['link_rewrite'].'" class="btn green" target="_blank"><span class="icon-zoom-in"></span> Voir la page</a>';
                /*$html.= $this->Html->link('<span class="icon-zoom-in"></span> '.__('Voir la page'),
                    array(
                        'language'          => $this->Session->read('Config.language'),
                        'controller'        => 'pages',
                        'action'            => 'display',
                        'admin'             => false,
                        'id'                => $page['page_id'],
                        'link_rewrite'      => $page['link_rewrite'],
                    ),
                    array('escape' => false, 'class' => 'btn green', 'target' => '_blank')
                );*/
                $html.= '</li>';
            }
            $html.= '<li>';
            $html.= $this->Metronic->getLinkButton(
                __('Modifier'),
                array('controller' => 'pages','action' => 'edit', 'admin' => true, 'id' => $page['page_id']),
                'btn blue',
                'icon-edit');
            $html.= '</li>';


           // if (!$page['hidden_page']){
                $html.= '<li>';
                if($page['active'])
                    $html.= $this->Metronic->getLinkButton(
                        __('Désactiver'),
                        array('controller' => 'pages','action' => 'delete', 'admin' => true, 'id' => $page['page_id']),
                        'btn yellow',
                        'icon-remove',
                        __('Voulez-vous vraiment désactiver la page ').$page['name'].' ?');
                else
                    $html.= $this->Metronic->getLinkButton(
                        __('Activer'),
                        array('controller' => 'pages','action' => 'add', 'admin' => true, 'id' => $page['page_id']),
                        'btn green',
                        'icon-plus',
                        __('Voulez-vous vraiment activer la page ').$page['name'].' ?');
                $html.= '</li>';



                $html.= '<li>';
                $html.= $this->Metronic->getLinkButton(
                    __('Supprimer'),
                    array('controller' => 'pages','action' => 'true_delete', 'admin' => true, 'id' => $page['page_id']),
                    'btn red',
                    'icon-remove',
                    __('Voulez-vous vraiment supprimer définitivement la page ').$page['name'].' ?');
                $html.= '</li>';
           // }


            $html.= '</ul>';
            $html.= '</div>';
            $html.= '</td>';
            $html.= '</tr>';
        endforeach;

        $html.= '</tbody></table>';
        if($this->Paginator->param('pageCount') > 1) :
            $html.= $this->Metronic->pagination($this->Paginator);
        endif;
    endif;

    $html.= '</div></div></div>';

    $tabs = array(
        0 => array('text'       => __('Pages'),
            'icon'       => 'icon-list',
            'content'    => $html
        ),
        1 => array('text'       => __('Nouvelle page'),
            'icon'       => 'icon-plus',
            'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['Page']))?1:0);
                            
                            

