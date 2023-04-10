<?php

echo $this->Metronic->titlePage(__('Relances Refusées'));
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
            <div class="caption"><?php echo __('Relances Refusées').' '.$page_title; ?></div>
<div class="pull-right">
                <span class="label-search"><?php //echo __('Recherche') ?></span>
                <?php
                   /* echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false, 'value' => $filtre_texte));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';
*/
                
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
	$html.= '<th>'.__('Refus').'</th>';
	$html.= '<th>'.__('Titre').'</th>';
	$html.= '<th>'.__('Bonjour').'</th>';
    $html.= '<th>'.__('Message').'</th>';
	$html.= '<th>'.__('Signature').'</th>';
    $html.= '<th>'.$this->Paginator->sort('Message.date_add', __('Date d\'envoi')).'</th>';
    $html.= '<th>&nbsp;</th>';
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
		$cut_mesg = explode('###',$message['Message']['content']);	
		$cut_message = explode('<!---->',$cut_mesg[1]);	
		$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
				$html.= $cut_mesg[0]	;
		
		$html.='</div></td>';	
		$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
				$html.= $cut_message[0]	;
		
		$html.='</div></td>';
			
		$html.= '<td><div style="word-wrap: break-word; max-width:200px; padding:10px; font-size:11px">';
				$html.= $cut_message[1]	;
		$html.='</div></td>';	
        
		$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
		
			$html.= $cut_message[2]	;
		
		$html.='</div></td>';
$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
		
				$html.= $cut_message[3]	;
		
		$html.='</div></td>';
        $html.= '<td>'.$message['Message']['date_add'].'</td>';
		$html .= '<td><input type="hidden" class="AdminMessageid" value="'.$message['Message']['id'].'" /><input class="btn green btnvalidaterelancerefus" type="button" value="Rétablir" /></td>';
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