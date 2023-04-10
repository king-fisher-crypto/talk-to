<?php
    echo $this->Metronic->titlePage(__('Horoscopes'),__('Les signes'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Signes'), 'classes' => 'icon-asterisk', 'link' => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'signs', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    $html = '<div class="row-fluid">';
    $html.= '<div class="portlet box blue">';
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les signes'). '</div>';
    $html.= '</div><div class="portlet-body">';

    if(empty($signes)) :
        $html.= __('Pas de signes');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>#</th>';
        $html.= '<th>'.$this->Paginator->sort('HoroscopeSign.name', __('Signe du zodiaque')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('HoroscopeSign.info_dates', __('Date')).'</th>';
        $html.= '<th></th>';
        $html.= '</tr></thead><tbody>';

        foreach($signes as $signe) :
            $html.= '<tr>';
            $html.= '<td>'. $signe['HoroscopeSign']['sign_id'] .'</td>';
            $html.= '<td>'. $signe['HoroscopeSign']['name'] .'</td>';
            $html.= '<td>'. $signe['HoroscopeSign']['info_dates'] .'</td>';
            $html.= '<td>';
            $html.= 
                $this->Metronic->getLinkButton(
                    __('Modifier'),
                    array('controller' => 'horoscopes','action' => 'signs_edit', 'admin' => true, 'id' => $signe['HoroscopeSign']['sign_id']),
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
echo $html;