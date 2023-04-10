<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));

    echo $this->Metronic->titlePage(__('Client'),__('Liste des clients'));
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
        )
    ));

    echo $this->Session->flash();
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInputClient($consult_accounts); ?>
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Liste clients'); ?></div>
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('personal_code', array('class' => 'input-mini  margin-left margin-right', 'type' => 'text', 'label' => __('Code client').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
                    echo '</form>'
                ?>
                <?php
                    echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('fullname', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'label' => __('Nom').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';



                    echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'label' => __('E-mail').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
					
					echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('adr_ip', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'label' => __('IP').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
				if(!empty($user_level) && $user_level != 'moderator'){
                echo $this->Html->link('<span class="icon icon-download-alt"></span> Tout exporter',
                    array(
                        'controller' => 'accounts',
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
                        'onclick' => 'document.location.href = \'/admin/accounts/exportcsv\'; return true'
                    ));
				echo $this->Html->link('<span class="icon icon-download-alt"></span> Audiotel',
                    array(
                        'controller' => 'accounts',
                        'action'     => 'exportcsvaudiotel',
                        'admin'      => true
                    ),
                    array(
                        'style' => 'margin-left:20px',
                        'class' => 'btn',
                        'type' => 'button',
                        'label' => false,
                        'div' => false,
                        'escape' => false,
                        'onclick' => 'document.location.href = \'/admin/accounts/exportcsvaudiotel\'; return true'
                    ));	
				}
                ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($users)): ?>
                <?php echo __('Aucun client'); ?>
            <?php else: ?>
                <div class="row-fluid">
                    <?php if(isset($this->request->query['email'])): ?>
                        <p class="badge badge-info">
                            <span class="icon-info-sign icon_margin_right"></span>
                            <?php echo __('Liste des clients qui n\'ont pas confirmé leurs adresses mails'); ?>
                        </p>
                    <?php elseif (isset($this->request->query['compte'])): ?>
                        <p class="badge badge-info">
                            <span class="icon-info-sign icon_margin_right"></span>
                            <?php echo __('Liste des clients dont le compte n\'est pas activé'); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('firstname', __('Nom complet')); ?></th>
                        <th><?php echo $this->Paginator->sort('personal_code', __('Code personnel')); ?></th>
                        <th><?php echo $this->Paginator->sort('date_add', __('Inscription')); ?></th>
                        <th><?php echo $this->Paginator->sort('source', __('Source')); ?></th>
                        <th><?php echo $this->Paginator->sort('credit', __('Credit')); ?></th>
                        <th><?php echo $this->Paginator->sort('email', __('Email')); ?></th>
                        <th><?php echo $this->Paginator->sort('phone_number', __('Téléphone')); ?></th>
                        <th><?php echo $this->Paginator->sort('city', __('Ville')); ?></th>
                        <th><?php echo $this->Paginator->sort('country_id', __('Pays de résidence')); ?></th>
                        <th><?php echo $this->Paginator->sort('valid', __('Compte')); ?></th>
                        <th><?php echo $this->Paginator->sort('emailConfirm', __('Email')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['User']['firstname'].' '.$user['User']['lastname']; ?></td>
                            <td><?php echo $user['User']['personal_code']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$user['User']['date_add']),'%d %B %Y %H:%I:%S'); ?></td>
                            <td><?php echo $user['User']['source']; ?></td>
                            <td><?php echo $user['User']['credit']; ?></td>
                            <td><?php echo $user['User']['email']; ?></td>
                            <td><?php echo $user['User']['phone_number']; ?></td>
                            <td><?php echo $user['User']['city']; ?></td>
                            <td><?php echo $user['User']['country_id']; ?></td>
                            <td>
                                <?php
                                    if($user['User']['active'] == 1 && $user['User']['valid'] == 1)
                                        echo '<span class="badge badge-success">'.__('Compte activé').'</span>';
                                    elseif(empty($user['User']['firstname']))
                                        echo '<span class="badge badge-warning">'.__('Compte non validé').'</span>';
                                    else
                                        echo '<span class="badge badge-danger">'.__('Compte désactivé').'</span>';
                                ?>
                            </td>
                            <td>
                                <?php if($user['User']['emailConfirm']):
                                    echo '<span class="badge badge-success">'.__('Email confirmé').'</span>';
                                else :
                                    echo '<span class="badge badge-warning">'.__('Email non confirmé').'</span>';
                                    if(isset($this->request->query['email'])): ?>
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
                                    <?php endif;
                                endif; ?>
                            </td>
                            <td>
                                <?php
								if(!empty($user_level) && $user_level != 'moderator'){
                                echo $this->Metronic->getLinkButton(
                                        __('Modifier'),
                                        array('controller' => 'accounts', 'action' => 'edit', 'admin' => true, 'id' => $user['User']['id']),
                                        'btn blue',
                                        'icon-edit'
                                ).' ';
								}
                                 echo $this->Metronic->getLinkButton(
                                        __('Voir'),
                                        array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']),
                                        'btn green',
                                        'icon-zoom-in'
                                ).' '
                                .(isset($this->request->query['compte'])
                                        ?$this->Metronic->getLinkButton(
                                            __('Forcer l\'activation du compte'),
                                            array('controller' => 'accounts', 'action' => 'activate_user', 'admin' => true, 'id' => $user['User']['id']),
                                            'btn green',
                                            'icon-check',
                                            __('Voulez-vous vraiment forcer l\'activation du compte ?'))
                                        :''
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