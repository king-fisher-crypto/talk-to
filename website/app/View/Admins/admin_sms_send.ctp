<?php

echo $this->Metronic->titlePage(__('SMS envoyé'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('SMS envoyé'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
$dbb_r = new DATABASE_CONFIG();
$dbb_route = $dbb_r->default;
$mysqli = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);


?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
        <div class="caption"><?php echo __('Envoi SMS '); ?></div>
        <div class="pull-right">
                <?php
                    echo $this->Form->create('SMSHistory', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					?>
                    <select id="expert" name="expert">
                    <option value="">Choisir...</option>
                    <?php
                    
                    $result = $mysqli->query("SELECT * from users where role = 'agent' and pseudo != '' order by pseudo");
                    while($row = $result->fetch_array(MYSQLI_ASSOC)){
                        echo '<option value="'.$row['id'].'">'.$row['pseudo']. '('.$row['id'].')</option>';
                    }
                    
                    
                     ?>
                    </select>
                    <?php
                    echo $this->Form->input('', array('class' => 'input  margin-left margin-right', 'type' => 'textarea', 'placeholder' => __('Message'), 'div' => false));
                    echo '<input class="btn green" type="submit" value="Envoyer">';
                    echo '</form>'
                ?>
         </div>
    </div>
	    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Liste SMS'); ?></div>
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                  /*  echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('agent_number', array('class' => 'input-mini  margin-left margin-right', 'type' => 'texte', 'label' => __('Code agent').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok">';
                    echo '</form>'*/
                ?>
                <?php
                  /*  echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('pseudo', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Pseudo').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';


                    echo $this->Form->create('Account', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('email', array('class' => 'input-small  margin-left margin-right', 'type' => 'text', 'label' => __('E-mail').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                    echo $this->Html->link('<span class="icon icon-download-alt"></span> Tout exporter',
                        array(
                            'controller' => 'agents',
                            'action'     => 'exportcsv',
                            'admin'      => true
                        ),
                        array(
                        'style' => 'margin-left:20px',
                        'class' => 'btn',
                        'type' => 'button',
                        'label' => false,
                        'div' => false,
                        'escape' => false,
                        'onclick' => 'document.location.href = \'/admin/agents/exportcsv\'; return true'
                    ));
                */
                ?>
            </div>
        </div>

    <div class="portlet-body">
<?php
$html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('SmsHistory.id', '#').'</th>';
    $html.= '<th>Type</th>';
	$html.= '<th>Destinataire</th>';
    $html.= '<th>'.$this->Paginator->sort('SmsHistory.phone_number', __('Tél.')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('SmsHistory.content', __('Texte')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('SmsHistory.date_add', __('Date d\'envoi')).'</th>';
	$html.= '<th>'.__('Répondu').'</th>';
					$html.= '<th>'.__('Client').'</th>';
    $html.= '</tr></thead><tbody>';

foreach ($list_sms AS $sms):
	$type = '';
    $html.= '<tr>';
        $html.= '<td>'.$sms["SmsHistory"]['id'].'</td>';
		$html.= '<td>'.$sms["SmsHistory"]['type'].'</td>';
	
		if($sms["SmsHistory"]['id_tchat']){
			$html.= '<td>'.$this->Html->link($sms['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sms['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		}else{
			if($sms["SmsHistory"]['id_message']){
				$html.= '<td>'.$this->Html->link($sms['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sms['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
			}else{
				if($sms["SmsHistory"]['id_client']){
					$html.= '<td>'.$this->Html->link($sms['User']['lastname'].' '.$sms['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sms['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
				}else{
					if($sms["SmsHistory"]['type'] == "ADMIN CONTACT"){
						$html.= '<td>'.$this->Html->link($sms['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sms['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
					}else{
						$html.= '<td>'.$sms["SmsHistory"]['email'].'</td>';	
					}
					
				}
			}
		}
		
		
		 $html.= '<td>'.$sms["SmsHistory"]['phone_number'].'</td>';
		 $html.= '<td>'.$sms["SmsHistory"]['content'].'</td>';
		  $html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$sms["SmsHistory"]['date_add']),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		if($sms["SmsHistory"]['id_tchat']){
			$html.= '<td>'.$sms["SmsHistory"]['respond'].'</td>';	
			$html.= '<td>'.$this->Html->link($sms["SmsHistory"]['client'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $sms["SmsHistory"]['id_client']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';	
		}else{
			$html.= '<td>&nbsp;</td><td>&nbsp;</td>';
		}
					
		
    $html.= '</tr>';
endforeach;



$html.= '</tbody></table>    </div>
</div>
</div>';
if($this->Paginator->param('pageCount') > 1) :
    $html.= $this->Metronic->pagination($this->Paginator);
endif;



echo $html;
?>