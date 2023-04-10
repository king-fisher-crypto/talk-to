<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Croisement opposition client'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Clients'),
            'classes' => 'icon-user',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Croisement opposition'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'watchopposed', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
  <?php echo $this->Metronic->getDateInput(); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Croisement opposition'); ?></div>
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Email').' :', 'div' => false, 'value' => $filtre_email));
					echo $this->Form->input('phone_number', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Telephone').' :', 'div' => false, 'value' => $filtre_phone));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($allUsers)): ?>
                <?php echo __('Pas de clients'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.date_blocked', __('Date')); ?></th>
                        <th><?php echo $this->Paginator->sort('Parent.id', __('Client opposé')); ?></th>
                        <th><?php echo $this->Paginator->sort('Parent.id', __('Email')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.id', __('Autre compte')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.email', __('Email')); ?></th>
						            <th><?php echo $this->Paginator->sort('User.email', __('Tél.')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.active', __('Compte actif')); ?></th>
                        <th><?php echo $this->Paginator->sort('User.payment_blocked', __('Mode paiement restreint')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($allUsers as $k => $row): 
                      
                      if($row['Parent']['id']){                      
                      ?>
                        <tr>
                          <td><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$row['User']['date_blocked']),'%d/%m/%y %Hh%Mmin%Ss'); ?></td>
                          <td><?php echo $this->Html->link($row['Parent']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['Parent']['id'], 'full_base' => true)); ?></td>
                          <td><?php echo $row['Parent']['email']; ?></td>
                          <td><?php echo $this->Html->link($row['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true)); ?></td>
                          <td><?php echo $row['User']['email']; ?></td>
                          <td><?php echo $row['User']['phone_number']; ?></td>
							            <td>
                       			<?php
									           if($row['User']['active'])echo 'Oui'; else echo 'Non';	
                       			?>	
                       		</td>
                          <td>
                       			<?php
									           if($row['User']['payment_blocked'])echo 'Oui'; else echo 'Non';	
                       			?>	
                       		</td>
                        </tr>
                      <?php }else{ ?>
                        <tr>
                          <td><?php echo $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$row['User']['date_blocked']),'%d/%m/%y %Hh%Mmin%Ss'); ?></td>
                          <td><?php echo $this->Html->link($row['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true)); ?></td>
                          <td><?php echo $row['User']['email']; ?></td>
                          <td>&nbsp;</td>
                          <td>&nbsp;</td>
                          <td><?php echo $row['User']['phone_number']; ?></td>
							            <td>
                       			<?php
									           if($row['User']['active'])echo 'Oui'; else echo 'Non';	
                       			?>	
                       		</td>
                          <td>
                       			<?php
									           if($row['User']['payment_blocked'])echo 'Oui'; else echo 'Non';	
                       			?>	
                       		</td>
                        </tr>
                      
                      <?php } ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>