<?php

echo $this->Metronic->titlePage(__('Contacter les Experts'));
echo $this->Metronic->breadCrumb(array(
    0 => array(
        'text' => __('Accueil'), 'classes' => 'icon-home', 'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
    ),
    1 => array(
        'text' => __('SMS envoyé'), 'classes' => 'icon-envelope', 'link' => ''
    )
));

echo $this->Session->flash();
?>
<div class="row-fluid">
<div class="portlet box yellow">
    <div class="portlet-title">
        <div class="caption"><?php echo __('Nouveau message :'); ?></div>
		<div class="row-fluid">
			<div class="box yellow span8" style="clear:both;float:left;">
				<div class="pull-left">
					<h3>EMAIL</h3>
					<?php
						echo $this->Form->create('ContactExpertMail', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
						?>
						<?php
						echo $this->Form->input('', array('class' => 'input  margin-left margin-right', 'style' => 'width:500px','type' => 'textarea', 'placeholder' => __('Message'), 'div' => false));
						echo '<input class="" type="hidden" value="" name="data[ContactExperts]" id="expertsmails"><input class="btn green" type="submit" value="Envoyer">';
						echo '</form>'
					?>
			 </div>
			</div>
			<div class="box yellow span4" style="float:left;">
				<div class="pull-left">
					<h3>SMS</h3>
					<?php
						echo $this->Form->create('ContactExpertSms', array('nobootstrap' => 1,'class' => 'form-inline display-inline', 'default' => 1));
						?>
						<?php
						echo $this->Form->input('', array('class' => 'input  margin-left margin-right', 'type' => 'textarea', 'placeholder' => __('Message'), 'div' => false));
						echo '<input class="" type="hidden" value="" name="data[ContactExperts]" id="expertssms"><input class="btn green" type="submit" value="Envoyer">';
						echo '</form>'
					?>
				</div>
			</div>
		</div>
        
    </div>
	    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption"><?php echo __('Recherche'); ?></div>
        </div>

    <div class="portlet-body">
		<div class="row-fluid">	
			<div class="box span6" style="clear:both;float:left;">
			<fieldset>
				<legend>Status :</legend>
				<div class="">
					<input type="checkbox" id="status_dispo" name="status_dispo" /><label for="status_dispo" style="display:inline-block">Dispo</label>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" id="status_indispo" name="status_indispo" /><label for="status_indispo" style="display:inline-block">Indispo</label>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" id="status_consult" name="status_consult" /><label for="status_consult" style="display:inline-block">En consultation</label>
				</div>
			</fieldset>
			</div>
			<div class="box span6" style="float:left;">
			<fieldset>
				<legend>Modes :</legend>
				<div class="">
					<input type="checkbox" id="modes_tel" name="modes_tel" /><label for="modes_tel" style="display:inline-block">Téléphone</label>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" id="modes_chat" name="modes_chat" /><label for="modes_chat" style="display:inline-block">Tchat</label>
					&nbsp;&nbsp;&nbsp;&nbsp;
					<input type="checkbox" id="modes_mail" name="modes_mail" /><label for="modes_mail" style="display:inline-block">Email</label>
				</div>
			</fieldset>
			</div>
		</div>
<?php
$html.= '<table class="table table-striped table-hover table-bordered" style="margin-top:30px" id="tableexpertcomm"><thead><tr>';
$html.= '<th width="100"><input type="checkbox" id="checkallexpert"></th>';
$html.= '<th>'.$this->Paginator->sort('User.id', '#').'</th>';
$html.= '<th>'.$this->Paginator->sort('User.pseudo', __('Expert.')).'</th>';
$html.= '<th>'.$this->Paginator->sort('User.agent_number', __('Code')).'</th>';
$html.= '<th>'.$this->Paginator->sort('User.email', __('Email')).'</th>';
$html.= '<th>'.$this->Paginator->sort('User.phone_number2', __('Tél. mobile')).'</th>';
$html.= '</tr></thead><tbody id="allexpertbody">';

	foreach($agents as $agent){
		$html.= '<tr>';
			$html.= '<td><input type="checkbox" class="checkboxexpertcontact" id="checkexpert" rel="'.$agent['User']['id'].'"></td>';
			$html.= '<td>'.$agent['User']['id'].'</td>';
			$html.= '<td>'.$agent['User']['pseudo'].'</td>';
			$html.= '<td>'.$agent['User']['agent_number'].'</td>';
			$html.= '<td>'.$agent['User']['email'].'</td>';
			$html.= '<td>'.$agent['User']['phone_number2'].'</td>';
		$html.= '</tr>';
	}



$html.= '</tbody></table></div>
</div>
</div>';

echo $html;
?>