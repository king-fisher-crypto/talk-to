<?php

echo $this->Metronic->titlePage(__('Mails payants'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Mails payants'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Mails remboursés').' '.$page_title; ?></div>
<div class="pull-right">
               <!-- <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false, 'value' => $filtre_texte));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>-->
            </div>
        </div>
        <div class="portlet-body">

<?php
$html = '';
if(empty($messages)) :
    $html.= __('Pas de page');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('Message.id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('From.lastname', __('De')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('To.lastname', __('Pour')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Message.private', __('Type')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Message.content', __('Message')).'</th>';
	$html.= '<th>'.__('Piece jointe').'</th>';
    $html.= '<th>'.$this->Paginator->sort('Message.date_add', __('Date d\'envoi')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Message.archive', __('Archive')).'</th>';
    $html.= '<th>Remboursement</th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($messages AS $message):
$type = '';
switch ($message['Message']['private']) {
    case 0:
        $type = 'Mails payants';
        break;
    case 1:
        $type = 'Messages privés';
        break;
}

$role = 'accounts';
if($message['From']['role'] == 'agent') $role = 'agents';

	$class_ligne = '';
    $html.= '<tr class="'.$class_ligne.'">';
        $html.= '<td>'.$message['Message']['id'].'</td>';
			if($role == 'agents')
			$name = $message['From']['pseudo'];
		else
			$name = $message['From']['lastname'].' '.$message['From']['firstname'];
        $html.= '<td>'.$this->Html->link($name,
                                            array(
                                                'controller' => $role,
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $message['From']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
$role = 'accounts';
if($message['To']['role'] == 'agent') $role = 'agents';	
			if($role == 'agents')
			$name = $message['To']['pseudo'];
		else
			$name = $message['To']['lastname'].' '.$message['To']['firstname'];
        $html.= '<td>'.$this->Html->link($name,
                                            array(
                                                'controller' => $role,
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $message['To']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		$html.= '<td>'.$type.'</td>';
        $html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
			$html.= nl2br($message['Message']['content'])	;
		$html.='</div></td>';
		$html.= '<td>';
			 if(!empty($message['Message']['attachment']))
				// $html.= '<a class="btn btn-primary btn-xs attachment" target="_blank" href="/admin/admins/downloadAttachment-'.$message['Message']['attachment'].'">'.__('Télécharger la pièce jointe').'</a>';
                $html.= $this->Html->link(''.__('Télécharger la pièce jointe'),
                    array(
                        'controller' => 'admins',
                        'action' => 'downloadAttachment',
					'admin' => true,
                        'id' => str_replace('-','_',$message['Message']['attachment'])
                    ),
                    array(
                        'escape' => false,
                        'class' => 'btn btn-primary btn-xs attachment'
                    )
                );
			if(!empty($message['Message']['attachment2']))
				// $html.= '<a class="btn btn-primary btn-xs attachment" target="_blank" href="/admin/admins/downloadAttachment-'.$message['Message']['attachment'].'">'.__('Télécharger la pièce jointe').'</a>';
                $html.= '<br />'.$this->Html->link(''.__('Télécharger la pièce jointe 2'),
                    array(
                        'controller' => 'admins',
                        'action' => 'downloadAttachment',
					'admin' => true,
                        'id' => str_replace('-','_',$message['Message']['attachment2'])
                    ),
                    array(
                        'escape' => false,
                        'class' => 'btn btn-primary btn-xs attachment'
                    )
                );
		$html.= '</td>';	
        $html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$message['Message']['date_add']),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		$html .= '<td>';
			if($message['Message']['archive'] >= 1){
				$html.= 'oui';
			}else{
				$html.= 'non';
			}
		$html.= '</td>';	
		$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$message['UserPenality']['date_add']),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
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