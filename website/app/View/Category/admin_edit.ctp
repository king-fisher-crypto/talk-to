<?php
echo $this->Metronic->titlePage(__('Catégories'),__('Edition de').' '.$nameCat);
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Catégorie'), 'classes' => 'icon-sitemap', 'link' => $this->Html->url(array('controller' => 'category', 'action' => 'list', 'admin' => true))
    )
));
echo $this->Session->flash();

//On crée les onglets pour chaque langue
$tabs = array();
foreach($langs as $id => $val){
    array_push($tabs, array(
        'text'      => __($val[array_keys($val)[0]]),
        'icon'      => 'lang_flags lang_'.array_keys($val)[0],
        'content'   => $this->Metronic->formCategoryLang($idCat,$id,(isset($langDatas[$id])?$langDatas[$id]:array()),$activeCat)
    ));
}

echo $this->Metronic->getTabs($tabs);
                            
                            

