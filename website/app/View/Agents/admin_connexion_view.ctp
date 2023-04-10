<?php
    echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agents'),__('Historique des communications'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Agents'),
            'classes' => 'icon-user-md',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'index', 'admin' => true))
        ),
        2 => array(
            'text' => __('Communications'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'com', 'admin' => true))
        ),
        3 => array(
            'text' => (!isset($user['User']['pseudo']) || empty($user['User']['pseudo'])?__('Agent'):$user['User']['pseudo']),
            'classes' => 'icon-zoom-in',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id']))
        ),
        4 => array(
            'text' => __('Historique des connexions'),
            'classes' => 'icon-archive',
            'link' => $this->Html->url(array('controller' => 'agents', 'action' => 'connexion_view', 'admin' => true, 'id' => $user['User']['id']))
        )
    ));
    echo $this->Session->flash();
    $this->Paginator->options(array('url' => array('id' => $user['User']['id'])));
?>

<div class="row-fluid">
    <?php echo $this->Metronic->getDateInput($consult_medias); ?>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Toutes les connexions').' '.$this->Html->link($user['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $user['User']['id'], 'full_base' => true)); ?></div>
           
        </div>
        <div class="portlet-body">
            <?php if(empty($allConnexions)): ?>
                <?php echo '<p>'.__('Aucune connexion.').'</p>' ?>
            <?php else: ?>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                      <th><?php echo __('Modifié par'); ?></th>
                       <th><?php echo __('Connexion'); ?></th>
                       <th><?php echo __('Statut'); ?></th>
                       <th><?php echo $this->Paginator->sort('UserConnexion.date_connexion', __('Date')); ?></th>
                       <th><?php echo $this->Paginator->sort('UserConnexion.phone', __('Téléphone')); ?></th>
                       <th><?php echo $this->Paginator->sort('UserConnexion.tchat', __('Tchat')); ?></th>
                       <th><?php echo $this->Paginator->sort('UserConnexion.mail', __('Mail')); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php 
					
					foreach ($allConnexions as $k => $row):
						
						$next_line = $allConnexions[$k+1];
						if(!$next_line) $next_line = $row;
						?>
                        <tr>
                            <td><?php 
							   
							   switch ($row['UserConnexion']['who']) {
									case $row['UserConnexion']['user_id']:
									case '':
										echo "agent";
										break;
									  case '1':
										echo "Robot";
										break;
									default:
										echo "admin";
										break;
								}
							    ?></td>
                           <td><?php  
							   
							   if($next_line['UserConnexion']['session_id'] != $row['UserConnexion']['session_id'])echo '<b>';
							   
							   
							  
							   if(!$row['UserConnexion']['status'] && $next_line['UserConnexion']['session_id'] != $row['UserConnexion']['session_id']) echo 'reconnexion';
							   if($row['UserConnexion']['status'] == 'login' && $next_line['UserConnexion']['session_id'] != $row['UserConnexion']['session_id']) echo 'reconnexion par login';
							   if($row['UserConnexion']['status'] == 'login' && $next_line['UserConnexion']['session_id'] == $row['UserConnexion']['session_id']) echo 'connexion par login';
							   if($next_line['UserConnexion']['session_id'] != $row['UserConnexion']['session_id'])echo '</b>';
							   ?></td>
                           <td><?php  
							   
							   if($row['UserConnexion']['status'] )echo '<b>';
							   
							   
							   switch ($row['UserConnexion']['status']) {
									case 'available':
										echo "disponible";
										break;
									case 'unavailable':
										echo "indisponible";
										break;
									
								}
							  
							   if($row['UserConnexion']['status'])echo '</b>';
							   ?></td>
                             <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserConnexion']['date_connexion']),'%d %B %Y %H:%M:%S'); ?></td>
                            <td><?php 
								if($next_line['UserConnexion']['phone'] != $row['UserConnexion']['phone'] && $row['UserConnexion']['phone'] >= 0 && $next_line['UserConnexion']['phone'] == -1)echo '<b>débloqué</b>';
								else{
								if($next_line['UserConnexion']['phone'] != $row['UserConnexion']['phone'])echo '<b>';
								switch ($row['UserConnexion']['phone']) {
									case -1:
										echo "bloqué";
										break;
									case 0:
										echo "non actif";
										break;
									case 1:
										echo "actif";
										break;
								}
								if($next_line['UserConnexion']['phone'] != $row['UserConnexion']['phone'])echo '</b>';
								}
								?></td>
                            <td><?php 
								if($next_line['UserConnexion']['tchat'] != $row['UserConnexion']['tchat'] && $row['UserConnexion']['tchat'] >= 0 && $next_line['UserConnexion']['tchat'] == -1)echo '<b>débloqué</b>';
								else{
								if($next_line['UserConnexion']['tchat'] != $row['UserConnexion']['tchat'])echo '<b>';
								switch ($row['UserConnexion']['tchat']) {
									case -1:
										echo "bloqué";
										break;
									case 0:
										echo "non actif";
										break;
									case 1:
										echo "actif";
										break;
								}
								if($next_line['UserConnexion']['tchat'] != $row['UserConnexion']['tchat'])echo '</b>';
								}
								?></td>
                            <td><?php 
								if($next_line['UserConnexion']['mail'] != $row['UserConnexion']['mail'])echo '<b>';
								switch ($row['UserConnexion']['mail']) {
									case -1:
										echo "bloqué";
										break;
									case 0:
										echo "non actif";
										break;
									case 1:
										echo "actif";
										break;
								}
								if($next_line['UserConnexion']['mail'] != $row['UserConnexion']['mail'])echo '</b>';
								?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                
                <?php if($this->Paginator->param('pageCount') > 1) echo $this->Metronic->pagination($this->Paginator); ?>
            <?php endif; ?>
        </div>
    </div>
</div>