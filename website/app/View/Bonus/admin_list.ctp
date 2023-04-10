<?php
    echo $this->Metronic->titlePage(__('Bonus'),__('Les bonus agent'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Bonus'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'bonus', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les bonus Agent'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($bonus)) :
                echo __('Aucun bonus');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Bonus.id', __('#')); ?></th>
                        <th><?php echo $this->Paginator->sort('Bonus.bearing', __('Palier ( minutes )')); ?></th>
                        <th><?php echo $this->Paginator->sort('Bonus.amount', __('Bonus ( euros )')); ?></th>
                        <th><?php echo $this->Paginator->sort('Bonus.name', __('Nom')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bonus as $bonu): ?>
                        <tr>
                            <td><?php echo $bonu['Bonus']['id']; ?></td>
                            <td><?php echo $bonu['Bonus']['bearing']; ?> minutes</td>
                            <td><?php echo $bonu['Bonus']['amount']; ?> € </td>
                            <td><?php echo $bonu['Bonus']['name']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'bonus', 'action' => 'edit', 'admin' => true, 'id' => $bonu['Bonus']['id']),
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