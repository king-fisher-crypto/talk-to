<?php

echo $this->Metronic->titlePage(__('Callinfos'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Callinfos'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
	<div class="portlet box red">
		 <div class="portlet-title">
            <div class="caption"><?php //echo __('request failed') ?></div>
			<div class="pull-left">
                <span class="label-search"><?php echo __('Mail :') ?></span>
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('requestfailed', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:250px', 'type' => 'textaera', 'label' => 'request failed', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
				
            </div>
			 <p class="pull-left">Mails a traiter dans l'ordre de reception mais en priorité les requests :<br />"endconsult", "callstop" et "setstatus" dans l ordre aussi ! </p>
        </div>
	</div>
    <div class="portlet box yellow">
        <div class="portlet-title">
            <div class="caption"><span class="label-search"><?php echo __('Recherche') ?></span></div>
<div class="pull-left">
               <!-- <span class="label-search"><?php echo __('Recherche') ?></span>-->
                <?php
                    echo $this->Form->create('Admin', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
					echo $this->Form->input('callerid', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Numéro').' :', 'div' => false));
					echo $this->Form->input('sessionid', array('class' => 'input-small  margin-left margin-right', 'style' => 'width:120px', 'type' => 'texte', 'label' => __('Session ID').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
        <div class="portlet-body">

<?php
$html = '';
if(empty($callinfos)) :
    $html.= __('Pas de page');
else :
    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('Callinfo.id', '#').'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.line',  __('Line')).'</th>';
	$html.= '<th>'.__('Type call').'</th>';		
	$html.= '<th>'.$this->Paginator->sort('Callinfo.sessionid',  __('Session ID')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.callerid',  __('Caller ID')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.called_number',  __('Called Number')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.mob_info',  __('Mob Info')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.agent',  __('Agent')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.customer',  __('Client')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.accepted',  __('Etoile push')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.hungupby',  __('Qui Close')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.reason',  __('Raison')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.timestamp',  __('Date debut')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_check',  __('Date code client')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_getcredit',  __('Date get credit')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_getstatut',  __('Date get status')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_setstatut',  __('Date set status')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_start',  __('Debut communication')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_end',  __('Fin communication')).'</th>';
	$html.= '<th>'.$this->Paginator->sort('Callinfo.time_stop',  __('Date fin appel')).'</th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($callinfos AS $callinfo):
			
			$is_audiotel = false;
    $html.= '<tr>';
        $html.= '<td>'.$callinfo["Callinfo"]['call_info_id'].'</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['line'].'</td>';
		$called = '';
		if(substr_count($callinfo["Callinfo"]['line'],'901801885')){
			$is_audiotel = true;
			$called = 'suisse audiotel';
		}
			
		if(substr_count($callinfo["Callinfo"]['line'],'41225183456'))
			$called = 'suisse prepaye';	
		if(substr_count($callinfo["Callinfo"]['line'],'90755456')){
			$called = 'Belgique audiotel';
			$is_audiotel = true;
		}
					
		if(substr_count($callinfo["Callinfo"]['line'],'3235553456'))
			$called = 'Belgique prepaye';	
		if(substr_count($callinfo["Callinfo"]['line'],'90128222')){
			$called = 'Luxembourg audiotel';
			$is_audiotel = true;
		}
			
		if(substr_count($callinfo["Callinfo"]['line'],'27864456'))
			$called = 'Luxembourg prepaye';
		if(substr_count($callinfo["Callinfo"]['line'],'19007884466')){
			$called = 'Canada audiotel';
			$is_audiotel = true;
		}
			
		if(substr_count($callinfo["Callinfo"]['line'],'18442514456'))
			$called = 'Canada prepaye';
		if(substr_count($callinfo["Callinfo"]['line'],'33970736456'))
			$called = 'France prepaye';

		$html.= '<td>'.$called.'</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['sessionid'].'</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['callerid'].'</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['called_number'].'</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['mob_info'].'</td>';
		$html.= '<td>';
			if($callinfo["Callinfo"]['agent']) $html.=$this->Html->link($callinfo["Agent"]['pseudo'].' ('.$callinfo["Callinfo"]['agent'].')',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Agent"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       ).'<br />';
			if($callinfo["Callinfo"]['agent5'] && $callinfo["Callinfo"]['agent5'] != $callinfo["Callinfo"]['agent']) $html.=$this->Html->link($callinfo["Agent5"]['pseudo'].' ('.$callinfo["Callinfo"]['agent5'].')',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Agent5"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       ).'<br />';
			if($callinfo["Callinfo"]['agent4'] && $callinfo["Callinfo"]['agent4'] != $callinfo["Callinfo"]['agent']) $html.=$this->Html->link($callinfo["Agent4"]['pseudo'].' ('.$callinfo["Callinfo"]['agent4'].')',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Agent4"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       ).'<br />';
			if($callinfo["Callinfo"]['agent3'] && $callinfo["Callinfo"]['agent3'] != $callinfo["Callinfo"]['agent']) $html.=$this->Html->link($callinfo["Agent3"]['pseudo'].' ('.$callinfo["Callinfo"]['agent3'].')',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Agent3"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       ).'<br />';
			if($callinfo["Callinfo"]['agent2'] && $callinfo["Callinfo"]['agent2'] != $callinfo["Callinfo"]['agent']) $html.=$this->Html->link($callinfo["Agent2"]['pseudo'].' ('.$callinfo["Callinfo"]['agent2'].')',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Agent2"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       ).'<br />';
			if($callinfo["Callinfo"]['agent1'] && $callinfo["Callinfo"]['agent1'] != $callinfo["Callinfo"]['agent']) $html.=$this->Html->link($callinfo["Agent1"]['pseudo'].' ('.$callinfo["Callinfo"]['agent1'].')',
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Agent1"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       ).'<br />';
			
			
			
			
			
		$html.=	'</td>';
		$html.= '<td>';
			
				if($is_audiotel){
					$html.= 'AUDIO'.(substr($callinfo['Callinfo']['callerid'], -4)*15);
				}else{
				if($callinfo["Callinfo"]['customer']) $html.=$this->Html->link($callinfo["Client"]['firstname'].' ('.$callinfo["Callinfo"]['customer'].')',
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $callinfo["Client"]['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                       );
				}
		$html.= '</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['accepted'].'</td>';
			
		if($callinfo["Callinfo"]['hungupby'] == 'caller')$callinfo["Callinfo"]['hungupby'] = 'client';	
			if($callinfo["Callinfo"]['hungupby'] == 'called')$callinfo["Callinfo"]['hungupby'] = 'agent';
		$html.= '<td>'.$callinfo["Callinfo"]['hungupby'].'</td>';
		$html.= '<td>'.$callinfo["Callinfo"]['reason'].'</td>';
		if($callinfo["Callinfo"]['timestamp'])
		$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['timestamp'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';

		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_check'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_check'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_getcredit'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_getcredit'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_getstatut'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_getstatut'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_setstatut'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_setstatut'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_start'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_start'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_end'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_end'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		if($callinfo["Callinfo"]['time_stop'])
			$html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),date('Y-m-d H:i:s',$callinfo["Callinfo"]['time_stop'])),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		else
		$html.= '<td>&nbsp;</td>';
		
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