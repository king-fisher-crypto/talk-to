<?php
    echo $this->Metronic->titlePage(__('Cout'),__('Les couts téléphone'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Cout'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'cost', 'action' => 'list_phone', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les couts téléphones'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($costs)) :
                echo __('Aucun cout');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('CostPhone.id', __('#')); ?></th>
                        <th><?php echo $this->Paginator->sort('CostPhone.label', __('Pays')); ?></th>
						<th><?php echo $this->Paginator->sort('CostPhone.indicatif', __('Indicatif')); ?></th>
                        <th><?php echo $this->Paginator->sort('CostPhone.cost', __('Cout ( euros )')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($costs as $cost): ?>
                        <tr>
                            <td><?php echo $cost['CostPhone']['id']; ?></td>
                            <td><?php echo $cost['CostPhone']['label']; ?></td>
                            <td>+<?php echo $cost['CostPhone']['indicatif']; ?></td>
                            <td><?php echo $cost['CostPhone']['cost']; ?> € / min</td>
                            
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>