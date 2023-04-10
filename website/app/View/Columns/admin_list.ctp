<?php
    echo $this->Metronic->titlePage(__('Colonne'),__('Les éléments de la colonne'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Colonne'),
            'classes' => 'icon-th-large',
            'link' => $this->Html->url(array('controller' => 'columns', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les éléments de la colonne'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($column)) :
                echo __('Aucun élément');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('LeftColumn.id', __('Elément')); ?></th>
                        <th><?php echo __('Nom') ?></th>
                        <th><?php echo $this->Paginator->sort('LeftColumn.position', __('Position')); ?></th>
                        <th><?php echo $this->Paginator->sort('LeftColumn.validity_start', __('Début de validité')); ?></th>
                        <th><?php echo $this->Paginator->sort('LeftColumn.validity_end', __('Fin de validité')); ?></th>
                        <th><?php echo $this->Paginator->sort('LeftColumn.active', __('Etat')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($column as $block): ?>
                        <tr>
                            <td><?php echo __('Element').' - '.$block['LeftColumn']['id']; ?></td>
                            <td><?php echo (empty($block['LeftColumnLang']['title']) ?__('Pas de nom'):$block['LeftColumnLang']['title']); ?></td>
                            <td><?php echo $block['LeftColumn']['position']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$block['LeftColumn']['validity_start']),'%d %B %Y') ?></td>
                            <td>
                                <?php
                                    if(empty($block['LeftColumn']['validity_end']))
                                        echo __('Sans limite.');
                                    else
                                        echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$block['LeftColumn']['validity_end']),'%d %B %Y')
                                ?>
                            </td>
                            <td><?php echo ($block['LeftColumn']['active'] == 1
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'columns', 'action' => 'edit', 'admin' => true, 'id' => $block['LeftColumn']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($block['LeftColumn']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'columns', 'action' => 'deactivate', 'admin' => true, 'id' => $block['LeftColumn']['id']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'columns', 'action' => 'activate', 'admin' => true, 'id' => $block['LeftColumn']['id']),
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