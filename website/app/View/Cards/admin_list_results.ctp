<?php
$view_title = __('Liste des textes résultat pour %s', [$card['CardLang'][0]['title']]);
echo $this->Metronic->titlePage(__('Jeux de cartes'), $view_title);
echo $this->Metronic->breadCrumb(array(
    array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    array(
        'text' => __('Jeux de cartes'), 'classes' => 'icon-list', 'link' => $this->Html->url(array('controller' => 'cards', 'action' => 'list', 'admin' => true))
    ),
    array(
        'text' => $view_title, 'classes' => 'icon-list', 'link' => $this->Html->url(array('controller' => 'cards', 'action' => 'list_results', 'admin' => true, 'id' => $card['Card']['card_id']))
    )
));

echo $this->Session->flash();

// Listing des pages
// ---------------------------------------------------------------------


echo '<div class="row-fluid" style="margin-bottom: 0.5rem"><div class="text-right">';
echo $this->Metronic->getLinkButton(__('Ajouter un texte résultat'), array(
    'controller' => 'cards', 'action' => 'create_result', 'admin' => true, 'id' => $card['Card']['card_id']
), 'btn green', 'icon-plus');
echo '</div></div>';

echo '<div class="row-fluid">';
echo '<div class="portlet box blue">';
echo '<div class="portlet-title"><div class="caption">'. __('Les cartes') . '</div>';
echo '<div class="pull-right">';
echo '<span class="label-search">'. __('Recherche') .'</span>';
echo $this->Form->create('Search', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
echo $this->Form->input('title', array('class' => 'input-mini margin-left margin-right', 'type' => 'text', 'label' => __('Titre').' :', 'div' => false));
echo $this->Form->input('search', array('type' => 'hidden'));
echo '<input class="btn green" type="submit" value="Ok">';
echo $this->Form->end();
echo '</div></div>';
echo '<div class="portlet-body">';

if (empty($card_results)) {
    echo __('Pas de textes résultat trouvés');
} else {
    echo '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    echo '<th>'.$this->Paginator->sort('CardResult.card_result_id', '#').'</th>';
    echo '<th>'.$this->Paginator->sort('CardResultLang.title', __('Titre')).'</th>';
    echo '<th>'.__('Langues').'</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody>';

    foreach ($card_results as $card_result) {
        echo '<tr>';
        echo '<td>' . $card_result['card_result_id'] .'</td>';
        echo '<td>' . $card_result['title'] .'</td>';
        echo '<td>' . $card_result['langs'] .'</td>';
        echo '<td>';
        echo $this->Metronic->getLinkButton(__('Modifier'), array(
            'controller' => 'cards', 'action' => 'edit_result', 'admin' => true, 'id' => $card_result['card_result_id']
        ), 'btn blue', 'icon-edit');
        echo ' ';
        echo $this->Metronic->getLinkButton(__('Supprimer'), array(
            'controller' => 'cards', 'action' => 'true_delete_result', 'admin' => true, 'id' => $card_result['card_result_id']),
            'btn red', 'icon-remove',  __('Voulez-vous vraiment supprimer définitivement le texte résultat ') . $card_result['title'] . ' ?'
        );
        echo '</td></tr>';
    }

    echo '</tbody></table>';
    if ($this->Paginator->param('pageCount') > 1) {
        echo $this->Metronic->pagination($this->Paginator);
    }
}

echo '</div></div></div>';



