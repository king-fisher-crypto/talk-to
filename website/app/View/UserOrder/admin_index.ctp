<?php
echo $this->Metronic->titlePage(__('Facturation'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Facturation').' '.$page_title, 'classes' => 'icon-euro'
    )
));

echo $this->Session->flash();


?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
        <div class="caption"><?php echo $this->Html->link(__('Créér une ecriture'),array('controller' => 'user_order', 'action' => 'create', 'admin' => true)); ?></div>
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('UserOrder', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('agent', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client').' :', 'div' => false));
					echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Email').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                
                ?>
            </div>
    </div>
    <div class="portlet-body">
        <?php if(empty($refunds)): ?>
            <?php echo __('Pas d\'écriture'); ?>
        <?php else: ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('UserOrder.id', __('#')); ?></th>
                    <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Agent')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserOrder.label', __('Libelle')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserOrder.amount', __('Montant')); ?></th>


                    <th><?php echo $this->Paginator->sort('UserOrder.id_com', __('Communication')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserOrder.commentaire', __('Commentaire')); ?></th>


                    <th><?php echo $this->Paginator->sort('UserOrder.is_sold', __('Facturé')); ?></th>
                    <th><?php echo $this->Paginator->sort('UserOrder.date_ecriture', __('Date')); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($refunds as $k => $row): ?>
                    <tr>
                        <td><?php echo $row['UserOrder']['id']; ?></td>
                        <td><?php


                            echo $this->Html->link('<span class="icon-user"></span> '.$row['Agent']['pseudo'], array(
                                'controller' => 'agents',
                                'action'     => 'view',
                                'admin'      => true,
                                'id'         => $row['Agent']['id']
                            ), array(
                                'target' => '_blank',
                                'title'  => __('Voir la fiche client #'.$row['Agent']['id']),
                                'escape' => false
                            ));



                            ?></td>
                        <td><?php echo $row['UserOrder']['label']; ?></td>
                        <td><?php echo $row['UserOrder']['amount']; ?></td>


                        <td><?php
								if( $row['UserOrder']['type_com']>1){
										switch ($row['UserOrder']['type_com']) {
											case 2:
												echo "Téléphone";
												break;
											case 3:
												echo "Tchat";
												break;
											case 4:
												echo "Mail";
												break;
										}
									echo ' : '.$row['UserOrder']['id_com'];
								}
                                 ?></td>
                        <td><?php echo nl2br($row['UserOrder']['commentaire']); ?></td>
                        <td><?php 
							
							if($row['UserOrder']['is_sold']) echo 'Oui'; else echo 'Non';
							?></td>
                      
                        <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserOrder']['date_ecriture']),'%d %B %Y %H:%M'); ?></td>
                        <td>
                            <?php if ((int)$row['UserOrder']['is_sold'] == 0): ?>
                                <div class="btn-group margin-left">
                                    <a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo __('Actions'); ?><span class="caret"></span></a>
                                    <ul class="dropdown-menu">

                                        <li><?php echo $this->Html->link(
                                        '<span class="icon-check"></span>'.__('Solder'),
                                        array('controller' => $this->request->controller, 'action' => 'sold', 'admin' => true, 'id' => $row['UserOrder']['id']),
                                        array('escape' => false),
                                        __('Voulez-vous vraiment solder cette écriture "'.$row['UserOrder']['id'].'" ?')
                                    ); ?></li>

                                        <li><?php echo $this->Html->link(
                                                '<span class="icon-remove"></span>'.__('SUPPRIMER'),
                                                array('controller' => $this->request->controller, 'action' => 'delete', 'admin' => true, 'id' => $row['UserOrder']['id']),
                                                array('escape' => false),
                                                __('Voulez-vous vraiment SUPPRIMER cette écriture "'.$row['UserOrder']['id'].'" ? (Action irréversible)')
                                            ); ?></li>


                                    </ul>
                                </div>
                            <?php endif; ?>
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