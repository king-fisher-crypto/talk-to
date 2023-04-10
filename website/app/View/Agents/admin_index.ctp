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

				//	echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('adr_ip', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'label' => __('IP').' :', 'div' => false));
                   // echo '<input class="btn green" type="submit" value="Ok" /></form>';


					echo '<select class="agent_status_select" name="data[Account][status]">
                                	<option value="0">Statut</option>
                                	<option value="19" >Contrôle PI & déclaration</option>
                                	<option value="1" >Envoi questionnaire</option>
                                    <option value="2" >Questionnaire reçu</option>
                                    <option value="3" >Experts refusés</option>
                                    <option value="4" >Experts relancé</option>
                                    <option value="5" >Experts sans réponse</option>
                                    <option value="6" >Refus de l\'expert</option>
                                    <option value="16">A faire patienter</option>
                                    <option value="13" >Envoi contrat</option>
                                    <option value="7" >Dossier incomplet Standby</option>
                                    <option value="11">Expert à controler</option>
                                    <option value="12" >Attente entretien téléphone</option>
									<option value="20" >Ouverture en cours</option>
                                    <option value="8" >Divers</option>
                                    <option value="9" >Expert en ligne</option>
                                    <option value="10" >Expert en pause</option>
                                    <option value="14" >Portage salarial</option>
                                    <option value="17">En ligne/portage</option>
                                    <option value="15" >Cessation partenariat</option>
                                    <option value="18">Radié/détournement clients</option>
                                    <option value="21">Questionnaire non répondu</option>
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
				}
                ?>
            </div>
        </div>
        <div class="portlet-body flip-scroll">
            <?php if(empty($users)): ?>
                <?php echo __('Aucun expert'); ?>
            <?php else: ?>
                <div class="row-fluid">
                    <?php if(isset($this->request->query['email'])): ?>
                        <p class="badge badge-info">
                            <span class="icon-info-sign icon_margin_right"></span>
                            <?php echo __('Liste des agents qui n\'ont pas confirmé leurs adresses mails'); ?>
                        </p>
                    <?php elseif (isset($this->request->query['compte'])): ?>
                        <p class="badge badge-info">
                            <span class="icon-info-sign icon_margin_right"></span>
                            <?php echo __('Liste des agents dont le compte n\'est pas validé'); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="row-fluid">
                    <table class="table-bordered table-striped table-condensed flip-content">
                        <thead class="flip-content">
                        <tr>
                            <th><?php echo $this->Paginator->sort('firstname', __('Nom complet')); ?></th>
                            <th><?php echo $this->Paginator->sort('pseudo', __('Pseudo')); ?></th>
                            <th class="hidden-phone hidden-tablet"><?php echo $this->Paginator->sort('email', __('E-mail')); ?></th>
                            <th><?php echo $this->Paginator->sort('phone_number', __('Téléphone')); ?></th>
                            <th><?php echo $this->Paginator->sort('phone_number2', __('Téléphone 2')); ?></th>
                            <th><?php echo $this->Paginator->sort('phone_number2', __('Numéro mobile')); ?></th>
                            <th><?php echo $this->Paginator->sort('phone_number2', __('Numéro utilisé')); ?></th>
                            <th><?php echo $this->Paginator->sort('agent_number', __('Code agent')); ?></th>
                            <th class="hidden-phone hidden-tablet"><?php echo $this->Paginator->sort('date_add', __('Inscription')); ?></th>
                            <th class="hidden-phone hidden-tablet"><?php echo $this->Paginator->sort('city', __('Ville')); ?></th>
                            <th><?php echo $this->Paginator->sort('country_id', __('Pays de résidence')); ?></th>
                            <th><?php echo $this->Paginator->sort('active', __('Compte')); ?></th>
                            <th><?php echo $this->Paginator->sort('emailConfirm', __('Email')); ?></th>
                            <th><?php echo $this->Paginator->sort('status', __('Statut')); ?></th>
                            <th colspan="3"><?php echo __('Actions'); ?></th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['User']['firstname'].' '.$user['User']['lastname']; ?></td>
                                <td><?php echo $user['User']['pseudo']; ?></td>
                                <td class="hidden-phone hidden-tablet"><?php echo $user['User']['email']; ?></td>
                                <td><?php echo $user['User']['phone_number']; ?></td>
                                <td><?php echo $user['User']['phone_number2']; ?></td>
                                <td><?php echo $user['User']['phone_mobile']; ?></td>
                                <td><?php echo $user['User']['phone_api_use']; ?></td>
                                <td><?php echo $user['User']['agent_number']; ?></td>
                                <td class="hidden-phone hidden-tablet"><?php echo $this->Time->format($user['User']['date_add'],'%d/%m/%Y %H:%I:%S'); ?></td>
                                <td class="hidden-phone hidden-tablet"><?php echo $user['User']['city']; ?></td>
                                <td><?php echo $user['User']['country_id']; ?></td>
                                <td>
                                    <?php
                                        if($user['User']['active'] == 1 && $user['User']['valid'] == 1)
                                            echo '<span class="badge badge-success">'.__('Compte activé').'</span>';
                                        elseif(empty($user['User']['date_lastconnexion']))
                                            echo '<span class="badge badge-warning">'.__('Compte non validé').'</span>';
                                        else
                                            echo '<span class="badge badge-danger">'.__('Compte désactivé').'</span>';
                                    ?>
                                </td>
                                <td>
                                    <?php if($user['User']['emailConfirm'] == 0):
                                        echo '<span class="badge badge-warning">'.__('Email non confirmé').'</span>';
                                        if(isset($this->request->query['email'])): ?>
                                            <div class="btn-group margin-left">
                                                <a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo __('Actions'); ?><span class="caret"></span></a>
                                                <ul class="dropdown-menu">
                                                    <li><?php echo $this->Html->link(
                                                            '<span class="icon-envelope"></span>'.__('Relancer un mail de confirmation'),
                                                            array('controller' => 'agents', 'action' => 'relance_mail_confirm', 'admin' => true, 'id' => $user['User']['id']),
                                                            array('escape' => false),
                                                            __('Voulez-vous vraiment renvoyer un mail de confirmation ?')
                                                        ); ?>
                                                    </li>
                                                    <li><?php echo $this->Html->link(
                                                            '<span class="icon-check"></span>'.__('Confirmer l\'email'),
                                                            array('controller' => 'agents', 'action' => 'confirm_mail', 'admin' => true, 'id' => $user['User']['id']),
                                                            array('escape' => false),
                                                            __('Voulez-vous vraiment forcer la confirmation de l\'adresse mail ?')
                                                        ); ?>
                                                    </li>
                                                </ul>
                                            </div>
                                        <?php endif;
                                    else :
                                        echo '<span class="badge badge-success">'.__('Email confirmé').'</span>';
                                    endif; ?>
                                </td>
                                <td>
								<select class="agent_status_select">
                                	<option value="0_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "0") echo 'selected'; ?>>--</option>
                                	<option value="19_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "19") echo 'selected'; ?>>Contrôle PI & déclaration</option>
                                	<option value="1_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "1") echo 'selected'; ?>>Envoi questionnaire</option>
                                    <option value="2_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "2") echo 'selected'; ?>>Questionnaire reçu</option>
                                    <option value="3_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "3") echo 'selected'; ?>>Experts refusés</option>
                                    <option value="4_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "4") echo 'selected'; ?>>Experts relancé</option>
                                    <option value="5_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "5") echo 'selected'; ?>>Experts sans réponse</option>
                                    <option value="6_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "6") echo 'selected'; ?>>Refus de l'expert</option>
                                    <option value="16_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "16") echo 'selected'; ?>>A faire patienter</option>
                                    <option value="13_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "13") echo 'selected'; ?>>Envoi contrat</option>
                                    <option value="7_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "7") echo 'selected'; ?>>Dossier incomplet Standby</option>
                                    <option value="11_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "11") echo 'selected'; ?>>Expert à controler</option>
                                    <option value="12_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "12") echo 'selected'; ?>>Attente entretien téléphone</option>
									<option value="20_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "20") echo 'selected'; ?>>Ouverture en cours</option>
                                    <option value="8_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "8") echo 'selected'; ?>>Divers</option>
                                    <option value="9_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "9") echo 'selected'; ?>>Expert en ligne</option>
                                    <option value="10_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "10") echo 'selected'; ?>>Expert en pause</option>
                                    <option value="14_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "14") echo 'selected'; ?>>Portage salarial</option>
                                    <option value="17_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "17") echo 'selected'; ?>>En ligne/portage</option>
                                    <option value="15_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "15") echo 'selected'; ?>>Cessation partenariat</option>
                                    <option value="18_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "18") echo 'selected'; ?>>Radié/détournement clients</option>
                                    <option value="21_<?php echo $user['User']['id']; ?>" <?php if($user['User']['status'] == "21") echo 'selected'; ?>>Questionnaire non répondu</option>
                                </select>

								</td>
                                <td>
                                    <?php
                                        echo $this->Html->link('<i class="icon-zoom-in icon_margin_right"> </i> '.__('Voir'),
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
                                <td>
                                    <?php
										if(!empty($user_level) && $user_level != 'moderator'){
                                        echo $this->Html->link('<i class="icon-edit-sign icon_margin_right"> </i> '.__('Modifier'),
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'edit',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'btn green-stripe', 'escape' => false)
                                        );
										}
                                    ?>
                                </td>
                                <td>
                                    <?php
									if(!empty($user_level) && $user_level != 'moderator'){
                                        echo $this->Html->link('<i class="icon-remove-sign icon_margin_right"> </i> '.__('Supprimer le compte'),
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'delete_user',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'btn red-stripe', 'escape' => false),
                                            __('Voulez-vous vraiment supprimer définitivement le compte ?')
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
