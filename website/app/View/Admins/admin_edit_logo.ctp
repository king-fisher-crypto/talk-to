<?php
    echo $this->Metronic->titlePage(__('Logo'),__('Logos des sites'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Logos'),
            'classes' => 'icon-picture',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'logo', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Edition du logo pour : ').$nameDomain; ?></div>
        </div>
        <div class="portlet-body">
            <div class="row-fluid">
                <?php
                    echo $this->Form->create('Logo', array('nobootstrap' => 1,'class' => 'form-horizontal span4 panel-contenu', 'default' => 1, 'enctype' => 'multipart/form-data',
                                                           'inputDefaults' => array(
                                                               'div' => 'control-group',
                                                               'between' => '<div class="controls">',
                                                               'class'   => 'span10',
                                                               'after' => '</div>'
                                                           )));


                    echo '<label class="control-label">'.__('Logo actuel').'</label>';
                    echo '<br>';
                    echo '<p class="pull-right">'.$this->Html->image('/'.Configure::read('Site.pathLogo').'/'.$id.'_logo.jpg?'.$this->Time->gmt()).'</p>';
                    echo '<br><br>';

                    echo $this->Form->inputs(array(
                        'legend' => false,
                        'domain'    => array('type' => 'hidden', 'value' => $id),
                        'photo'     => array(
                            'label' => array('text' => __('Photo (.jpg .png .gif)'),'class' => 'control-label required'),
                            'required' => true,
                            'type' => 'file',
                            'accept' => 'image/*',
                            'after' => '<p>'.__('Taille finale de l\'image').' '.Configure::read('Logo.width').'x'.Configure::read('Logo.height').'</p></div>'
                        )
                    ));

                    echo $this->Form->end(array(
                        'label' => __('Modifier'),
                        'class' => 'btn blue',
                        'div' => array('class' => 'controls')
                    ));
                ?>
                <div class="span8">
                    <h4><strong>SecureLogo.com (hébergement des logos SSL)</strong></h4>
                    <div style="padding:20px; background-color:#EEE">
                        <img src="<?php echo $ssl_logo['url']; ?>" title="<?php echo $ssl_logo['url']; ?>" style="float:left; margin-right:10px" />
                        <p><?php echo $ssl_logo['url']; ?></p>
                        <p>Logo à modifier si nécessaire, directement sur le site <a href="http://www.securelogo.com" title="_blank">www.securelogo.com</a></p>

                        <div class="clearfix"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>