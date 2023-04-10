<?php
    echo $this->Metronic->titlePage(__('Logo'),__('Logos des sites'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Logos'),
            'classes' => 'icon-picture',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'logo', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    //-------------- Listing des logos----------------------------------------------------------------------------

    $html = '<div class="row-fluid"><div class="portlet box yellow"><div class="portlet-title">';
    $html.= '<div class="caption">'. __('Les logos pour chaque site'). '</div></div>';
    $html.= '<div class="portlet-body">';
    if(empty($domains)):
        $html.= __('Pas de logo');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead>';
        $html.= '<tr>';
        $html.= '<th>'. $this->Paginator->sort('Domain.id', '#') .'</th>';
        $html.= '<th>'. $this->Paginator->sort('Domain.domain', __('Site')) .'</th>';
        $html.= '<th>'. __('Logo') .'</th>';
        $html.= '<th>'. __('Logo SSL (securelogo.com)') .'</th>';
        $html.= '<th>'. __('Actions') .'</th>';
        $html.= '</tr></thead><tbody>';

        foreach ($domains as $domain):
            $html.= '<tr>';
            $html.= '<td>' .$domain['Domain']['id']. '</td>';
            $html.= '<td>' .$domain['Domain']['domain']. '</td>';
            $html.= '<td>' .$this->Html->image('/'.Configure::read('Site.pathLogo').'/'.$domain['Domain']['id'].'_logo.jpg'). '</td>';
            $html.= '<td>' .($domain['Domain']['ssl_hosting']?$this->Html->image($domain['Domain']['ssl_hosting']):'-'). '<br/>'.($domain['Domain']['ssl_hosting']?$domain['Domain']['ssl_hosting']:'-').'</td>';
            $html.= '<td>' .$this->Metronic->getLinkButton(
                __('Modifier')   ,
                array('controller' => 'admins', 'action' => 'edit_logo', 'admin' => true, 'id' => $domain['Domain']['id']),
                'btn blue',
                'icon-edit'
            ). '</td>';
            $html.= '</tr>';
        endforeach;

        $html.= '</tbody></table>';
        if($this->Paginator->param('pageCount') > 1) $html.= $this->Metronic->pagination($this->Paginator);

    endif;
    $html.= '</div></div></div>';

    //--------------------------Nouveau logo------------------------------------------------------------------------

    $form = $this->Form->create('Logo', array('nobootstrap' => 1,'class' => 'form-horizontal span6 panel-contenu', 'default' => 1, 'enctype' => 'multipart/form-data',
                                              'inputDefaults' => array(
                                                  'div' => 'control-group',
                                                  'between' => '<div class="controls">',
                                                  'class'   => 'span10',
                                                  'after' => '</div>'
                                              )));

    $form.= $this->Form->inputs(array(
        'legend' => false,
        'domain'    => array('label' => array('text' => __('Site'), 'class' => 'control-label required'), 'required' => true, 'options' => $domain_select, 'empty' => __('Choisissez')),
        'photo'     => array(
            'label' => array('text' => __('Photo (.jpg .jpeg)'),'class' => 'control-label required'),
            'required' => true,
            'type' => 'file',
            'accept' => 'image/*',
            'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Logo.width').'x'.Configure::read('Logo.height').'</p></div>'
        )
    ));

    $form.= $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue',
        'div' => array('class' => 'controls')
    ));

    $tabs = array(
    0 => array('text'       => __('Logos'),
    'icon'       => 'icon-list',
    'content'    => $html
    ),
    1 => array('text'       => __('Nouveau logo'),
    'icon'       => 'icon-plus',
    'content'    => $form
    )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create_logo' || isset($this->request->data['Page']))?1:0);