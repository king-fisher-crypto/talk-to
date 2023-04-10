$(document).ready(function() {
	nx_chat.hasSession();
   	if($('#review_stars2').size()>0){
	$('#review_stars2').raty({
										  half   : true,
										  size   : 15,
										  score:   5,
										  target : '#evaluation_expert',
										  targetText: '',
										  targetType: 'hint',
										  hints  : ['Mauvais', 'Peu satisfaisant','Satisfaisant','Bon','Excellent'],
										   click: function(score, evt) {
											$("#AccountRate").val($("input[name=score]").val());
										  }
										});
	}
	
	$(document).on("click", ".phonebox .cb_notes", function() {	
		nxMain.ajaxRequest("/phones/hasclientnotes", {}, function(t) {
			$('.phoneboxnote').remove();
			$('body').append(t.html);
			$('body').find('.phoneboxnote .name').html(t.phone_note_title);
			$('body').find('.phoneboxnote .content').val(t.phone_note_text);
			$('body').find('.phoneboxnote #phone_note_call').val(t.phone_note_call);
			$('body').find('.phoneboxnote #phone_note_tchat').val(t.phone_note_tchat);
			$('body').find('.phoneboxnote #phonenoteagent').val(t.phone_note_agent);
			//$('body').find('.phoneboxnote #phone_note_birthday').val(t.phone_note_birthday);
					$('body').find('.phoneboxnote #phone_note_birthday_day option[value="'+t.phone_note_birthday_day+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_month option[value="'+t.phone_note_birthday_month+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_year option[value="'+t.phone_note_birthday_year+'"]').prop('selected', true);
			
			$('body').find('.phoneboxnote #phone_note_sexe option[value="'+t.phone_note_sexe+'"]').prop('selected', true);
			$('body').find('.phoneboxnote .nx_openlightbox_note').attr('param',t.phone_id_client);
			
			//$('body').find('.phoneboxnote .nx_openlightbox_note').remove();
			$('.phoneboxnote').draggable();
		});
		phoneboxnotationview = 1;
	});
	$(document).on("click", ".chatbox .cb_notes", function() {	
		nxMain.ajaxRequest("/phones/hasclientnotes", {}, function(t) {
			$('.phoneboxnote').remove();
			$('body').append(t.html);
			$('body').find('.phoneboxnote .name').html(t.phone_note_title);
			$('body').find('.phoneboxnote .content').val(t.phone_note_text);
			$('body').find('.phoneboxnote #phone_note_call').val(t.phone_note_call);
			$('body').find('.phoneboxnote #phone_note_tchat').val(t.phone_note_tchat);
			$('body').find('.phoneboxnote #phonenoteagent').val(t.phone_note_agent);
			//$('body').find('.phoneboxnote #phone_note_birthday').val(t.phone_note_birthday);
					$('body').find('.phoneboxnote #phone_note_birthday_day option[value="'+t.phone_note_birthday_day+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_month option[value="'+t.phone_note_birthday_month+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_year option[value="'+t.phone_note_birthday_year+'"]').prop('selected', true);
			$('body').find('.phoneboxnote #phone_note_sexe option[value="'+t.phone_note_sexe+'"]').prop('selected', true);
			$('body').find('.phoneboxnote .nx_openlightbox_note').attr('param',t.phone_id_client);
			//$('body').find('.phoneboxnote .nx_openlightbox_note').remove();
			$('.phoneboxnote').css('z-index','10000');
			$('.phoneboxnote').draggable();
		});
		phoneboxnotationview = 1;
	});
	
	$(document).on("click", ".phoneboxnote .cb_close", function() {	
		
		var birthday = $('#NotesHasclientnotesForm #phone_note_birthday_day').val()+'-'+$('#NotesHasclientnotesForm #phone_note_birthday_month').val()+'-'+$('#NotesHasclientnotesForm #phone_note_birthday_year').val();
		
		nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesHasclientnotesForm .content').val(),call:$('#NotesHasclientnotesForm #phone_note_call').val(),tchat:$('#NotesHasclientnotesForm #phone_note_tchat').val(),agent:$('#NotesHasclientnotesForm #phonenoteagent').val(),sexe:$('#NotesHasclientnotesForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
					
			});
		$('.phoneboxnote').css('z-index','2008');
		 $('.phoneboxnote').remove();
		 phoneboxnotationview = 0;
	});
	
	$(document).on("click", "#NotesHasclientnotesForm .btn", function() {
		
		var birthday = $('#NotesHasclientnotesForm #phone_note_birthday_day').val()+'-'+$('#NotesHasclientnotesForm #phone_note_birthday_month').val()+'-'+$('#NotesHasclientnotesForm #phone_note_birthday_year').val();

		  nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesHasclientnotesForm .content').val(),call:$('#NotesHasclientnotesForm #phone_note_call').val(),tchat:$('#NotesHasclientnotesForm #phone_note_tchat').val(),agent:$('#NotesHasclientnotesForm #phonenoteagent').val(),sexe:$('#NotesHasclientnotesForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
					
			});
	});
	
	$(document).on("change", "#NotesHasclientnotesForm .content", function() {	
		
		var birthday = $('#NotesHasclientnotesForm #phone_note_birthday_day').val()+'-'+$('#NotesHasclientnotesForm #phone_note_birthday_month').val()+'-'+$('#NotesHasclientnotesForm #phone_note_birthday_year').val();

		  nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesHasclientnotesForm .content').val(),call:$('#NotesHasclientnotesForm #phone_note_call').val(),tchat:$('#NotesHasclientnotesForm #phone_note_tchat').val(),agent:$('#NotesHasclientnotesForm #phonenoteagent').val(),sexe:$('#NotesHasclientnotesForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
					
			});
	});
	
	$(document).on("click", "#NotesShowclientnotesForm .btn", function() {	
		
		$(this).slideUp( 300 ).delay( 500 ).fadeIn( 400 );
		
		//$(this).css('background','#008000').html('OK').slideUp( 300 ).delay( 2000 ).css('background','#d2322d').html('Enregistrer').fadeIn( 400 );
		
		var birthday = $('#NotesShowclientnotesForm #phone_note_birthday_day').val()+'-'+$('#NotesShowclientnotesForm #phone_note_birthday_month').val()+'-'+$('#NotesShowclientnotesForm #phone_note_birthday_year').val();
		
		  nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesShowclientnotesForm .content').val(),call:$('#NotesShowclientnotesForm #phone_note_call').val(),tchat:$('#NotesShowclientnotesForm #phone_note_tchat').val(),agent:$('#NotesShowclientnotesForm #phonenoteagent').val(),sexe:$('#NotesShowclientnotesForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
			});
	});
	
	$(document).on("change", "#NotesShowclientnotesForm .content", function() {	
		
		var birthday = $('#NotesShowclientnotesForm #phone_note_birthday_day').val()+'-'+$('#NotesShowclientnotesForm #phone_note_birthday_month').val()+'-'+$('#NotesShowclientnotesForm #phone_note_birthday_year').val();

		  nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesShowclientnotesForm .content').val(),call:$('#NotesShowclientnotesForm #phone_note_call').val(),tchat:$('#NotesShowclientnotesForm #phone_note_tchat').val(),agent:$('#NotesShowclientnotesForm #phonenoteagent').val(),sexe:$('#NotesShowclientnotesForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
					
			});
	});
	
	$(document).on("click", "#NotesGetTemplateChatForm .btn", function() {	
		
		var birthday = $('#NotesGetTemplateChatForm #phone_note_birthday_day').val()+'-'+$('#NotesGetTemplateChatForm #phone_note_birthday_month').val()+'-'+$('#NotesGetTemplateChatForm #phone_note_birthday_year').val();

		  nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesGetTemplateChatForm .content').val(),call:$('#NotesGetTemplateChatForm #phone_note_call').val(),tchat:$('#NotesGetTemplateChatForm #phone_note_tchat').val(),agent:$('#NotesGetTemplateChatForm #phonenoteagent').val(),sexe:$('#NotesGetTemplateChatForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
					
			});
	});
	
	$(document).on("change", "#NotesGetTemplateChatForm .content", function() {	
		
		var birthday = $('#NotesGetTemplateChatForm #phone_note_birthday_day').val()+'-'+$('#NotesGetTemplateChatForm #phone_note_birthday_month').val()+'-'+$('#NotesGetTemplateChatForm #phone_note_birthday_year').val();

		  nxMain.ajaxRequest("/phones/addclientnotes", {note:$('#NotesGetTemplateChatForm .content').val(),call:$('#NotesGetTemplateChatForm #phone_note_call').val(),tchat:$('#NotesGetTemplateChatForm #phone_note_tchat').val(),agent:$('#NotesGetTemplateChatForm #phonenoteagent').val(),sexe:$('#NotesGetTemplateChatForm #phone_note_sexe').val(),birthday:birthday}, function(t) {
					
			});
	});
	
	$(document).on("click", ".phonenote_edit", function() {	
		nxMain.ajaxRequest("/phones/showclientnotes", {note:$(this).attr('rel')}, function(t) {
			phoneboxnotationview =1;
			$('.phoneboxnote').remove();
			$('body').append(t.html);
			$('body').find('.phoneboxnote .name').html(t.phone_note_title);
			$('body').find('.phoneboxnote .content').val(t.phone_note_text);
			$('body').find('.phoneboxnote #phone_note_call').val(t.phone_note_call);
			$('body').find('.phoneboxnote #phone_note_tchat').val(t.phone_note_tchat);
			$('body').find('.phoneboxnote #phonenoteagent').val(t.phone_note_agent);
			//$('body').find('.phoneboxnote #phone_note_birthday').val(t.phone_note_birthday);
			$('body').find('.phoneboxnote #phone_note_birthday_day option[value="'+t.phone_note_birthday_day+'"]').prop('selected', true);
			$('body').find('.phoneboxnote #phone_note_birthday_month option[value="'+t.phone_note_birthday_month+'"]').prop('selected', true);
			$('body').find('.phoneboxnote #phone_note_birthday_year option[value="'+t.phone_note_birthday_year+'"]').prop('selected', true);
			$('body').find('.phoneboxnote #phone_note_sexe option[value="'+t.phone_note_sexe+'"]').prop('selected', true);
			$('body').find('.phoneboxnote .nx_openlightbox_note').attr('param',t.id_client);
			$('.phoneboxnote').draggable();
			
		});
	});
	
	$(document).on("click", ".nx_openlightbox_note", function(e) {
		$(this).css('background','#008000');
		e.preventDefault();
		return nxMain.openModal($(this).attr("href"), $(this).attr("param")),$(this).css('background','#008000');
	});
	
	
	$(document).on("click", ".box_sponsorship_share_content_btn", function(e) {
		
		  var $temp = $("<input>");
		  $("body").append($temp);
		  $temp.val($('.box_sponsorship_share_content_url').html()).select();
		  document.execCommand("copy");
		  $temp.remove();
			var txt = $('.box_sponsorship_share_content_url').html();
			$('.box_sponsorship_share_content_url').addClass("highlighted");
			$('.box_sponsorship_share_content_url').html('<i>'+txt+'</i>');
			/*$('.box_sponsorship_share_content_url').addClass("highlighted").delay(1000).queue(function(){
				$(this).removeClass("highlighted").dequeue();
			});*/
		//$('.box_sponsorship_share_content_url').addClass('highlighted').delay(3000).removeClass('highlighted');
	});

	/*$(document).on("click", ".box_sponsorship_share_content_sharebox_content_mail", function(e) {
		$(document).find('.box_sponsorship_invit').css('display','inline-block');	
	});*/
	$(document).on("click", ".box_sponsorship_share_content_sharebox_content_facebook", function(e) {
		var url = $(".box_sponsorship_share_content_url").html();
		spiritPopup('https://www.facebook.com/share.php?u='+url);
	});
	$(document).on("click", ".box_sponsorship_share_content_sharebox_content_twitter", function(e) {
		var url = $(".box_sponsorship_share_content_url").html();
		spiritPopup('https://twitter.com/intent/tweet?url='+url+'&text='+encodeURIComponent('#agents Devenez mon filleul et obtenez 5min de agents gratuite sur Spiriteo'));
	});
	$(document).on("click", ".box_sponsorship_share_content_sharebox_content_google", function(e) {
		var url = $(".box_sponsorship_share_content_url").html();
		spiritPopup('https://plus.google.com/share?url='+url);
	});
	
	
	$(document).on("click", ".appointmentAnswerBtn", function(e) {
		//ouvrir le formulaire reponse
		if($(document).find( ".appointmentAnswer[rel='"+$(this).attr('appointment')+"']" ).css('display') == 'none'){
			$(document).find( ".appointmentAnswer[rel='"+$(this).attr('appointment')+"']" ).slideDown();
		}else{
			$(document).find( ".appointmentAnswer[rel='"+$(this).attr('appointment')+"']" ).slideUp();
		}
		
	});
	
	$(document).on("change", ".appointments_status", function(e) {
		var table = $(this).parent().parent().parent().parent();
		var status = $(this).val();
		if(status != ''){
			table.find( "tbody tr" ).css('display','none');
			table.find( "tbody tr[rel='"+status+"']" ).css('display','');
		}else{
			table.find( "tbody tr" ).css('display','');
		}
		
	});
	
	$(document).on("click", "#agentChoiceRDV1", function(e) {
		$(this).parent().parent().parent().find( "#agentsContent" ).prop('disabled', true);
	});
	$(document).on("click", "#agentChoiceRDV3", function(e) {
		$(this).parent().parent().parent().find( "#agentsContent" ).prop('disabled', true);
	});
	$(document).on("click", "#agentChoiceRDV2", function(e) {
		$(this).parent().parent().parent().find( "#agentsContent" ).prop('disabled', false);
	});
	
	var push_mform_mail = false;
	$('#MessageNewMailForm .btn-newmail').bind( "click",function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
		
			if($("#MessageContent").val() == '' ){
				alert('Merci de rediger un message.');
			}else{
				$('#MessageNewMailForm .btn-newmail').hide();
				push_mform_mail = true;
				$( "#dialog-confirm-mail" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							"Refuser": function() {
							  $( this ).dialog( "close" );
								$('#MessageNewMailForm .btn-newmail').show();
							  return false;
							},
							"Valider": function() {
							  $( this ).dialog( "close" );
							  $('#MessageNewMailForm').submit();
							}
						  }
				});
			}
	});
	
	if($('#MessageNewMailForm').size() > 0){
		
		 var confirmationMessage = "Etes-vous certain(e) de vouloir quitter cette page ? Vous n'avez pas encore validé votre message, voulez-vous quitter sans le terminer ?";
		window.addEventListener("beforeunload", function (e) {
			if($('#MessageContent').val() != '' && push_mform_mail == false){
				e.returnValue = confirmationMessage; 
			 (e || window.event).returnValue = confirmationMessage;     //Gecko + IE
			 return confirmationMessage;     
				
			}
		}); 
		
	}
	
	$(document).on("click", "#AgentMailsForm .checkminlgh", function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
		
		var nbCaract = $(document).find('#AgentContent').attr('data-minlength');
		var content = $(document).find('#AgentContent').val();
		if (content.length < nbCaract) {
		   $(document).find('.input-alert-txt').show();
		}else{
			$('#AgentMailsForm').submit();
		}
	});
	
	
	$(document).on("click", ".btn-modify-old-msg", function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
		$(this).parent().parent().find( "#AccountContent" ).css('display','block');
	});
	
	
	$(document).on("click", ".modal-consult .modal_consult_select li a", function(e) {
		var country = $(this).attr('country');
		var phone = $(this).attr('phone');
		var youare = $(this).attr('youare');
		$(document).find('.modal_consult_select').find('.dropdown-toggle.main-drop').find('img').attr('src','/theme/default/img/flag/'+country+'.png');
		$(document).find('.modal_consult_select').find('.dropdown-menu').find('.li_flag').removeClass('hide');
		$(document).find('.modal_consult_select').find('.dropdown-menu').find('.li_flag.'+country).addClass('hide');
		$(document).find('.modal_consult_step').find('.num_dynamic').html(phone);
		$(document).find('.consult-connected').find('.num_link_dynamic').attr('href','tel:'+phone);
		$(document).find('.consult-connected').find('.youare').html(youare);
		$(document).find('.dropdown.mobile-flag').removeClass('open');
	});
	
	$(document).find( ".user-logged.dropdown-accordion" ).hover(
	  function() {
		$( this ).addClass( "open" );
	  }, function() {
		//$( this ).removeClass( "open" );
	  }
	);
	
	$(document).on('click', '.chat_thumbnail .img-wrap .close', function () {
                var id = $(this).closest('.img-wrap').find('img').data('id');

                //to remove the deleted item from array
                var elementPos = chat_AttachmentArray.map(function (x) { return x.FileName; }).indexOf(id);
                if (elementPos !== -1) {
                    chat_AttachmentArray.splice(elementPos, 1);
                }
				
				$(this).parent().parent().remove();
		
                //to remove image tag
              /*  $(this).parent().find('img').not().remove();

                //to remove div tag that contain the image
                $(this).parent().find('div').not().remove();

                //to remove div tag that contain caption name
                $(this).parent().parent().find('div').not().remove();*/

                //to remove li tag
               /* var lis = document.querySelectorAll('#imgList li');
                for (var i = 0; li = lis[i]; i++) {
                    if (li.innerHTML == "") {
                        li.parentNode.removeChild(li);
                    }
                }*/
				chat_RemoveChatFile(id);

            });
	
	$(document).on("click", "#chat_files", function(e) {
		document.getElementById('chat_files').addEventListener('change', chat_handleFileSelect, false);
	});
	$(document).on("click", ".chat_picture", function(e) {
		e.preventDefault();
			e.stopImmediatePropagation();
			e.stopPropagation();
		$("#imageModal").remove();
        var t = '<div class="modal fade " id="imageModal" tabindex="-1" role="dialog" aria-hidden="true"><div class="modal-dialog"><div class="modal-img-content"><button type="button" class="close_modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button><img class="modal-img" src="" width="100px" height="100px"/></div></div></div>';
		 $("body").append(t);
		 var src = $(this).attr("href");
     	$(".modal-img").prop("src",src);
		
		$("#imageModal").modal({
			width:'auto',
            backdrop: 1,
            keyboard: 1,
            show: 1
        })
	});
	
	$(document).on("click", "#VAT_choice_1", function(e) {
		$(document).find('#txt_tva_notfr').show();
		$(document).find('#txt_tva_fr').hide();
		$(document).find('#choice_tva_fr').hide();
		$(document).find('#choice_tva_notfr').show();
		$(document).find('#desc_tva_fr').hide();
	});
	$(document).on("click", "#VAT_choice_0", function(e) {
		$(document).find('#txt_tva_fr').show();
		$(document).find('#choice_tva_fr').show();
		$(document).find('#choice_tva_notfr').hide();
		$(document).find('#txt_tva_notfr').hide();
		$(document).find('#desc_tva_fr').hide();
	});
	$(document).on("click", "#VAT_choice_3", function(e) {
		$(document).find('#desc_tva_fr').show();
		$(document).find('#desc_tva_notfr').hide();
	});
	$(document).on("click", "#VAT_choice_6", function(e) {
		$(document).find('#desc_tva_notfr').show();
		$(document).find('#desc_tva_fr').hide();
	});
	$(document).on("click", "#VAT_choice_2", function(e) {
		$(document).find('#desc_tva_fr').hide();
		$(document).find('#desc_tva_notfr').hide();
	});
	$(document).on("click", "#VAT_choice_4", function(e) {
		$(document).find('#desc_tva_fr').hide();
		$(document).find('#desc_tva_notfr').hide();
	});
	$(document).on("click", "#VAT_choice_5", function(e) {
		$(document).find('#desc_tva_fr').hide();
		$(document).find('#desc_tva_notfr').hide();
	});
	$(document).on("click", "#VAT_choice_7", function(e) {
		$(document).find('#desc_tva_fr').hide();
		$(document).find('#desc_tva_notfr').hide();
	});
	
	$(document).on("change", ".inputfiletwo", function(e) {
		if (parseInt($(this)[0].files.length) > 2){
         	alert(" 2 fichiers maximum !");
			$(this).val('');
        }
	});
	
	$('.div_readonly').bind( "click",function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped();
		
		return false;
	});
	
	$(document).on("change", ".country_choice", function(e) {
		nxMain.ajaxRequest("/agents/updateselectcountries", {address:$(document).find('.country_address').val(),society:$(document).find('.country_society').val()}, function(t) {
				if(t.return){
					$(document).find('.status_choice').empty().html(t.status);
					$(document).find('.vat_choice').empty().html(t.vat);
				}
			});
	});
	$(document).on("change", ".status_choice", function(e) {
		nxMain.ajaxRequest("/agents/updateselectstatus", {address:$(document).find('.country_address').val(),society:$(document).find('.country_society').val(),status:$(this).val()}, function(t) {
				if(t.return){
					$(document).find('.vat_choice').empty().html(t.vat);
				}
			});
	});
  
  if($(document).find('.mailcustomerupload').size() > 0){
    // enable fileuploader plugin
  $('input[name="data[Message][attachment]"]').fileuploader({
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
    
  }
  
	
});

//document.addEventListener("DOMContentLoaded", init, false);
	 //To save an array of attachments 
        var chat_AttachmentArray = [];

        //counter for attachment array
        var chat_arrCounter = 0;

        //to make sure the error message for number of files will be shown only one time.
        var chat_filesCounterAlertStatus = false;

/*function init() {
  //add javascript handlers for the file upload event
  document.getElementById('chat_files').addEventListener('change', handleFileSelect, false);
}*/

//the handler for file upload event
        function chat_handleFileSelect(e) {
            //to make sure the user select file/files
            if (!e.target.files) return;

            //To obtaine a File reference
            var files = e.target.files;

            // Loop through the FileList and then to render image files as thumbnails.
            for (var i = 0, f; f = files[i]; i++) {

                //instantiate a FileReader object to read its contents into memory
                var fileReader = new FileReader();

                // Closure to capture the file information and apply validation.
                fileReader.onload = (function (readerEvt) {
                    return function (e) {
                        
                        //Apply the validation rules for attachments upload
                        if(chat_ApplyFileValidationRules(readerEvt,e)){
							
							//Fill the array of attachment
							chat_FillAttachmentArray(e, readerEvt);
							
							chat_DownloadChatFile(readerEvt);
							
							//Render attachments thumbnails.
							chat_RenderThumbnail(e, readerEvt);

							
								
						}
                    };
                })(f);

                // Read in the image file as a data URL.
                // readAsDataURL: The result property will contain the file/blob's data encoded as a data URL.
                // More info about Data URI scheme https://en.wikipedia.org/wiki/Data_URI_scheme
                fileReader.readAsDataURL(f);
            }
           // document.getElementById('chat_files').addEventListener('change', handleFileSelect, false);
        }

 //Apply the validation rules for attachments upload
        function chat_ApplyFileValidationRules(readerEvt,e)
        {
            //To check file type according to upload conditions
            if (chat_CheckFileType(readerEvt.type) == false) {
                alert("Votre photo (" + readerEvt.name + ") n\'est pas au bon format. Formats autorisés : jpg/png/gif");
                e.preventDefault();
                return false;
            }

            //To check file Size according to upload conditions
            if (chat_CheckFileSize(readerEvt.size) == false) {
                alert("Votre photo (" + readerEvt.name + ") ne dois pas dépasser les 10 Mo");
                e.preventDefault();
                return false;
            }

            //To check files count according to upload conditions
            if (chat_CheckFilesCount(chat_AttachmentArray) == false) {
                if (!filesCounterAlertStatus) {
                    filesCounterAlertStatus = true;
                    alert("2 Photos maximum");
                }
                e.preventDefault();
                return false;
            }
			
			return true;
        }

        //To check file type according to upload conditions
        function chat_CheckFileType(fileType) {
            if (fileType == "image/jpeg") {
                return true;
            }
            else if (fileType == "image/png") {
                return true;
            }
            else if (fileType == "image/gif") {
                return true;
            }
            else {
                return false;
            }
            return true;
        }

        //To check file Size according to upload conditions
        function chat_CheckFileSize(fileSize) {
            if (fileSize < 10000000) {
                return true;
            }
            else {
                return false;
            }
            return true;
        }

        //To check files count according to upload conditions
        function chat_CheckFilesCount(chat_AttachmentArray) {
            //Since AttachmentArray.length return the next available index in the array, 
            //I have used the loop to get the real length
            var len = 0;
            for (var i = 0; i < chat_AttachmentArray.length; i++) {
                if (chat_AttachmentArray[i] !== undefined) {
                    len++;
                }
            }
            //To check the length does not exceed 2 files maximum
            if (len > 1) {
                return false;
            }
            else
            {
                return true;
            }
        }

        //Render attachments thumbnails.
        function chat_RenderThumbnail(e, readerEvt)
        {
            var li = document.createElement('li');
			var ul = document.getElementById('imgList');
            ul.appendChild(li);
            li.innerHTML = ['<div class="img-wrap"> <span class="close">&times;</span>' +
                '<img class="thumb" src="', e.target.result, '" title="', escape(readerEvt.name), '" data-id="',
                readerEvt.name, '"/>' + '</div>'].join('');

            var div = document.createElement('div');
            div.className = "FileNameCaptionStyle";
            li.appendChild(div);
            div.innerHTML = [readerEvt.name].join('');
           // document.getElementById('Filelist').insertBefore(ul, null);
        }

        //Fill the array of attachment
        function chat_FillAttachmentArray(e, readerEvt)
        {
            chat_AttachmentArray[chat_arrCounter] =
            {
                AttachmentType: 1,
                ObjectType: 1,
                FileName: readerEvt.name,
                FileDescription: "Attachment",
                NoteText: "",
                MimeType: readerEvt.type,
                Content: e.target.result.split("base64,")[1],
                FileSizeInBytes: readerEvt.size,
            };
            chat_arrCounter = chat_arrCounter + 1;
        }

		function chat_DownloadChatFile(readerEvt){
			
			var fd = new FormData(); 
			
            var file = $(document).find('#chat_files')[0].files[0]; 
            fd.append('file', file); 
			 $.ajax({ 
                    url: '/chats/uploadphoto', 
                    type: 'post', 
                    data: fd, 
                    contentType: false, 
                    processData: false, 
                    success: function(response){ 
                    }, 
                }); 
			if($(document).find('#chat_files')[0].files.length > 1){
				var file = $(document).find('#chat_files')[0].files[1]; 
            fd.append('file', file); 
			 $.ajax({ 
                    url: '/chats/uploadphoto', 
                    type: 'post', 
                    data: fd, 
                    contentType: false, 
                    processData: false, 
                    success: function(response){ 
                    }, 
                }); 
			}
		}

		function chat_RemoveChatFile(filename){
			var fd = new FormData(); 
			fd.append('filename', filename); 
			 $.ajax({ 
                    url: '/chats/removephoto', 
                    type: 'post', 
                    data: fd, 
                    contentType: false, 
                    processData: false, 
                    success: function(response){ 
                       
                    }, 
                }); 
		
		}