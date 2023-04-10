<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Achats des clients'));
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
            'text' => __('Achats'),
            'classes' => 'icon-euro',
            'link' => $this->Html->url(array('controller' => 'gifts', 'action' => 'order', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    
    <div class="portlet box red">
        <div class="portlet-title">
<div class="pull-left">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('GiftOrder', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Email Client').' :', 'div' => false));
					echo $this->Form->input('email_benef', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Email Benef.').' :', 'div' => false));
					echo $this->Form->input('nom_benef', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Nom Benef.').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
			<div class="pull-right">
            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les achats'),
                array('controller' => 'gifts', 'action' => 'export_order', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
			</div>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastOrder)): ?>
                <?php echo __('Pas d\'achat'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.id', __('Client')); ?></th>
						<th><?php echo $this->Paginator->sort('User.email', __('Email')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.gift_id', __('Cadeau')); ?></th>
						<th><?php echo $this->Paginator->sort('GiftOrder.beneficiary_lastname', __('Bénéficaire')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.beneficiary_email', __('Email')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.date_add', __('Date achat')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.amount', __('Montant')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.devise', __('Devise')); ?></th>
						<th><?php echo $this->Paginator->sort('GiftOrder.code', __('Code')); ?></th>
						<th><?php echo $this->Paginator->sort('GiftOrder.send_date', __('Date envoi')); ?></th>
						<th><?php echo $this->Paginator->sort('GiftOrder.date_use', __('Date utilisation')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.sold', __('Restant')); ?></th>
                        <th><?php echo $this->Paginator->sort('GiftOrder.valid', __('Etat')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastOrder as $k => $row): ?>
                        <tr>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['GiftOrder']['user_id'], 'full_base' => true)); ?></td>
                            <td><?php echo $row['User']['email']; ?></td>
							<td><?php echo $row['Gift']['name']. ' '.$row['Gift']['amount']; ?></td>
							<td>
								<?php 
								if($row['GiftOrder']['beneficiary_id']){
									echo $this->Html->link($row['GiftOrder']['beneficiary_firstname'].' '.$row['GiftOrder']['beneficiary_lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['GiftOrder']['beneficiary_id'], 'full_base' => true));
									
								}else{
									echo $row['GiftOrder']['beneficiary_firstname']. ' '.$row['GiftOrder']['beneficiary_lastname'];
								}
								
								 ?></td>
							<td><?php echo $row['GiftOrder']['beneficiary_email']; ?></td>
							<td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['GiftOrder']['date_add']),'%d %B %Y %H:%M'); ?></td>
							<td><?php echo $row['GiftOrder']['amount']; ?></td>
							<td><?php echo $row['GiftOrder']['devise']; ?></td>
							<td><?php echo $row['GiftOrder']['code']; ?></td>
							<td><?php if($row['GiftOrder']['send_date'] && $row['GiftOrder']['send_date'] != '0000-00-00 00:00:00' )echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['GiftOrder']['send_date']),'%d %B %Y %H:%M'); ?></td>
							<td><?php if($row['GiftOrder']['date_use'] && $row['GiftOrder']['date_use'] != '0000-00-00 00:00:00' )echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['GiftOrder']['date_use']),'%d %B %Y %H:%M'); ?></td>
							<td><?php echo $row['GiftOrder']['sold']; ?></td>
							
							<td><?php 
								
								switch ($row['GiftOrder']['valid']) {
									case 0:
										echo "Non payé";
										break;
									case 1:
										echo "Payé";
										break;
									case 2:
										echo "Utilisé partiellement";
										break;
									case 3:
										echo "Utilisé";
										break;
									case 4:
										echo "Périmé";
										break;
								}
								
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