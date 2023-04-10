<?php
    echo $this->Metronic->titlePage(__('Administrateur'),__('Les Administrateur'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Administrateur'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'user', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les administrateurs'); ?></div>
			<div class="pull-right">
			<?php  echo $this->Html->link('<span class="icon icon-new"></span> Créer',
                    array(
                        'controller' => 'support',
                        'action'     => 'user_create',
                        'admin'      => true
                    ),array(
                        'style' => 'margin-left:20px',
                        'class' => 'btn',
                        'type' => 'button',
                        'label' => false,
                        'div' => false,
                        'escape' => false,
                        
                    )); ?>
			</div>
        </div>
        <div class="portlet-body">
            <?php if(empty($admins)) :
                echo __('Aucun admin');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('SupportAdmin.id', __('ID'));; ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Nom'));; ?></th>
                        <th><?php echo $this->Paginator->sort('SupportService.name', __('Service')); ?></th>
						<th><?php echo $this->Paginator->sort('SupportAdmin.level', __('Level')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($admins as $admin): ?>
                        <tr>
                            <td><?php echo $admin['SupportAdmin']['id']; ?></td>
                            <td><?php echo $admin['User']['firstname'].' '.$admin['User']['lastname']; ?></td>
                            <td><?php echo $admin['SupportService']['name']; ?></td>
							<td><?php echo $admin['SupportAdmin']['level']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Supprimer')   ,
                                        array('controller' => 'support', 'action' => 'user_delete', 'admin' => true, 'id' => $admin['SupportAdmin']['id']),
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