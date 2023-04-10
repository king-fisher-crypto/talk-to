<?php
    echo $this->Metronic->titlePage(__('Parrainage'),__('Listing'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Parrainage'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'agent_view', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les parrainages'); ?></div>
        
		 <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Sponsorship', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('ip', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('IP').' :', 'div' => false));
					echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Email Filleul').' :', 'div' => false));
					echo $this->Form->input('email_parrain', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Email Parrain').' :', 'div' => false));
					echo $this->Form->input('pseudo', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Pseudo agent').' :', 'div' => false));
					echo $this->Form->input('firstname', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Prenom client').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div></div>
        <div class="portlet-body">
            <?php if(empty($sponsorships)) :
                echo __('Aucun');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Sponsorship.id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.date_add', __('Date')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.IP', __('IP du Filleul')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.type_user', __('Type')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.user_id', __('Pseudo')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.user_id', __('Email Parrain')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.email', __('Email Filleul')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.source', __('Mode')); ?></th>
						<th><?php echo $this->Paginator->sort('Customer.date_add', __('Date creation')); ?></th>
						<th><?php echo $this->Paginator->sort('Customer.date_add', __('Compte parrainé')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.user_id', __('Parrain')); ?></th>
                        <th><?php echo $this->Paginator->sort('Sponsorship.id_customer', __('Filleul')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.status', __('Statut')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.status', __('Palier filleul')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.bonus', __('Gain')); ?></th>
						<th><?php echo $this->Paginator->sort('Sponsorship.date_block', __('Date stoppé')); ?></th>
						<th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sponsorships as $sponsorship): ?>
                        <tr>
                            <td><?php echo $sponsorship['Sponsorship']['id']; ?></td>
							<td><?php 
								$date_add = new DateTime($sponsorship['Sponsorship']['date_add']);
								echo $date_add->format('d-m-Y H').'h'.$date_add->format('i').'min'.$date_add->format('s').'s'; ?></td>
							<td><?php echo $sponsorship['Sponsorship']['IP']; ?></td>
							<td><?php echo $sponsorship['Sponsorship']['type_user']; ?></td>
							<td><?php 
								if($sponsorship['Sponsorship']['type_user'] == 'client'){
									echo $sponsorship['Parrain']['firstname'];
								}else{
									echo $sponsorship['Parrain']['pseudo'];
								}
								
								 ?></td>
							<td><?php echo $sponsorship['Parrain']['email']; ?></td>
							<td><?php echo $sponsorship['Sponsorship']['email']; ?></td>
							<td><?php 
								if($sponsorship['Sponsorship']['source'] == 'partage')
									echo $sponsorship['Customer']['source'];
								else
								echo $sponsorship['Sponsorship']['source']; ?></td>
							<td><?php 
								if($sponsorship['Customer']['date_add']){
								$date_add = new DateTime($sponsorship['Customer']['date_add']);
								echo $date_add->format('d-m-Y H').'h'.$date_add->format('i').'min'.$date_add->format('s').'s'; } ?></td>
							<td><?php 
								if($sponsorship['Sponsorship']['id_customer'] != '0')
									echo 'Oui';
								else
								echo 'Non'; ?></td>
							
							
							<td><?php 
								if($sponsorship['Sponsorship']['type_user'] == 'client'){
									echo $this->Html->link($sponsorship['Parrain']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sponsorship['Parrain']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
								}else{
									echo $this->Html->link($sponsorship['Parrain']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sponsorship['Parrain']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );
								}
								
								 ?></td>
                           
                            <td><?php 
								
								if($sponsorship['Customer']['id']){
								
								echo $this->Html->link($sponsorship['Customer']['lastname'].' '.$sponsorship['Customer']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sponsorship['Customer']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        );} ?></td>
							
							
							<td style="font-size:10px;"><?php 
								switch ($sponsorship['Sponsorship']['status']) {
									case 0:
										echo "Envoyé";
										break;
									case 1:
										echo "Vu";
										break;
									case 2:
										echo "Inscrit";
										break;
									case 3:
										echo "Bonus en cours";
										break;
									case 4:
										echo "Bonus récupéré";
										break;
									case 5:
										echo "Détecté Multi Ip lors de l'inscription";
										break;
									case 6:
										echo "Parrainage stoppé";
										break;
									case 7:
										echo "Détecté Multi Ip lors de communication";
										break;
									case 10:
										echo "Client deja inscrit";
										break;
								}
								 ?></td>
							<td><?php echo $sponsorship["Sponsorship"]["filleul_palier"]; ?> €</td>
							<td><?php 
								if($sponsorship['Sponsorship']['type_user'] == 'agent'){
									$sponsorship['Sponsorship']['bonus'] = str_replace(',','.',$sponsorship['Sponsorship']['bonus']) /60 * $sponsorship[0]['total'];
								}
								echo number_format($sponsorship['Sponsorship']['bonus'],2,',',' ').' '.$sponsorship['Sponsorship']['bonus_type']; ?></td>
							<td><?php 
								if($sponsorship['Sponsorship']['date_block']){
								$date_block = new DateTime($sponsorship['Sponsorship']['date_block']);
								echo $date_block->format('d-m-Y H').'h'.$date_block->format('i').'min'.$date_block->format('s').'s';
								}
								?>
							</td>
							<td>
								<?php
									if($sponsorship['Sponsorship']['status'] < 5){
								?>
								<input class="btn red deleted_sponsorship" type="button" rel="<?php echo $sponsorship['Sponsorship']['id'] ?>" value="Stopper" />
								<?php
									}
								?>
								<?php
									if($sponsorship['Sponsorship']['status'] == 6){
								?>
								<input class="btn green active_sponsorship" type="button" rel="<?php  echo $sponsorship['Sponsorship']['id'] ?>" value="Autoriser" />
								<?php
									}
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