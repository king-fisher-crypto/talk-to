<?php
    echo $this->Metronic->titlePage(__('Visite'),__('Visite'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Les visites'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'crm', 'action' => 'agent_view', 'admin' => true))
        ),
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les visites'); ?></div>
            <div class="pull-right">
              <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Crm', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                   // echo $this->Form->input('vouchers_title', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Titre').' :', 'div' => false));
                   // echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'
                ?>
                <?php
                   /* echo $this->Form->create('Vouchers', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('vouchers_code', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Code').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'*/
                ?>

            <?php /*echo $this->Metronic->getLinkButton(
                __('Export CSV de tous les vouchers'),
                array('controller' => 'vouchers', 'action' => 'export_voucher', 'admin' => true),
                'btn blue pull-right',
                'icon-file'
            ); */?>
            </div>
        </div>
        <div class="portlet-body">
            <?php if(empty($views)) :
                echo __('Aucune visite');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('AgentView.id', __('ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('AgentView.date_view', __('Date')); ?></th>
                        
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                         <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Agent')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($views as $view): ?>
                        <tr>
                            <td><?php echo $view['AgentView']['id']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$view['AgentView']['date_view']),'%d %B %Y %Hh%M'); ?></td>
                            <td><?php echo $this->Html->link($view['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $view['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ) ?></td>
                           <td><?php echo $this->Html->link($view['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $view['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ) ?></td>
                            
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>