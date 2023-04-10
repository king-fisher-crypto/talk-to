<?php
    echo $this->Metronic->titlePage(__('Horoscopes'),__(($this->params['action'] == 'admin_list' ?'Les horoscopes':'Créer un nouvel horoscope')));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Horoscopes'), 'classes' => 'icon-asterisk', 'link' => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'list', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    //Formulaire de création d'un horoscope---------------------------------------------------------------------------------------------------------------

    $form = '<div class="row-fluid">';
    $form.= $this->Form->create('Horoscope', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                              'inputDefaults' => array(
                                                  'div' => 'control-group',
                                                  'between' => '<div class="controls">',
                                                  'class'   => 'span12',
                                                  'after' => '</div>'
                                              )));

    $form.= '<div class="span8"><div class="tabbable tabbable-custom tabbable-full-width"><ul class="nav nav-tabs">';
    $i=0;
    foreach($langs as $id => $val):
        $form.= '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
        $i = 1;
    endforeach;
    $form.= '</ul><div class="tab-content">';
    $i=0;
    foreach($langs as $id => $val):
        $form.= '<div id="tab'.$id.'" class="tab-pane'.($i==0 ?' active':'').'">';
        $form.= $this->Metronic->formHoroscopeLang($id);
        $form.= '</div>';
        $i = 1;
    endforeach;
    $form.= '</div>';
    $form.= $this->Form->submit(__('Enregistrer'),array('class' => 'btn blue'));
    $form.= '</div></div>';
    $form.= '<div class="span3 block_edit_admin panel-contenu">';

    $form.= '<div class="row-fluid">';
    $form.= $this->Form->inputs(array(
        'date_publication'  => array('label' => array('text' => __('Date de publication'), 'class' => 'required'), 'required' => true, 'type' => 'text', 'class' => 'span8', 'between' => false, 'after' => false, 'maxlength' => 10, 'placeholder' => 'JJ-MM-AAAA', 'value' => (isset($this->request->data['Horoscope']['date_publication']) ?$this->request->data['Horoscope']['date_publication']:$this->Time->format('now', '%d-%m-%Y'))),
        'sign_id'           => array('label' => array('text' => __('Signe'), 'class' => 'required'), 'required' => true, 'options' => $sign_options, 'class' => 'span8', 'between' => false, 'after' => false)
    ));
    $form.= '</div></div>';
    $form.= $this->Form->end();

    //Listing des pages---------------------------------------------------------------------------------------------------------------------------------------

    $html = '<div class="row-fluid">';
    $html.= '<div class="portlet box blue">';
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les horoscopes'). '</div>';
    $html.= '</div><div class="portlet-body">';

    if(empty($horoscopes)) :
        $html.= __('Pas d\'horoscope');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>#</th>';
        $html.= '<th>'.$this->Paginator->sort('HoroscopeLang.sign_id', __('Signe du zodiaque')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('Horoscope.date_publication', __('Date de publication')).'</th>';
        $html.= '<th></th>';
        $html.= '</tr></thead><tbody>';

        foreach($horoscopes as $horoscope) :
            $html.= '<tr>';
            $html.= '<td>'. $horoscope['id'] .'</td>';
            $html.= '<td>'. $horoscope['name'] .'</td>';
            $html.= '<td>'. $this->Time->format($horoscope['date_publication'],'%d %B %Y') .'</td>';
            $html.= '<td>';
            $html.= $this->Html->link('<span class="icon-zoom-in"></span> '.__('Voir la page'),
                    array('action' => 'display', 'language' => $this->Session->read('Config.language'), 'id' => $horoscope['id_sign'], 'horoscope' => $horoscope['id'], 'admin' => false),
                    array('escape' => false, 'class' => 'btn green', 'target' => '_blank')
                ).' '.
                $this->Metronic->getLinkButton(
                    __('Modifier'),
                    array('controller' => 'horoscopes','action' => 'edit', 'admin' => true, 'id' => $horoscope['id'], 'sign' => $horoscope['id_sign']),
                    'btn blue',
                    'icon-edit');
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
        0 => array('text'       => __('Horoscopes'),
                   'icon'       => 'icon-list',
                   'content'    => $html
        ),
        1 => array('text'       => __('Nouvel horoscope'),
                   'icon'       => 'icon-plus',
                   'content'    => $form
        )
    );

    echo $this->Metronic->getTabs($tabs, ($this->params['action'] == 'admin_create' || isset($this->request->data['Horoscope']))?1:0);
                            
                            

