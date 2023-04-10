<?php
    echo $this->Metronic->titlePage(__('Gift'),__('Les bons cadeau'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Gift'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'gifts', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les bons cadeau'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($gifts)) :
                echo __('Aucun bon');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Gift.id', __('Gift')); ?></th>
                        <th><?php echo $this->Paginator->sort('Gift.name', __('Nom')); ?></th>
                        <th><?php echo $this->Paginator->sort('Gift.amount', __('Prix')); ?></th>
                        <th><?php echo $this->Paginator->sort('Gift.domains', __('Domain')); ?></th>
                        <th><?php echo $this->Paginator->sort('Gift.active', __('Etat')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($gifts as $gift): ?>
                        <tr>
                            <td><?php echo __('Gift').' - '.$gift['Gift']['id']; ?></td>
                            <td><?php echo $gift['Gift']['name']; ?></td>
                            <td><?php echo $gift['Gift']['amount']; ?></td>
                            <td><?php 
								
								$domains = explode(',',$gift['Gift']['domains']);
								foreach($domains as $d){
									
									switch ($d) {
										case 11:
											echo 'Belgique ';
											break;
										case 13:
											echo 'Suisse ';
											break;
										case 19:
											echo 'France ';
											break;
										case 22:
											echo 'Luxembourg ';
											break;
										case 29:
											echo 'Canada ';
											break;
									}
									
								}
								
								 ?></td>
                            <td><?php echo ($gift['Gift']['active'] == 1
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'gifts', 'action' => 'edit', 'admin' => true, 'id' => $gift['Gift']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($gift['Gift']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'gift', 'action' => 'deactivate', 'admin' => true, 'id' => $gift['Gift']['id']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'gift', 'action' => 'activate', 'admin' => true, 'id' => $gift['Gift']['id']),
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