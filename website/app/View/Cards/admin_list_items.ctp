<?php
$view_title = __('Liste de cartes pour %s', [$card['CardLang'][0]['title']]);
echo $this->Metronic->titlePage(__('Jeux de cartes'), $view_title);
echo $this->Metronic->breadCrumb(array(
    array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    array(
        'text' => __('Jeux de cartes'), 'classes' => 'icon-list', 'link' => $this->Html->url(array('controller' => 'cards', 'action' => 'list', 'admin' => true))
    ),
    array(
        'text' => $view_title, 'classes' => 'icon-list', 'link' => $this->Html->url(array('controller' => 'cards', 'action' => 'list_items', 'admin' => true, 'id' => $card['Card']['card_id']))
    )
));

echo $this->Session->flash();

// Listing des pages
// ---------------------------------------------------------------------


echo '<div class="row-fluid" style="margin-bottom: 0.5rem"><div class="text-right">';
echo $this->Metronic->getLinkButton(__('Ajouter Une Carte'), array(
    'controller' => 'cards', 'action' => 'create_item', 'admin' => true, 'id' => $card['Card']['card_id']
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

if (empty($card_items)) {
    echo __('Pas de cartes trouvées');
} else {
    echo '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    echo '<th>'.$this->Paginator->sort('CardItem.card_item_id', '#').'</th>';
    echo '<th>'.$this->Paginator->sort('CardItemLang.title', __('Titre')).'</th>';
    echo '<th>'.$this->Paginator->sort('CardItemLang.keywords', __('Mot(s) Clé(s)')).'</th>';
    echo '<th>'.__('Langues').'</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody>';

    foreach ($card_items as $card_item) {
        echo '<tr>';
        echo '<td>' . $card_item['card_item_id'] .'</td>';
        echo '<td>' . $card_item['title'] .'</td>';
        echo '<td>' . $card_item['keywords'] .'</td>';
        echo '<td>' . $card_item['langs'] .'</td>';
        echo '<td>';
        echo $this->Metronic->getLinkButton(__('Modifier'), array(
            'controller' => 'cards', 'action' => 'edit_item', 'admin' => true, 'id' => $card_item['card_item_id']
        ), 'btn blue', 'icon-edit');
        echo ' ';
        echo $this->Metronic->getLinkButton(__('Supprimer'), array(
            'controller' => 'cards', 'action' => 'true_delete_item', 'admin' => true, 'id' => $card_item['card_item_id']),
            'btn red', 'icon-remove',  __('Voulez-vous vraiment supprimer définitivement la carte ') . $card_item['title'] . ' ?'
        );
        echo '</td></tr>';
    }

    echo '</tbody></table>';
    if ($this->Paginator->param('pageCount') > 1) {
        echo $this->Metronic->pagination($this->Paginator);
    }
}

echo '</div></div></div>';

echo '<div class="row-fluid" style="margin-top: 0.5rem">';
echo $this->Form->create('File', [
    'url'        => $this->Html->url(array('controller' => 'cards', 'action' => 'admin_create_items_from_zip', 'admin' => true, 'id' => $card['Card']['card_id'])),
    'nobootstrap'   => 1,
    'default'       => 1,
    'enctype'       => 'multipart/form-data'
]);
echo $this->Form->inputs([
    'zip' => [
        'label' => array('text' => __('Ajouter un Zip avec plusieurs cartes'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ],
]);

echo '<div class="controls text-left">';
echo '<button class="btn blue" type="submit" style="margin-top: 0.5rem">', __('Envoyer'), '</button>';
echo '</div>';

echo $this->Form->end();
echo '</div>';



