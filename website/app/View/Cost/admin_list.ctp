<?php
    echo $this->Metronic->titlePage(__('Cout'),__('Les couts agent'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Cout'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'cost', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les couts Agent'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($costs)) :
                echo __('Aucun cout');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Cost.id', __('#')); ?></th>
                        <th><?php echo $this->Paginator->sort('Cost.level', __('Palier ( minutes )')); ?></th>
                        <th><?php echo $this->Paginator->sort('Cost.cost', __('Cout ( euros )')); ?></th>
                        <th><?php echo $this->Paginator->sort('Cost.name', __('Nom')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($costs as $cost): ?>
                        <tr>
                            <td><?php echo $cost['Cost']['id']; ?></td>
                            <td><?php echo $cost['Cost']['level']; ?> minutes</td>
                            <td><?php echo $cost['Cost']['cost']; ?> € </td>
                            <td><?php echo $cost['Cost']['name']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'cost', 'action' => 'edit', 'admin' => true, 'id' => $cost['Cost']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>