<?php
    echo $this->Metronic->titlePage(__('Card Items'),__(($this->params['action'] == 'admin_list' ?'Les régles':'Créer une nouvelle item')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Card Items'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'cardItems', 'action' => 'list', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    //Formulaire de création d'une cards---------------------------------------------------------------------------------------------------------------

    $form = $this->Form->create('CardItem', array('nobootstrap' => 1,'class' => 'panel-contenu', 'default' => 1,  'enctype' => 'multipart/form-data',
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class' => 'span10',
            'after' => '</div>'
        )));

    $form.= '<div class="row-fluid"><div class="span4"><div class="form-horizontal">';

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'name'               => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true),
		'image'       => array('label' => array('text' => __('Image Carte (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file'),
		'card_id'            => array('label' => array('text' => __('Choisir card'), 'class' => 'control-label required'), 'allowEmpty' => false, 'required' => true, 'options' => $card_options,'value'=>'card_id'),
    ));

$form.= '</div></div><div class="span8"> <h3>'. __('Description').'</h3><div class="tinymce">';

$form.= $this->Form->input('description', array('type' => 'textarea', 'tinymce' => true, 'label' => false, 'div' => false, 'between' => false, 'after' => false));

$form.= '</div></div>';

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    //Listing des pages---------------------------------------------------------------------------------------------------------------------------------------

    $html = '<div class="row-fluid">';
    $html.= '<div class="portlet box blue">';
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les items'). '</div>';
    $html.= '<div class="pull-right">';
    $html.= '<span class="label-search">'. __('Recherche') .'</span>';
    $html.= $this->Form->create('CardItem', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    $html.= $this->Form->input('title', array('class' => 'input-mini  margin-left margin-right', 'type' => 'text', 'label' => __('Titre').' :', 'div' => false));
    $html.= $this->Form->input('search', array('type' => 'hidden'));
    $html.= '<input class="btn green" type="submit" value="Ok">';
    $html.= '</form>';
    $html.= '</div></div>';
    $html.= '<div class="portlet-body">';

    if(empty($rules)) :
        $html.= __('Pas de item');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>'.$this->Paginator->sort('id', '#').'</th>';
        $html.= '<th>'.$this->Paginator->sort('name', __('Nom')).'</th>';
        $html.= '<th>'.__('Card ').'</th>';
        $html.= '<th></th>';
        $html.= '</tr></thead><tbody>';

        foreach($rules as $rule) :
		    $html.= '<tr>';
            $html.= '<td>'. $rule['CardItem']['id'] .'</td>';
            $html.= '<td>'. $rule['CardItem']['name'] .'</td>';
            $html.= '<td>'. $rule['CardLang']['name'] .'</td>';
            $html.= '<td>';
            $html.= '<div class="btn-group margin-left">';
            $html.= '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
            $html.= '<ul class="dropdown-menu pull-right">';

            $html.= '<li>';
            $html.= $this->Metronic->getLinkButton(
                __('Modifier'),
                array('controller' => 'cardItems','action' => 'edit', 'admin' => true, 'id' => $rule['CardItem']['id']),
                'btn blue',
                'icon-edit');
            $html.= '</li>';


                $html.= '<li>';
                $html.= $this->Metronic->getLinkButton(
                    __('Supprimer'),
                    array('controller' => 'cardItems','action' => 'true_delete', 'admin' => true, 'id' => $rule['CardItem']['id']),
                    'btn red',
                    'icon-remove',
                    __('Voulez-vous vraiment supprimer définitivement le carte ').$rule['CardItem']['name'].' ?');
                $html.= '</li>';

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
        0 => array('text'       => __('Carte Item'),
            'icon'       => 'icon-list',
            'content'    => $html
        ),
        1 => array('text'       => __('Nouvelle carte'),
            'icon'       => 'icon-plus',
            'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['CardItem']))?1:0);



