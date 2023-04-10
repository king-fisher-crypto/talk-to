<?php
    echo $this->Metronic->titlePage(__('Signe'),__('Modification'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Modification signe'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'horoscopes', 'action' => 'signs_edit', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Modification signe'); ?>
            </div>
        </div>
        <div class="portlet-body form">
            <?php
                echo $this->Form->create('Horoscopes', array('nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data','inputDefaults' => array(
                    'div' => 'control-group',
                    'between' => '<div class="controls">',
                    'after' => '</div>',
                    'class' => 'span8'
                )));
            ?>

            <div class="row-fluid">
                <div class="span4">
					<h3>Infos</h3>
                       <?php
                            //Les inputs du formulaire
                            $conf = array(

                                'name'              => array('label' => array('text' => __('Nom'), 'class' => 'control-label required'), 'required' => true, 'value'=> $signe['HoroscopeSign']['name']),
								'info_dates'              => array('label' => array('text' => __('Dates'), 'class' => 'control-label required'), 'required' => true, 'value'=> $signe['HoroscopeSign']['info_dates']),
                                
                            );

                            echo $this->Form->inputs($conf);
                    ?>
                </div>
                <div class="span4">
                    <h3>Termes</h3>
					<?php
                            //Les inputs du formulaire
                            $conf = array(

                                'def1'              => array('label' => array('text' => __('Terme 1'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def1']),
								'def1_color'              => array('label' => array('text' => __('Terme 1 couleur'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def1_color']),
								
								
                                'def1_img'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopeTerme.width').'x'.Configure::read('HoroscopeTerme.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['def1_img'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['def1_img'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
					
							 //Les inputs du formulaire
                            $conf = array(

                                'def2'              => array('label' => array('text' => __('Terme 2'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def2']),
								'def2_color'              => array('label' => array('text' => __('Terme 2 couleur'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def2_color']),
								
								
                                'def2_img'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopeTerme.width').'x'.Configure::read('HoroscopeTerme.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['def2_img'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['def2_img'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
					
							 //Les inputs du formulaire
                            $conf = array(

                                'def3'              => array('label' => array('text' => __('Terme 3'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def3']),
								'def3_color'              => array('label' => array('text' => __('Terme 3 couleur'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def3_color']),
								
								
                                'def3_img'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopeTerme.width').'x'.Configure::read('HoroscopeTerme.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['def3_img'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['def3_img'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
					
							 //Les inputs du formulaire
                            $conf = array(

                                'def4'              => array('label' => array('text' => __('Terme 4'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def4']),
								'def4_color'              => array('label' => array('text' => __('Terme 4 couleur'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['def4_color']),
								
								
                                'def4_img'       => array('label' => array('text' => __('Image (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopeTerme.width').'x'.Configure::read('HoroscopeTerme.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['def4_img'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['def4_img'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
                    ?>
                </div>
				<div class="span4">
                    <h3>Pubs</h3>
					<?php
                            //Les inputs du formulaire
                            $conf = array(

                              
								'pub_link'              => array('label' => array('text' => __('Pub lien'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['pub_link']),
                                'pub'       => array('label' => array('text' => __('Pub desktop (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopePubDesktop.width').'x'.Configure::read('HoroscopePubDesktop.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['pub'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['pub'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
					
							 //Les inputs du formulaire
                            $conf = array(
								'pub_mobile_link'              => array('label' => array('text' => __('Pub lien'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['pub_mobile_link']),
                                'pub_mobile'       => array('label' => array('text' => __('Pub mobile (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopePubMobile.width').'x'.Configure::read('HoroscopePubMobile.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['pub_mobile'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['pub_mobile'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
					
							 //Les inputs du formulaire
                            $conf = array(
								'pub_sidebar_top_link'              => array('label' => array('text' => __('Pub lien'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['pub_sidebar_top_link']),
                                'pub_sidebar_top'       => array('label' => array('text' => __('Pub sidebar top (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopePubSidebarTop.width').'x'.Configure::read('HoroscopePubSidebarTop.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['pub_sidebar_top'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['pub_sidebar_top'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
					
							 //Les inputs du formulaire
                            $conf = array(
								'pub_sidebar_bottom_link'              => array('label' => array('text' => __('Pub lien'), 'class' => 'control-label'), 'required' => false, 'value'=> $signe['HoroscopeSign']['pub_sidebar_bottom_link']),
                                'pub_sidebar_bottom'       => array('label' => array('text' => __('Pub sidebar Bottom (.jpg .jpeg)'), 'class' => 'control-label'), 'type' => 'file', 'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('HoroscopePubSidebarBottom.width').'x'.Configure::read('HoroscopePubSidebarBottom.height').'</p></div>')
                            );

                            echo $this->Form->inputs($conf);
							if(!empty($signe['HoroscopeSign']['pub_sidebar_bottom'])){
									echo $this->Html->image('/'.Configure::read('Site.pathHoroscope').'/'.$signe['HoroscopeSign']['pub_sidebar_bottom'].'?'.$this->Time->gmt());
									echo '<br><br>';
								}
                    ?>
                </div>
            </div>

            <?php
                echo $this->Form->end(array(
                    'label' => __('Modifier'),
                    'class' => 'btn blue',
                    'div' => array('class' => 'controls')
                ));
            ?>
        </div>
    </div>
</div>