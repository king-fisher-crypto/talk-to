<?php
    echo $this->Metronic->titlePage(__('Horoscopes'),__('Les inscriptions'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Signes'), 'classes' => 'icon-asterisk', 'link' => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'signs_subscribe', 'admin' => true))
        )
    ));

    echo $this->Session->flash();

    $html = '<div class="row-fluid">';
    $html.= '<div class="portlet box blue">';
    $html.= '<div class="portlet-title"><div class="caption">'. __('Les inscriptions'). '</div><div class="pull-right">';
	  $html.= $this->Html->link('<span class="icon icon-download-alt"></span> Tout exporter',
                    array(
                        'controller' => 'horoscopes',
                        'action'     => 'exportcsv',
                        'admin'      => true
                    ),
                    array(
                        'style' => 'margin-left:20px',
                        'class' => 'btn',
                        'type' => 'button',
                        'label' => false,
                        'div' => false,
                        'escape' => false,
                        'onclick' => 'document.location.href = \'/admin/horosocpes/exportcsv\'; return true'
                    ));
	$html.=' </div>';
    $html.= '</div><div class="portlet-body">';

    if(empty($subs)) :
        $html.= __('Pas d\'inscrit');
    else :
        $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
        $html.= '<th>#</th>';
        $html.= '<th>'.$this->Paginator->sort('Sign.name', __('Signe du zodiaque')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('HoroscopeSubscribe.date_add', __('Date')).'</th>';
        $html.= '<th>'.$this->Paginator->sort('HoroscopeSubscribe.email', __('Email')).'</th>';
		$html.= '<th>'.$this->Paginator->sort('HoroscopeSubscribe.firstname', __('Prénom')).'</th>';
$html.= '<th>'.$this->Paginator->sort('User.date_add', __('Inscrit agents')).'</th>';
$html.= '<th>'.$this->Paginator->sort('User.date_add', __('Date inscription')).'</th>';
        $html.= '</tr></thead><tbody>';

        foreach($subs as $signe) :
            $html.= '<tr>';
			$html.= '<td>'. $signe['HoroscopeSubscribe']['id'] .'</td>';
            $html.= '<td>'. $signe['Sign']['name'] .'</td>';
            $html.= '<td>'. $signe['HoroscopeSubscribe']['date_add'] .'</td>';
            $html.= '<td>'. $signe['HoroscopeSubscribe']['email'] .'</td>';
            $html.= '<td>'. $signe['HoroscopeSubscribe']['firstname'] .'</td>';

			if($signe['User']['date_add']){
				$html.= '<td>Oui</td>';
				$html.= '<td>'. $signe['User']['date_add'] .'</td>';
			}else{
				$html.= '<td>Non</td>';
				$html.= '<td>&nbsp;</td>';
			}

            $html.= '</tr>';
        endforeach;

        $html.= '</tbody></table>';
        if($this->Paginator->param('pageCount') > 1) :
            $html.= $this->Metronic->pagination($this->Paginator);
        endif;
    endif;

    $html.= '</div></div></div>';
echo $html;