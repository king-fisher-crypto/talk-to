<?php
    echo $this->Metronic->titlePage(__('Bon de réductions'),__('Les bons'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Les coupons'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'vouchers', 'action' => 'index', 'admin' => true))
        ),
        /*
        2 => array(
            'text' => __('Test utilisation coupon (A SUPPRIMER)'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'vouchers', 'action' => 'use_voucher', 'admin' => true))
        )
        */
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les bons de réduction'); ?></div>
            <div class="pull-right">
              <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Vouchers', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('vouchers_title', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Titre').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'
                ?>
                <?php
                    echo $this->Form->create('Vouchers', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('vouchers_code', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Code').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'
                ?>
				
				<?php
                    echo $this->Form->create('Vouchers', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('vouchers_population', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Client').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'
                ?>

            <?php echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les vouchers'),
                array('controller' => 'vouchers', 'action' => 'export_voucher', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); ?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($coupons)) :
                echo __('Aucun bon de réduction');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo $this->Paginator->sort('Voucher.id', __('ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.title', __('Titre')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.code', __('Code')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.validity_start', __('Début de validité')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.validity_end', __('Fin de validité')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.active', __('Etat')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.credit', __('Nombre de crédit')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.amount', __('Remise')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.population', __('Clients pouvant l\'utiliser')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.number_use', __('Nombre d\'utilisation')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.number_use_by_user', __('Nbre d\'utilisation/client')); ?></th>
                        <th><?php echo $this->Paginator->sort('Voucher.user_id', __('Créé par')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($coupons as $coupon): ?>
                        <tr>
							<td><?php echo $coupon['Voucher']['id']; ?></td>
                            <td><?php echo $coupon['Voucher']['title']; ?></td>
                            <td><?php echo $coupon['Voucher']['code']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$coupon['Voucher']['validity_start']),'%d %B %Y %Hh%M'); ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$coupon['Voucher']['validity_end']),'%d %B %Y %Hh%M'); ?></td>
                            <td><?php echo ($coupon['Voucher']['active'] == 1
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?></td>
                            <td><?php echo $coupon['Voucher']['credit']; ?></td>
                            <td><?php


                                if ((int)$coupon['Voucher']['credit'] > 0){
                                    echo '+'.$coupon['Voucher']['credit'].' credits';
                                }elseif ((float)$coupon['Voucher']['amount'] > 0){
                                    echo $this->Nooxtools->displayPrice($coupon['Voucher']['amount'], 'all');
                                }elseif ((float)$coupon['Voucher']['percent'] > 0){
                                    echo $coupon['Voucher']['percent'].'%';
                                }


                                ?></td>
                            <td><?php echo (empty($coupon['Voucher']['population'])
                                    ?__('Tous les clients')
                                    :substr($coupon['Voucher']['population'],0,50)
                                ); ?></td>
                            <td><?php echo ($coupon['Voucher']['number_use'] == 0
                                    ?__('Illimité')
                                    :$coupon['Voucher']['number_use']
                                ); ?></td>
                            
                            <td><?php echo ($coupon['Voucher']['number_use_by_user'] == 0
                                    ?__('Illimité')
                                    :$coupon['Voucher']['number_use_by_user']
                                ); ?></td>
                            <td><?php echo $coupon['User']['firstname']; ?></td>    
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'vouchers', 'action' => 'edit', 'admin' => true, 'code' => $coupon['Voucher']['code']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($coupon['Voucher']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'vouchers', 'action' => 'deactivate', 'admin' => true, 'code' => $coupon['Voucher']['code']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'vouchers', 'action' => 'activate', 'admin' => true, 'code' => $coupon['Voucher']['code']),
                                            'btn green',
                                            'icon-add'
                                        )
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