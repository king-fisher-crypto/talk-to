<?php

if ((isset($isHiddenSystemPage) && $isHiddenSystemPage))
{
    echo $this->Metronic->titlePage(__('Template e-mail'),__('Edition'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Template e-mail'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'pages', 'action' => 'list', 'admin' => true))
        )
    ));
}else{
    echo $this->Metronic->titlePage(__('Pages'),__('Edition de').' '.$namePage);
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Page'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'pages', 'action' => 'list', 'admin' => true))
        )
    ));
}


echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Form->create('Page', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class'    => 'span10',
            'after' => '</div>'
        ))); ?>
    <div class="row-fluid">
        <div class="span12">
            <div class="tabbable tabbable-custom tabbable-full-width">
                <ul class="nav nav-tabs">
                    <?php $i=0; foreach($langs as $id => $val):
                        echo '<li'.($i==0 ?' class="active"':'').'><a data-toggle="tab" href="#tab'.$id.'"><span class="margin-right lang_flags lang_'.array_keys($val)[0].'"></span>'.__($val[array_keys($val)[0]]).'</a></li>';
                        $i = 1;
                    endforeach; ?>
                </ul>
                <div class="tab-content">
					<p>Code lire la suite : <prev>< !--PAGEBREAK-- ></prev>
					<prev>< !--PAGEBREAKMOBILE-- ></prev> ( merci de supprimer l'espace au debut entre < et ! pour utiliser ce code )
					</p>
                    <?php $i=0; foreach($langs as $id => $val): ?>
                        <div id="tab<?php echo $id; ?>" class="tab-pane<?php echo ($i==0 ?' active':''); ?>">
                            <?php echo $this->Metronic->formPageLang($idPage,$id,(isset($langDatas[$id])?$langDatas[$id]:array()), $isHiddenSystemPage, $page_parameters); ?>
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
            echo $this->Metronic->inputActive('Page', ((isset($this->request->data['Page']['active']) && $this->request->data['Page']['active'] == 1) || $activePage == 1 ?1:0));

            echo $this->Form->inputs(array(
                'page_category_id'                => array('label' => array('text' => __('Catégorie'), 'class' => 'control-label required'), 'required' => true, 'options' => $cat_options, 'selected' => $catPage),
            ));
            ?>


            <?php
            if(isset($langDatas[$this->Session->read('Config.id_lang')]) && $activePage == 1)
               /* echo $this->Html->link('<span class="icon-zoom-in"></span> '.__('Voir la page'),
                    array(
                        'language'          => $this->Session->read('Config.language'),
                        'controller'        => 'pages',
                        'action'            => 'display',
                        'admin'             => false,
                        'id'                => $idPage,
                        'link_rewrite'      => $langDatas[$this->Session->read('Config.id_lang')]['link_rewrite']
                    ),
                    array('escape' => false, 'class' => 'btn green', 'target' => '_blank')
                );*/
				echo '<a href="'.Configure::read('Site.baseUrlFull').$linkpage.'" class="btn green" target="_blank"><span class="icon-zoom-in"></span> Voir la page</a>';
            ?>
        </div>
    </div>
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
<br /><br />
<div class="row-fluid"<?php if (!$isHiddenSystemPage): ?> style="display:none"<?php endif; ?>>
	<div class="span12 panel-contenu">
         <?php echo $this->Form->create('Page', array('nobootstrap' => 1,'class' => 'form', 'default' => 1,
        'inputDefaults' => array(
            'div' => 'control-group',
            'between' => '<div class="controls">',
            'class'    => 'span10',
            'after' => '</div>'
        ))); ?>
          <div class="control-group">
          		<div class="controls">
          		<input value="<?php echo $idPage; ?>" id="AdminEmailIdPage" name="AdminEmailIdPage" class="span2" type="hidden" /> 
           <input value="" placeholder="Saisir email test" id="AdminEmailTest" name="AdminEmailTest" class="span2" type="text" /> 
			  </div>
		</div>
		 <?php
    echo $this->Form->end(array(
        'label' => __('Envoyer'),
        'class' => 'btn green send_mail align-left',
        'div' => array('class' => 'controls')
    ));
    ?>
	</div>
</div>                            
                            

