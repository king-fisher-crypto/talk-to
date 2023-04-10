<?php
    echo $this->Metronic->titlePage(__('Block'),__('Les blocks'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Block'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'block', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les blocks'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($block)) :
                echo __('Aucun block');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Block.id', __('Block')); ?></th>
                        <th><?php echo $this->Paginator->sort('BlockLang.title', __('Nom')); ?></th>
                        <th><?php echo $this->Paginator->sort('Slide.active', __('Etat')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($block as $bloc): 
						?>
                        <tr>
                            <td><?php echo __('Block').' - '.$bloc['Block']['id']; ?></td>
                            <td><?php echo $bloc['BlockLang']['title']; ?></td>
                            <td><?php echo ($bloc['Block']['active'] == 1
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'block', 'action' => 'edit', 'admin' => true, 'id' => $bloc['Block']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($bloc['Block']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'block', 'action' => 'deactivate', 'admin' => true, 'id' => $bloc['Block']['id']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'block', 'action' => 'activate', 'admin' => true, 'id' => $bloc['Block']['id']),
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