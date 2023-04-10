<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/admin_record_audio', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agents'),__('Enregistrement audio'));
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
            'text' => __('Enregistrements audio'),
            'classes' => 'icon-headphones',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'record_audio', 'admin' => true))
        )
    ));

    echo $this->Session->flash(); ?>

    <div style="float: left; margin-right: 10px;">
        <?php //echo $this->Metronic->getDateInput(); ?>
    </div>
    <div class="pull-left div-search">
        <?php
		echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
		echo $this->Form->input('sessionid', array('class' => 'span2  margin-left margin-right', 'type' => 'texte', 'label' => __('Session ID').' :', 'div' => false, 'value' => (isset($this->params->query['sessionid']) ?$this->params->query['sessionid']:false)));
            //echo $this->Form->input('expert', array('class' => 'span2  margin-left margin-right', 'type' => 'texte', 'label' => __('Expert').' :', 'div' => false, 'value' => (isset($this->params->query['expert']) ?$this->params->query['expert']:false)));
		//echo $this->Form->input('timing_min', array('class' => 'span2  margin-left margin-right', 'type' => 'texte', 'label' => __('Temps min (sec.)').' :', 'div' => false, 'value' => (isset($this->params->query['timing_min']) ?$this->params->query['timing_min']:false)));
		//echo $this->Form->input('timing', array('class' => 'span2  margin-left margin-right', 'type' => 'texte', 'label' => __('Temps max (sec.)').' :', 'div' => false, 'value' => (isset($this->params->query['timing']) ?$this->params->query['timing']:false)));
           echo '<input class="btn green" type="submit" value="Ok" /></form>';
        ?>
    </div>

    <?php if(empty($lastRecord)):
        echo '<br/><br/><br/><div>'.__('Il n\'y a aucun enregistrement.').'</div>';
    else : ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo $this->Paginator->sort('Agent.pseudo', __('Expert')); ?></th>
						<th><?php echo $this->Paginator->sort('User.firstname', __('Clients')); ?></th>
                        <th><?php echo $this->Paginator->sort('Record.date_add', __('Enregistré le')); ?></th>
                        <th><?php echo $this->Paginator->sort('Record.sessionid', __('Session ID')); ?></th>
                        <th><?php echo $this->Paginator->sort('Comm.id', __('Durée')); ?></th>
                        <th><?php echo __('Actions'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastRecord as $k => $row): ?>
                        <tr>
							<td><?php echo $this->Html->link($row['Agent']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $row['Agent']['id'], 'full_base' => true)); ?></td>
                            <td><?php echo $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true)); ?></td>
							<td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Record']['date_add']),'%d %B %Y %H:%M'); ?></td>
							<td><?php echo $row['Record']['sessionid']; ?></td>
							<td><?php 
								
								$min = number_format($row['Comm']['seconds'] / 60,0);
								
								echo $min.' min.'; ?></td>
                            
                            <td><?php 
							echo$this->Metronic->getLinkButton(
                        __('Télécharger'),
                        array('controller' => 'agents','action' => 'download_record', 'admin' => true, 'id' => $row['Record']['id']),
                        'btn blue',
                        'icon-download').' '
                    .$this->Metronic->getLinkButton(
                        __('Supprimer'),
                        array('controller' => 'agents', 'action' => 'delete_record', 'admin' => true, 'id' => $row['Record']['id']),
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