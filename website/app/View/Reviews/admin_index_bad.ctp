<?php
echo $this->Metronic->titlePage(__('Avis'),__('Liste des avis clients'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'),
        'classes' => 'icon-home',
        'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __((isset($this->request->query['refuse'])?'Avis refusé':'Avis')),
        'classes' => 'icon-comments',
        'link' => $this->Html->url(array('controller' => 'reviews', 'action' => 'index', 'admin' => true, '?' => (isset($this->request->query['refuse'])?'refuse':false)))
    )
));

echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">
                <?php
                    if(isset($this->request->query['refuse']))
                        echo __('Avis des clients refusés');
                    elseif(isset($this->request->query['online']))
                        echo __('Liste des avis clients publiés');
                    else
                        echo __('Liste des avis clients en attente');
                ?>
            </div>
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('name', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Pseudo / Nom').' :', 'div' => false, 'value' => $agent_name));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($reviews)): ?>
                <?php echo __('Aucun avis en attente'); ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                        <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Agent')); ?></th>
                        <th colspan="2"><?php echo __('Avis'); ?></th>
                        <th><?php echo $this->Paginator->sort('Review.rate', __('Note')); ?></th>
                        <th><?php echo $this->Paginator->sort('Review.pourcent', __('% forcé')); ?></th>
						<th><?php echo $this->Paginator->sort('Review.rate', __('Note expert')); ?></th>
						<th><?php echo $this->Paginator->sort('Review.rate', __('Note affichée sur le site')); ?></th>
						<th><?php echo $this->Paginator->sort('Review.rate', __('Nouvelle Note')); ?></th>
						<th><?php echo $this->Paginator->sort('Review.rate', __('Nouvelle Note affichée sur le site')); ?></th>
                        <th><?php echo $this->Paginator->sort('Review.date_add', __('Date d\'ajout')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                        if(isset($this->request->query['refuse']))
                            $query = 'refuse';
                        elseif (isset($this->request->query['online']))
                            $query = 'online';
                        else
                            $query = false;
                    ?>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td>
                             <?php echo $this->Html->link($review['User']['firstname'],
                                    array(
                                        'controller' => 'accounts',
                                        'action' => 'view',
                                        'admin' => true,
                                        'id' => $review['User']['id']
                                    )
                                ); ?>
                            </td>
                            <td>
                                <?php echo $this->Html->link($review['Agent']['pseudo'],
                                    array(
                                        'controller' => 'agents',
                                        'action' => 'view',
                                        'admin' => true,
                                        'id' => $review['Agent']['id']
                                    )
                                ); ?>
                            </td>
                            <td class="content-review" id="<?php echo $review['Review']['review_id']; ?>"><?php 
							
							if($review['ReviewsRep']['content']){
								echo 'Réponse de l\'agent : <br />'.$review['Review']['content'].' <br /> à <br />'.nl2br($review['ReviewsRep']['content']);
							}else{
														
								echo nl2br($review['Review']['content']);
							}
							 ?></td>
                            <td class="td_edit_review">
                                <?php 
								if((!empty($user_level) && $user_level != 'moderator') || $review['Review']['rate'] == 5){
									if($review['Review']['status'] != 0){
									echo $this->Metronic->getLinkButton(
										__('Modifier l\'avis'),
										array(
											'controller' => 'reviews',
											'action' => 'edit_review',
											'admin' => true,
											'id' => $review['Review']['review_id'],
											'?' => $query
										),
										'btn blue nx_editreview',
										'icon-edit-sign'
									);
									}else{
									echo $this->Metronic->getLinkButton(
										__('Informer le client'),
										array(
											'controller' => 'reviews',
											'action' => 'send_response_review',
											'admin' => true,
											'id' => $review['Review']['review_id'],
											'?' => $query
										),
										'btn blue nx_editreview',
										'icon-edit-sign'
									);
									}
								}
								?>
                            </td>
                            <td><?php echo $review['Review']['rate'].'/5'; ?></td>
                            <td><?php echo $review['Review']['pourcent'].'%'; ?></td>
							<td><?php echo number_format($review['Agent']['reviews_avg'],3); ?></td>
							<td><?php echo number_format($review['Agent']['reviews_avg'],1); ?></td>
							<td><?php echo $review['Review']['notation']; ?></td>
							<td><?php echo number_format($review['Review']['notation'],1); ?></td>
							
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'), $review['Review']['date_add']),'%d %B %Y'); ?></td>
                            <td>
                                <?php
									if((!empty($user_level) && $user_level != 'moderator') || $review['Review']['rate'] == 5) {
										if(isset($this->request->query['online'])):
												echo $this->Metronic->getLinkButton(
													__('Refuser'),
													array('controller' => 'reviews', 'action' => 'refuse_review', 'admin' => true, 'id' => $review['Review']['review_id']),
													'btn yellow',
													'icon-remove',
													__('Voulez-vous vraiment rejeter l\'avis ?')
												);
										elseif(isset($this->request->query['refuse'])):
											echo $this->Metronic->getLinkButton(
												__('Accepter'),
												array('controller' => 'reviews', 'action' => 'accept_review', 'admin' => true, 'id' => $review['Review']['review_id'], '?' => 'refuse'),
												'btn green',
												'icon-check',
												__('Cet avis a été refusé, voulez-vous vraiment l\'accepter ? Un email sera envoyé au client.')
											);
										else :
											echo $this->Metronic->getLinkButton(
													__('Accepter'),
													array('controller' => 'reviews', 'action' => 'accept_review', 'admin' => true, 'id' => $review['Review']['review_id']),
													'btn green',
													'icon-check',
													__('Voulez-vous vraiment valider cet avis ? Le client en sera informé par mail.')
												).' '
												.$this->Metronic->getLinkButton(
													__('Refuser'),
													array('controller' => 'reviews', 'action' => 'refuse_review', 'admin' => true, 'id' => $review['Review']['review_id']),
													'btn yellow',
													'icon-remove',
													__('Voulez-vous vraiment rejeter l\'avis ?')
												)
											;
										endif;
									}
									if((!empty($user_level) && $user_level != 'moderator'))
                                    echo ' '.$this->Metronic->getLinkButton(
                                        __('Supprimer'),
                                        array('controller' => 'reviews', 'action' => 'delete_review', 'admin' => true, 'id' => $review['Review']['review_id']),
                                        'btn red',
                                        'icon-remove',
                                        __('Voulez-vous supprimer définitivement cet avis ?')
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