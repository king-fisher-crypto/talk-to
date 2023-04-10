<?php
    echo $this->Metronic->titlePage(__('Contenu'),__('Les numéros'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Numéro'),
            'classes' => 'icon-bell',
            'link' => $this->Html->url(array('controller' => 'phones', 'action' => 'list', 'admin' => true))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Les numéros'); ?></div>
        </div>
        <div class="portlet-body">
            <?php if(empty($phones)) :
                echo __('Aucun numéro');
            else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.coutry_id', __('Pays')); ?></th>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.lang_id', __('Langue')); ?></th>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.surtaxed_phone_number', __('Téléphone surtaxé')); ?></th>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.surtaxed_minute_cost', __('Coût de la minute surtaxé')); ?></th>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.prepayed_phone_number', __('Téléphone prépayé')); ?></th>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.prepayed_minute_cost', __('Coût de la minute prépayé')); ?></th>
                        <th><?php echo $this->Paginator->sort('CountryLangPhone.prepayed_second_credit', __('Nombre de secondes pour un crédit')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($phones as $phone): ?>
                        <tr>
                            <td><?php echo $phone['CountryLang']['name']; ?></td>
                            <td><?php echo $phone['Lang']['name']; ?></td>
                            <td><?php echo $phone['CountryLangPhone']['surtaxed_phone_number']; ?></td>
                            <td><?php echo $phone['CountryLangPhone']['surtaxed_minute_cost']; ?></td>
                            <td><?php echo $phone['CountryLangPhone']['prepayed_phone_number']; ?></td>
                            <td><?php echo $phone['CountryLangPhone']['prepayed_minute_cost']; ?></td>
                            <td><?php echo $phone['CountryLangPhone']['prepayed_second_credit']; ?></td>
                            <td><?php echo $this->Metronic->getLinkButton(
                                    __('Modifier')   ,
                                    array('controller' => 'phones', 'action' => 'edit', 'admin' => true, 'country' => $phone['CountryLangPhone']['country_id'], 'lang' => $phone['CountryLangPhone']['lang_id']),
                                    'btn blue',
                                    'icon-edit'
                                ); ?>
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