<?php
    echo $this->Metronic->titlePage(__('Crm'),__('Les crm'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Crm'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les crm'); ?></div>
             <div class="pull-right">
            <?php
            echo ($this->Metronic->getLinkButton(
                                            __('Désactiver tous les CRM'),
                                            array('controller' => 'crm', 'action' => 'deactivate_all', 'admin' => true),
                                            'btn red',
                                            'icon-remove'
                                        )                                     
                                    );
            ?>
			</div>
        </div>
        <div class="portlet-body">
            <?php if(empty($crm)) :
                echo __('Aucun crm');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Crm.id', __('CRM')); ?></th>
                        <th><?php echo __('Nom'); ?></th>
                        <th><?php echo __('Page'); ?></th>
                        <th><?php echo __('Tracker'); ?></th>
                        <th><?php echo $this->Paginator->sort('Crm.active', __('Etat')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($crm as $cr): 
						?>
                        <tr>
                            <td><?php echo __('Crm').' - '.$cr['Crm']['id']; ?></td>
                            <td><?php 
								
								switch($cr['Crm']['type']){
									case "NEVER":
										echo 'N\'ayant jamais acheter sur le site';
									break;
										case "SINCE":
										echo 'Inscrit mais n ayant pas acheter depuis';
									break;
										case "CART":
										echo 'Panier abandonné';
									break;
										case "BUY":
										echo 'Achat non finalisé';
									break;
										case "LOYAL":
										echo 'Bonus fidélité non utilisé';
									break;
								}
								echo ' depuis ';
								switch($cr['Crm']['timing']){
									case "0.005":
										echo '30 min';
										break;	
									case "0.01":
										echo '1 heure';
										break;
									case "0.02":
										echo '2 heures';
										break;
									case "0.03":
										echo '3 heures';
										break;
									case "0.04":
										echo '4 heures';
										break;
									case "0.05":
										echo '5 heures';
										break;
									case "0.06":
										echo '6 heures';
										break;
									case "0.08":
										echo '8 heures';
										break;
									case "0.10":
										echo '10 heures';
										break;
									case "0.12":
										echo '12 heures';
										break;
									case "1":
										echo '1 jour';
									break;
										case "2":
										echo '2 jours';
									break;
										case "3":
										echo '3 jours';
									break;
										case "4":
										echo '4 jours';
									break;
										case "5":
										echo '5 jours';
									break;
										case "6":
										echo '6 jours';
									break;
										case "7":
										echo '7 jours';
									break;
										case "14":
										echo '2 semaines';
									break;
										case "21":
										echo '3 semaines';
									break;
										case "30":
										echo '1 mois';
									break;
										case "60":
										echo '2 mois';
									break;
										case "90":
										echo '3 mois';
									break;
										case "120":
										echo '4 mois';
									break;
										case "150":
										echo '5 mois';
									break;
										case "180":
										echo '6 mois';
									break;
										case "210":
										echo '7 mois';
									break;
										case "240":
										echo '8 mois';
									break;
										case "270":
										echo '9 mois';
									break;
										case "300":
										echo '10 mois';
									break;
										case "330":
										echo '11 mois';
									break;
										case "360":
										echo '12 mois';
									break;
										case "390":
										echo '13 mois';
									break;
										case "540":
										echo '18 mois';
									break;
										case "720":
										echo '2 ans';
									break; }
								 ?></td>
                          <td><?php echo $cr['Crm']['page']; ?></td>
                           <td><?php echo $cr['Crm']['tracker']; ?></td>
                            <td><?php echo ($cr['Crm']['active'] == 1
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'crm', 'action' => 'edit', 'admin' => true, 'id' => $cr['Crm']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($cr['Crm']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'crm', 'action' => 'deactivate', 'admin' => true, 'id' => $cr['Crm']['id']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'crm', 'action' => 'activate', 'admin' => true, 'id' => $cr['Crm']['id']),
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