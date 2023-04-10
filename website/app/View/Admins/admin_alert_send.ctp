<?php

echo $this->Metronic->titlePage(__('Alertes envoyées'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Alertes envoyées'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
$dbb_r = new DATABASE_CONFIG();
$dbb_route = $dbb_r->default;
$mysqli = new mysqli($dbb_route['host'], $dbb_route['login'], $dbb_route['password'], $dbb_route['database']);


?>
<div class="row-fluid">
	    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Liste Alertes'); ?></div>
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
    $html.= '<th>'.$this->Paginator->sort('AlertHistory.alerts_id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('AlertHistory.alert_type', 'Type').'</th>';
	$html.= '<th>'.$this->Paginator->sort('Alert.users_id', 'Client').'</th>';
	$html.= '<th>'.$this->Paginator->sort('Alert.agent_id', 'Agent').'</th>';
    $html.= '<th>'.$this->Paginator->sort('Alert.phone_number', __('Tél.')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Alert.email', __('Email')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('AlertHistory.date_add', __('Date d\'envoi')).'</th>';
    $html.= '</tr></thead><tbody>';

foreach ($list_alerte AS $alerte):
	$type = '';
    $html.= '<tr>';
        $html.= '<td>'.$alerte["AlertHistory"]['alerts_id'].'</td>';
		$html.= '<td>'.$alerte["AlertHistory"]['alert_type'].'</td>';
		$html.= '<td>'.$this->Html->link($alerte['User']['lastname'].' '.$alerte['User']['firstname'],
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $alerte['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		$html.= '<td>'.$this->Html->link($alerte['Agent']['pseudo'],
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $alerte['Agent']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
		
		 $html.= '<td>'.$alerte["Alert"]['phone_number'].'</td>';
		 $html.= '<td>'.$alerte["Alert"]['email'].'</td>';
		  $html.= '<td>'.$this->Time->format(Tools::dateZoneUser($this->Session->read('Config.timezone_user'),$alerte["AlertHistory"]['date_add']),'%d/%m/%y %Hh%Mmin%Ss').'</td>';
		
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