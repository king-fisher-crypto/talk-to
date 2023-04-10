<?php

echo $this->Metronic->titlePage(__('Agents'),__('Liste des agents'));
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
    )
));

echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Liste agents'); ?></div>
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('agent_number', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Code agent').' :', 'div' => false));
                   // echo '<input class="btn green" type="submit" value="Ok">';
                   // echo '</form>'
                ?>
                <?php
                   // echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('pseudo', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Pseudo').' :', 'div' => false));
                   // echo '<input class="btn green" type="submit" value="Ok" /></form>';


                   // echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'label' => __('E-mail').' :', 'div' => false));
                  //  echo '<input class="btn green" type="submit" value="Ok" /></form>';
				
				echo '<label>Statut :</label> <select class="agent_active_select" name="data[Agent][active]">
							<option value="">Choisir</option>
                                	<option value="0">Inactif</option>
                                	<option value="1" >Actif</option>
                                </select>';
					
					echo '&nbsp;&nbsp;<input class="btn green" type="submit" value="Ok" /></form>';
				
				if(!empty($user_level) && $user_level != 'moderator'){
                    echo $this->Html->link('<span class="icon icon-download-alt"></span> Tout exporter',
                        array(
                            'controller' => 'agents',
                            'action'     => 'exportcsv',
                            'admin'      => true
                        ),
                        array(
                        'style' => 'margin-left:20px',
                        'class' => 'btn',
                        'type' => 'button',
                        'label' => false,
                        'div' => false,
                        'escape' => false,
                        'onclick' => 'document.location.href = \'/admin/agents/exportcsv\'; return true'
                    ));
					
					 echo '&nbsp;'.$this->Html->link('<span class="icon icon-download-alt"></span> Export fonds',
                        array(
                            'controller' => 'agents',
                            'action'     => 'exportstripebasecsv',
                            'admin'      => true
                        ),
                        array(
                        'style' => 'margin-left:20px',
                        'class' => 'btn',
                        'type' => 'button',
                        'label' => false,
                        'div' => false,
                        'escape' => false,
                        'onclick' => 'document.location.href = \'/admin/agents/exportstripebasecsv\'; return true'
                    ));
				}
                ?>
            </div>
        </div>
        <div class="portlet-body flip-scroll">
            <?php if(empty($users)): ?>
                <?php echo __('Aucun expert'); ?>
            <?php else: ?>
                
                <div class="row-fluid">
                    <table class="table-bordered table-striped table-condensed flip-content">
                        <thead class="flip-content">
                        <tr>
                            <th><?php echo $this->Paginator->sort('firstname', __('Nom complet')); ?></th>
                            <th><?php echo $this->Paginator->sort('pseudo', __('Pseudo')); ?></th>
                            <th class="hidden-phone hidden-tablet"><?php echo $this->Paginator->sort('email', __('E-mail')); ?></th>
							              <th class="hidden-phone hidden-tablet"><?php echo $this->Paginator->sort('active', __('Actif ?')); ?></th>
                            <th><?php echo $this->Paginator->sort('stripe_base', __('Fond roulement')); ?></th>
							              <th><?php echo $this->Paginator->sort('stripe_available', __('Fond dispo.')); ?></th>
                            <th><?php echo $this->Paginator->sort('stripe_balance', __('Solde')); ?></th>
                          <th><?php echo __('Facture non payé'); ?></th>
							            <th><?php echo $this->Paginator->sort('stripe_balance', __('Manque')); ?></th>
                            <th colspan="3"><?php echo __('Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['User']['firstname'].' '.$user['User']['lastname']; ?></td>
                                <td>
									 <?php
                                        echo $this->Html->link('<i class="icon-zoom-in icon_margin_right"> </i> '.$user['User']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
                                    ?>
									</td>
                                <td class="hidden-phone hidden-tablet"><?php echo $user['User']['email']; ?></td>
								<td class="hidden-phone hidden-tablet"><?php 
									if($user['User']['active']) echo 'Oui'; else echo 'Non';
									?></td>
                                <td><?php echo $user['User']['stripe_base']; ?></td>
								<td><b style="color:#11025B"><?php echo $user['User']['stripe_available']; ?></b></td>
                <td><?php echo $user['User']['stripe_balance']; ?></td>
                <td><?php 
                  if(isset($user['InvoiceAgent']))
                  echo $user['InvoiceAgent']['paid_total'];
                  else
                    echo '';
                  ?></td>
								<td><?php 
									if(isset($user['InvoiceAgent'])){
									$delta = $user['User']['stripe_available'] - $user['InvoiceAgent']['paid_total'];
									if($delta >= 0)
										echo '0';
									else{
										$diff = number_format($delta,2) ;
											echo '<b>'.$diff.'</b>';
										}
									}
									?></td>
                                <td>
                                   
                                </td>
                                <td>
                                    <?php
										if($user['User']['stripe_account'] && !empty($user_level) && $user_level != 'moderator'  ){//&& (date('d') >= 29)
                                        echo $this->Html->link('<i class="icon-edit-sign icon_margin_right"> </i> '.__('Ajouter des fonds'),
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'stripe_sold_depos',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'nx_modal_stripe btn blue-stripe', 'escape' => false),
                                            __('Voulez-vous vraiment envoyer des fonds sur ce compte connect ?')
                                        );
										}
                                    ?>
                                </td>
                                <td>
                                    <?php
									if($user['User']['stripe_account'] && !empty($user_level) && $user_level != 'moderator' ){//&& (date('d') >= 29)
                                        echo $this->Html->link('<i class="icon-remove-sign icon_margin_right"> </i> '.__('Retirer des fonds'),
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'stripe_sold_refund',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'nx_modal_stripe btn red-stripe', 'escape' => false),
                                            __('Voulez-vous vraiment retirer des fonds sur ce compte connect ?')
                                        );
									}
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>