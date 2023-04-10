<?php
echo $this->Metronic->titlePage(__('Blocs pub'),__('colonne gauche'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Blocs pub'), 'classes' => 'icon-picture', 'link' => false
    )
));



$html = '<div class="row-fluid">';
$html.= '<div class="portlet box blue">';
$html.= '<div class="portlet-title"><div class="caption">'. __('Les visuels'). '</div>';
$html.= '</div>';
$html.= '<div class="portlet-body">';



$html.= '</div>';
$html.= '</div>';


echo $html;