<?php
    echo $this->Metronic->titlePage(__('Loyalty'),__('Les programmes fidélités'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Fidélité'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'loyalty', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les programmes fidélités'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($loyalty)) :
                echo __('Aucun programme');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Loyalty.id', __('Programme')); ?></th>
                        <th><?php echo $this->Paginator->sort('Loyalty.product_id', __('Produit')); ?></th>
                        <th><?php echo $this->Paginator->sort('Loyalty.name', __('Nom')); ?></th>
                        <th><?php echo $this->Paginator->sort('Loyalty.pourcent', __('Pourcentage')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($loyalty as $loyal): ?>
                        <tr>
                            <td><?php echo $loyal['Loyalty']['id']; ?></td>
                            <td>
                            <?php
							  if (!empty($products)): 
                              
                                foreach($products as $id => $name):
								
									if($id == $loyal['Loyalty']['product_id'])
                                    echo $name;
                                endforeach;
                        endif;
                        ?></td>
                            <td><?php echo $loyal['Loyalty']['name']; ?></td>
                            <td><?php echo $loyal['Loyalty']['pourcent']; ?>%</td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'loyalty', 'action' => 'edit', 'admin' => true, 'id' => $loyal['Loyalty']['id']),
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