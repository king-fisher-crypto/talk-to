<?php
echo $this->Metronic->titlePage(__('Classifications'),__('Liste'));
echo $this->Metronic->breadCrumb(array(
    array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    array(
        'text' => __('Classifications'), 'classes' => 'icon-list', 'link' => $this->Html->url(array('controller' => 'support', 'action' => 'classification', 'admin' => true))
    )
));

echo $this->Session->flash();

// Listing des pages
// ---------------------------------------------------------------------


echo '<div class="row-fluid" style="margin-bottom: 0.5rem"><div class="text-right">';
echo $this->Metronic->getLinkButton(__('Ajouter'), array(
    'controller' => 'support', 'action' => 'classification_create', 'admin' => true
), 'btn green', 'icon-plus');
echo '</div></div>';

echo '<div class="row-fluid">';
echo '<div class="portlet box blue">';
echo '<div class="portlet-title"><div class="caption">'. __('Classifications') . '</div>';
echo '<div class="pull-right">';
echo '<span class="label-search">'. __('Recherche') .'</span>';
echo $this->Form->create('Classification', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
echo $this->Form->input('title', array('class' => 'input-mini margin-left margin-right', 'type' => 'text', 'label' => __('Titre').' :', 'div' => false));
echo $this->Form->input('search', array('type' => 'hidden'));
echo '<input class="btn green" type="submit" value="Ok">';
echo $this->Form->end();
echo '</div></div>';
echo '<div class="portlet-body">';

if (empty($classifications)) {
    echo __('Pas de classification trouvé');
} else {
    echo '<table class="table table-striped table-hover table-bordered"><thead><tr>';
	echo '<th>'.$this->Paginator->sort('SupportClassificationParent.num', 'Parent').'</th>';
    echo '<th>'.$this->Paginator->sort('SupportClassification.num', '#').'</th>';
    echo '<th>'.$this->Paginator->sort('SupportClassification.name', __('Titre')).'</th>';
    echo '<th>'.$this->Paginator->sort('SupportClassification.description', __('Information')).'</th>';
	echo '<th>'.$this->Paginator->sort('SupportClassification.solution_link', __('Solution')).'</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody>';

    foreach ($classifications as $classification) {
        echo '<tr>';
		echo '<td>' . $classification['SupportClassificationParent']['num'].' '.$classification['SupportClassificationParent']['name']  .'</td>';
		echo '<td>' . $classification['SupportClassification']['num'] .'</td>';
        echo '<td>' . $classification['SupportClassification']['name'] .'</td>';
		echo '<td>' . $classification['SupportClassification']['description'] .'</td>';
		echo '<td>' . $classification['SupportClassification']['solution_link'] .'</td>';
        echo '<td>';
        echo '<div class="btn-group margin-left">';
        echo '<a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#">'. __('Actions'). '<span class="caret"></span></a>';
        echo '<ul class="dropdown-menu pull-right">';

        echo '<li>';
        echo $this->Metronic->getLinkButton(__('Modifier'), array(
            'controller' => 'support', 'action' => 'classification_edit', 'admin' => true, 'id' => $classification['SupportClassification']['id']
        ), 'btn blue', 'icon-edit');
        echo '</li>';


        echo '<li>';
        echo $this->Metronic->getLinkButton(__('Supprimer'), array(
            'controller' => 'support', 'action' => 'classification_delete', 'admin' => true, 'id' => $classification['SupportClassification']['id']),
            'btn red', 'icon-remove',  __('Voulez-vous vraiment supprimer définitivement cette classification ') . $classification['SupportClassification']['name'] . ' ?'
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



