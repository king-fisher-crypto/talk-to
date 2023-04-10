<?php
	echo $this->Html->css('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'css'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/date', array('block' => 'script'));
    echo $this->Html->script('/assets/plugins/bootstrap-daterangepicker/daterangepicker', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/nx_datepickerrange', array('block' => 'script'));
    echo $this->Html->script('/theme/default/js/admin_agent_rowspan', array('block' => 'script'));
    echo $this->Metronic->titlePage(__('Agent'),__('Données de l\'agent'));
	echo $this->Html->script('/theme/default/js/chart.min', array('block' => 'script'));
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
            'text' => (!isset($agent['User']['pseudo']) && empty($agent['User']['pseudo'])?__('Agent'):$agent['User']['pseudo']),
            'classes' => 'icon-zoom-in',
            'link' => (empty($agent)?'':$this->Html->url(array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $agent['User']['id'])))
        )
    ));
    echo $this->Session->flash();
?>

<div class="row-fluid">
   
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Agent'); ?></div>
        </div>
        
        <div class="portlet-body">
            <?php if(empty($agent)): ?>
                <?php echo __('Aucun expert trouvé'); ?>
            <?php else: ?>
               
               <?php
				if($agent['User']['absence']){
					echo '<div class="portlet-title" style="background-color: #bb2413 ;display:block;width:98%;padding:10px;color:#fff">
            <div class="caption">'.$agent['User']['absence'].'</div>
        </div>';
				}
			?>
               
               <div class="row-fluid">
               	
				   <div class="portlet box yellow span12" style="margin-top:20px">
						<div class="portlet-title">
							<div class="caption">Dashboard</div>
							<div class="pull-right">
							</div>
						</div>
						
						
						<div class="portlet-body">
						<div><?php echo $this->Metronic->getDateInputStats(); ?></div>
							<?php
				if($stats_agent){
				?>
							<div class="row-fluid">
				   				<div class="span2">
									<canvas id="canvas_Note"></canvas>
								</div>
								<div class="span2">
									<canvas id="canvas_PresencePourcent"></canvas>
									<p style="text-align: justify;font-size:9px;line-height:10px;">Le taux de présence représente votre taux présence moyen par téléphone et tchat</p>
								</div>
								<div class="span2">
									<canvas id="canvas_TxDecroche"></canvas>
								</div>
								<div class="span2">
									<canvas id="canvas_TxTransfoPresent"></canvas>
									<p style="text-align: justify;font-size:9px;line-height:10px;">Nombre de clients transformés par rapport au taux de clics sur votre profil expert lorsque vous êtes connecté(e) en mode téléphone et tchat.</p>
								</div>
								<div class="span2">
									<canvas id="canvas_TMC"></canvas>
								</div>
								<div class="span2">
									<canvas id="canvas_Proportion"></canvas>
								</div>
							</div>
							<?php 
				}
				?>
						</div>
				   </div>
               </div>
               
                <table class="table table-striped table-hover table-bordered td_view">
                    <tbody>
                    <?php
                        $i = 0; //Pour construire le tableau avec 4 colonnes
                        //On sauvegarde l'id langue de la dernière présentation. Servira pour le design
                        $lastPresentationIdLang = end($presentations)['UserPresentLang']['lang_id'];
                    ?>
                    <?php 
					//add time in connexion
					$agent['User']['Temps_Connexion'] = '';
					$connexion_second  = 0;
					$dbb_patch = new DATABASE_CONFIG();
					$dbb_connect = $dbb_patch->default;
					
					$mysqli_connect = new mysqli($dbb_connect['host'], $dbb_connect['login'], $dbb_connect['password'], $dbb_connect['database']);
					$my_result = $mysqli_connect->query("SELECT TIME_TO_SEC(TIMEDIFF(date_connexion,date_lastactivity)) as time,date_connexion,date_lastactivity from user_connexion where user_id = '{$agent['User']['id']}'");
					while($row_connec = $my_result->fetch_array(MYSQLI_ASSOC)){
						
						//var_dump( $row_connec['date_connexion'].' -> '.$row_connec['date_lastactivity'] .' : '.$row_connec['time']);
						$connexion_second  += $row_connec['time'];
					}
					if($connexion_second){
						 $dtF = new DateTime("@0");
						 $dtT = new DateTime("@$connexion_second");
						 $agent['User']['Temps_Connexion'] =  $dtF->diff($dtT)->format('%a jours, %h heures, %i minutes and %s secondes');	
					}
				
					foreach ($agent['User'] as $key => $value): ?>

                        <?php if(in_array($key,$listData)) continue; ?>
                        <?php if($i%2 == 0) echo '<tr>'; ?>
                            <td class="txt-bold name"><?php echo isset($nameField[$key])?__($nameField[$key]):$key; ?></td>
                            <?php if(strcmp($key, 'active') == 0) : ?>
                                <td class="value">
                                    <?php
                                        if($agent['User']['active'] == 1 && $agent['User']['valid'] == 1)
                                            echo '<span class="badge badge-success">'.__('Compte activé').'</span>';
                                        elseif(empty($agent['User']['date_lastconnexion']))
                                            echo '<span class="badge badge-warning">'.__('Compte non validé').'</span>';
                                        else
                                            echo '<span class="badge badge-danger">'.__('Compte désactivé').'</span>';
                                    ?>
                                </td>
                            <?php $i++; continue; endif; ?>
                            <?php if(strcmp($key, 'emailConfirm') == 0) : ?>
                                <td class="value">
                                    <?php if($value == 0):
                                        echo '<span class="badge badge-warning">'.__('Email non confirmé').'</span>'; ?>
                                        <div class="btn-group margin-left">
                                            <a class="btn btn dropdown-toggle" data-toggle="dropdown" href="#"><?php echo __('Actions'); ?><span class="caret"></span></a>
                                            <ul class="dropdown-menu">
                                                <li><?php echo $this->Html->link(
                                                        '<span class="icon-envelope"></span>'.__('Relancer un mail de confirmation'),
                                                        array('controller' => 'agents', 'action' => 'relance_mail_confirm', 'admin' => true, 'id' => $agent['User']['id'], '?' => 'view'),
                                                        array('escape' => false),
                                                        __('Voulez-vous vraiment renvoyer un mail de confirmation ?')
                                                    ); ?>
                                                </li>
                                                <li><?php echo $this->Html->link(
                                                        '<span class="icon-check"></span>'.__('Confirmer l\'email'),
                                                        array('controller' => 'agents', 'action' => 'confirm_mail', 'admin' => true, 'id' => $agent['User']['id'], '?' => 'view'),
                                                        array('escape' => false),
                                                        __('Voulez-vous vraiment forcer la confirmation de l\'adresse mail ?')
                                                    ); ?>
                                                </li>
                                            </ul>
                                        </div>
                                    <?php else :
                                        echo '<span class="badge badge-success">'.__('Email confirmé').'</span>';
                                    endif; ?>
                                </td>
                            <?php $i++; continue; endif; ?>
                            <?php if(strcmp($key, 'has_photo') == 0) : ?>
                                <td class="value">
                                    <?php if($value == 1): ?>
                                        <?php if(empty($pathPhoto))
                                            echo $this->Html->image('/'.Configure::read('Site.defaultImage'));
                                        else{
                                            if(empty($agent['User']['agent_number']))
                                                echo $this->Html->image('/'.Configure::read('Site.pathInscriptionMedia').'/'.$agent['User']['id'].'/'.$pathPhoto);
                                            else
                                                echo $this->Html->image('/'.Configure::read('Site.pathPhoto').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$pathPhoto);
                                        }
                                        ?>
                                    <?php else : ?>
                                        <?php echo __('Non') ?>
                                    <?php endif; ?>
                                </td>
                            <?php $i++; continue; endif; ?>
                            <?php if(strcmp($key, 'date_lastconnexion') == 0) : ?>
                                <td class="value">
                                    <?php echo (empty($value)
                                        ?__('N/D')
                                        :$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value),'%d %B %Y %H:%M'))
                                    ; ?>
                                </td>
                            <?php $i++; continue; endif; ?>
                            <?php if(strcmp($key, 'record') == 0) : ?>
                                <td class="value">
                                    <?php echo ($value == 1 ?__('Oui'):__('Non')); ?>
                                </td>
                            <?php $i++; continue; endif; ?>
                            <?php if(strcmp($key, 'has_audio') == 0) : ?>
                                <td class="value">
                                    <?php if($value == 1): ?>
                                        <?php if(empty($pathAudio))
                                        echo __('Fichier audio introuvable');
                                    else{
                                        if(empty($agent['User']['agent_number']))
                                            echo '<audio src="/'.Configure::read('Site.pathInscriptionMedia').'/'.$agent['User']['id'].'/'.$pathAudio.'" controls preload="none" type="audio/mpeg"></audio>';
                                        else
                                            echo '<audio src="/'.Configure::read('Site.pathPresentation').'/'.$agent['User']['agent_number'][0].'/'.$agent['User']['agent_number'][1].'/'.$pathAudio.'" controls preload="none" type="audio/mpeg"></audio>';
                                    }
                                        ?>
                                    <?php else : ?>
                                        <?php echo __('Non') ?>
                                    <?php endif; ?>
                                </td>
                            <?php $i++; continue; endif; ?>
							<?php if(strcmp($key, 'vat_num') == 0) : ?>
								<td class="value"><a target="_blank" href="http://ec.europa.eu/taxation_customs/vies/vatResponse.html?locale=FR&memberStateCode=<?=substr($value,0,2) ?>&number=<?=substr($value,2,strlen($value)-2) ?>"><?php echo $value; ?></a></td>
							<?php $i++; continue; endif; ?>
							<?php if(strcmp($key, 'stripe_account') == 0) : ?>
                                <td class="value">
                                    <?php if($value)echo '<a target="_blank" href="https://dashboard.stripe.com/connect/accounts/'.$value.'"><img src="/theme/default/img/strile-pay.png" /></a>';
                                        
                                     ?>
                                </td>
                            <?php $i++; continue; endif; ?>
                            <td class="value"><?php echo $value; ?></td>
                            <?php if($i == 1){  //La cellule pour les boutons
                                echo '<td class="td-button" rowspan="1">';
								//if(!empty($user_level) && $user_level != 'moderator'){
                                	echo $this->Metronic->getLinkButton(
                                    __('Modifier'),
                                    array('controller' => 'agents', 'action' => 'edit', 'admin' => true, 'id' => $agent['User']['id']),
                                    'btn blue',
                                    'icon-edit').'<br/><br/>';
								
                                	echo ($agent['User']['active'] == 1 && $agent['User']['record'] == 0
                                    ?$this->Metronic->getLinkButton(
                                        __('Activer enregistrement téléphonique'),
                                        array('controller' => 'agents', 'action' => 'activate_record', 'admin' => true, 'id' => $agent['User']['id']),
                                        'btn purple',
                                        'icon-plus-sign',
                                        __('Voulez-vous vraiment activer l\'enregistrement téléphonique de l\'agent ?')
                                    )
                                    :($agent['User']['active'] == 1
                                        ?$this->Metronic->getLinkButton(
                                            __('Désactiver enregistrement téléphonique'),
                                            array('controller' => 'agents', 'action' => 'deactivate_record', 'admin' => true, 'id' => $agent['User']['id']),
                                            'btn purple',
                                            'icon-ban-circle',
                                            __('Voulez-vous vraiment désactiver l\'enregistrement téléphonique de l\'agent ?')
                                        )
                                        :''
                                    )
                                	).'<br/><br/>';
								//}
								//if(!empty($user_level) && $user_level != 'moderator')
                                echo ($agent['User']['valid'] == 1 && $agent['User']['active'] == 1
                                    ?$this->Metronic->getLinkButton(
                                        __('Désactiver le compte'),
                                        array('controller' => 'agents', 'action' => 'deactivate_user', 'admin' => true, 'id' => $agent['User']['id']),
                                        'btn red',
                                        'icon-remove',
                                        __('Voulez-vous vraiment désactiver le compte de l\'agent ? Il n\'aura plus accès au site.')
                                    )
                                    :($agent['User']['valid'] == 0 || $agent['User']['active'] == 0
                                        ?$this->Metronic->getLinkButton(
                                            __('Activer le compte'),
                                            array('controller' => 'agents', 'action' => 'activate_user', 'admin' => true, 'id' => $agent['User']['id']),
                                            'btn green',
                                            'icon-check',
                                            __('Voulez-vous activer le compte ?')
                                        )
                                        :''
                                    )
                                ).'<br/><br/>';
	
								//if(!empty($user_level) && $user_level != 'moderator'){
								echo '<a class="btn green" href="http://spiriteo.daotec.com/api/agent/'.$agent['User']['agent_number'].'/information" target="_blank">
<span class="icon-check"></span>
Voir le statut Daotec
</a>'	
									.'<br/><br/>'.
                                ($agent['User']['valid'] == 0 && $agent['User']['active'] == 0
                                    ?$this->Metronic->getLinkButton(
                                        __('Demander SIRET ou PI/PASSEPORT'),
                                        array('controller' => 'agents', 'action' => 'send_demand_passport', 'admin' => true, 'id' => $agent['User']['id']),
                                        'btn green',
                                        'icon-file',
                                        __('Voulez-vous vraiment envoyer cette demande pour ce compte agent ?')
                                    )
                                    :''
                                ) ;
								if($agent['User']['date_demand_doc']){
									echo '<p class="small" style="font-size:11px;">Envoyé le '.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$agent['User']['date_demand_doc']),'%d %B %Y %H:%M').'</p><br />';
									}else{
									echo '<br/><br/>';
								}
	
	
                                echo ($agent['User']['valid'] == 0 && $agent['User']['active'] == 0
                                    ?$this->Metronic->getLinkButton(
                                        __('Envoyer le questionnaire'),
                                        array('controller' => 'agents', 'action' => 'send_survey', 'admin' => true, 'id' => $agent['User']['id']),
                                        'btn green',
                                        'icon-file',
                                        __('Voulez-vous vraiment envoyer le questionnaire pour ce compte agent ?')
                                    )
                                    :''
                                ) ;
								//}
									//if(!empty($user_level) && $user_level != 'moderator'){
									if($questionnaires && $agent['User']['valid'] == 0 && $agent['User']['active'] == 0){
										foreach($questionnaires as $questionnaire){
											echo '<p class="small" style="font-size:11px;">Envoyé le '.$this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$questionnaire['Survey']['date_add']),'%d %B %Y %H:%M').'</p>';
										}
									}
									//}
									
                                 echo '</td>';
                            } ?>
                        <?php if($i%2 != 0) echo '</tr>' ?>
                        <?php $i++; ?>
                    <?php endforeach; ?>
                    <?php if($i%2 == 0) echo '<tr>'; ?>
                        <td class="txt-bold"><?php echo __('Univers'); ?></td>
                        <td class="value"><?php echo $univers; ?></td>
                    <?php if($i%2 != 0) echo '</tr>' ?>
                    <?php $i++; ?>
                    <?php foreach($presentations as $presentation) :
						if($presentation['UserPresentLang']['lang_id'] != 8 && $presentation['UserPresentLang']['lang_id'] != 9 && $presentation['UserPresentLang']['lang_id'] != 10 && $presentation['UserPresentLang']['lang_id'] != 11 && $presentation['UserPresentLang']['lang_id'] != 12 ){
					 ?>
                        <?php if($i%2 == 0) echo '<tr>'; ?>
                            <td class="txt-bold"><?php echo '<i class="lang_flags lang_'. $flags[$presentation['UserPresentLang']['lang_id']] .' icon_view_agent"></i>'.__('Présentation'); ?></td>
                            <td class="value"><?php echo $presentation['UserPresentLang']['texte']; ?></td>
                        <?php if(($presentation['UserPresentLang']['lang_id'] == $lastPresentationIdLang) && $i%2 == 0) echo '<td></td><td></td></tr>'; //Uniquement pour un aspect plus jolie?>
                        <?php if($i%2 != 0) echo '</tr>'; ?>
                        <?php $i++;
						}
						 ?>
                    <?php endforeach; ?>
                    </tbody>
                </table>
                <span rows="<?php echo ceil(($i+1)/2); ?>" style="display: none;"></span>
            <?php endif; ?>
        </div>
    </div>
</div>
<div class="row-fluid">
	<div class="portlet box yellow span12" style="margin-top:20px"><div class="portlet-title"><div class="caption">Documents & questionnaire</div><div class="pull-right">
		<?php
		echo $this->Form->create('Agent', array('action' => 'upload_documents','nobootstrap' => 1,'enctype' => 'multipart/form-data','class' => 'form-inline display-inline', 'default' => 1));
		echo $this->Form->input('document', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'file', 'label' => __('Fichier').' :', 'div' => false));
		echo '<input type="hidden" name="data[Agent][user_id]" value="'.$agent['User']['id'].'"  />';
		 echo '<input class="btn green" type="submit" value="Uploader" /></form>';
		?>
		</div></div><div class="portlet-body"><div class="row-fluid">
		<?php
		if($questionnaire){
			echo'<a class="span2" href="/admin/agents/survey_view-'.$questionnaire['Survey']['id'].'" target="_blank"> <span class="icon-check"></span> Questionnaire </a>';
		}
		if($documents){
			foreach($documents as $doc){
				echo'<div class="span2" ><a  href="'.Configure::read('Site.pathDocument').DS.$agent['User']['id'].DS.$doc['UserDocument']['name'].'" target="_blank"> <span class="icon-file"></span> '.$doc['UserDocument']['name'].'</a>&nbsp;&nbsp;&nbsp;<a href="/admin/agents/delete_documents-'.$doc['UserDocument']['id'].'" class="doc_delete"><i class="icon-remove"></i></a></div>';
			}
		}
		?>
		</div>
	</div></div>
</div>
<div class="row-fluid">
		<div class="span2" style="max-height:150px;overflow-y: auto">
			 <?php
		$txt_adr_ip = '';
		$note_ip = '';
		$nmax = 7;
		$nindex=1;
		foreach($userIp as $uIp){
			$txt_adr_ip .=  '<p>';
			$txt_adr_ip .=  $uIp['UserIp']['IP'];
			$txt_adr_ip .=  ' - ><br />';
			$txt_adr_ip .=  $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$uIp['UserIp']['date_conn']),'%d %B %Y %H:%M');
			$txt_adr_ip .=  '</p>';
			if($uIp['UserIp']['note'])
				$note_ip = $uIp['UserIp']['note'];	
			if($nindex == $nmax)break;
			$nindex++;
		}
			echo __('Adresses IP').$txt_adr_ip;
			?>
	</div>
	<?php
	echo '<div class="span2" style="max-height:150px;overflow:auto">';
	$txt_adr_ip = '';
	foreach($userNotIp as $uIp){
			$txt_adr_ip .=  '<p style="font-size:12px;">';
			$txt_adr_ip .=  $uIp['UserIp']['IP'];
			$txt_adr_ip .=  ' - ><br />';
			if($uIp['User']['role'] == 'client'){
				$txt_adr_ip .= 'Client : '.$this->Html->link($uIp['User']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $uIp['User']['id'], 'full_base' => true));
			}else{
				$txt_adr_ip .= 'Expert : '.$this->Html->link($uIp['User']['pseudo'],array('controller' => 'agents', 'action' => 'view', 'admin' => true, 'id' => $uIp['User']['id'], 'full_base' => true));
			}
			
		}
	
	echo __('Comptes avec même IP').'<br />'.$txt_adr_ip.'</div>';
	?>
	<div class="span3">
		     <?php
        echo $this->Form->create('Agent', array('action' => 'admin_note_ip-'.$agent['User']['id'], 'nobootstrap' => 1, 'class' => 'form ', 'default' => 1));
        echo $this->Form->input('note', array(
                'label' => array(
                    'text' => __('IP'),
                    'class' => 'control-label'
                ),
                'type' => 'textarea',
                'between' => '<div class="controls">',
                'after' => '</div>',
                'value' => $note_ip)
        );
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));
    ?>
	</div>
        <div class="span5" style="margin-left:0px;">
		 <?php
        echo $this->Form->create('Agent', array('action' => 'admin_note-'.$agent['User']['id'], 'nobootstrap' => 1, 'class' => 'form ', 'default' => 1));
        echo $this->Form->input('note', array(
                'label' => array(
                    'text' => __('Note privée'),
                    'class' => 'control-label'
                ),
                'type' => 'textarea',
                'between' => '<div class="controls">',
                'after' => '</div>',
				'style' => 'width:100%',
                'value' => $agent['User']['admin_note'])
        );
        echo $this->Form->end(array(
            'label' => __('Enregistrer'),
            'class' => 'btn blue',
            'div' => array('class' => 'controls')
        ));
    ?>	
	</div>
</div>

<div class="row-fluid">
        <div class="portlet box yellow span12">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. __(' communications'); ?></div>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                        <th><?php echo __('Phone'); ?></th>
                        <th><?php echo __('Tchat'); ?></th>
                        <th><?php echo __('Email'); ?></th>
                        <th><?php echo __('Total'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $nbComPhone ?> (<?php echo gmdate("H:i:s", $nbMinComPhone); ?>)</td>
                            <td><?php echo $nbComTchat ?> (<?php echo gmdate("H:i:s", $nbMinComTchat);  ?>)</td>
                            <td><?php echo $nbComMail ?> (<?php echo gmdate("H:i:s", $nbMinComMail);  ?>)</td>
                            <td><?php 
									$nbComTotal = $nbComPhone + $nbComTchat + $nbComMail;
								$nbMinComTotal = $nbMinComPhone + $nbMinComTchat;
								echo $nbComTotal ?> (<?php echo gmdate("H:i:s", $nbMinComTotal);  ?>)</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
</div>
<div class="row-fluid">
    <?php if(!empty($lastCom)): ?>
        <div class="portlet box yellow span12">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. Configure::read('Site.limitStatistique') .' '.__(' dernières communications'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet de l\'agent'),array('controller' => 'agents', 'action' => 'com_view', 'admin' => true, 'id' => $agent['User']['id'])); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo __('ID') ?></th>
                        <th><?php echo __('Client'); ?></th>
                        <th><?php echo __('Media'); ?></th>
                        <th><?php echo __('Numéro de téléphone'); ?></th>
                        <th><?php echo __('Type appel'); ?></th>
                        <th><?php echo __('Durée'); ?></th>
                        <th><?php echo __('Date consultation'); ?></th>
						<?php if(!empty($user_level) && $user_level != 'moderator'){ ?>
						<th><?php echo __('Facturé'); ?></th>
						<th><?php echo __('Soldé'); ?></th>
						<?php } ?>
						<th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastCom as $k => $row): ?>
                        <tr>
							<td><?php echo $row['UserCreditHistory']['sessionid']; ?></td>
                            <td>
                            
                            <?php 
								
								$client_name = '';
								if(substr_count($row['User']['firstname'], 'AUDIOTEL')){
									$client_name = 'AUDIO'.(substr($row['UserCreditHistory']['phone_number'], -4)*15);
								}else{
									$client_name = $row['User']['firstname'].' '.$row['User']['lastname'];
								}
								
								echo $this->Html->link($client_name,array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['UserCreditHistory']['user_id']));
								
								?>
                            
                            </td>
                            <td><?php 
								if($row['UserCreditHistory']['media'] == 'refund') 
									echo 'E-mail remboursé';
								else
									echo __($consult_medias[$row['UserCreditHistory']['media']]); ?></td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['phone_number'])
                                    ?__('N/D')
                                    :$row['UserCreditHistory']['phone_number']
                                ); ?>
                            </td>
                            <td>
                                <?php 
									switch ($row['UserCreditHistory']['called_number']) {
										case 901801885:
											echo 'Suisse audiotel';
											break;
										case 225183456:
										case 41225183456:
											echo 'Suisse prépayé';
											break;
										case 90755456:
											echo 'Belgique audiotel';
											break;
										case 3235553456:
											echo 'Belgique prépayé';
											break;
										case 90128222:
											echo 'Luxembourg audiotel';
											break;
										case 35227864456:
											echo 'Luxembourg prépayé';
											break;
										case 4466:
											echo 'Canada audiotel mobile';
											break;
										case 19007884466:
											echo 'Canada audiotel fixe';
											break;
										case 18442514456:
											echo 'Canada prépayé';
											break;
										case 33970736456:
											echo 'France prépayé';
											break;
									}
									if(!$row['UserCreditHistory']['called_number']){
										switch ($row['UserCreditHistory']['domain_id']) {
										case '11':
											echo 'Belgique';
											break;
										case '13':
											echo 'Suisse';
											break;
											case '19':
											echo 'France';
											break;
										case '22':
											echo 'Luxembourg';
											break;
										case '29':
											echo 'Canada';
											break;
										}
										switch ($row['UserCreditHistory']['type_pay']) {
										case 'pre':
											echo ' prépayé';
											break;
										case 'aud':
											echo ' audiotel';
											break;
										}
									}
								 ?>
                            </td>
                            <td>
                                <?php echo (empty($row['UserCreditHistory']['seconds'])
                                    ?__('N/D')
                                    :gmdate('H:i:s', $row['UserCreditHistory']['seconds'])
                                ); ?>
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserCreditHistory']['date_start']),'%d %B %Y %H:%M:%S'); ?></td>
							<?php if(!empty($user_level) && $user_level != 'moderator'){ ?>
							<td>
							<input type="checkbox" class="agent_facture_choice" value="<?php echo $row['UserCreditHistory']['user_credit_history']; ?>" <?php	if($row['UserCreditHistory']['is_factured']) echo "checked"; ?>/>
							<i class="icon-pencil show_text_factured"></i>
                            <textarea class="text_agent_factured" id="text_agent_factured_<?php echo $row['UserCreditHistory']['user_credit_history']; ?>" style="display:none;"><?php echo $row['UserCreditHistory']['text_factured']; ?></textarea>
                            </td>
							<td>
							<input type="checkbox" class="agent_solde_choice" value="<?php echo $row['UserCreditHistory']['user_credit_history']; ?>" <?php	if($row['UserCreditHistory']['is_sold']) echo "checked"; ?>/>
							</td>
							<?php } ?>
							<td><a class="btn blue nx_viewcomm" href="/admins/getCommunicationData-<?php echo $row['UserCreditHistory']['user_credit_history']; ?>">Voir</a></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
   
</div>
<div class="row-fluid">
    <?php if(!empty($lastComlost)): ?>
        <div class="portlet box yellow span4" style="clear:both;float:left;">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. Configure::read('Site.limitStatistique') .' '.__(' derniers appels perdus'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet de l\'agent'),array('controller' => 'agents', 'action' => 'comlost_view', 'admin' => true, 'id' => $agent['User']['id'])); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo __('ID'); ?></th>
                        <th><?php echo __('Client'); ?></th>
                        <th><?php echo __('Date consultation'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastComlost as $k => $row): ?>
                        <tr>
							<td><?php echo $row['Callinfo']['sessionid']; ?></td>
                            <td>
                            
                            <?php 
								
								$client_name = '';
								if($row['User']['firstname']){
									$client_name = $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true));
								}else{
									$client_name = 'AUDIO'.(substr($row['Callinfo']['callerid'], -4)*15);
									
								}
								echo $client_name;
								
								
								?>
                            
                            </td>
                            <td><?php echo $this->Time->format(date('Y-m-d H:i:s',$row['Callinfo']['timestamp']),'%d %B %Y %H:%M:%S'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
    <?php if(!empty($lastComlostchat)): ?>
        <div class="portlet box yellow span4"  style="clear:none;float:left;">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. Configure::read('Site.limitStatistique') .' '.__(' derniers chats perdus'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet de l\'agent'),array('controller' => 'agents', 'action' => 'comlostchat_view', 'admin' => true, 'id' => $agent['User']['id'])); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo __('ID'); ?></th>
                        <th><?php echo __('Client'); ?></th>
                        <th><?php echo __('Date consultation'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastComlostchat as $k => $row): ?>
                        <tr>
							<td><?php echo $row['Chat']['id']; ?></td>
                            <td>
                            
                            <?php 
								
								$client_name = '';
								if($row['User']['firstname']){
								$client_name = $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true));
									echo $client_name;
								}
								
								
								
								?>
                            
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Chat']['date_start']),'%d %B %Y %H:%M:%S'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
	 <?php if(!empty($lastComlostmessage)): ?>
        <div class="portlet box yellow span4"  style="clear:none;float:left;">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. Configure::read('Site.limitStatistique') .' '.__(' derniers mails perdus'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet de l\'agent'),array('controller' => 'agents', 'action' => 'comlostmessage_view', 'admin' => true, 'id' => $agent['User']['id'])); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
						<th><?php echo __('ID'); ?></th>
                        <th><?php echo __('Client'); ?></th>
                        <th><?php echo __('Date consultation'); ?></th>
						<th><?php echo __('Délai (H:i:s)'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastComlostmessage as $k => $row): ?>
                        <tr>
							<td><?php echo $row['Message']['id']; ?></td>
                            <td>
                            
                            <?php 
								
								$client_name = '';
								if($row['User']['firstname']){
								$client_name = $this->Html->link($row['User']['firstname'].' '.$row['User']['lastname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $row['User']['id'], 'full_base' => true));
									echo $client_name;
								}
								
								
								
								?>
                            
                            </td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['Message']['date_add']),'%d %B %Y %H:%M:%S'); ?></td>
							<td><?php 
								
								$delay = gmdate("H:i:s", $row['UserPenality']['delay']);
								
								echo $delay; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php if(!empty($sponsorships)): ?>
    <div class="row-fluid" >
        <div class="portlet box yellow">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '.Configure::read('Site.limitStatistique').' '.__('derniers gains parrainage'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet de l\'agent'),array('controller' => 'agents', 'action' => 'sponsorship_view', 'admin' => true, 'id' => $agent['User']['id'], 'full_base' => true)); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th><?php echo __('Filleul') ?></th>
							<th><?php echo __('Session ID') ?></th>
							<th><?php echo __('Credits') ?></th>
                            <th><?php echo __('Gain') ?></th>
                            <th><?php echo __('Date') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($sponsorships as $key => $value): ?>
                        <tr>
                            <td><?php
								echo $this->Html->link($value['Filleul']['firstname'],array('controller' => 'accounts', 'action' => 'view', 'admin' => true, 'id' => $value['Sponsorship']['id_customer'], 'full_base' => true));
								?></td>
							<td><?php echo $value['UserCreditHistory']['sessionid']; ?></td>
							<td><?php echo $value['UserCreditHistory']['credits']; ?></td>
                            <td><?php 
								
								$bonus = str_replace(',','.',$value['Sponsorship']['bonus']) / 60 * $value['UserCreditHistory']['credits'];
								echo number_format($bonus,2,',',' ').' '.$value['Sponsorship']['bonus_type'] ?></td>
                            <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$value['UserCreditHistory']['date_start']),'%d %B %Y %H:%M'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="row-fluid">
    <?php if(!empty($lastConnexions)): ?>
        <div class="portlet box yellow span8" style="clear:both;float:left;">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. Configure::read('Site.limitStatistique') .' '.__(' dernieres connexions'); ?></div>
                <p class="pull-right color_link_white"><?php echo $this->Html->link(__('Voir l\'historique complet de l\'agent'),array('controller' => 'agents', 'action' => 'connexion_view', 'admin' => true, 'id' => $agent['User']['id'])); ?></p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                       <th><?php echo __('Modifié par'); ?></th>
                       <th><?php echo __('Connexion'); ?></th>
                       <th><?php echo __('Statut'); ?></th>
                        <th><?php echo __('Date'); ?></th>
                        <th><?php echo __('Téléphone'); ?></th>
                        <th><?php echo __('Tchat'); ?></th>
                        <th><?php echo __('Mail'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($lastConnexions as $k => $row): 
						
						$next_line = $lastConnexions[$k+1];
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
                          <!-- <td><?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['UserConnexion']['date_lastactivity']),'%d %B %Y %H:%M:%S'); ?></td>-->
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
            </div>
        </div>
    <?php endif; ?>
	<?php if(!empty($bonus_agent)): ?>
        <div class="portlet box yellow span4" style="clear:none;float:left;">
            <div class="portlet-title">
                <div class="caption"><?php echo __('Les').' '. Configure::read('Site.limitStatistique') .' '.__(' dernieres primes'); ?></div>
                <p class="pull-right color_link_white">&nbsp;</p>
            </div>
            <div class="portlet-body">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                    <tr>
                       <th><?php echo __('Date'); ?></th>
                       <th><?php echo __('Prime'); ?></th>
                        <th><?php echo __('Montant payé'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($bonus_agent as $k => $row): 
						
						?>
                        <tr>
                           <td>
							   <?php echo $this->Time->format(Tools::dateUser($this->Session->read('Config.timezone_user'),$row['BonusAgent']['date_add']),'%d %B %Y %H:%M:%S'); ?>
							   </td>
							<td><?php 
							   echo $row['Bonuse']['name'].' '.$row['Bonuse']['amount'].'€';
							    ?></td>
							<td><?php 
							  /* if($row['BonusAgent']['mois'] == date('m') && $row['BonusAgent']['annee'] == date('Y'))
								   echo 'en cours';
								else
									echo 'payé';
*/	
								if($row['BonusAgent']['paid']){
									if($row['BonusAgent']['paid_amount'])
										echo $row['BonusAgent']['paid_amount'].'€';
									else
										echo $row['Bonuse']['amount'].'€';
								}
								 
								else
									echo 'a payer';
								
							    ?></td>
                          
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>


<script defer>
	
		window.chartColors = {
			red: 'rgb(255, 99, 132)',
			orange: 'rgb(255, 159, 64)',
			yellow: 'rgb(255, 205, 86)',
			green: 'rgb(75, 192, 192)',
			blue: 'rgb(54, 162, 235)',
			purple: 'rgb(131, 114, 170)',
			grey: 'rgb(201, 203, 207)',
			black: 'rgb(0, 0, 0)',
			white: 'rgb(255, 255, 255)',
		};
	
		<?php
	
			foreach($stats_agent as $stat => $dash){
		?>		
	
			var config_<?=$stat?> = {
						type: 'doughnut',
						data: {
							datasets: [{
								data: [
									<?=$dash['min']?>,
									<?php
									if(is_numeric($dash['medium'])){
									?>
									<?=$dash['medium']?>,
									<?php } ?>
									<?=$dash['max']?>,
								],
								backgroundColor: [
									window.chartColors.purple,
									<?php
									if(is_numeric($dash['medium'])){
									?>
									window.chartColors.orange,
									<?php } ?>
									window.chartColors.grey,
								],
								label: '<?=$dash['label']?>'
							}],
							
							labels: [
								"<?=$dash['min_label']?>",
								<?php
									if(is_numeric($dash['medium'])){
									?>
									"<?=$dash['medium_label']?>",
									<?php } ?>
								"<?=$dash['max_label']?>",
							]
						},
						options: {
							responsive: true,
							title:{
									display:true,
									text:"<?=$dash['label']?>"
								},
							 legend: {
									display:false,
								},
						}
					};
	
		<?php
			}
	?>
	window.onload = function() {
		<?php
			foreach($stats_agent as $stat => $dash){
		?>
		
			var ctx_<?=$stat ?> = document.getElementById("canvas_<?=$stat ?>").getContext("2d");
        	window.myPie = new Chart(ctx_<?=$stat ?>, config_<?=$stat ?>);
	<?php
			}
		?>
};
    </script>
