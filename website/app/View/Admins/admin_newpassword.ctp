<?php

echo $this->Metronic->titlePage(__('Mot de passe'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Mot de passe'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Mot de passe').' '.$page_title; ?></div>
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('mail', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Email').' :', 'div' => false, 'value' => $filtre_mail));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body">

<?php
$html = '';
if(empty($users)) :
    $html.= __('Pas de page');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('User.id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('User.firstname', __('User')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('User.email', __('Email')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('User.last_passwd_gen', __('Date')).'</th>';		
	$html.= '<th>'.$this->Paginator->sort('User.forgotten_password', __('Lien')).'</th>';
    $html.= '<th></th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($users AS $user):

$role = 'accounts';
if($user['User']['role'] == 'agent') $role = 'agents';



    $html.= '<tr>';
        $html.= '<td>'.$user['User']['id'].'</td>';
		if($role == 'agents')
			$name = $user['User']['pseudo'];
		else
			$name = $user['User']['lastname'].' '.$user['User']['firstname'];
        $html.= '<td>'.$this->Html->link($name,
                                            array(
                                                'controller' => $role,
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $user['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		$html.= '<td>'.$user['User']['email'].'</td>';
			
		$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$user['User']['last_passwd_gen']),'%d/%m/%y %Hh%Mmin%Ss').'</td>';	
			
		$html.= '<td>'.Router::url('/', true).'users/newpasswd?key='.$user['User']['forgotten_password'].'</td>';
			
		$html.= '</tr>';
			

endforeach;



$html.= '</tbody></table>';
if($this->Paginator->param('pageCount') > 1) :
    $html.= $this->Metronic->pagination($this->Paginator);
endif;


echo $html;
?>
</div>
    </div>
</div>