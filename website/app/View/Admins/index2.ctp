<?php 

echo $this->Metronic->titlePage('Backoffice','Accueil');
echo $this->Metronic->breadCrumb(array(0 => array('text' => 'Accueil', 'classes' => 'icon-home')));

echo $this->Metronic->getTabs(
                        array(
                            0 => array('text'       => 'Onglet 1',
                                       'content'    => 'Contenu 1'),
                                       
                            1 => array('text'       => 'Onglet 2',
                                       'content'    => 'COntenu 2')
                        ));
                            
