<?php
    echo $this->Metronic->titlePage(__('Penalité'),__('Les regles'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Penalité'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'penality', 'action' => 'rules', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les regles'); ?></div>
			 <?php echo $this->Metronic->getLinkButton(
                __('Créér'),
                array('controller' => 'penality', 'action' => 'create', 'admin' => true),
                'btn red pull-right',
                'icon-pen'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($penalities)) :
                echo __('Aucune regles');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Penality.id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('Penality.typer', __('Quoi')); ?></th>
                        <th><?php echo $this->Paginator->sort('Penality.delay_min', __('Palier min')); ?></th>
                        <th><?php echo $this->Paginator->sort('Penality.delay_max', __('Palier max')); ?></th>
						<th><?php echo $this->Paginator->sort('Penality.cost', __('Penalité')); ?></th>
						<th><?php echo $this->Paginator->sort('Penality.active', __('Actif')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($penalities as $penality): ?>
                        <tr>
                            <td><?php echo $penality['Penality']['id']; ?></td>
							<td><?php echo $penality['Penality']['type']; ?></td>
							<td><?php echo $penality['Penality']['delay_min']; ?> sec.</td>
							<td><?php echo $penality['Penality']['delay_max']; ?> sec.</td>
                            <td><?php echo $penality['Penality']['cost']; ?> €</td>
                            <td><?php  echo ($penality['Penality']['active'] == "1"
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>
							
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'penality', 'action' => 'edit', 'admin' => true, 'id' => $penality['Penality']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($penality['Penality']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'penality', 'action' => 'deactivate', 'admin' => true, 'id' => $penality['Penality']['id']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'penality', 'action' => 'activate', 'admin' => true, 'id' => $penality['Penality']['id']),
                                            'btn green',
                                            'icon-add'
                                        )
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