<?php
    echo $this->Metronic->titlePage(__('Crm envoi'),__('Crm envoi'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Les envois'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'sends', 'admin' => true))
        ),
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les envois CRM'); ?></div>
            <div class="pull-right">
              <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Crm', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('crm_tracker', array('class' => 'input  margin-left margin-right', 'type' => 'texte', 'label' => __('Tracker').' :', 'div' => false));
				   echo $this->Form->input('crm_client', array('class' => 'input  margin-left margin-right', 'type' => 'texte', 'label' => __('Client prénom').' :', 'div' => false));
				   echo $this->Form->input('crm_code', array('class' => 'input  margin-left margin-right', 'type' => 'texte', 'label' => __('Client code').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'
                ?>
                <?php
                   /* echo $this->Form->create('Crm', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                   
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'*/
                ?>

            <?php /*echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les vouchers'),
                array('controller' => 'vouchers', 'action' => 'export_voucher', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); */?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($crms)) :
                echo __('Aucun envoi');
            else : 
			
			/*$dt = new DateTime(date('Y-m-d H:i:s'));
			$dx = new DateTime(date('Y-m-d H:i:s'));
			
			$dt->modify('- 330 day');
			$delai = $dt->format('Y-m-d H:i:s');
			$dx->modify('- 360 day');
			$delai_max = $dx->format('Y-m-d H:i:s');
			
			var_dump($delai_max. ' -> '.$delai);*/
			?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('CrmStat.id', __('ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('CrmStat.date', __('Date')); ?></th>
                        
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.personal_code', __('Code')); ?></th>
                        <th><?php echo $this->Paginator->sort('CrmStat.email', __('Email')); ?></th>
                        <th><?php echo $this->Paginator->sort('Crm.tracker', __('Tracker')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.date_add', __('Date inscription')); ?></th>
                        <th><?php echo $this->Paginator->sort('Order.date_add', __('Date dernier paiment')); ?></th>
                        <th><?php echo $this->Paginator->sort('Com.date_start', __('Date derniere consult')); ?></th>
                        
                        <th><?php echo $this->Paginator->sort('CrmStat.view', __('Ouvert')); ?></th>
                        <th><?php echo $this->Paginator->sort('CrmStat.click', __('Clické')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
						
						foreach ($crms as $crm): 
						
							
							
							
							
							$date_envoi = new DateTime($crm['CrmStat']['date']);
							
							
						
						?>
                        <tr>
                            <td><?php echo $crm['CrmStat']['id']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$crm['CrmStat']['date']),'%d %B %Y %Hh%M'); ?></td>
                            <td><?php echo $this->Html->link($crm['User']['firstname'].' '.$crm['User']['lastname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $crm['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ) ?></td>
                            <td><?php echo $crm['User']['personal_code']; ?></td>
                            <td><?php echo $crm['CrmStat']['email']; ?></td>
                            <td><?php echo $crm['Crm']['tracker']; ?></td>
                            <td><?php 
								$date_ins = new DateTime($crm['User']['date_add']);
								$interval_ins = $date_ins->diff($date_envoi);
								echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$crm['User']['date_add']),'%d %B %Y %Hh%M'). '('.$interval_ins->format('%a').'j)'; ?></td>
                            <td><?php 
								if($crm['Order']['date_add'] != '' && $crm['Order']['date_add'] != '0000-00-00 00:00:00'){
								$date_buy = new DateTime($crm['Order']['date_add']);
								$interval_buy = $date_buy->diff($date_envoi);
								echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$crm['Order']['date_add']),'%d %B %Y %Hh%M'). '('.$interval_buy->format('%a').'j)'; 
								}
								?></td>
                            <td><?php
								if($crm['Com']['date_start'] != '' && $crm['Com']['date_start'] != '0000-00-00 00:00:00'){
								$date_consult = new DateTime($crm['Com']['date_start']);
								$interval_com = $date_consult->diff($date_envoi);
								echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$crm['Com']['date_start']),'%d %B %Y %Hh%M'). '('.$interval_com->format('%a').'j)';
								}
								?></td>
                            <td><?php 
								if($crm['CrmStat']['view'] == 1) echo 'Oui';
								if($crm['CrmStat']['view'] == 0) echo 'Non';
								
								?></td>
                            <td><?php 
								if($crm['CrmStat']['click'] == 1) echo 'Oui';
								if($crm['CrmStat']['click'] == 0) echo 'Non';
								
								?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>