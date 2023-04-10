<?php

if ((isset($isHiddenSystemPage) && $isHiddenSystemPage))
{
    echo $this->Metronic->titlePage(__('Template e-mail'),__('Edition'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Template e-mail'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'landings', 'action' => 'list', 'admin' => true))
        )
    ));
}else{
    echo $this->Metronic->titlePage(__('Landings'),__('Edition de').' '.$namePage);
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Landing'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'landings', 'action' => 'list', 'admin' => true))
        )
    ));
}


echo $this->Session->flash();
?>

<div class="row-fluid">
       <?php echo $this->Form->create('Landing', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
                                                 'inputDefaults' => array(
                                                     'div' => 'control-group',
                                                     'between' => '<div class="controls">',
                                                     'class' => 'span10',
                                                     'after' => '</div>'
                                                 ))); ?>
  
    <div class="row-fluid">
        <div class="span12">
            <div class="tabbable tabbable-custom tabbable-full-width">
                <ul class="nav nav-tabs">
                    <?php $i=0; foreach($langs as $id => $val):
						if($id==1){
                        echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
						}
                        $i = 1;
                    endforeach; ?>
                </ul>
                <div class="tab-content">
                    <?php $i=0; foreach($langs as $id => $val): ?>
                        <div id="tab<?php echo $id; ?>" class="tab-pane<?php echo ($i==0 ?' active':''); ?>">
                            <?php 
							
							echo $this->Metronic->formLandingLang($idPage,$id,(isset($langDatas[$id])?$langDatas[$id]:array()), $isHiddenSystemPage, $page_parameters); ?>
                        </div>
                        <?php $i = 1; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid"<?php if ($isHiddenSystemPage): ?> style="display:none"<?php endif; ?>>
        <div class="span12 block_edit_admin panel-contenu">
            <?php
            echo $this->Metronic->inputActive('Landing', ((isset($this->request->data['Landing']['active']) && $this->request->data['Landing']['active'] == 1) || $activePage == 1 ?1:0));

            echo $this->Form->inputs(array(
                'page_category_id'                => array('label' => array('text' => __('Catégorie'), 'class' => 'control-label required'), 'required' => true, 'options' => $cat_options, 'selected' => $catPage),
            ));
            ?>


            <?php
			
			$option_domain = array();
			foreach($domain_select as $id => $name):
				if($id == $domainPage)$name_cat = $name;
                   $option_domain[$id] = $name;
                                endforeach;
			 echo $this->Form->inputs(array(
                'domain'                => array('label' => array('text' => __('Domain'), 'class' => 'control-label required'), 'required' => true, 'options' => $option_domain, 'selected' => $domainPage),
            ));
			switch ($domainPage) {
						case 19:
							$lang_cat = 'fre';
							break;
						case 11:
							$lang_cat = 'frb';
							break;
						case 13:
							$lang_cat = 'frs';
							break;
						case 22:
							$lang_cat = 'frl';
							break;
						case 29:
							$lang_cat = 'frc';
							break;
					}
           /* if(isset($langDatas[$this->Session->read('Config.id_lang')]) && $activePage == 1)
                echo $this->Html->link('<span class="icon-zoom-in"></span> '.__('Voir la page'),
                    array(
                        'language'          => $this->Session->read('Config.language'),
                        'controller'        => 'landings',
                        'action'            => 'display',
                        'admin'             => false,
                        'id'                => $idPage,
                        'link_rewrite'      => $langDatas[$this->Session->read('Config.id_lang')]['link_rewrite']
                    ),
                    array('escape' => false, 'class' => 'btn green', 'target' => '_blank')
                );*/
			echo '<a target="_blank" class="btn green" href="https://'.$name_cat.'/'.$lang_cat.'/'.'voyant-medium'.'/'.$linkPage.'-'.$idPage.'" >'.'<span class="icon-zoom-in"></span> '.__('Voir la page').'</a>';
            ?>
        </div>
    </div>
  <!--  <div class="row-fluid">
        <div class="span12 block_duplicate_admin panel-contenu">
            <?php
            echo $this->Metronic->formDuplicateLangs('Landing', $this->Session->read('Config.id_lang'));
            ?>
        </div>
    </div>-->
</div>

<div class="row-fluid">
    <?php
    echo $this->Form->end(array(
        'label' => __('Enregistrer'),
        'class' => 'btn blue save_page',
        'div' => array('class' => 'controls')
    ));
    ?>
</div>