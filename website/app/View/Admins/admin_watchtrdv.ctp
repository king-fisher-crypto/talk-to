<?php

echo $this->Metronic->titlePage(__('RDV Clients'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('RDV Clients'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('RDV Clients').' '.$page_title; ?></div>
<div class="pull-right">
             <!--   <span class="label-search"><?php echo __('Recherche') ?></span>-->
                <?php
                   /* echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false, 'value' => $filtre_texte));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';*/

                
                ?>
            </div>
        </div>
        <div class="portlet-body">

<?php
$html = '';
if(empty($appointments)) :
    $html.= __('Pas de page');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('CustomerAppointment.id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('User.firstname', __('Client')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Agent.pseudo', __('Agent')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('CustomerAppointment.J', __('Date')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('CustomerAppointment.txt', __('Reponse')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('CustomerAppointment.valid', __('Etat')).'</th>';
    $html.= '<th></th>';
    $html.= '</tr></thead><tbody>';
endif;

foreach ($appointments AS $appointment):
	$class_ligne = '';
	if($appointment['CustomerAppointment']['status'] == 0){
		$class_ligne = 'alerte';	
		$html.= $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
	}
	 $html.= '<tr class="'.$class_ligne.'">';
        $html.= '<td>'.$appointment['CustomerAppointment']['id'].'</td>';
		$html.= '<td>'.$this->Html->link($appointment['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $appointment['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		$html.= '<td>'.$this->Html->link($appointment['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $appointment['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
	$html.= '<td>';
		$timestamp = mktime($appointment['CustomerAppointment']["H"],$appointment['CustomerAppointment']["Min"],0,$appointment['CustomerAppointment']["M"],$appointment['CustomerAppointment']["J"],$appointment['CustomerAppointment']["A"]);
		$date = $this->Time->format(date('Y/m/d H:i:s',$timestamp),'%d/%m/%y %Hh%Mmin%Ss');
		$html.= $date;
	$html.= '</td>';
	$html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">';
		if($appointment['CustomerAppointment']['status'] == 0){
			$contenu = 'Mots détéctés : ';
			$content_text = $appointment['CustomerAppointment']['txt'];
			foreach($filtres as $filtre){
				if(substr_count($appointment['CustomerAppointment']['txt'], $filtre["FiltreMessage"]["terme"]))
					$contenu .= $filtre["FiltreMessage"]["terme"].' , ';
					$content_text = str_ireplace($filtre["FiltreMessage"]["terme"],'<b style="color:#ff0000">'.$filtre["FiltreMessage"]["terme"].'</b>',$content_text );
			}
			
			$html.= $contenu.'<br /><div id="AdminContentTxt" class=" input-small" style="background:#fff;width:350px;height:auto;padding:5px;font-weight:normal;" onclick="showTextareaMessage(this)">'.$content_text.'</div>';
			$html.= '<br /><textarea id="AdminContent" class=" input-small margin-left margin-right" name="data[Admin][content]" style="display:none;width:350px;overflow: hidden;height:auto;" onclick="textAreaAdjust(this)">'.$appointment['CustomerAppointment']['txt'].'</textarea>';
		}else{
			$html.= nl2br($appointment['CustomerAppointment']['txt'])	;
		}
	$html.= '<td>';
	if($appointment['CustomerAppointment']['valid'] && $appointment['CustomerAppointment']['status']) $html.= 'Confirmé';
	else{
		if($appointment['CustomerAppointment']['status'])
			$html.= 'En attente';
		else
			$html.= 'A modéré';
	}
	
	$html.= '</td>';
	$html .= '<td>';
		if($appointment['CustomerAppointment']['status'] == 0){
			
			 $html .=  '<input type="hidden" name="AdminRDVid" value="'.$appointment['CustomerAppointment']['id'].'" /><input class="btn green" type="submit" value="Valider ainsi et l\'envoyer" />';
			
		}
        $html.= '</td>';
	 $html.= '</tr>';
	 if($appointment['CustomerAppointment']['status'] == 0){
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