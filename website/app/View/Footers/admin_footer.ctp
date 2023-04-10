<?php
    $this->Html->css('/theme/default/js/dasbaum/dasBaum', array('block' => 'css'));
    $this->Html->css('/theme/default/css/nx_admintopmenu', array('block' => 'css'));
    $this->Html->script('/theme/default/js/dasbaum/dasBaum', array('block' => 'script'));
    $this->Html->script('/theme/default/js/nx_adminfooter', array('block' => 'script'));
    $this->Html->script('/theme/default/js/nx_adminfooterlink', array('block' => 'script'));
    $this->Html->script('/theme/default/js/nx_adminfooterblocklink', array('block' => 'script'));

    echo $this->Metronic->titlePage(__('Footer'),__('Footer'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Le footer'),
            'classes' => 'icon-list',
            'link' => $this->Html->url(array('controller' => 'footers', 'action' => 'footer', 'admin' => true))
        )
    ));
    echo $this->Session->flash();

?>
<div class="row-fluid">
    <div class="span3">
        <div class="portlet box blue elements_block" id="elements_of_sautdecolonne">
            <div class="portlet-title">
                <div class="caption">Saut de colonne</div>
            </div>
            <div class="portlet-body">
                <div class="html_element he_enabled" data-id="1" data-active="1">
                    <div class="he_title"><?php echo __('Saut de colonne'); ?></div>
                    <div class="he_btns">
                        <button type="button" class="btn btn-xs" id="genpassword"><i class=" icon-plus-sign"></i></button>
                    </div>
                    <div style="clear:both"></div>
                </div>
            </div>
        </div>
        <div class="portlet box blue elements_block" id="elements_of_block">
            <div class="portlet-title">
                <div class="caption">Bloc de liens</div>
                <div class="tools">
                    <a class="reload" href=""></a>
                </div>
            </div>
            <div class="portlet-body"></div>
        </div>
        <div class="portlet box blue elements_block" id="elements_of_category">
            <div class="portlet-title">
                <div class="caption">Univers</div>
                <div class="tools">
                    <a class="reload" href=""></a>
                </div>
            </div>
            <div class="portlet-body"></div>
        </div>

    </div>
    <div class="span6">
        <div class="portlet box blue elements_block" id="menu_elements">
            <div class="portlet-title">
                <div class="caption">Le footer</div>
                <div class="tools">
                    <?php echo $this->Form->button('<span class="icon-remove margin-right"></span>'.__('Supprimer'), array(
                            'type'  => 'button',
                            'class' => 'btn red-stripe',
                            'id'    => 'removeitem'
                    )); ?>
                </div>
            </div>
            <div class="portlet-body">
                <div id="container-drag">
                    <div id="tree"></div>
                </div>
                <?php
                    echo $this->Form->button('Enregistrer', array(
                        'type' => 'button',
                        'id'   => 'savefooter',
                        'class' => 'btn blue',
                        'style' => 'margin-top:15px; float:right'
                    ));

                ?>
            </div>
        </div>
    </div>
    <div class="span3">

        <div class="portlet box blue elements_block" id="elements_of_cms">
            <div class="portlet-title">
                <div class="caption">Pages CMS</div>
                <div class="tools">
                    <a class="reload" href=""></a>
                </div>
            </div>
            <div class="portlet-body"></div>
        </div>
        <div class="portlet box blue elements_block" id="elements_of_link">
            <div class="portlet-title">
                <div class="caption">Liens</div>
                <div class="tools">
                    <a class="reload" href=""></a>
                </div>
            </div>
            <div class="portlet-body"></div>
        </div>
    </div>
</div>




<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Les liens'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row-fluid">
                <?php
                    echo $this->Metronic->getLinkButton(
                        __('Créer un lien'),
                        array('controller' => 'footers', 'action' => 'add_link', 'admin' => true),
                        'btn green add_link',
                        'icon-plus-sign'
                    );
                ?>
                <div class="menu-link">
                    <?php echo $this->element('admin_footer_select_link', array('data' => $footer_select['link'])); ?>
                </div>
            </div>
            <div id="block-link">
            </div>
        </div>
    </div>
</div>

<div class="row-fluid">
    <div class="portlet box red">
        <div class="portlet-title">
            <div class="caption">
                <?php echo __('Les blocs de liens'); ?>
            </div>
        </div>
        <div class="portlet-body">
            <div class="row-fluid">
                <?php
                    echo $this->Metronic->getLinkButton(
                        __('Créer un bloc de lien'),
                        array('controller' => 'footers', 'action' => 'add_block_link', 'admin' => true),
                        'btn green add_block_link',
                        'icon-plus-sign'
                    );
                ?>
                <div class="menu-block-link">
                    <?php echo $this->element('admin_footer_select_block_link', array('options' => $footer_select['block_link'])); ?>
                </div>
            </div>
            <div id="block-link-2">
            </div>
        </div>
    </div>
</div>


