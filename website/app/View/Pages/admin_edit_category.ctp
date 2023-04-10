<?php
    echo $this->Metronic->titlePage(__('Pages'),__('Edition de la catégorie').' '.$namePageCat);
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Catégorie CMS'), 'classes' => 'icon-sitemap', 'link' => $this->Html->url(array('controller' => 'pages', 'action' => 'list_category', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php
        echo $this->Form->create('PageCategory', array('nobootstrap' => 1,'class' => 'form', 'default' => 1,
                                                       'inputDefaults' => array(
                                                           'div' => 'control-group',
                                                           'between' => '<div class="controls">',
                                                           'class'  => 'span10',
                                                           'after' => '</div>'
                                                       )));
    ?>
    <div class="span9">
        <div class="tabbable tabbable-custom tabbable-full-width">
            <ul class="nav nav-tabs">
                <?php $i=0; foreach($langs as $id => $val):
                    echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
                    $i = 1;
                endforeach; ?>
            </ul>
            <div class="tab-content">
                <?php $i=0; foreach($langs as $id => $val): ?>
                    <div id="tab<?php echo $id; ?>" class="tab-pane<?php echo ($i==0 ?' active':''); ?>">
                        <?php echo $this->Metronic->formPageCatLang($idPageCat,$id,(isset($langDatas[$id])?$langDatas[$id]:array())); ?>
                    </div>
                    <?php $i = 1; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="span2 block_edit_admin panel-contenu">
        <?php
            echo $this->Metronic->inputActive('PageCategory', ((isset($this->request->data['PageCategory']['active']) && $this->request->data['PageCategory']['active'] == 1) || $activePageCat == 1 ?1:0));

            echo $this->Metronic->inputFooter('PageCategory', ((isset($this->request->data['PageCategory']['footer']) && $this->request->data['PageCategory']['footer'] == 1) || $footerPageCat == 1 ?1:0));
		
			/*echo $this->Metronic->inputs(array(
				'PageCategory.id_parent2'  => array('label' => array('text' => __('ID Catégorie parente'), 'class' => 'control-label'), 'value' => $id_parentPageCat)
			));*/
			 echo $this->Form->inputs(array(
                'id_parent'                => array('label' => array('text' => __('ID Catégorie parente'), 'class' => 'control-label'), 'required' => false, 'value' => $id_parentPageCat),
            ));
		/*
		 echo $this->Form->inputs(array(
                'page_category_id'                => array('label' => array('text' => __('Catégorie'), 'class' => 'control-label required'), 'required' => true, 'options' => $cat_options, 'selected' => $catPage),
            ));
		*/
        ?>
    </div>
</div>
<div class="row-fluid">
    <?php
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue save_page_cat',
            'div' => array('class' => 'controls')
        ));
    ?>
</div>