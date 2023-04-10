<?php
    echo $this->Metronic->titlePage(__('Supports'),__('Envoyer un message'));
    echo $this->Metronic->breadCrumb(array(
        0 => array(
            'text' => __('Accueil'),
            'classes' => 'icon-home',
            'link' => $this->Html->url(array('controller' => 'admins', 'action' => 'index', 'admin' => true))
        ),
        1 => array(
            'text' => __('Support'),
            'classes' => 'icon-bullhorn',
            'link' => $this->Html->url(array('controller' => 'support', 'action' => 'write', 'admin' => true))
        ),
    ));
    echo $this->Session->flash();
?>
<style>
  .ui-autocomplete {
    position: absolute;
    top: 100%;
    left: 0;
    z-index: 1000;
    float: left;
    display: none;
    min-width: 160px;   
    padding: 4px 0;
    margin: 0 0 10px 25px;
    list-style: none;
    background-color: #ffffff;
    border-color: #ccc;
    border-color: rgba(0, 0, 0, 0.2);
    border-style: solid;
    border-width: 1px;
    -webkit-border-radius: 5px;
    -moz-border-radius: 5px;
    border-radius: 5px;
    -webkit-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -moz-box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    -webkit-background-clip: padding-box;
    -moz-background-clip: padding;
    background-clip: padding-box;
    *border-right-width: 2px;
    *border-bottom-width: 2px;
}

.ui-menu-item > a.ui-corner-all {
    display: block;
    padding: 3px 15px;
    clear: both;
    font-weight: normal;
    line-height: 18px;
    color: #555555;
    white-space: nowrap;
    text-decoration: none;
}

.ui-state-hover, .ui-state-active {
    color: #ffffff;
    text-decoration: none;
    background-color: #0088cc;
    border-radius: 0px;
    -webkit-border-radius: 0px;
    -moz-border-radius: 0px;
    background-image: none;
}
.ui-helper-hidden-accessible{display:none;}
</style>
<div class="row-fluid">
    <div class="portlet box blue">
        <div class="portlet-title">
            <div class="caption">Ecrire un message</div>
        </div>
        <div class="portlet-body" style="display:inline-block;width:100%;">
			
			<div class="support_answer" style="margin-top:30px;">
				<?php
					$user = $this->Session->read('Auth.User');
					//echo '<h3>'.__('Votre réponse').'</h3>';
					echo $this->Form->create('Support', array('nobootstrap' => 1,'class' => 'form-horizontal FormSupportFil', 'default' => 1, 'enctype' => 'multipart/form-data',
														   'inputDefaults' => array(
															   'div' => 'form-group',
															   'between' => '<div class="span8">',
															   'after' => '</div>',
															   'class' => 'form-control span8'
														   )
					));
					?>
					<div class="row-fluid">
						<div class="span12">
						   <div class="control-group">
							<label for="SupportWho" class="control-label span3 required"><?php echo __('Destinataire'); ?></label>
							<div class="controls span8">
								<input type="text" name="data[Support][who]" class=" recherche_upd" required="required" id="SupportWho" style="min-width:50%" />
								<input type="hidden" name="data[Support][who_id]" id="who_id">
							</div>
						  </div>
						</div>
					</div>
					<div class="row-fluid">
						<div class="span12">
						   <div class="control-group">
							<label for="SupportGuestmail" class="control-label span3"><?php echo __('Ou Guest'); ?></label>
							<div class="controls span8">
								<input type="text" name="data[Support][guestmail]" class=""  id="SupportGuestmail" style="width:25%;margin-right:1%;margin-bottom:10px;" placeholder="Email" />
								<input type="text" name="data[Support][guestname]" class=""  id="SupportGuestname" style="width:25%;margin-right:1%;margin-bottom:10px;" placeholder="Nom" />
								<input type="text" name="data[Support][guestfirstname]" class=""  id="SupportGuestfirstname" style="width:25%;margin-right:1%;margin-bottom:10px;" placeholder="Prénom" />
							</div>
						  </div>
						</div>
					</div>
					 <div class="row-fluid">
						<div class="span12">
						   <div class="control-group">
							<label for="SupportTitle" class="control-label span3 required"><?php echo __('Sujet email'); ?></label>
							<div class="controls span8">
								<input type="text" name="data[Support][title]" class="" required="required" id="SupportTitle" style="min-width:50%" />
							</div>
						  </div>
						</div>
					</div>
				<?php
          $content_value = '<br /><br /><br />Cordialement,<br />'.$user['firstname'].'<br />Et toute l\'équipe Talkappdev';
          $content_value .= '<br /><br /><p style="font-size:10px">Nous vous remercions de répondre dans la continuité de ce message afin que nos équipes et collaborateurs puissent suivre cet échange et à ne pas ouvrir un nouveau ticket à chacune de vos réponses s\'il vous plait.</p>';
					echo $this->Form->inputs(array(
						'content' => array('label' => array('text' => __('Votre message'), 'class' => 'control-label span3  required'), 'required' => true, 'type' => 'textarea', 'class' => 'tinymce', 'style'=> 'width:100%', 'tinymce' => 'tinymce', 'value' => $content_value)
					));
				
					echo '<div class="form-group"><label for="SupportAttachment" class="control-label span3 norequired ">Joindre un fichier</label><div class="span8"><input type="file" name="data[Support][attachment][]" class="form-control inputfiletwo" multiple="multiple" id="SupportAttachment"></div></div>';

					echo $this->Form->end(array(
						'label' => 'Envoyer',
						'class' => 'btn btnmessage',
						'div' => array('class' => ' span10 offset2 support_btn_submit')
					));
				?>
			</div>
        </div>
    </div>
</div>