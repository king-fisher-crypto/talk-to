<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agent'),__('TVA'));
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
            'text' => __('TVA'),
            'classes' => 'icon-euro',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'vat', 'admin' => true))
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
  
    <div class="portlet box red">
		  
        <div class="portlet-title">
            <div class="caption"><?php echo __('TVA agents'); ?></div>

            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV'),
                array('controller' => 'agents', 'action' => 'export_vat', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
			<div class="pull-right">
                <?php
                    echo $this->Form->create('Agents', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('status', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'select', 'label' => __('Statut').' :', 'div' => false, 'options' => array('empty' => 'Choisir','null' => 'Aucun','valide' => 'Valide', 'invalide'=>'Invalide')));
					
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                
                ?>
            </div>
        </div>
		
        <div class="portlet-body">
            <?php if(empty($agents)): ?>
                <?php echo __('Pas de TVA'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('ID agent'); ?></th>
						<th><?php echo __('Prenom'); ?></th>
						<th><?php echo __('Nom'); ?></th>
						<th><?php echo __('Email'); ?></th>
						<th><?php echo __('Pseudo'); ?></th>
						<th><?php echo __('Pays'); ?></th>
						<th><?php echo __('Num TVA'); ?></th>
						<th><?php echo __('Status'); ?></th>
						<th><?php echo __('Raison'); ?></th>
						<th><?php echo __('Info'); ?></th>
						<th><?php echo __('Observation'); ?></th>
						<th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
						$total = 0;
						foreach ($agents as $k => $row): 
							$country = $row['CountrySociety']['name'];
							if(!$country)$country =$row['Country']['name']; 
						
						?>
                        <tr>
							<td><?php echo $row['User']['id']; ?></td>
							<td><?php echo $row['User']['firstname']; ?></td>
							<td><?php echo $row['User']['lastname']; ?></td>
							<td><?php echo $row['User']['email']; ?></td>
							<td><?php 
								 echo $this->Html->link($row['User']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $row['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
								
							 ?></td>
							<td><?php echo $country; ?></td>
							<td><?php echo $row['User']['vat_num']; ?></td>
							<td><?php 
								echo $row['User']['vat_num_status'];
								
								if($row['User']['vat_num_proof'] && $row['User']['vat_num_status'] == 'invalide') echo ' / preuve fournit';
								 ?></td>
							<td><?php echo $row['User']['vat_num_status_reason']; ?></td>
							<td><?php echo $row['User']['vat_num_status_reason_desc']; ?></td>
							<td>
							<textarea class="VATAgent_obs" rel="<?php echo $row['User']['id']; ?>"><?php echo $row['User']['vat_num_status_reason_obs']; ?></textarea>
							
							</td>
							 <td><?php
                                        echo $this->Html->link('<i class="icon-remove-sign icon_margin_right"> </i> '.__('Relancer demande'),
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'delete_vat_reason',
                                                'admin' => true,
                                                'id' => $row['User']['id']
                                            ),
                                            array('class' => 'btn red-stripe', 'escape' => false),
                                            __('Voulez-vous vraiment relancer cet Expert ?')
                                        );
                                    ?></td>
                        </tr>
						<?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>