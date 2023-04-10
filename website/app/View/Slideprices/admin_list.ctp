<?php
    echo $this->Metronic->titlePage(__('Slide'),__('Les slides'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Slide'),
            'classes' => 'icon-exchange',
            'link' => $this->Html->url(array('controller' => 'slides', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les slides'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($slideprices)) :
                echo __('Aucun slide');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('slideprice.id', __('Slide'));; ?></th>
                        <th><?php echo $this->Paginator->sort('slidepriceLang.title', __('Nom'));; ?></th>
                        <th><?php echo $this->Paginator->sort('slideprice.position', __('Position')); ?></th>
                        <th><?php echo $this->Paginator->sort('slideprice.validity_start', __('Début de validité')); ?></th>
                        <th><?php echo $this->Paginator->sort('slideprice.validity_end', __('Fin de validité')); ?></th>
                        <th><?php echo $this->Paginator->sort('slideprice.active', __('Etat')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($slideprices as $slide): ?>
                        <tr>
                            <td><?php echo __('Slide').' - '.$slide['Slideprice']['id']; ?></td>
                            <td><?php echo $slide['SlidepriceLang']['title']; ?></td>
                            <td><?php echo $slide['Slideprice']['position']; ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$slide['Slideprice']['validity_start']),'%d %B %Y') ?></td>
                            <td>
                                <?php
                                    if(empty($slide['Slideprice']['validity_end']))
                                        echo __('Sans limite.');
                                    else
                                        echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$slide['Slideprice']['validity_end']),'%d %B %Y')
                                ?>
                            </td>
                            <td><?php  echo ($slide['Slideprice']['active'] == "1"
                                    ?'<span class="badge badge-success">'.__('Active').'</span>'
                                    :'<span class="badge badge-danger">'.__('Inactive').'</span>'
                                ); ?>
                            </td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'slideprices', 'action' => 'edit', 'admin' => true, 'id' => $slide['Slideprice']['id']),
                                        'btn blue',
                                        'icon-edit'
                                    );
                                    echo ($slide['Slideprice']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver'),
                                            array('controller' => 'slideprices', 'action' => 'deactivate', 'admin' => true, 'id' => $slide['Slideprice']['id']),
                                            'btn red',
                                            'icon-remove'
                                        )
                                        :$this->Metronic->getLinkButton(
                                            __('Activer'),
                                            array('controller' => 'slideprices', 'action' => 'activate', 'admin' => true, 'id' => $slide['Slideprice']['id']),
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