<?php
echo $this->Metronic->titlePage(__('Questionnaires'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Questionnaires').' '.$page_title, 'classes' => 'icon-person'
    )
));

echo $this->Session->flash();


?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
<div class="pull-right">
  <span class="label-search"><?php echo __('Recherche') ?></span>
    <?php
    echo $this->Form->create('filter_lu', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    $options = array(
        '' => __('Lu / Non Lu'),
        '1' => __('Lu'),
        '2' => __('Non Lu')
    );

    echo $this->Form->select('filter_lu', $options, array('id' => 'filter_lu', 'class' => 'form-control', 'empty' => false));

    echo $this->Form->create('filter_traiter', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));

    $options = array(
        '0' => __('A traiter'),
        '1' => __('Oui'),
        '2' => __('Non')
    );

    echo $this->Form->select('filter_traiter', $options, array('id' => 'filter_traiter', 'class margin-left margin-right' => 'form-control', 'empty' => false));

    echo '<input class="btn green" type="submit" value="Ok">';
    echo '</form>'
    ?>

    <?php
    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
    echo $this->Form->input('agent', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Agent').' :', 'div' => false));
    echo '<input class="btn green" type="submit" value="Ok" /></form>';

    ?>

    <?php echo $this->Metronic->getLinkButton(
        __('Export CSV non lu '),
        array('controller' => 'agents', 'action' => 'export_vu', 'admin' => true),
        'btn blue pull-right',
        'icon-file'
    ); ?>
            </div>
    </div>
    <div class="portlet-body">
        <?php if(empty($surveys)): ?>
            <?php echo __('Pas de resultat'); ?>
        <?php else: ?>
            <table class="table table-striped table-hover table-bordered">
                <thead>
                <tr>
                    <th><?php echo $this->Paginator->sort('Survey.id', __('#')); ?></th>
                    <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Agent')); ?></th>
                    <th><?php echo $this->Paginator->sort('Survey.date_add', __('Date envoi')); ?></th>
                    <th><?php echo $this->Paginator->sort('Survey.is_view', __('Statut')); ?></th>
					<th><?php echo $this->Paginator->sort('Survey.date_valid', __('Date réponse')); ?></th>
					<th><?php echo $this->Paginator->sort('Survey.status', __('Traité')); ?></th>
                    <th><?php echo $this->Paginator->sort('Survey.is_valid', __('Compte Validé')); ?></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($surveys as $k => $row): ?>
                    <tr>
                        <td><?php echo $row['Survey']['id']; ?></td>
                        <td><?php


                            echo $this->Html->link('<span class="icon-user"></span> '.$row['Agent']['pseudo'].' - '.$row['Agent']['firstname'].' '.$row['Agent']['lastname'], array(
                                'controller' => 'agents',
                                'action'     => 'view',
                                'admin'      => true,
                                'id'         => $row['Agent']['id']
                            ), array(
                                'target' => '_blank',
                                'title'  => __('Voir la fiche client #'.$row['Agent']['id']),
                                'escape' => false
                            ));



                            ?></td>

						<td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Survey']['date_add']),'%d %B %Y %H:%M'); ?></td>

                        <td><?php
							if($row['Survey']['is_view']) echo 'Lu par l\'expert'; else echo 'Non lu';
							?>
						</td>
						<td><?php if($row['Survey']['is_respons'])echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Survey']['date_valid']),'%d %B %Y %H:%M'); ?></td>

						 <td><?php
							if($row['Survey']['status'] > 0 ) echo 'Oui'; else echo 'Non';
							?>
						</td>

                        <td><?php
							if($row['Survey']['is_valid']) echo 'Oui'; else echo 'Non';
							?>
						</td>


                        <td>
                            <?php if ((int)$row['Survey']['is_valid'] == 0): ?>
                                <div class="btn-group margin-left">
                                    <a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo __('Actions'); ?><span class="caret"></span></a>
                                    <ul class="dropdown-menu">

                                        <li><?php echo $this->Html->link(
                                        '<span class="icon-zoom-in"></span>'.__('Voir'),
                                        array('controller' => $this->request->controller, 'action' => 'survey_view', 'admin' => true, 'id' => $row['Survey']['id']),
                                        array('escape' => false)
                                    ); ?></li>
										<?php if ((int)$row['Survey']['is_respons'] == 0 && (int)$row['Survey']['is_view'] == 1): ?>
										 <li><?php echo $this->Html->link(
                                        '<span class="icon-edit-sign"></span>'.__('Remettre en Non Lu'),
                                        array('controller' => $this->request->controller, 'action' => 'survey_modify_not_view', 'admin' => true, 'id' => $row['Survey']['id']),
                                        array('escape' => false)
                                    ); ?></li>
										<?php endif; ?>
										<?php //if ((int)$row['Survey']['status'] == 0): ?>
										 <li><?php echo $this->Html->link(
                                        '<span class="icon-edit-sign"></span>'.__('Modifier'),
                                        array('controller' => $this->request->controller, 'action' => 'survey_modify', 'admin' => true, 'id' => $row['Survey']['id']),
                                        array('escape' => false)
                                    ); ?></li>
										<?php //endif; ?>
										<?php if ((int)$row['Survey']['status'] == 0): ?>
										 <li><?php echo $this->Html->link(
                                        '<span class="icon-check"></span>'.__('Traité'),
                                        array('controller' => $this->request->controller, 'action' => 'survey_done', 'admin' => true, 'id' => $row['Survey']['id']),
                                        array('escape' => false)
                                    ); ?></li>
										 <?php endif; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
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
