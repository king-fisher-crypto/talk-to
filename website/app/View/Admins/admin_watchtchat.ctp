<?php

echo $this->Metronic->titlePage(__('Tchat'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Tchat'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Tchat').' '.$page_title; ?></div>
<div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('client', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Client/Agent').' :', 'div' => false, 'value' => $filtre_client));
					echo $this->Form->input('texte', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Expression').' :', 'div' => false, 'value' => $filtre_texte));
                   $options = array('' => 'Choisir','1' => 'Oui', '0' => 'Non');
				    echo '<label for="AdminRepondu">Répondu :</label>'.$this->Form->select('repondu',$options, array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px'));
					$options = array('' => 'Choisir','0' => 'Normal', '1' => 'Termes indésirables');
				    echo '<label for="AdminEtat">Etat :</label>'.$this->Form->select('etat',$options, array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px'));
					echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body">

<?php
$html = '';
if(empty($chats)) :
    $html.= __('Pas de page');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('Chat.id', '#').'</th>';
    $html.= '<th style="width:10%">'.$this->Paginator->sort('User.lastname', __('De')).'</th>';
    $html.= '<th style="width:10%">'.$this->Paginator->sort('Agent.lastname', __('Pour')).'</th>';
    $html.= '<th style="width:60%">'. __('Message').'</th>';
    $html.= '<th style="width:15%">'.$this->Paginator->sort('Chat.date_start', __('Date debut')).'</th>';
	$html.= '<th style="width:15%">'.$this->Paginator->sort('Chat.consult_date_start', __('Date consult')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Chat.source', __('Source')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Chat.closed_by', __('Coupé par')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Chat.alert', __('Alerte Tel')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('AdminAction.date_action', __('Temps de validation')).'</th>';
	$html.= '<th></th>';
	$html.= '<th><a class="btn red" id="AdminTchatValidAll">Tous accepter</a></th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($chats AS $chat):

$message = '';
$user_id = $chat['User']['id'];
$agent_id = $chat['Agent']['id'];

$date_reponse_agent = '';			
$terme_indesirable = '';			
foreach($chat["ChatMessage"] as $mes){
	$user_name = '';
	if($user_id == $mes["user_id"]){
		$user_name = $chat['User']['firstname'];
	}
	if($agent_id == $mes["user_id"]){
		$user_name = $chat['Agent']['pseudo'];
		if(!$date_reponse_agent)
		$date_reponse_agent = $mes["date_add"];
	}
	$message_txt = $mes["content"];
	$is_terme_indes = false;
	foreach($filtres as $filtre){
		
		
		
				if(substr_count($message_txt, $filtre["FiltreMessage"]["terme"])){
					$message_txt = str_replace($filtre["FiltreMessage"]["terme"],'<b style="color:#000;font-size:17px">'.$filtre["FiltreMessage"]["terme"].'</b>',$message_txt);
					$terme_indesirable .= $filtre["FiltreMessage"]["terme"]. ' ';
					$is_terme_indes = true;
				}elseif(substr_count($message_txt, ucfirst($filtre["FiltreMessage"]["terme"]))){
					$message_txt = str_replace(ucfirst($filtre["FiltreMessage"]["terme"]),'<b style="color:#000;font-size:17px">'.ucfirst($filtre["FiltreMessage"]["terme"]).'</b>',$message_txt);
					$terme_indesirable .= $filtre["FiltreMessage"]["terme"]. ' ';
					$is_terme_indes = true;
				}elseif(substr_count($message_txt, strtoupper($filtre["FiltreMessage"]["terme"]))){
					$message_txt = str_replace(strtoupper($filtre["FiltreMessage"]["terme"]),'<b style="color:#000;font-size:17px">'.strtoupper($filtre["FiltreMessage"]["terme"]).'</b>',$message_txt);
					$terme_indesirable .= $filtre["FiltreMessage"]["terme"]. ' ';
					$is_terme_indes = true;
				}
			}
	if($is_terme_indes)
		$message_txt = '<span style="background:#d84a38;color:#fff;padding:2px;">'.$message_txt.'</span>';
	$message .= $this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$mes["date_add"]),'%d/%m/%y %Hh%Mmin%Ss') . ' <b>'.$user_name.'</b> -> '. $message_txt.'<br />';	
}



    $html.= '<tr>';
        $html.= '<td>'.$chat["Chat"]['id'].'</td>';
		 $html.= '<td>'.$this->Html->link($chat['User']['lastname'].' '.$chat['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $chat['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
      //  $html.= '<td>'.$chat['User']['lastname'].' '.$chat['User']['firstname'].'</td>';
	  $html.= '<td>'.$this->Html->link($chat['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $chat['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
       // $html.= '<td>'.$chat['Agent']['lastname'].' '.$chat['Agent']['firstname'].'</td>';
        $html.= '<td>';
			
			$picture = '';
			$folder = new Folder(Configure::read('Site.pathChatArchiveAdmin').DS.$chat['Chat']['id'],true,0755);
			if(is_dir($folder->path)){
				$files = array_diff(scandir($folder->path), array('.','..'));
				foreach ($files as $file) {
					$picture .= '<a href="'.DS.Configure::read('Site.pathChatArchive').DS.$chat['Chat']['id'].DS.$file.'" class="chat_picture"><img src="'.DS.Configure::read('Site.pathChatArchive').DS.$chat['Chat']['id'].DS.$file.'" style="width:100px;height:auto;margin-left:5px;" /></a>';
				}
			}
		if($picture){
			$html .= '<div class="cb_pictures">'.$picture.'</div>';
		}
			
		if($terme_indesirable)	$html.= '<p><b>Terme(s) détécté : '.$terme_indesirable.'</b></p>';
			$html .='<div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px;">'.$message.'</div></td>';
        
		$date_start = new DateTime($chat['Chat']['date_start']);
			$date_start->modify('+ 1 hour');
		$date_end = new DateTime($chat['Chat']['date_end']);
			$date_end->modify('+ 1 hour');
		$consult_date_start = new DateTime($chat['Chat']['consult_date_start']);
			$consult_date_start->modify('+ 1 hour');
		if(isset($chat['Chat']['consult_date_end'])){
			$consult_date_end = new DateTime($chat['Chat']['consult_date_end']);
			$consult_date_end->modify('+ 1 hour');
		}
			else
				$consult_date_end = '';
		$date_reponse 	= new DateTime($date_reponse_agent);
		$date_reponse->modify('+ 1 hour');
		if($date_reponse_agent)
			$interval_reponse = $date_start->diff($date_reponse);
		
		if($chat['Chat']['consult_date_start'])	
		$interval = $date_start->diff($consult_date_start);
			else
		$interval = $date_start->diff($date_end);	
			
		$diff = $interval->format('%H:%I:%S');
		
		if($date_reponse_agent)	
		$tps_reponse = $interval_reponse->format('%H:%I:%S');
		
		$html.= '<td><b>Start</b>: '.$date_start->format('d-m-Y H').'h'.$date_start->format('i').'min'.$date_start->format('s').'s';
		if($date_reponse_agent)
			$html.='<br /><b>Reponse agent</b>: '.$tps_reponse;
		if($chat['Chat']['consult_date_start'])		
		$html.= '<br /><b>Délai avant consult</b>: '.$diff;
			else
		$html.= '<br /><b>Tps attente</b>: '.$diff;
		$html.= '</td>';
			
		if($chat['Chat']['consult_date_start'])
			$html.= '<td>'.$consult_date_start->format('d-m-Y H').'h'.$consult_date_start->format('i').'min'.$consult_date_start->format('s').'s'.' > <br />'.$date_end->format('d-m-Y H').'h'.$date_end->format('i').'min'.$date_end->format('s').'s'.'</td>';
		else
			$html.= '<td>'.$date_start->format('d-m-Y H').'h'.$date_start->format('i').'min'.$date_start->format('s').'s'.' -> '.$date_end->format('d-m-Y H').'h'.$date_end->format('i').'min'.$date_end->format('s').'s'.'</td>';
		$html.= '<td>'.$chat['Chat']['source'].'</td>';
		$html.= '<td>';
			if($chat['Chat']['date_end'])
			$html.= $chat['Chat']['closed_by'];
			
		$html.= '</td>';
		$html.= '<td>';
			
			switch ($chat['Chat']['alert']) {
				case 0:
					$html.=  "Pas d'appel";
					break;
				case 1:
					$html.=  "Appel exécuté";
					break;
				case 2:
					$html.=  "Appel décroché";
					break;
			}
		$html.= '</td>';
		 if ($chat[0]['hasAdminActions']) {
            $html.= '<td>'.$chat['Chat']['validation_time'].'</td>';
        } else {
            $html.= '<td></td>';
        }	
		$html .= '<td>';
		if(!$chat['Chat']['etat'])
                                    $html .= $this->Metronic->getLinkButton(
                                        __('Accepter')   ,
                                        array('controller' => 'admins', 'action' => 'accept_chat_indesirable', 'admin' => true, 'id' => $chat['Chat']['id']),
                                        'btn red',
                                        'icon-remove'
                                    );
        $html.= '</td>';
		$html .= '<td>';
		if(!$chat['Chat']['etat'])
           $html .= '<input type="checkbox" class="AdminTchatCheckbox" name="AdminTchatCheckAll_'.$chat['Chat']['id'].'" rel="'.$chat['Chat']['id'].'" />';
        $html.= '</td>';
		
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