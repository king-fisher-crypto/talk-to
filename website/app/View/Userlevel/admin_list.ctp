<?php
    echo $this->Metronic->titlePage(__('Level'),__('Les levels'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Levels'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'userlevel', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les administrateurs'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($levels)) :
                echo __('Aucun level');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Userlevel.id', __('ID'));; ?></th>
                        <th><?php echo $this->Paginator->sort('User.firstname', __('Nom'));; ?></th>
                        <th><?php echo $this->Paginator->sort('Userlevel.level', __('Level')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($levels as $level): ?>
                        <tr>
                            <td><?php echo __('Utilisateur').' - '.$level['Userlevel']['id']; ?></td>
                            <td><?php echo $level['User']['firstname']; ?></td>
                            <td><?php echo $level['Userlevel']['level']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'userlevel', 'action' => 'edit', 'admin' => true, 'id' => $level['Userlevel']['id']),
                                        'btn blue',
                                        'icon-edit'
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