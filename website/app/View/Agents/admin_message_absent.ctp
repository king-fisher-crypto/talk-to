<?php
echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
echo $this->Html->script('/theme/default/js/admin_record_audio', array('block' => 'script'));
echo $this->Metronic->titlePage(__('Agents'),__('Messages'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Agents'),
        'classes' => 'icon-user-md',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
    ),
    2 => array(
        'text' => __('Messages'),
        'classes' => 'icon-headphones',
        'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'message_absent', 'admin' => true))
    )
));

echo $this->Session->flash(); ?>




<?php if(empty($LastMessage)):
    echo '<br/><br/><br/><div>'.__('Il n\'y a aucun enregistrement.').'</div>';
else : ?>
    <table class="table table-striped table-hover table-bordered">
        <thead>
        <tr>
            <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Expert')); ?></th>
            <th><?php echo $this->Paginator->sort('User.firstname', __('Message')); ?></th>
            <th><?php echo $this->Paginator->sort('Record.date_add', __('Enregistré le')); ?></th>
            <th><?php echo $this->Paginator->sort('Record.sessionid', __('Status')); ?></th>
            <th><?php echo __('Actions'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($LastMessage as $k => $row): ?>
            <tr>
                <td><?php echo $this->Html->link($row['Agent']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent']['id'], 'full_base' => true)); ?></td>
                <td><?php echo $row['AgentMessage']['last_message']; ?></td>
                <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['AgentMessage']['date_add']),'%d %B %Y %H:%M'); ?></td>
                <td><?php 
					
					if($row['AgentMessage']['status'] == 'Envoyer') echo 'Nouveau message';
					if($row['AgentMessage']['status'] == '') echo 'Nouveau message';
					if($row['AgentMessage']['status'] == 'Vu') echo 'Vu';
					if($row['AgentMessage']['status'] == 'Marquer non lu') echo 'Non vu';
					 ?></td>
                <td><?php
                    echo$this->Metronic->getLinkButton(
                            __('Vu'),
                            array('controller' => 'agents','action' => 'message_status_vu', 'admin' => true, 'id' => $row['AgentMessage']['id']),
                            'btn blue',
                            'icon-ok').' '
                        .$this->Metronic->getLinkButton(
                            __('Marquer non lu'),
                            array('controller' => 'agents','action' => 'message_status_lu', 'admin' => true,'id' => $row['AgentMessage']['id']),
                            'btn green',
                            'icon-remove-sign').' '
                        .$this->Metronic->getLinkButton(
                            __('Supprimer'),
                            array('controller' => 'agents', 'action' => 'delete_message', 'admin' => true, 'id' => $row['AgentMessage']['id']),
                            'btn red',
                            'icon-remove',
                            __('Supprimer définitivement l\'enregistrement audio ?')
                        );

                    ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
<?php endif; ?>
