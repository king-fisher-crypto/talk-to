<?php
echo $this->Metronic->titlePage(__('Comptes supprimés'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Comptes supprimés').' '.$page_title, 'classes' => 'icon-user'
    )
));

echo $this->Session->flash();


?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
        <div class="caption"></div>
<div class="pull-right">
            </div>
    </div>
    <div class="portlet-body">
        <?php if(empty($accounts)): ?>
            <?php echo __('Aucun résultat'); ?>
        <?php else: ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('User.id', __('#')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.firstname', __('Client')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.email', __('Email')); ?></th>
                    <th><?php echo $this->Paginator->sort('User.date_upd', __('Date')); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($accounts as $k => $row): ?>
                    <tr>
                        <td><?php echo $row['User']['id']; ?></td>
                        <td><?php


                            echo $this->Html->link('<span class="icon-user"></span> '.$row['User']['firstname'], array(
                                'controller' => 'accounts',
                                'action'     => 'view',
                                'admin'      => true,
                                'id'         => $row['User']['id']
                            ), array(
                                'target' => '_blank',
                                'title'  => __('Voir la fiche client #'.$row['User']['id']),
                                'escape' => false
                            ));



                            ?></td>
                        <td><?php echo $row['User']['email']; ?></td>
                      
                        <td><?php echo $this->Time->format($row['User']['date_upd'],'%d %B %Y %H:%M'); ?></td>
                        <td>
                            &nbsp;
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