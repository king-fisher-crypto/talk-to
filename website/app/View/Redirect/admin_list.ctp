<?php
    echo $this->Metronic->titlePage(__('Redirection'),__('Les redirections'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Cout'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'cost', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les redirections'); ?></div>
			
        </div>
        <div class="portlet-body">
           <p>
				Important :<br />
			   Ne pas indiquer <strong>https://www.talkappdev.com</strong> dans ancienne url, exemple : <strong>/fre/la-agents-en-ligne</strong><br />
			   Indiquer toute l url pour l url de redirection , exemple : <strong>https://www.talkappdev.com/fre/la-agents-en-ligne</strong>
			</p>
            <?php if(empty($redirects)) :
                echo __('Aucune redirection');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Redirect.id', __('#')); ?></th>
                        <th><?php echo $this->Paginator->sort('Redirect.type', __('Type')); ?></th>
						<th><?php echo $this->Paginator->sort('Redirect.domain_id', __('Domain')); ?></th>
                        <th><?php echo $this->Paginator->sort('Redirect.old', __('Ancienne URL')); ?></th>
                        <th><?php echo $this->Paginator->sort('Redirect.new', __('Nouvelle URL')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($redirects as $redirect): ?>
                        <tr>
                            <td><?php echo $redirect['Redirect']['id']; ?></td>
                            <td><?php echo $redirect['Redirect']['type']; ?></td>
							<td><?php 
								
								switch ($redirect['Redirect']['domain_id']) {
									case 19:
										echo 'France';
										break;
									case 11:
										echo 'Belgique';
										break;
									case 13:
										echo 'Suisse';
										break;
									case 22:
										echo 'Luxembourg';
										break;
									case 29:
										echo 'Canada';
										break;
								}
								
								 ?></td>
                            <td><?php echo $redirect['Redirect']['old']; ?></td>
                            <td><?php echo $redirect['Redirect']['new']; ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'redirect', 'action' => 'edit', 'admin' => true, 'id' => $redirect['Redirect']['id']),
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