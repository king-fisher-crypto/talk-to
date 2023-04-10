<?php
// View initialization
// ---------------------------------------------------------------------

echo $this->Metronic->titlePage(__('Jeux de cartes'), $card ? __('Édition') : __('Création'));

echo $this->Metronic->breadCrumb([
    [
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(['controller' => 'admins', 'action' => 'index', 'admin' => true])
    ], [
        'text' => __('Jeux de cartes'),
        'classes' => 'icon-list',
        'link' => $this->Html->url(['controller' => 'cards', 'action' => 'list', 'admin' => true])
    ], [
        'text' => $card ? __('Édition') : __('Création'),
        'classes' => 'icon-pencil',
        'link' => ''
    ]
]);

echo $this->Session->flash();

// Form initialization
// ---------------------------------------------------------------------

echo $this->Form->create('Card', [
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
$model = 'Card.';
ob_start();

echo '<div class="row-fluid"><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'game_type' => [
        'label'     => array('text' => __('Type'), 'class' => 'control-label required'),
        'type'      => 'select',
        'options'   => array_flip($card_game_types),
        'required'  => true,
        'div'       => 'control-group span6',
    ],
]);
echo '<div class="control-group span6"><label for="'.trim($model, '.').'Active" class="control-label" style="margin: 0;padding: 0;">'.__('Actif').'</label><div class="controls">';
echo $this->Form->inputs([
    $model . 'active' => [
        'label'     => false,
        'type'      => 'checkbox',
        'div'       => false,
        'between'   => false,
        'after'     => false
    ],
]);
echo '</div></div>';

echo '</div></div><div class="row-fluid"><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'display_mode' => [
        'label'     => array('text' => __('Mode d\'affichage'), 'class' => 'control-label required'),
        'type'      => 'select',
        'options'   => array_flip($card_display_modes),
        'required'  => true,
        'div'       => 'control-group span6',
    ],
    $model . 'count_to_pick' => [
        'label'     => array('text' => __('Nombre de cartes'), 'class' => 'control-label required'),
        'type'      => 'number',
        'min'       => 1,
        'max'       => 99,
        'maxlength' => 2,
        'required'  => true,
        'div'     => 'controls span6',
    ],
]);
echo '</div></div><div class="row-fluid"><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'embed_image' => [
        'label' => array('text' => __('Image vignette'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
        'div'     => 'controls span6',
    ],
    $model . 'embed_image_text_color' => [
        'label' => array('text' => __('Couleur pour le text sur l\'image vignette'), 'class' => 'control-label required'),
        'placeholder' => '#ffffff',
        'maxlength' => 24,
        'required'  => false,
        'div'     => 'controls span6',
    ],
]);
echo '</div></div>';

echo '<div class="row-fluid"><h3 class="span12">' . __('Design des cartes') . '</h3><div><div class="span6"><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'item_bg_image' => [
        'label' => array('text' => __('Image pour le dos de la carte'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ],
    $model . 'item_disabled_bg_image' => [
        'label' => array('text' => __('Image pour l\'espace réservé des cartes (lors de la sélection)'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ],
    $model . 'item_bg_color' => [
        'label' => array('text' => __('Couleur pour le dos de la carte'), 'class' => 'control-label required'),
        'placeholder' => '#ffffff',
        'maxlength' => 24,
        'required'  => true,
    ],
]);
echo '</div></div><div class="span6"><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'item_css' => [
        'label' => array('text' => __('CSS additionnel pour la carte'), 'class' => 'control-label'),
        'type' => 'textarea',
        'rows' => 4,
    ],
]);
echo '</div></div></div></div>';

echo '<div class="row-fluid"><div class="span4 well"><h3 class="span12">' . __('Étape: sélection de cartes') . '</h3><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'step_choose_bg_image' => [
        'label'     => array('text' => __('Image d\'arrière plan'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ],
    $model . 'step_choose_mobile_bg_image' => [
        'label'     => array('text' => __('Image d\'arrière plan (mobile)'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ],
    $model . 'step_choose_bg_color' => [
        'label'     => array('text' => __('Couleur d\'arrière plan'), 'class' => 'control-label required'),
        'placeholder' => '#ffffff',
        'maxlength' => 24,
        'required'  => true,
    ],
]);
echo '</div></div><div class="span4 well"><h3 class="span12">' . __('Étape: interprétation') . '</h3><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . ' 	step_interpretation_bg_image' => array(
        'label'     => array('text' => __('Image d\'arrière plan'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ),
    $model . ' 	step_interpretation_mobile_bg_image' => array(
        'label'     => array('text' => __('Image d\'arrière plan (mobile)'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ),
    $model . 'step_interpretation_bg_color' => array(
        'label'     => array('text' => __('Couleur d\'arrière plan'), 'class' => 'control-label required'),
        'placeholder' => '#ffffff',
        'maxlength' => 24,
        'required'  => true,
    ),
]);
echo '</div></div><div class="span4 well"><h3 class="span12">' . __('Étape: résultat') . '</h3><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'step_result_bg_image' => array(
        'label'     => array('text' => __('Image d\'arrière plan'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ),
    $model . 'step_result_mobile_bg_image' => array(
        'label'     => array('text' => __('Image d\'arrière plan (mobile)'), 'class' => 'control-label'),
        'type'  => 'file',
        'required'  => false,
    ),
    $model . 'step_result_bg_color' => array(
        'label'     => array('text' => __('Couleur d\'arrière plan'), 'class' => 'control-label required'),
        'placeholder' => '#ffffff',
        'maxlength' => 24,
        'required'  => true,
    ),
]);
echo '</div></div></div>';

echo '<div class="row-fluid"><div class="form-horizontal">';
echo $this->Form->inputs([
    $model . 'main_css' => [
        'label' => array('text' => __('CSS additionnel pour ce jeu'), 'class' => 'control-label'),
        'type' => 'textarea',
        'rows' => 4,
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
    $model = 'CardLang.i' . $lang_id . '.';
    ob_start();

    echo '<div class="row-fluid"><div class="span6"><div class="form-horizontal">';

    echo $this->Form->inputs([
        $model . 'title' => array(
            'label'     => array('text' => __('Titre'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required'  => $req,
        ),
        $model . 'url_path' => array(
            'label' => array('text' => __('Lien'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required'  => $req,
        ),
    ]);

    echo $this->Form->inputs([
        $model . 'meta_title' => array(
            'label' => array('text' => __('Méta titre'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required' => $req,
            'type'  => 'text',
            'after' => '<p class="label label-info" style="white-space:normal">'. __('Nombre de caractères recommandé : ') . Configure::read('Site.lengthMetaTitle').'</p></div>'
        ),
        $model . 'meta_keywords' => array(
            'label' => array('text' => __('Méta mots-clés'), 'class' => 'control-label'),
            'after' => '<p class="label label-info" style="white-space:normal">' . __('Nombre de caractères recommandé : ') . Configure::read('Site.lengthMetaKeywords') . '</p></div>'
        ),
        $model . 'meta_description' => array(
            'label' => array('text' => __('Méta description'), 'class' => 'control-label'),
            'after' => '<p class="label label-info" style="white-space:normal">' . __('Nombre de caractères recommandé : ') . Configure::read('Site.lengthMetaDescription') . '</p></div>'
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

    echo '<div class="row-fluid"><div class="span4 well"><h3 class="span12">' . __('Étape: sélection de cartes') . '</h3><div class="form-horizontal">';
    echo $this->Form->inputs([
        $model . 'step_choose_title' => array(
            'label'     => array('text' => __('Titre'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required'  => $req
        ),
        $model . 'step_choose_description' => array(
            'label'     => array('text' => __('Description'), 'class' => 'control-label' . ($reqD ? ' required' : '')),
            'required'  => $reqD
        ),
        $model . 'step_choose_lines' => array(
            'label'     => array('text' => __('Textes à afficher lors des choix de cartes (un par ligne)'), 'class' => 'control-label' . ($reqD ? ' required' : '')),
            'required'  => $reqD
        ),
    ]);

    echo '</div></div><div class="span4 well"><h3 class="span12">' . __('Étape: interprétation') . '</h3><div class="form-horizontal">';
    echo $this->Form->inputs([
        $model . 'step_interpretation_title' => array(
            'label'     => array('text' => __('Titre'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required'  => $req
        ),
        $model . 'step_interpretation_description' => array(
            'label'     => array('text' => __('Description'), 'class' => 'control-label' . ($reqD ? ' required' : '')),
            'required'  => $reqD
        ),
    ]);

    echo '</div></div><div class="span4 well"><h3 class="span12">' . __('Étape: résultat') . '</h3><div class="form-horizontal">';
    echo $this->Form->inputs([
        $model . 'step_result_title' => array(
            'label'     => array('text' => __('Titre'), 'class' => 'control-label' . ($req ? ' required' : '')),
            'required'  => $req
        ),
        $model . 'step_result_description' => array(
            'label'     => array('text' => __('Description'), 'class' => 'control-label' . ($reqD ? ' required' : '')),
            'required'  => $reqD
        ),
    ]);

	echo '</div></div><div class="span4 well"><h3 class="span12">' . __('Étape: résultat - et maintenant') . '</h3><div class="form-horizontal">';
    echo $this->Form->inputs([
        $model . 'step_result_next' => array(
            'label'     => array('text' => __('Description'), 'class' => 'control-label' . ($reqD ? ' required' : '')),
            'required'  => $reqD,
			'type' 		=> 'textarea'
        ),
        $model . 'step_result_embed' => array(
            'label'     => array('text' => __('Vignette'), 'class' => 'control-label' . ($reqD ? ' required' : '')),
            'required'  => $reqD,
			'type' 		=> 'textarea'
        ),
    ]);


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



