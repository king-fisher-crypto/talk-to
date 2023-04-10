<?php
// View initialization
// ---------------------------------------------------------------------
$view_title = $card_result ? __('Édition d\'un text résultat') : __('Création d\'un texte résultat');

echo $this->Metronic->titlePage(__('Jeux de cartes'), $view_title);

echo $this->Metronic->breadCrumb([
    [
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(['controller' => 'admins', 'action' => 'index', 'admin' => true])
    ], [
        'text' => __('Liste de cartes pour %s', [$card['CardLang'][0]['title']]),
        'classes' => 'icon-list',
        'link' => $this->Html->url(array('controller' => 'cards', 'action' => 'list_results', 'admin' => true, 'id' => $card['Card']['card_id']))
    ], [
        'text' => $view_title,
        'classes' => 'icon-pencil',
        'link' => ''
    ]
]);

echo $this->Session->flash();

// Form initialization
// ---------------------------------------------------------------------

echo $this->Form->create('CardResult', [
    'nobootstrap'   => 1,
    'class'         => 'panel-contenu',
    'default'       => 1,
    'enctype'       => 'multipart/form-data',
    'inputDefaults' => [
        'div'           => 'control-group',
        'between'       => '<div class="controls">',
        'class'         => 'span12',
        'after'         => '</div>'
    ]
]);

$tabs = [];

// General information tab
// ---------------------------------------------------------------------

if ($card['Card']['game_type'] == Card::GAME_TYPE_YES_NO) {
    $model = 'CardResult.';
    ob_start();

    echo '<div class="row-fluid"><div class="form-horizontal">';
    echo $this->Form->inputs([
        $model . 'type' => [
            'label'     => array('text' => __('Type'), 'class' => 'control-label required'),
            'type'      => 'select',
            'options'   => array_flip($card_result_types),
            'required'  => true,
            'div'       => 'control-group span6',
        ],
    ]);
    echo '</div></div>';

    echo '<div class="row-fluid"><div class="controls text-right">';
    echo '<button class="btn blue load-next-tab-btn" type="button" style="margin-left: 0.5rem">', __('Suivant'), '</button>';
    echo '</div></div>';

    $tabs[] = [
        'text'       => __('Informations générales'),
        'icon'       => 'icon-list',
        'content'    => ob_get_clean()
    ];
}


// Language specific tabs
// ---------------------------------------------------------------------
$req = true;
$reqD = false;
end($langs);
$last_lang_key = key($langs);
foreach ($langs as $lang_key => $lang) {
    $lang = !empty($lang['Lang']) ? $lang['Lang'] : $lang ;
    $lang_id = $lang['id_lang'];
    $lang_title = $lang['name'];
    $lang_flag_url = $this->Language->getFlagIconUrl($lang);
    $model = 'CardResultLang.i' . $lang_id . '.';
    ob_start();

    echo '<div class="row-fluid"><div class="span6"><div class="form-horizontal">';

    echo $this->Form->inputs([
        $model . 'title' => array(
            'label'     => array('text' => __('Titre'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required'  => $req,
        ),
    ]);

    echo '</div></div><div class="span6"><div class="form-horizontal">';

    echo '<div class="tinymce">';
    echo $this->Form->input($model . 'description', array(
        'type' => 'textarea',
        'label' => false,
        'tinymce' => true,
        'data-tinymce-height' => '296',
        'placeholder' => __('Description du jeu'),
        'div' => false,
        'between' => false,
        'after' => false
    ));
    echo '</div>';

    echo '</div></div></div>';

    echo '<div class="controls text-right">';
    echo '<button class="btn blue" type="submit">', __('Enregistrer'), '</button>';
    if ($lang_key !== $last_lang_key) {
        echo '<button class="btn blue load-next-tab-btn" type="button" style="margin-left: 0.5rem">', __('Suivant'), '</button>';
    }
    echo '</div>';

    $tabs[] = [
        'text'       => '<img src="' . $lang_flag_url . '" alt="' . __($lang_title) . '" /> ' . __($lang_title),
        'content'    => ob_get_clean()
    ];

    $req = false;
}


// Form finalization
// ---------------------------------------------------------------------
echo $this->Metronic->getTabs($tabs, 0);
echo $this->Form->end();



