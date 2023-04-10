<?php
    echo $this->Metronic->titlePage(__('Agents'),__('Live communications'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Communications'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'livecom', 'admin' => true))
        ),
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Toutes les communications en cours'); ?> | Connectés : <span class="agent_in"></span> | Occupés : <span class="agent_busy"></span> | Ratio : <span class="agent_ratio"></span> | Agents souhaités : <span class="agent_need"></span></div>
        </div>
        <div class="portlet-body" style="display:inline-block;width:100%;">
            <div id="livecom_container" style="display:inline-block;width:100%;"></div>
        </div>
    </div>
</div>