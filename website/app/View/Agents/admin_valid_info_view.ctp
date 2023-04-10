<?php
echo $this->Html->script('/theme/default/js/admin_agent_rowspan', array('block' => 'script'));
echo $this->Metronic->titlePage(__('Agent'),__('Données de l\'agent'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Agents'),
        'classes' => 'icon-user-md',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
    ),
    2 => array(
        'text' => (!isset($data['actuelle']['Pseudo']) && empty($data['actuelle']['Pseudo'])?__('Agent'):$data['actuelle']['Pseudo']),
        'classes' => 'icon-zoom-in',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $data['actuelle']['id']))
    ),
    3 => array(
        'text' => __('Données en attente'),
        'classes' => 'icon-hdd',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'valid_info_view', 'admin' => true, 'id' => $data['validation']['id']))
    )
));
echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Comparaison des données'); ?></div>
        </div>
        <div class="portlet-body">
            <table class="table table-striped table-hover table-bordered td_agent_view">
                <thead>
                    <tr>
                        <th></th>
                        <th><?php echo __('Données actuelles'); ?></th>
                        <th><?php echo __('Données en attente'); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; ?>
                    <?php foreach($data['actuelle'] as $key => $val) : ?>
                        <?php if($key === 'id') continue; ?>
                        <tr>
                            <td class="txt-bold<?php echo (strcmp($data['actuelle'][$key],$data['validation'][$key]) == 0?'':' info-different'); ?>"><?php echo __($key); ?></td>
                            <td class="txt-bold"><?php echo $data['actuelle'][$key]; ?></td>
                            <td class="txt-bold"><?php echo $data['validation'][$key]; ?></td>
                            <?php if($i == 0){  //La cellule pour les boutons
                                echo '<td class="td-button" rowspan="1">'.
                                    $this->Metronic->getLinkButton(
                                        __('Accepter'),
                                        array('controller' => 'agents','action' => 'accept_valid_info', 'admin' => true, 'id' => $data['validation']['id']),
                                        'btn green',
                                        'icon-check').'<br/><br/>'.
                                    $this->Metronic->getLinkButton(
                                        __('Refuser'),
                                        array('controller' => 'agents','action' => 'refuse_valid_info', 'admin' => true, 'id' => $data['validation']['id']),
                                        'btn red nx_refuselightbox',
                                        'icon-remove'
                                    )
                                    .'</td>';
                            } ?>
                        </tr>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <span rows="<?php echo $i; ?>" style="display: none;"></span>
        </div>
    </div>
</div>