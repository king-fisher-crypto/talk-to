<?php
    echo $this->Metronic->titlePage(__('Parrainage'),__('Les regles'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Parrainage'),
            'classes' => 'icon-barcode',
            'link' => $this->Html->url(array('controller' => 'sponsorship', 'action' => 'rules', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les regles'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($sponsorships)) :
                echo __('Aucune regles');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('SponsorshipRule.id', __('#')); ?></th>
						<th><?php echo $this->Paginator->sort('SponsorshipRule.type_user', __('Qui')); ?></th>
                        <th><?php echo $this->Paginator->sort('SponsorshipRule.palier', __('Palier dépense filleul')); ?></th>
                        <th><?php echo $this->Paginator->sort('SponsorshipRule.data', __('Bonus pour parrain')); ?></th>
						<th><?php echo $this->Paginator->sort('SponsorshipRule.data', __('Palier déclenchement bonus')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sponsorships as $sponsorship): ?>
                        <tr>
                            <td><?php echo $sponsorship['SponsorshipRule']['id']; ?></td>
							<td><?php echo $sponsorship['SponsorshipRule']['type_user']; ?></td>
                            <td><?php echo $sponsorship['SponsorshipRule']['palier'].' '.$sponsorship['SponsorshipRule']['palier_type']; ?></td>
                            <td><?php echo $sponsorship['SponsorshipRule']['data'].' '.$sponsorship['SponsorshipRule']['data_type']; ?></td>
							<td><?php if($sponsorship['SponsorshipRule']['palier_declenche']){ echo $sponsorship['SponsorshipRule']['palier_declenche'].' '.$sponsorship['SponsorshipRule']['palier_declenche_type'].' par '.$sponsorship['SponsorshipRule']['declenche'];} ?></td>
                            <td><?php
                                    echo $this->Metronic->getLinkButton(
                                        __('Modifier')   ,
                                        array('controller' => 'sponsorship', 'action' => 'edit', 'admin' => true, 'id' => $sponsorship['SponsorshipRule']['id']),
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