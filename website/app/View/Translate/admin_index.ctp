<?php
echo $this->Metronic->titlePage(__('Traductions'),__('Les traductions'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Traductions'),
        'classes' => 'icon-globe',
        'link' => $this->Html->url(array('controller' => 'slides', 'action' => 'list', 'admin' => true))
    )
));
echo $this->Session->flash();

?>
<div class="row-fluid">
    <div class="span3">
        <div style="padding:20px; background-color:#EEE">
        <p>
            <strong>Explication :</strong><br/>
            Cette plate-forme utilise la norme d'internationalisation I18N pour traduire tous les textes dits "d'interface".<br/>
            <br/>Ceci signifie qu'un fichier .pot (inventaire de tous les mots du site) est généré par ce module qui vous permet de le télécharger.<br/><br/>
            Ensuite, par le biais du logiciel <a style="font-weight:bold" href="https://poedit.net/download" target="_blank">PoEdit</a> (par exemple), vous utilisez ce fichier pour créer les fichiers PO (un fichier par langue).<br/><br/>
            Ensuite il convient de charger le fichier .po (créé avec PoEdit) de la langue sur laquelle vous venez de travailler pour que le site prenne en compte vos nouvelles traductions.<br/><br/>
            Ce module va donc vous permettre :
            <br/>1: de télécharger le fichier dictionnaire de tous les textes du site
            <br/>2: d'importer (uploader) sur le site votre fichier .po de traduction par langue (1 fichier = 1 langue)
            <br/><br/>
            <strong>A noter :</strong><br/>
            Si de nouveaux textes sont ajoutés dans le site, mais ne sont pas disponibles dans PoEdit,
            il vous faut re-télécharger le fichier .POT (grâce à ce module), et mettre à jour vos fichiers .po pour qu'ils récupèrent les nouveaux textes.<br/>
            (Pour cela ouvrez le .po de la langue Anglaise par exemple et dans le menu CATALOGUE cliquez sur "Mettre à jour depuis un fichier POT..."), traduisez vos nouveaux textes, enregistrez le fichier et importez-le ici.
        </p>
        </div>
    </div>
    <div class="span9">
        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption">Téléchargement du fichier "dictionnaire" .POT à jour</div>
            </div>
            <div class="portlet-body">
                <?php

                echo $this->Html->link('Télécharger le fichier POT du site', array(
                    'controller' => 'translate',
                    'action'     => 'get_pot',
                    'admin'      => true
                ), array(
                    'class' => 'btn'
                ));

                ?>
                &nbsp; Dernier rafraichissement du fichier : <?php echo $date_pot; ?>
            </div>
        </div>

        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption">Import des fichiers langue .PO</div>
            </div>
            <div class="portlet-body">
                <p>Attention ! Le choix de la langue dans la liste déroulante est important. Il indique au module dans quel dossier traduction serveurs il doit ranger votre fichier importé.<br/>
                Le nom de votre fichier n'a donc pas d'importance, seul compte le choix dans la liste déroulante.</p>
                <?php

                $form = $this->Form->create(false, array('nobootstrap' => 1,'class' => 'form-horizontal span6 panel-contenu', 'default' => 1, 'enctype' => 'multipart/form-data',
                    'url' => array(
                        'controller' => 'translate',
                        'action'     => 'upload',
                        'admin'      => true
                    ),
                    'inputDefaults' => array(
                        'div' => 'control-group',
                        'between' => '<div class="controls">',
                        'class'   => 'span10',
                        'after' => '</div>'
                    )));

                $form.= $this->Form->inputs(array(
                    'legend' => false,
                    'langue' => array(
                        'type' => 'select',
                        'label' => array('text' => 'Langue à importer ','class' => 'control-label required'),
                        'options' => array_merge(array('' => '-- choisir --'),$lang_options),
                        'required' => true,
                    ),
                    'pofile'     => array(
                        'label' => array('text' => __('Fichier .po correspondant'),'class' => 'control-label required'),
                        'required' => true,
                        'type' => 'file',
                        'accept' => 'text/x-po',
                        'after' => '</div>'
                    )
                ));

                $form.= $this->Form->end(array(
                    'label' => __('Importer'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));

                echo $form;

                ?>
                <div style="clear:both"></div>
            </div>
        </div>


        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption">Télécharger les fichiers .po existants</div>
            </div>
            <div class="portlet-body">
                <table class="table">
                    <?php foreach ($po_files_exists AS $iso => $file): ?>
                        <tr>
                            <td><?php echo $file['langue'].' ('.$iso.')'; ?></td>
                            <td><?php echo $file['exists']?'<span class="badge badge-success">Existant</span>':'<span class="badge badge-error">Inexistant</span>'; ?></td>
                            <td><?php echo $file['filemtime']?$file['filemtime']:'Inexistant'; ?></td>
                            <td><?php


                                echo $file['exists']?$this->Html->link('Télécharger', array(
                                    'controller' => 'translate',
                                    'action'     => 'get_po',
                                    'admin'      => true,
                                    '?' => array('iso' => $iso)
                                ), array(
                                    'class' => 'btn'
                                )):'';

                                ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>
    </div>
</div>