<?php

if ((isset($isHiddenSystemPage) && $isHiddenSystemPage))
{
    echo $this->Metronic->titlePage(__('Template page inscription'),__('Edition'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Template e-mail'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true))
        )
    ));
}else{
    echo $this->Metronic->titlePage(__('Subscribes'),__('Edition de').' '.$namePage);
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Page inscription'), 'classes' => 'icon-pencil', 'link' => $this->Html->url(array('controller' => 'subscribes', 'action' => 'list', 'admin' => true))
        )
    ));
}


echo $this->Session->flash();
?>

<div class="row-fluid">
       <?php echo $this->Form->create('Subscribe', array('nobootstrap' => 1,'class' => 'form', 'default' => 1, 'enctype' => 'multipart/form-data',
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
							
							echo $this->Metronic->formSubscribeLang($idPage,$id,(isset($langDatas[$id])?$langDatas[$id]:array())); ?>
                        </div>
                        <?php $i = 1; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row-fluid">
        <div class="span12 block_edit_admin panel-contenu">
            <?php
            echo $this->Metronic->inputActive('Subscribe', ((isset($this->request->data['Subscribe']['active']) && $this->request->data['Subscribe']['active'] == 1) || $activePage == 1 ?1:0));

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