<?php
    echo $this->Metronic->titlePage(__('Services'),__('Les Services'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Services'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'service', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les Services'); ?></div>
			<div class="pull-right">
			<?php  echo $this->Html->link('<span class="icon icon-new"></span> Créer',
                    array(
                        'controller' => 'support',
                        'action'     => 'service_create',
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
            <?php if(empty($services)) :
                echo __('Aucun service');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('SupportService.id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('SupportService.who', __('Visible')); ?></th>
						<th><?php echo $this->Paginator->sort('SupportService.name', __('Nom')); ?></th>
						<th><?php echo $this->Paginator->sort('SupportService.mail', __('Email')); ?></th>
						<th><?php echo $this->Paginator->sort('SupportService.description', __('Selecteur')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
						
						foreach ($services as $service):
						?>
                        <tr>
                            <td><?php echo $service['SupportService']['id']; ?></td>
							<td><?php echo $service['SupportService']['who']; ?></td>
							<td><?php echo $service['SupportService']['name']; ?></td>
							<td><?php echo $service['SupportService']['mail']; ?></td>
							<td><?php echo $service['SupportService']['description']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'support', 'action' => 'service_edit', 'admin' => true, 'id' => $service['SupportService']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                ?>
                            </td>
                        </tr>
                    <?php 
								endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>