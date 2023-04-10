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
            <div class="caption"><?php echo __('Mails payants').' '.$page_title; ?></div>
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false, 'value' => $filtre_texte));
					echo $this->Form->input('id', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('# ID/Parent ID').' :', 'div' => false, 'value' => $filtre_id));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
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
	$html.= '<th>'.$this->Paginator->sort('Message.parent_id', 'Parent').'</th>';
    $html.= '<th>'.$this->Paginator->sort('From.lastname', __('De')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('To.lastname', __('Pour')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Message.private', __('Type')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Message.content', __('Message')).'</th>';
	$html.= '<th>'.__('Piece jointe').'</th>';
    $html.= '<th>'.$this->Paginator->sort('Message.date_add', __('Date d\'envoi')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Message.date_add', __('Délai réponse (H:i:s)')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Message.archive', __('Archive')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Message.deleted', __('Supprime')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('AdminAction.date_action', __('Temps de validation')).'</th>';
    $html.= '<th></th>';
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
	if($message['Message']['etat'] == 2){
		$class_ligne = 'alerte';	
		$html.= $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
	}
    $html.= '<tr class="'.$class_ligne.'">';
        $html.= '<td>'.$message['Message']['id'].'</td>';
		$html.= '<td>'.$message['Message']['parent_id'].'</td>';
		if($role == 'agents'){
			$name = $message['From']['pseudo'];
			$css_type = "red-stripe";
		}else{
			$name = $message['From']['lastname'].' '.$message['From']['firstname'];
			$css_type = "blue-stripe";
		}
        $html.= '<td>'.$this->Html->link($name,
                                            array(
                                                'controller' => $role,
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $message['From']['id']
                                            ),
                                            array('class' => 'btn '.$css_type, 'escape' => false)
                                        ).'</td>';
$role = 'accounts';
if($message['To']['role'] == 'agent') $role = 'agents';	
			if($role == 'agents'){
			$name = $message['To']['pseudo'];
				$css_type = "red-stripe";
			}else{
			$name = $message['To']['lastname'].' '.$message['To']['firstname'];
				$css_type = "blue-stripe";
			}
        $html.= '<td>'.$this->Html->link($name,
                                            array(
                                                'controller' => $role,
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $message['To']['id']
                                            ),
                                            array('class' => 'btn '.$css_type, 'escape' => false)
                                        ).'</td>';
		$html.= '<td>'.$type.'</td>';
        $html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
			if($filtre_texte){
				$message['Message']['content'] = str_ireplace($filtre_texte,'<span style="background:#d84a38;color:#fff;padding:2px;">'.$filtre_texte.'</span>',$message['Message']['content'] );
			}
			
		if($message['Message']['etat'] == 2){
			$contenu = 'Mots détéctés : ';
			$content_text = $message['Message']['content'];
			foreach($filtres as $filtre){
				if(substr_count(strtolower($message['Message']['content']), strtolower($filtre["FiltreMessage"]["terme"])))
					$contenu .= $filtre["FiltreMessage"]["terme"].' , ';
					$content_text = str_ireplace($filtre["FiltreMessage"]["terme"],'<span style="background:#d84a38;color:#fff;padding:2px;">'.$filtre["FiltreMessage"]["terme"].'</span>',$content_text );
			}
				$html.= $contenu.'<br /><div id="AdminContentTxt" class=" input-small" style="background:#fff;width:350px;height:auto;padding:5px;font-weight:normal;" onclick="showTextareaMessage(this)">'.$content_text.'</div>';
			$html.= '<br /><textarea id="AdminContent" class="input-small margin-left margin-right" name="data[Admin][content]" style="width:350px;overflow: hidden;height:auto;display:none" onclick="textAreaAdjust(this)">'.$message['Message']['content'].'</textarea>';
		}else{
			$html.= nl2br($message['Message']['content'])	;
		}
		
			
		
		
		$html.='</div></td>';
		$html.= '<td>';
			 if(!empty($message['Message']['attachment']))
				// $html.= '<a class="btn btn-primary btn-xs attachment" target="_blank" href="/admin/admins/downloadAttachment-'.$message['Message']['attachment'].'">'.__('Télécharger la pièce jointe').'</a>';
                $html.= $this->Html->link('<img src="/media/attachment/'.$message['Message']['attachment'][0].'/'.$message['Message']['attachment'][1].'/'.$message['Message']['attachment'].'"  style="width:100px;height:auto;" />',
                    array(
                        'controller' => 'admins',
                        'action' => 'downloadAttachment',
					'admin' => true,
                        'id' => str_replace('-','_',$message['Message']['attachment'])
                    ),
                    array(
                        'escape' => false,
                        'class' => 'chat_picture'
                    )
                );
			if(!empty($message['Message']['attachment2']))
				// $html.= '<a class="btn btn-primary btn-xs attachment" target="_blank" href="/admin/admins/downloadAttachment-'.$message['Message']['attachment'].'">'.__('Télécharger la pièce jointe').'</a>';
                $html.= '<br />'.$this->Html->link('<img src="/media/attachment/'.$message['Message']['attachment2'][0].'/'.$message['Message']['attachment2'][1].'/'.$message['Message']['attachment2'].'"  style="width:100px;height:auto;" />',
                    array(
                        'controller' => 'admins',
                        'action' => 'downloadAttachment',
					'admin' => true,
                        'id' => str_replace('-','_',$message['Message']['attachment2'])
                    ),
                    array(
                        'escape' => false,
                        'class' => 'chat_picture'
                    )
                );
		$html.= '</td>';	
        $html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$message['Message']['date_add']),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		$html.= '<td>'.$message['Message']['delay'].'</td>';
		$html .= '<td>';
			if($message['Message']['archive'] >= 1){
				$html.= 'oui';
			}else{
				$html.= 'non';
			}
		$html.= '</td>';
		$html .= '<td>';
			if($message['Message']['deleted'] >= 1){
				$html.= 'oui';
			}else{
				$html.= 'non';
			}
		$html.= '</td>';
			
		if ($message[0]['hasAdminActions']) {
            $html.= '<td>'.$message['Message']['validation_time'].'</td>';
        } else {
            $html.= '<td></td>';
        }	
			
			
		$html .= '<td>';
		
                        if($message['Message']['etat'] == 2){
                                $html .=  '<input type="hidden" name="AdminMessageid" value="'.$message['Message']['id'].'" /><input class="btn green" type="submit" value="Valider ainsi et l\'autoriser" />';
							$html .=  '<br /><br /><input class="btn red deleted_watchmail" type="button" rel="'.$message['Message']['id'].'" value="Supprimer" /><br /><br />';
		}
			if($message['Message']['archive'] == 0){
			$html .=  '<input class="btn red archive_watchmail" type="button" rel="'.$message['Message']['id'].'" value="Annuler alerte" />';
			}
        $html.= '</td>';
    $html.= '</tr>';
	if($message['Message']['etat'] == 2){
		$html.= '</form>';	
	}
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