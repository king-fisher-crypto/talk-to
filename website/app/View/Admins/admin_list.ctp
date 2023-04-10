<?php
echo $this->Metronic->titlePage(__('Administrateurs'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Administrateurs'), 'classes' => 'icon-pencil', 'link' => false
    )
));





$html = '<div class="row-fluid">';
$html.= '<div class="portlet box blue">';
$html.= '<div class="portlet-title"><div class="caption">'. __('Les admins'). '</div>';
$html.= '</div>';
$html.= '<div class="portlet-body">';

if(empty($users)) :
    $html.= __('Pas d\'admin');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>#</th>';
    $html.= '<th>Nom</th>';
    $html.= '<th>Prénom</th>';
    $html.= '<th>E-mail</th>';
    $html.= '<th>Créé le</th>';
    $html.= '<th>Dernière connexion</th>';
    $html.= '<th></th>';

    $html.= '</tr></thead><tbody>';

    foreach($users as $user) :
        $html.= '<tr>';
        $html.= '<td>'. $user['User']['id'] .'</td>';
        $html.= '<td>'. $user['User']['firstname'] .'</td>';
        $html.= '<td>'. $user['User']['lastname'] .'</td>';
        $html.= '<td>'. $user['User']['email'] .'</td>';
        $html.= '<td>'. date("d/m/Y H:i:s",strtotime($user['User']['date_add'])) .'</td>';
        $html.= '<td>'. date("d/m/Y H:i:s",strtotime($user['User']['date_lastconnexion'])) .'</td>';
        $html.= '<td>';

        if ($user['User']['active'] != 1){
            $html.= $this->Metronic->getLinkButton(
                __('Activer'),
                array('controller' => 'admins','action' => 'admin_enable', 'admin' => true, 'id' => $user['User']['id']),
                'btn green',
                'icon-plus',
                __('Voulez-vous vraiment activer le compte admin de  ').$user['User']['firstname'].' '.$user['User']['lastname'].' ?');
        }else{
            $html.= $this->Metronic->getLinkButton(
                __('Désactiver'),
                array('controller' => 'admins','action' => 'admin_disable', 'admin' => true, 'id' => $user['User']['id']),
                'btn red',
                'icon-plus',
                __('Voulez-vous vraiment désactiver le compte admin de  ').$user['User']['firstname'].' '.$user['User']['lastname'].' ?');
        }
        $html.= '</td>';
        $html.= '<td>';
        $html.= $this->Metronic->getLinkButton(
            __('Supprimer'),
            array('controller' => 'admins','action' => 'admin_delete', 'admin' => true, 'id' => $user['User']['id']),
            'btn black',
            'icon-remove',
            __('Voulez-vous vraiment supprimer le compte admin de  ').$user['User']['firstname'].' '.$user['User']['lastname'].' (irréversible) ?');

        $html.= '</td>';


    endforeach;
endif;

$html.= '</div></div></div>';

echo $html;
echo $this->Session->flash();