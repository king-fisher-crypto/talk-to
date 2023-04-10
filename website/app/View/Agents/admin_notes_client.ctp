<?php

echo $this->Metronic->titlePage(__('Notes'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('Notes'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();

$html = '';
if(empty($notes)) :
    $html.= __('Pas de notes');
else :
?>
<div class="row-fluid">
    <div class="portlet box blue">
<div class="portlet-title">
            <div class="caption"><?php echo __('Liste notes'); ?></div>
            <div class="pull-right">
                <span class="label-search"><?php echo __('Recherche') ?></span>
                <?php
                    echo $this->Form->create('Agent', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
                    echo $this->Form->input('name', array('class' => 'input-small  margin-left margin-right', 'type' => 'texte', 'label' => __('Pseudo / Nom').' :', 'div' => false));
                    echo '<input class="btn green" type="submit" value="Ok" /></form>';

                
                ?>
            </div>
        </div>
     <div class="portlet-body flip-scroll">
        <?php

    $html.= '<table class="table table-striped table-hover table-bordered"><thead><tr>';
    $html.= '<th>'.$this->Paginator->sort('Notes.id', '#').'</th>';
    $html.= '<th>'.$this->Paginator->sort('User.lastname', __('Client')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Agent.firstname', __('Agent')).'</th>';
    $html.= '<th>'. __('Message').'</th>';
     $html.= '<th>'.$this->Paginator->sort('Notes.birthday', __('Date de naissance')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Notes.date_crea', __('Date de creation')).'</th>';
    $html.= '<th>'.$this->Paginator->sort('Notes.date_upd', __('Date de mise a jour')).'</th>';
    $html.= '</tr></thead><tbody>';
endif;


foreach ($notes AS $note):

$message = '';
$user_id = $note['User']['id'];
$agent_id = $note['Agent']['id'];

    $html.= '<tr>';
        $html.= '<td>'.$note["Notes"]['id'].'</td>';
		
		$client = $note['User']['firstname'].' '.$note['User']['lastname'];
		if($note['User']['lastname'] == 'AUDIOTEL')
			$client = $note['Notes']['client'];
		
		 $html.= '<td>'.$this->Html->link($client,
                                            array(
                                                'controller' => 'accounts',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $note['User']['id']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
      //  $html.= '<td>'.$chat['User']['lastname'].' '.$chat['User']['firstname'].'</td>';
	  
	  $nom_agent = '';
	  foreach($agents as $agent){
		if($agent['User']['id'] == $note['Notes']['id_agent']){
			$nom_agent = $agent['User']['pseudo'];	
		}
	  }	
	  
	  $html.= '<td>'.$this->Html->link($nom_agent,
                                            array(
                                                'controller' => 'agents',
                                                'action' => 'view',
                                                'admin' => true,
                                                'id' => $note['Notes']['id_agent']
                                            ),
                                            array('class' => 'btn blue-stripe', 'escape' => false)
                                        ).'</td>';
       // $html.= '<td>'.$chat['Agent']['lastname'].' '.$chat['Agent']['firstname'].'</td>';
        $html.= '<td><div style="word-wrap: break-word; max-width:400px; padding:10px; font-size:11px">'.nl2br($note['Notes']['note']).'</div></td>';
       $html.= '<td>'.$note['Notes']['birthday'].'</td>';
        $html.= '<td>'.$note['Notes']['date_crea'].'</td>';
		$html.= '<td>'.$note['Notes']['date_upd'].'</td>';
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