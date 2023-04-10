<?php

echo $this->Metronic->titlePage(__('Relances Agents'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Relances Agents'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Relances Agents').' '.$page_title; ?></div>
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false, 'value' => $filtre_texte));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body">
			<p id="relance_envoi_status" style="display:none;padding:5px 10px;text-align:right;border:1px solid #000;">Mails envoyés avec succes</p>
<?php
$html = '';
if(empty($messages)) :
    $html.= __('Pas de page');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('Message.id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('From.lastname', __('De')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('To.lastname', __('Pour')).'</th>';
	$html.= '<th>'.__('Titre').'</th>';
	$html.= '<th>'.__('Bonjour').'</th>';
    $html.= '<th>'.__('Message').'</th>';
	$html.= '<th>'.__('Signature').'</th>';
    $html.= '<th>'.$this->Paginator->sort('Message.date_add', __('Date d\'envoi')).'</th>';
	//$html.= '<th>'. __('Dernier envoi').'</th>';
    $html.= '<th><input class="btn green" value="Tout envoyer" type="button" id="AdminRelanceBtnValidateAll"><br /><input class="btn red" value="Tout refuser" type="button" id="AdminRelanceBtnRefuseAll"></th>';
	$html.= '<th><input type="checkbox" id="AdminRelanceCheckAll" /></th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($messages AS $message):

$role = 'accounts';
if($message['From']['role'] == 'agent') $role = 'agents';
			$is_suspect = false;
foreach($filtres as $filtre){
				if(substr_count($message['Message']['content'], $filtre["FiltreMessage"]["terme"]))
					$is_suspect = true;
			}
			
	$class_ligne = '';
	if($message['Message']['etat'] == 2){
		if($is_suspect)
		$class_ligne = 'alerte';	
		else
		$class_ligne = 'todo';	
	}
    $html.= '<tr class="'.$class_ligne.' ligne_'.$message['Message']['id'].'" >';
        $html.= '<td>'.$message['Message']['id'].'</td>';
        $html.= '<td>'.$this->Html->link($message['From']['pseudo'],
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
        $html.= '<td>'.$this->Html->link($message['To']['firstname'],
                                            array(
                                                'controller' => $role,
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $message['To']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		$cut_message = explode('<!---->',$message['Message']['content']);	
			
		$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
		if($message['Message']['etat'] == 2){
		$html.= '<textarea class="AdminContentTitre" class="input-small margin-left margin-right" name="data[Admin][content_titre]" style="width:350px;overflow: hidden;height:auto;" onclick="textAreaAdjust(this)" rel="'.$message['Message']['id'].'">'.$cut_message[0].'</textarea>';
			}else{
				$html.= $cut_message[0]	;
			}
		
		$html.='</div></td>';
			
		$html.= '<td><div style="word-wrap: break-word; max-width:200px; padding:10px; font-size:11px">';
		if($message['Message']['etat'] == 2){
		$html.= '<textarea class="AdminContentBonjour" class="input-small margin-left margin-right" name="data[Admin][content_titre]" style="width:180px;overflow: hidden;height:auto;" onclick="textAreaAdjust(this)" rel="'.$message['Message']['id'].'">'.$cut_message[1].'</textarea>';
			
			}else{
				$html.= $cut_message[0]	;
			}
		
		$html.='</div></td>';	
        
		$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
		if($message['Message']['etat'] == 2){
			if($is_suspect){
			$contenu = 'Mots détéctés : ';
			foreach($filtres as $filtre){
				if(substr_count($cut_message[2], $filtre["FiltreMessage"]["terme"]))
					$contenu .= $filtre["FiltreMessage"]["terme"].' , ';
			}
				$html.= $contenu.'<br /><textarea id="AdminContent" class="input-small margin-left margin-right" name="data[Admin][content]" style="width:350px;overflow: hidden;height:auto;" onclick="textAreaAdjust(this)" rel="'.$message['Message']['id'].'">'.$cut_message[2].'</textarea>';
			}else{
				$html.= '<textarea class="AdminContent" class="input-small margin-left margin-right" name="data[Admin][content]" style="width:350px;overflow: hidden;height:auto;" onclick="textAreaAdjust(this)" rel="'.$message['Message']['id'].'">'.$cut_message[2].'</textarea>';
			}
			$html.= '<br />'.$message['Message']['statut'];
		}else{
			$html.= $cut_message[2]	;
		}
		
		$html.='</div></td>';
$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
		if($message['Message']['etat'] == 2){
		$html.= '<textarea class="AdminContentSignature" class="input-small margin-left margin-right" name="data[Admin][content_titre]" style="width:350px;overflow: hidden;height:auto;" onclick="textAreaAdjust(this)" rel="'.$message['Message']['id'].'">'.$cut_message[3].'</textarea>';
			}else{
				$html.= $cut_message[3]	;
			}
		
		$html.='</div></td>';
        $html.= '<td>'.$message['Message']['date_add'].'</td>';
		//	$html.= '<td>'.$message['Message']['date_last_send'].'</td>';
		$html .= '<td>';
		
                        if($message['Message']['etat'] == 2){
                                $html .=  '<input type="hidden" class="AdminMessageid" value="'.$message['Message']['id'].'" /><input class="btn green btnvalidaterelance" type="button" value="Valider" /><br /><input class="btn red refuse_relance" type="button" value="Refuser" />';
		}
        $html.= '</td>';
			if($message['Message']['statut_send'])
				$html.= '<td><input type="checkbox" class="AdminRelanceCheckbox" name="AdminRelanceCheckAll_'.$message['Message']['id'].'" rel="'.$message['Message']['id'].'" /><br />'.$message['Message']['statut_send'].'</td>';	
				else
		$html.= '<td><input type="checkbox" class="AdminRelanceCheckbox" name="AdminRelanceCheckAll_'.$message['Message']['id'].'" rel="'.$message['Message']['id'].'" /></td>';
		 
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
<div id="dialog-refus-reance-mail" title="Refuser une relance" style="display:none">
	<p>
		<textarea class="relance_refus_reason" style="width:385px;height:150px" placeholder="Raison du refus"></textarea>
	</p>
	<p>&nbsp;</p>
</div>