<?php
    echo $this->Html->script('/theme/default/js/admin_agent_rowspan', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Client'),__('Données du client'));
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
            'text' => (!isset($user['User']['firstname']) || empty($user['User']['firstname'])?__('Client'):$user['User']['firstname']),
            'classes' => 'icon-hdd',
            'link' => $this->Html->url(array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Client'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($user)): ?>
                <?php echo __('Aucun client trouvé'); ?>
            <?php else: ?>
				   <?php
				if($user['User']['payment_opposed']){
					echo '<div class="portlet-title" style="background-color: #bb2413 ;display:block;width:98%;padding:10px;color:#fff">
						<div class="caption">'._('Ce compte client est en defaut de paiement.').'</div>
					</div>';
				}
				if($user['User']['parent_account_opposed']){
					echo '<div class="portlet-title" style="background-color: #bb2413 ;display:block;width:98%;padding:10px;color:#fff">
						<div class="caption">'._('Ce compte client est désactivé. Il s\'agit d\'un compte lié a ce <a href="/admin/accounts/view-'.$user['User']['parent_account_opposed'].'" target="_blank" style="color:#fff;text-decoration:underline">client</a> qui est en defaut de paiement.').'</div>
					</div>';
				}
			?>
                <table class="table table-striped table-hover table-bordered td_view">
                    <tbody>
                    <?php $i = 0; //Pour construire le tableau avec 4 colonnes ?>
                    <?php foreach ($user['User'] as $key => $value): ?>
                        <?php if(!isset($nameField[$key])) continue; ?>
                        <?php if($i%2 == 0) echo '<tr>'; ?>
                        <td class="txt-bold name"><?php echo __($nameField[$key]); ?></td>
                        <?php if(strcmp($key, 'date_lastconnexion') == 0) : ?>
                            <td class="value">
                                <?php echo (empty($value)
                                    ?__('N/D')
                                    :$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value),'%d %B %Y %H:%M'))
                                ; ?>
                            </td>
                        <?php $i++; continue; endif; ?>
                        <?php if(strcmp($key, 'emailConfirm') == 0) : ?>
                            <td class="value">
                                <?php if($value == 1):
                                    echo '<span class="badge badge-success">'.__('Email confirmé').'</span>';
                                else :
                                    echo '<span class="badge badge-warning">'.__('Email non confirmé').'</span>'; ?>
                                    <div class="btn-group margin-left">
                                        <a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo __('Actions'); ?><span class="caret"></span></a>
                                        <ul class="dropdown-menu">
                                            <li><?php echo $this->Html->link(
                                                    '<span class="icon-envelope"></span>'.__('Relancer un mail de confirmation'),
                                                    array('controller' => 'accounts', 'action' => 'relance_mail_confirm', 'admin' => true, 'id' => $user['User']['id']),
                                                    array('escape' => false),
                                                    __('Voulez-vous vraiment renvoyer un mail de confirmation ?')
                                                ); ?>
                                            </li>
                                            <li><?php echo $this->Html->link(
                                                    '<span class="icon-check"></span>'.__('Confirmer l\'email'),
                                                    array('controller' => 'accounts', 'action' => 'confirm_mail', 'admin' => true, 'id' => $user['User']['id']),
                                                    array('escape' => false),
                                                    __('Voulez-vous vraiment forcer la confirmation de l\'adresse mail ?')
                                                ); ?>
                                            </li>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </td>
                        <?php $i++; continue; endif; ?>
                        <?php if(strcmp($key, 'valid') == 0) : ?>
                            <td class="value">
                                <?php
                                    if($user['User']['active'] == 1 && $user['User']['valid'] == 1)
                                        echo '<span class="badge badge-success">'.__('Compte activé').'</span>';
                                    elseif(empty($user['User']['firstname']))
                                        echo '<span class="badge badge-warning">'.__('Compte non validé').'</span>';
                                    else
                                        echo '<span class="badge badge-danger">'.__('Compte désactivé').'</span>';
                                ?>
                            </td>
                        <?php $i++; continue; endif; ?>
                        <td class="value"><?php echo $value; ?></td>
                        <?php if($i == 1){  //La cellule pour les boutons
                            echo '<td class="td-button" rowspan="1">';
	
							if(!empty($user_level) && $user_level != 'moderator'){
                                echo $this->Metronic->getLinkButton(
                                    __('Modifier'),
                                    array('controller' => 'accounts', 'action' => 'edit', 'admin' => true, 'id' => $user['User']['id']),
                                    'btn blue',
                                    'icon-edit-sign').'<br/><br/>';
							}
                                echo ($user['User']['active'] == 1
                                    ?$this->Metronic->getLinkButton(
                                        __('Désactiver le compte'),
                                        array('controller' => 'accounts', 'action' => 'deactivate_user', 'admin' => true, 'id' => $user['User']['id']),
                                        'btn red',
                                        'icon-remove',
                                        __('Voulez-vous vraiment désactiver le compte du client ?')
                                    )
                                    :$this->Metronic->getLinkButton(
                                        __('Activer le compte'),
                                        array('controller' => 'accounts', 'action' => 'activate_user', 'admin' => true, 'id' => $user['User']['id']),
                                        'btn green',
                                        'icon-check'
                                    )
                                );
	
								if ($user['User']['personal_code'] !== '999999' && $user['User']['deleted'] == 0){

                                    echo ' '.$this->Metronic->getLinkButton(
                                            __('Désactiver le compte demandé par client'),
                                            array('controller' => 'accounts', 'action' => 'delete_user', 'admin' => true, 'id' => $user['User']['id']),
                                            'btn orange',
                                            'icon-remove',
                                            __('Voulez-vous vraiment supprimer définitivement le compte ?')
                                        );

                                }
								if ($user['User']['personal_code'] !== '999999' && $user['User']['deleted'] == 1){

                                    echo ' '.$this->Metronic->getLinkButton(
                                            __('Re-activer le compte'),
                                            array('controller' => 'accounts', 'action' => 'restore_user', 'admin' => true, 'id' => $user['User']['id']),
                                            'btn green',
                                            'icon-check',
                                            __('Voulez-vous vraiment supprimer définitivement le compte ?')
                                        );

                                }
                                 echo '</td>';
                        } ?>
                        <?php if($i%2 != 0) echo '</tr>' ?>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                    <?php if($i%2 != 0) echo '<td></td><td></td></tr>'; ?>
                    </tbody>
                </table>
                <span rows="<?php echo ceil(($i+1)/2); ?>" style="display: none;"></span>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="row-fluid">
	 
	 <?php
       
		$txt_adr_ip = '';
		$note_ip = '';
		echo '<div class="span2" style="max-height:150px;overflow:auto">';
		foreach($userIp as $uIp){
			$txt_adr_ip .=  '<p style="font-size:12px;">';
			$txt_adr_ip .=  $uIp['UserIp']['IP'];
			$txt_adr_ip .=  ' - ><br />';
			$txt_adr_ip .=  $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$uIp['UserIp']['date_conn']),'%d %B %Y %H:%M');
			$txt_adr_ip .=  '</p>';
			if($uIp['UserIp']['note'])
				$note_ip = $uIp['UserIp']['note'];	
		}
		echo __('Adresses IP').'<br />'.$txt_adr_ip.'</div>';
	echo '<div class="span2" style="max-height:150px;overflow:auto">';
	$txt_adr_ip = '';
	foreach($userNotIp as $uIp){
			$txt_adr_ip .=  '<p style="font-size:12px;">';
			$txt_adr_ip .=  $uIp['UserIp']['IP'];
			$txt_adr_ip .=  ' - ><br />';
			if($uIp['User']['role'] == 'client'){
				$txt_adr_ip .= 'Client : '.$this->Html->link($uIp['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $uIp['User']['id'], 'full_base' => true));
			}else{
				$txt_adr_ip .= 'Expert : '.$this->Html->link($uIp['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $uIp['User']['id'], 'full_base' => true));
			}
			
		}
	
	echo __('Comptes avec même IP').'<br />'.$txt_adr_ip.'</div>';
		echo '<div class="span3">';
		 echo $this->Form->create('Account', array('action' => 'admin_note_ip-'.$user['User']['id'], 'nobootstrap' => 1, 'class' => 'form', 'default' => 1));
        echo $this->Form->input('note', array(
               /* 'label' => array(
                    'text' => __('Adresses IP').$txt_adr_ip,
                    'class' => 'control-label label-overflow-top'
                ),*/
                'type' => 'textarea',
                'between' => '<div class="controls">',
                'after' => '</div>',
                'value' => $note_ip)
        );
	
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));
	echo '</div>';
    ?>

    <?php
	echo '<div class="span5" style="margin-left:0px;">';
        echo $this->Form->create('Account', array('action' => 'admin_note-'.$user['User']['id'], 'nobootstrap' => 1, 'class' => 'form ', 'default' => 1));
        echo $this->Form->input('note', array(
                'label' => array(
                    'text' => __('Note privée'),
                    'class' => 'control-label'
                ),
                'type' => 'textarea',
                'between' => '<div class="controls">',
                'after' => '</div>',
			'style' => 'width:100%',
                'value' => $user['User']['admin_note'])
        );
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));
	echo '</div>';
    ?>
</div>
<div class="row-fluid">
<?php if(!empty($historiqueCredit)): ?>
    <div class="span12">
        <div class="portlet box red">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '.Configure::read('Site.limitStatistique').' '.__('derniers achats'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet du client'),array('controller' => 'accounts', 'action' => 'credit_view', 'admin' => true, 'id' => $user['User']['id'])); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo __('Date d\'achat') ?></th>
                            <th><?php echo __('Credits') ?></th>
                            <th><?php echo __('Produit') ?></th>
                            <th><?php echo __('Total') ?></th>
							<th><?php echo __('Paiement') ?></th>
							<th><?php echo __('Transaction ID') ?></th>
							<th><?php echo __('Panier ID') ?></th>
                            <th><?php echo __('Voucher') ?></th>
							<th><?php echo __('Statut') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($historiqueCredit as $key => $value): ?>
                        <tr>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value['UserCredit']['date_upd']),'%d %B %Y %H:%M'); ?></td>
                            <td><?php echo $value['UserCredit']['credits']; ?></td>
                            <td><?php 
								
								if($value['Orders']['payment_mode']== 'refund'){
									echo $value['UserCredit']['product_name'].' ( ID : '.$value['Orders']['id_com'].' )';
								} else
								echo $value['UserCredit']['product_name']; ?></td>
							<td><?php if($value['Orders']['total']){echo number_format($value['Orders']['total'],2).' '.$value['Orders']['currency'];} ?></td>
                            <td><?php echo $value['UserCredit']['payment_mode']; ?><?php if(($value['Orders']['valid'] == 3 && $value['UserCredit']['payment_mode'] == 'hipay') || ($value['Orders']['valid'] == 4 && $value['UserCredit']['payment_mode'] == 'paypal') ) echo ' - <b>remboursé</b>'; ?></td>
							<td><?php 
								if($value['Orders']['payment_mode']== 'hipay') echo $value['Hipay']['transaction'];
								if($value['Orders']['payment_mode']== 'paypal') echo $value['Paypal']['payment_transactionid'];
								if($value['Orders']['payment_mode']== 'stripe') echo $value['Stripe']['id'];
								if($value['Orders']['payment_mode']== 'sepa') echo $value['Sepa']['charge_id'];
								
								?></td>
							<td><?php echo $value['Orders']['cart_id']; ?></td>
                            <td><?php if($value['Orders']['voucher_name']) echo $value['Orders']['voucher_name'].' ('.$value['Orders']['voucher_code'].')'; ?></td>
							<td><?php 
								if($value['Orders']['payment_mode']== 'hipay' || $value['Orders']['payment_mode']== 'stripe'){
									if($value['Orders']['valid']== '0') echo 'refusé';	
									if($value['Orders']['valid']== '1') echo 'accepté';
									if($value['Orders']['valid']== '2') echo 'impayé';
									if($value['Orders']['valid']== '3') echo 'remboursé';
									if($value['Orders']['valid']== '4') echo '';
								}

								if($value['Orders']['payment_mode']== 'paypal'){
									if($value['Orders']['valid']== '0') echo 'refusé';
									if($value['Orders']['valid']== '1') echo 'accepté';
									if($value['Orders']['valid']== '2') echo 'en attente';
									if($value['Orders']['valid']== '3') echo 'impayé';
									if($value['Orders']['valid']== '4') echo 'remboursé';
								}
								if($value['Orders']['payment_mode']== 'bankwire'){
									if($value['Orders']['valid']== '0') echo 'refusé';
									if($value['Orders']['valid']== '1') echo 'accepté';
									if($value['Orders']['valid']== '2') echo 'en attente';
									if($value['Orders']['valid']== '3') echo '';
									if($value['Orders']['valid']== '4') echo '';
								}
								
								if($value['Orders']['payment_mode']== 'sepa'){
									if($value['Orders']['valid']== '0') echo 'refusé';
									if($value['Orders']['valid']== '1') echo 'accepté';
									if($value['Orders']['valid']== '2') echo 'en attente';
									if($value['Orders']['valid']== '3') echo '';
									if($value['Orders']['valid']== '4') echo '';
								}
								
								
								?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
<?php endif; ?>
   
<?php if(!empty($loyaltyCom)): ?>
    <div class="span12" style="margin-left:0px;">
        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '.Configure::read('Site.limitStatistique').' '.__('derniers gains fidélités'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet du client'),array('controller' => 'accounts', 'action' => 'loyalty_view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo __('Libellé') ?></th>
                            <th><?php echo __('Crédit') ?></th>
                            <th><?php echo __('Date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($loyaltyCom as $key => $value): ?>
                        <tr>
                            <td>Gain fidélité + 10 Minutes</td>
                            <td>+ 600 crédits</td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value['LoyaltyCredit']['date_add']),'%d %B %Y %H:%M'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>
	<?php if(!empty($sponsorships)): ?>
    <div class="row-fluid" >
        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '.Configure::read('Site.limitStatistique').' '.__('derniers gains parrainage'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet du client'),array('controller' => 'accounts', 'action' => 'sponsorship_view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo __('Filleul') ?></th>
                            <th><?php echo __('Gain') ?></th>
                            <th><?php echo __('Date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sponsorships as $key => $value): ?>
                        <tr>
                            <td><?php
								echo $this->Html->link($value['Filleul']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $value['Sponsorship']['id_customer'], 'full_base' => true));
								?></td>
                            <td><?php echo $value['Sponsorship']['bonus'].' '.$value['Sponsorship']['bonus_type'] ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value['Sponsorship']['date_recup']),'%d %B %Y %H:%M'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php if(!empty($historiqueCom)): ?>
    <div class="span12" style="margin-left:0px;">
        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '.Configure::read('Site.limitStatistique').' '.__('dernières communications'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet du client'),array('controller' => 'accounts', 'action' => 'com_view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
							<th><?php echo __('ID') ?></th>
                            <th><?php echo __('Agent') ?></th>
                            <th><?php echo __('Media') ?></th>
                            <th><?php echo __('Crédit avant') ?></th>
                            <th><?php echo __('Coût (en credits)') ?></th>
                            <th><?php echo __('Durée') ?></th>
                            <th><?php echo __('Date') ?></th>
							<th></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($historiqueCom as $key => $value): ?>
                        <tr>
							<td><?php echo $value['UserCreditHistory']['sessionid']; ?></td>
                            <td><?php echo $this->Html->link($value['UserCreditHistory']['agent_pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $value['UserCreditHistory']['agent_id'], 'full_base' => true)); ?></td>
                            <td><?php echo __($consult_medias[$value['UserCreditHistory']['media']]); ?></td>
                            <td><?php echo $value['UserCreditHistory']['user_credits_before']; ?></td>
                            <td><?php echo '-'.$value['UserCreditHistory']['credits']; ?></td>
                            <td>
                                <?php echo (empty($value['UserCreditHistory']['seconds'])
                                    ?__('N/D')
                                    :gmdate('H:i:s', $value['UserCreditHistory']['seconds'])
                                ); ?>
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value['UserCreditHistory']['date_start']),'%d %B %Y %H:%M'); ?></td>
							<td><a class="btn blue nx_viewcomm" href="/admins/getCommunicationData-<?php echo $value['UserCreditHistory']['user_credit_history']; ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

</div>