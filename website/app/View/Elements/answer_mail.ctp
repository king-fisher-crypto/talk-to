<?php

if($model === 'Account') :
	if ($canreply && $canAnswer):
       // if($private == 0 && !$isDeprecated)
        //    echo '<p class="panel-body"><span class="credit-mail">'.$creditMail.'</span> '.__('crédits seront prélévés pour le message').'</p>';

        if ($private == 1){
            if (!$otherThanMeIsAdmin){
                echo '<p class="panel-body" style="text-align:center">'.__('Une réponse vous sera apportée prochainement par votre agent (en fonction de sa disponibilité)').'</p>';
            }
        }elseif ($showHourAnswerAlert && !$isDeprecated)
            echo '<p class="panel-body text-center" style="text-align:center">'.__('Une réponse vous sera apportée par votre agent dans un délai maximum de 6 heures.').'</p>';

	endif;
	$phrase = 'Je confirme l\'envoi ainsi que le débit de 15 minutes de mon compte.';
	if(!$isDeprecated){
		if((int)$private > 0){
			echo $this->Form->create($model, array('action' => 'mails','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
												   
                                               'inputDefaults' => array(
                                                   'div' => 'form-group',
                                                   'between' => '<div class="col-lg-6">',
                                                   'after' => '</div>',
                                                   'class' => 'form-control'
                                               )
        	));
		}else{
			echo $this->Form->create($model, array('action' => 'mails','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
											   'onsubmit' => 'return confirm("'.$phrase.'")',
                                               'inputDefaults' => array(
                                                   'div' => 'form-group',
                                                   'between' => '<div class="col-lg-6">',
                                                   'after' => '</div>',
                                                   'class' => 'form-control'
                                               )
        	));
		}
	}else{
		
		echo '<p class="panel-body msg-permie-txt">'.__('Le délai de réponse est dépassé par votre expert, la consultation vous a été automatiquement remboursée. Vous pouvez renvoyer cet Email ou le clôturer.').'</p>';
		echo $this->Form->create($model, array('action' => 'mails_deprecated','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1, 'enctype' => 'multipart/form-data',
											    'onsubmit' => 'return confirm("'.$phrase.'")',
                                               'inputDefaults' => array(
                                                   'div' => 'form-group',
                                                   'between' => '<div class="col-lg-6">',
                                                   'after' => '</div>',
                                                   'class' => 'form-control'
                                               )
        ));
	}
elseif($model === 'Agent'):
	
		 if ($isDeprecated){
                echo '<p class="panel-body msg-permie-txt2">'.__('Délai de réponse dépassé, client automatiquement remboursé.').'</p>';
            }
		
        echo $this->Form->create($model, array('action' => 'mails','nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
                                               'onsubmit' => ((int)$private > 0 ?'return confirm("'.__('C\'est une discussion privée, les discussions privées ne sont pas facturées. Dans votre intérêt, ne faites pas de consultation dans une discussion privée.').'")':false),
                                               'inputDefaults' => array(
                                                   'div' => 'form-group',
                                                   'between' => '<div class="col-lg-6">',
                                                   'after' => '</div>',
                                                   'class' => 'form-control'
                                               )
        ));
endif;
	
$maxlgth = '';
$minlgth = '';
$css_minlgth = '';
$msg_suite = '';
if ($private == 1 && $to_id != Configure::read('Admin.id') && $model === 'Account'){
	$maxlgth = '120';	
	$msg_suite = '(Les messages privés sont limités à 120 caractères)';
}
if ($private == 0 && $to_id != Configure::read('Admin.id') && $model === 'Agent'){
	$minlgth = '1000';
	$css_minlgth = 'checkminlgh';
}
if ($isDeprecated){

	if($model == 'Account'){
		echo '<div class="panel-body" style="margin-top:10px;">';
				echo $this->Form->inputs(array(
					'mail_id' => array('type' => 'hidden', 'value' => $idMail),
					'content' => array('label' => false, 'required' => true,'maxlength' => $maxlgth, 'type' => 'textarea', 'value' => $mail_content, 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 3, 'style'=>'display:none')
				));
				if ($canAnswer):
					echo $this->Form->end(array(
						'label' => __('Relancer'),
						'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0',
						'div' => array('class' => 'form-group margin-tp text-center'),
						'before' => '<button class="btn btn-pink btn-pink-modified btn-small-modified mb0 btn-modify-old-msg">Modifier</button>&nbsp;&nbsp;'
					));
				else:
						echo '<p style="color:#FF0000; font-weight:bold;text-align:center;padding:0 10px;">'.__('Votre expert n\'est pas disponible par Email actuellement.').'</p>';
				endif;
				echo '</div>';
	}
	
}else{
	if ($canActive):
	if ($canAnswer):
		if ($canreply):

			if ($canpost):
				echo '<div class="panel-body">';
				if($model === 'Account')
				echo $this->Form->inputs(array(
					'mail_id' => array('type' => 'hidden', 'value' => $idMail),
					'content' => array('label' => false, 'required' => true,'maxlength' => $maxlgth, 'type' => 'textarea', 'placeholder' => __('Votre réponse.').' '.$msg_suite, 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 3)
				));
				else
				echo $this->Form->inputs(array(
					'mail_id' => array('type' => 'hidden', 'value' => $idMail),
					'content' => array('label' => false, 'required' => true,'maxlength' => $maxlgth,'data-minlength' => $minlgth, 'type' => 'textarea', 'placeholder' => __('Votre réponse.').' '.$msg_suite, 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 3)
				));
				echo '<p style="display:none;color:#FF0000;text-align:center" class="input-alert-txt">'.__('Merci de rédiger au moins '.$minlgth.' caractères pour votre mail.').'</p>';

				if($model === 'Account' && $to_id != Configure::read('Admin.id')){
					//echo $this->Form->input('attachment', array('label' => array('text' => __('Joindre une ou deux photos(s) (.jpg .png .gif)'), 'class' => 'control-label col-lg-4 norequired'), 'type' => 'file', 'accept' => 'image/*', 'multiple' => true));
          echo '<div class="form-group">
						<label for="AccountAttachment" class="control-label col-lg-3 norequired ">Joindre une ou deux photo(s)<br> (.jpg .png .gif)</label>
						<div class="col-lg-8">
							<input type="file" name="data[Account][attachment]" multiple="multiple" data-fileuploader-limit="2">
						</div>
					</div>';
					//echo '<div class="form-group"><label for="AccountAttachment" class="control-label col-lg-4 norequired ">Joindre une ou deux photo(s)<br> (.jpg .png .gif)</label><div class="col-lg-7"><input type="file" name="data[Account][attachment][]" class="form-control inputfiletwo" accept="image/*" multiple="multiple" id="AccountAttachment"></div></div>';
					//echo $this->Form->input('attachment2', array('label' => array('text' =>'', 'class' => 'control-label col-lg-4 norequired'), 'type' => 'file', 'accept' => 'image/*'));
				}else{
					echo $this->Form->input('attachment', array('type' => 'hidden'));
					//echo $this->Form->input('attachment2', array('type' => 'hidden'));
				}
				echo $this->Form->end(array(
					'label' => __('Envoyer'),
					'class' => 'btn btn-pink btn-pink-modified btn-small-modified mb0 '.$css_minlgth,
					'div' => array('class' => 'form-group margin-tp')
				));
				echo '</div>';
			else:
				echo '<p style="" class="msg-alert-txt"">'.__('Vous n\'avez pas assez de crédits pour répondre à ce message<br />Veuillez recharger votre compte.').'</p>';

				echo $this->Form->inputs(array(
					'mail_id' => array('type' => 'hidden', 'value' => $idMail),
					'content' => array('disabled' => true,'label' => false, 'required' => true, 'type' => 'textarea', 'placeholder' => __('Votre réponse'), 'between' => '<div class="col-lg-12 col-md-12 col-sm-12">', 'rows' => 3)
				));
				
			endif;
		else:
			if($model == 'Agent'){
				echo '<p style="color:#FF0000; font-weight:bold;text-align:center;padding:0 10px;">'.__('Vous avez atteint le nombre maximum de messages privés autorisés pour ce client par tranche de 30 jours.').'</p>';
			}else{
				echo '<p style="color:#FF0000; font-weight:bold;text-align:center;padding:0 10px;">'.__('Vous avez atteint le nombre maximum journalier de messages privés autorisés pour cet expert.').'</p>';
			}

		endif;
	else:
			echo '<p style="color:#FF0000; font-weight:bold;text-align:center;padding:0 10px;">'.__('Votre expert n\'est pas disponible par Email actuellement.').'</p>';
	endif;
	else:
			echo '<p style="color:#FF0000; font-weight:bold;text-align:center;padding:0 10px;">'.__('Compte client suspendu temporairement.').'</p>';
	endif;
	
}
?>
<script>
$(document).ready(function() {
	// enable fileuploader plugin
  $('input[name="data[Account][attachment]"]').fileuploader({
		addMore: true,
		captions: {
			button: function (options) {
				return 'Choisir';
				//return 'Choisir ' + (options.limit == 1 ? 'fichier' : 'fichiers');
			},
			feedback: function(options) {
				return 'Choisir ' + (options.limit == 1 ? 'le fichier' : 'les fichiers') + ' à envoyer';
			},
			feedback2: function(options) {
				return options.length + ' ' + (options.length > 1 ? 'fichiers sélectionnés' : 'fichier sélectionné');
			},
			
      errors: {
          filesLimit: function(options) {
              return 'Seulement ${limit} ' + (options.limit == 1 ? 'fichier' : 'fichiers') + ' autorisé.'
          },
          filesType: 'Seulement ${limit} fichiers sont autorisé',
          fileSize: '${name} est trop volumineux! Veuillez choisir un fichier jusqu\'à ${fileMaxSize} Mo.',
          filesSizeAll: 'Les fichiers choisis sont trop volumineux! Veuillez sélectionner des fichiers jusqu\'à ${maxSize} Mo.',
          fileName: 'Un fichier avec le même nom ${name} a été déjà sélectionné.',
          remoteFile: 'Remote files are not allowed.',
          folderUpload: 'Les dossiers ne sont pas permis.',
      }
		},
		thumbnails: {
			item: '<li class="fileuploader-item" style="margin-bottom: 0px;padding: 5px 16px 10px 5px;">' +
				'<div class="columns">' +
				'<div class="column-thumbnail" style="display: none">${image}<span class="fileuploader-action-popup"></span></div>' +
				'<div class="column-title">' +
				'<div title="${name}">${name}</div>' +
				'<span>${size2}</span>' +
				'</div>' +
				'<div class="column-actions">' +
				'<button class="fileuploader-action fileuploader-action-remove" title="${captions.remove}"><i class="fileuploader-icon-remove"></i></a>' +
				'</div>' +
				'</div>' +
				'<div class="progress-bar2">${progressBar}<span></span></div>' +
				'</li>'
		}
	});
});
</script>