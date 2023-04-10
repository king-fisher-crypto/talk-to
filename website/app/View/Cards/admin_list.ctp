<?php
echo $this->Metronic->titlePage(__('Jeux de cartes'),__('Liste'));
echo $this->Metronic->breadCrumb(array(
    array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    array(
        'text' => __('Jeux de cartes'), 'classes' => 'icon-list', 'link' => $this->Html->url(array('controller' => 'cards', 'action' => 'list', 'admin' => true))
    )
));

echo $this->Session->flash();

// Listing des pages
// ---------------------------------------------------------------------


echo '<div class="row-fluid" style="margin-bottom: 0.5rem"><div class="text-right">';
echo $this->Metronic->getLinkButton(__('Ajouter un jeu'), array(
    'controller' => 'cards', 'action' => 'create', 'admin' => true
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

if (empty($cards)) {
    echo __('Pas de jeux de cartes trouvés');
} else {
    echo '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    echo '<th>'.$this->Paginator->sort('Card.card_id', '#').'</th>';
    echo '<th>'.$this->Paginator->sort('CardLang.title', __('Titre')).'</th>';
    echo '<th>'.$this->Paginator->sort('Card.active', __('Statut')).'</th>';
    echo '<th>'.__('Langues').'</th>';
    echo '<th>'.__('Cartes').'</th>';
    echo '<th>'.__('Textes Resultats').'</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody>';

    foreach ($cards as $card) {
        echo '<tr>';
        echo '<td>' . $card['card_id'] .'</td>';
        echo '<td>' . $card['title'] .'</td>';
        echo '<td>' . ($card['active'] ? '<span class="badge badge-success">'.__('Actif').'</span>' : '<span class="badge badge-danger">' .__('Inactif') . '</span>') . '</td>';
        echo '<td>' . $card['langs'] .'</td>';
        echo '<td>';
            echo $this->Metronic->getLinkButton(__('%d cartes', array($card['card_items_count'])), array(
                'controller' => 'cards', 'action' => 'list_items', 'admin' => true, 'id' => $card['card_id']
            ), 'btn blue', 'icon-edit');
        echo '</td>';
        echo '<td>';
            echo $this->Metronic->getLinkButton(__('%d textes', array($card['card_results_count'])), array(
                'controller' => 'cards', 'action' => 'list_results', 'admin' => true, 'id' => $card['card_id']
            ), 'btn blue', 'icon-edit');
        echo '</td>';
        echo '<td>';
        echo '<div class="btn-group margin-left">';
        echo '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
        echo '<ul class="dropdown-menu pull-right">';

        if ($card['active']) {
            echo '<li>';
            echo $this->Html->link('<span class="icon-zoom-in"></span> '.__('Voir la page'),
                array('action' => 'display', 'language' => $card['language_code'], 'id' => $card['card_id'], 'admin' => false),
                array('escape' => false, 'class' => 'btn green', 'target' => '_blank')
            );
            echo '</li>';
        }

        echo '<li>';
        echo $this->Metronic->getLinkButton(__('Modifier'), array(
            'controller' => 'cards', 'action' => 'edit', 'admin' => true, 'id' => $card['card_id']
        ), 'btn blue', 'icon-edit');
        echo '</li>';

        echo '<li>';
        if ($card['active']) {
            echo $this->Metronic->getLinkButton(__('Désactiver'), array(
                'controller' => 'cards', 'action' => 'deactivate', 'admin' => true, 'id' => $card['card_id']
            ), 'btn yellow', 'icon-remove', __('Voulez-vous vraiment désactiver le jeu ') . $card['title'] . ' ?');
        } else {
            echo $this->Metronic->getLinkButton(__('Activer'), array(
                'controller' => 'cards', 'action' => 'activate', 'admin' => true, 'id' => $card['card_id']
            ), 'btn green', 'icon-plus', __('Voulez-vous vraiment activer le jeu ') . $card['title'] . ' ?');
        }
        echo '</li>';

        echo '<li>';
        echo $this->Metronic->getLinkButton(__('Supprimer'), array(
            'controller' => 'cards', 'action' => 'true_delete', 'admin' => true, 'id' => $card['card_id']),
            'btn red', 'icon-remove',  __('Voulez-vous vraiment supprimer définitivement le jeu ') . $card['title'] . ' ?'
        );
        echo '</li>';

        echo '</ul></div></td></tr>';
    }

    echo '</tbody></table>';
    if ($this->Paginator->param('pageCount') > 1) {
        echo $this->Metronic->pagination($this->Paginator);
    }
}

echo '</div></div></div>';
