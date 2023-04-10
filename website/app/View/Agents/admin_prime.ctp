<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agent'),__('Prime'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Agents'),
            'classes' => 'icon-user',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Primes'),
            'classes' => 'icon-euro',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'prime', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput(); ?>
    <div class="pull-left">
                <?php
                    echo $this->Form->create('UserCredit', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                   // echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
    <div class="portlet box red">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Primes agents'); ?></div>

            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de toutes les primes'),
                array('controller' => 'agents', 'action' => 'export_prime', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
        </div>
        <div class="portlet-body">
            <?php if(empty($lastPrime)): ?>
                <?php echo __('Pas de prime'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('ID agent'); ?></th>
						<th><?php echo __('Prenom'); ?></th>
						<th><?php echo __('Nom'); ?></th>
						<th><?php echo __('Pseudo'); ?></th>
						<th><?php echo __('Prime'); ?></th>
						<th><?php echo __('Montant'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
						$total = 0;
						foreach ($lastPrime as $k => $row): ?>
                        <tr>
                            <td><?php echo $row['date']; ?></td>
							<td><?php echo $row['id']; ?></td>
							<td><?php echo $row['prenom']; ?></td>
							<td><?php echo $row['nom']; ?></td>
							<td><?php 
								 echo $this->Html->link($row['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $row['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
								
							 ?></td>
							<td><?php echo $row['prime']; ?></td>
							<td><?php echo $row['montant']; ?></td>
                        </tr>
                    <?php
						$total +=$row['montant']; 
						endforeach; ?>
						<tr>
                            <td><?php echo $row['date']; ?></td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>&nbsp;</td>
							<td>TOTAL</td>
							<td><?php echo $total; ?></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>