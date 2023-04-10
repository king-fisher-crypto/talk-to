var nxAdminMain = {
    init: function(){
        //Initialise les lightbox pour les refus
        this.initModals();

    },
    initModals : function(){
        //Initialise les lightbox
        $('.nx_refuselightbox').click(function(){
            nxAdminMain.openUrlInModal($(this).attr('href'), 'eventRefuse');
            return false;
        });
        $('.nx_editreview').click(function(){
            nxAdminMain.openUrlInModal($(this).attr('href'), 'eventEdit');
            return false;
        });
        $('.nx_editpresentation').click(function(){
            nxAdminMain.openUrlInModal($(this).attr('href'), 'eventEdit');
            return false;
        });
		$('.nx_viewcomm').click(function(){
            nxAdminMain.openUrlInModal($(this).attr('href'));
            return false;
        });
		$('.nx_modal_stripe').click(function(){
            nxAdminMain.openUrlInStripeModal($(this).attr('href'), 'eventStripeDone');
			$(document).find('#target_action').remove();
			$( "<div id=\"target_action\"></div>" ).appendTo($(this).parent());
            return false;
        });
    },
	openUrlInStripeModal: function(url, nameEvent){
        $("#myModal").remove();
        this.ajaxRequest(url, [], function(html){
            try {
                //Si aucune entite trouvée
                var result = JSON.parse(html);
                if(result.url != undefined)
                    window.location.assign(result.url);
            } catch (e) {}

            $("body").append(html);
            $("#myModal").modal({
                backdrop: true,
                keyboard: true,
                show: true
            });
            $("#myModal div[class*=admin_content_btn]").hide();
			//nx_tinymce.init();
            //Lorsque la lightbox est prête
            $("#myModal").bind('shown.bs.modal', function (e) {
                //Pour éviter le double click
                $("#myModal form")[0][1].focus();

                if(nameEvent === 'eventStripeRefuse')
                    nxAdminMain.eventStripeRefuse();
                else if(nameEvent === 'eventStripeDone')
                    nxAdminMain.eventStripeDone();
				else{
					$(document).find('#target_action').remove();
				}
            });

			//$(document).find('.page-content:nth-child(2)').remove();

        }, 'html');
    },
    openUrlInModal: function(url, nameEvent){
        $("#myModal").remove();
        this.ajaxRequest(url, [], function(html){
            try {
                //Si aucune entite trouvée
                var result = JSON.parse(html);
                if(result.url != undefined)
                    window.location.assign(result.url);
            } catch (e) {}

            $("body").append(html);
            $("#myModal").modal({
                backdrop: true,
                keyboard: true,
                show: true
            });
            $("#myModal div[class*=admin_content_btn]").hide();
            //Lorsque la lightbox est prête
            $("#myModal").bind('shown.bs.modal', function (e) {
                //Pour éviter le double click
                $("#myModal form")[0][1].focus();
                if(nameEvent === 'eventRefuse')
                    nxAdminMain.eventRefuse();
                else if(nameEvent === 'eventEdit')
                    nxAdminMain.eventEdit();
            });

        }, 'html');
    },
	eventStripeDone : function(){
        $('.ok_admin_modal').click(function(){
            $(this).prop('disabled', true);
            $('#myModal p[class="none"]').removeClass('none');
            var form = $(this).parents(".modal-dialog").find("form");
            var url = form.attr("action");
            var vars = form.serialize();
            vars+= '&isAjax=1';
            nxAdminMain.ajaxRequest(url, vars, function(json){
                //Si aucune entite pour cet id
               /* if(json.url != undefined)
                    window.location.assign(json.url);
                else if(json.id !== false)
                    $('td[id="'+json.id+'"]').html(json.content);*/
                $("#myModal").modal('hide');
				$("#myModal").off();
                if(json.msg != undefined){
					alert(json.msg);
					if(json.action != undefined){
						$(document).find('#target_action').parent().html(json.action);
					}
				}else{
					location.reload();
				}

            })
        });

		 $('.btn.btn-default').click(function(){
			 $(document).find('#target_action').remove();
		 });
    },
	eventStripeRefuse : function(){
        $('.ok_admin_modal').click(function(){
            $(this).prop('disabled', true);
            $('#myModal p[class="none"]').removeClass('none');
            var form = $(this).parents(".modal-dialog").find("form");
            var url = form.attr("action");
            var vars = form.serialize();
            vars+= '&isAjax=1';
            nxAdminMain.ajaxRequest(url, vars, function(json){
                $("#myModal").modal('hide');
                window.location.assign(json.url);
            })
        });
    },
    eventRefuse : function(){
        $('.ok_admin_modal').click(function(){
            $(this).prop('disabled', true);
            $('#myModal p[class="none"]').removeClass('none');
            var form = $(this).parents(".modal-dialog").find("form");
            var url = form.attr("action");
            var vars = form.serialize();
            vars+= '&isAjax=1';
            nxAdminMain.ajaxRequest(url, vars, function(json){
                $("#myModal").modal('hide');
                window.location.assign(json.url);
            })
        });
    },
    eventEdit : function(){
        $('.ok_admin_modal').click(function(){
            $(this).prop('disabled', true);
            $('#myModal p[class="none"]').removeClass('none');
            var form = $(this).parents(".modal-dialog").find("form");
            var url = form.attr("action");
            var vars = form.serialize();
			var sendmail = 0;
			if($("#ReviewSendMail").size()>0){
				if ($('#ReviewSendMail').attr('checked')){
					sendmail = 1;
				}
			}
            vars+= '&isAjax=1';
            nxAdminMain.ajaxRequest(url, vars, function(json){
                //Si aucune entite pour cet id
                if(json.url != undefined)
                    window.location.assign(json.url);
                else if(json.id !== false)
                    $('td[id="'+json.id+'"]').html(json.content);
                $("#myModal").modal('hide');
                if(json.msg != undefined){
					if(sendmail)json.msg = "Modification enregistrée et mail envoyé.";
					alert(json.msg);
				}
            })
        });
    },
    ajaxRequest: function(url, postVars, callback, dataType){
        if (dataType == undefined)
            dataType = 'json';
        $.ajax({
            type: 'POST',
            dataType: dataType,
            url: url,
            data: postVars,
            success: function(datas){
                if (callback != undefined)
                    callback(datas);
            }
        });
    }
}

$(document).ready(function(){
	nxAdminMain.init();
	jQuery(document).on('click', "div.radio span", function() {
		//jQuery(this).find('input').addClass('checked');
		//jQuery(this).find('span').addClass('checked');
		jQuery(this).parent().parent().find('span').removeClass('checked');
		jQuery(this).parent().find('span').addClass('checked');
		jQuery(this).parent().next().click();
	  });
	//agent status
	 jQuery(".page-container").on('change', '.agent_status_select', function() {
		jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/ws/W_saveAgentStatus.php",
					data        : {status:jQuery(this).val(),
					   },
					success: function(data) {
						var returnedData = JSON.parse(data);
					}
		  });
	  });

	//agent status
	jQuery(".page-container").on('click', '.show_text_factured', function() {
		jQuery(this).parent().find('textarea').css('display','block');
	  });
	 jQuery(".page-container").on('change', '.agent_facture_choice', function() {
		var status = 0;
		if(jQuery(this).is(':checked')){
			status = 1;
		}


		jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/admin/agents/saveAgentFactured",
					data        : {id:jQuery(this).val(),status:status
					   },
					success: function(data) {
						alert(data);
					}
		  });
	  });
	 jQuery(".page-container").on('change', '.agent_solde_choice', function() {
		var status = 0;
		if(jQuery(this).is(':checked')){
			status = 1;
		}


		jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/ws/W_saveAgentSolded.php",
					data        : {id:jQuery(this).val(),status:status
					   },
					success: function(data) {
						alert(data);
					}
		  });
	  });

	jQuery(".page-container").on('change', '.agent_facture_sold', function() {
		var status = 0;
		if(jQuery(this).is(':checked')){
			status = 1;
		}


		jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/ws/W_saveAgentFactureSolded.php",
					data        : {id:jQuery(this).val(),status:status,date:jQuery(this).attr('rel')
					   },
					success: function(data) {
						alert(data);
					}
		  });
	  });

	jQuery(".page-container").on('change', '.agent_solde_choiceall', function() {


		var status = 0;
		if(jQuery(this).is(':checked')){
			status = 1;
		}

		$(document).find('.agent_solde_choice').each(function() {
			if(status){
				$(this).prop('checked', true);
					$(this).parent().addClass('checked');
			}else{
				$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
			}
			jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/ws/W_saveAgentSolded.php",
					data        : {id:jQuery(this).val(),status:status
					   },
					success: function(data) {

					}
		  });
		});


	  });


	  jQuery(".page-container").on('change', '.text_agent_factured', function() {
		jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/admin/agents/saveAgentFactured",
					data        : {id:jQuery(this).attr('id'),txt:jQuery(this).val(),
					   },
					success: function(data) {
						alert(data);
					}
		  });
	  });



	/* check SEO data length */
	if(jQuery(".decompte").size() > 0){
		jQuery('.page-container .decompte').each(function () {
			var dmax = jQuery(this).html();
			var champ = jQuery(this).parent().parent().find('.span10').val();
			var lgt = champ.length;
			var dactu = dmax - lgt;
			jQuery(this).html(dactu);
			//console.log(dactu);
			if(dactu < 0 ){
				jQuery(this).parent().css('background-color','#ff0000');
			}
		});

	}

	 jQuery(".table").on('change', '.AdminContentTitre', function() {
		updateRelanceMessageAdmin(jQuery(this).attr("rel"));
	  });
	jQuery(".table").on('change', '.AdminContentBonjour', function() {
		updateRelanceMessageAdmin(jQuery(this).attr("rel"));
	  });
	jQuery(".table").on('change', '.AdminContent', function() {
		updateRelanceMessageAdmin(jQuery(this).attr("rel"));
	  });
	jQuery(".table").on('change', '.AdminContentSignature', function() {
		updateRelanceMessageAdmin(jQuery(this).attr("rel"));
	  });

	jQuery(document).on('change', '#PaypalInfoOpposition', function() {
		updateMesssageOpposition(jQuery(this).attr("rel"),jQuery(this).val());
	  });
	jQuery(document).on('change', '#PaypalEmailOpposition', function() {
		updateEmailOpposition(jQuery(this).attr("rel"),jQuery(this).val());
	  });

	$(document).on("click", "#AdminRelanceCheckAll", function() {
		$(document).find('.table tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked')) {
					$(this).prop('checked', false);
					$(this).parent().removeClass('checked');
				}else{
					$(this).prop('checked', true);
					$(this).parent().addClass('checked');
				}
		});
	});


	$('#AdminRelanceBtnValidateAll').bind( "click",function(e) {
		e.preventDefault();
		var listing = new Array();

		$(document).find('.table tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked')) {

					listing.push($(this).attr('rel'));

				}
		});

		if(listing.length == 0){
			alert('Merci de sélectionner au moins 1 message');
		}else{
			if (confirm("Confirmer l\'envoi de ces message sélectionné ?")) {
			   nxAdminMain.ajaxRequest("/admins/sendRelanceAgent", {messages:listing}, function(t) {
					location.reload();
				   $("#relance_envoi_status").slideDown( 300 ).delay( 800 ).slideUp( 400 );
				});
			}
		}
	});

	$('.btnvalidaterelance').bind( "click",function(e) {
		e.preventDefault();
		var listing = new Array();
		var btn = $(this);
		if (confirm("Confirmer l\'envoi de ce message ?")) {
			 nxAdminMain.ajaxRequest("/admins/sendRelanceAgent", {id_message:$(this).parent().find('.AdminMessageid').val()}, function(t) {
					 btn.parent().parent().remove();
				 	$("#relance_envoi_status").slideDown( 300 ).delay( 800 ).slideUp( 400 );
				});

		}

	});


	$('.discussion .btn.mini.red-stripe').bind( "click",function(e) {
		e.preventDefault();
		var link = $(this).attr('href');
		if (confirm("Confirmer suppression de ce message ?")) {
			window.location = link;

		}

	});


	$('.btnvalidaterelancerefus').bind( "click",function(e) {
		e.preventDefault();
		var btn = $(this);
		if (confirm("Confirmer retablir ce message ?")) {
			 nxAdminMain.ajaxRequest("/admins/retablirRelanceAgent", {id_message:$(this).parent().find('.AdminMessageid').val()}, function(t) {
					 btn.parent().parent().remove();

				});

		}

	});

	$('.refuse_relance').bind( "click",function(e) {
		e.preventDefault();
		var listing = new Array();
		var btn = $(this);

		$( "#dialog-refus-reance-mail" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							"Annuler": function() {
							  $( this ).dialog( "close" );
							  return false;
							},
							"Refuser": function() {
								if($('.relance_refus_reason').val() == ''){
				alert('Merci de renseigner la raison du refus');

			}else{
							  $( this ).dialog( "close" );
							   nxAdminMain.ajaxRequest("/admins/refusRelanceAgent", {id_message:btn.parent().find('.AdminMessageid').val(), refus:$('.relance_refus_reason').val()}, function(t) {
					 			btn.parent().parent().remove();
								});
							}
							}
						  }
				});

	});


	$('#AdminRelanceBtnRefuseAll').bind( "click",function(e) {
		e.preventDefault();
		var listing = new Array();

		$(document).find('.table tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked')) {

					listing.push($(this).attr('rel'));

				}
		});
		if(listing.length == 0){
			alert('Merci de sélectionner au moins 1 message');
		}else{

		$( "#dialog-refus-reance-mail" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							"Annuler": function() {
							  $( this ).dialog( "close" );
							  return false;
							},
							"Refuser": function() {
								if($('.relance_refus_reason').val() == ''){
				alert('Merci de renseigner la raison du refus');

			}else{
							  $( this ).dialog( "close" );
							   nxAdminMain.ajaxRequest("/admins/refusRelanceAgent", {messages:listing, refus:$('.relance_refus_reason').val()}, function(t) {
					 			location.reload();
								});
							}
							}
						  }
				});

		}

	});

	$(document).on("click", ".alertelost", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
            var url = $(this).attr('href');
            var idComm = $(this).attr('comm');
		 	var line = $(this);//.parent().parent();


                //On envoie la requête
                nxAdminMain.ajaxRequest(url, {id_comm: idComm}, function(json){
                    if(json.return == false){
                        if(json.url !== undefined){
                            //Redirection
                            document.location.href = json.url;
                        }
                    }else if(json.return == true){
                        line.hide();
                    }
                },'json');

        });

	$(document).on("click", ".payment_valid", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
            var url = $(this).attr('href');
            var idPay = $(this).attr('payment');
		 	var line = $(this);//.parent().parent();

if (confirm("Confirmer la modification de cette transaction ?")) {
                //On envoie la requête
                nxAdminMain.ajaxRequest(url, {id_order: idPay}, function(json){
                    if(json.return == false){
                        if(json.url !== undefined){
                            //Redirection
                            document.location.href = json.url;
                        }
                    }else if(json.return == true){
                        line.hide();
                    }
                },'json');
}
        });

	$(document).on("click", ".payment_rembourse", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
            var url = $(this).attr('href');
            var idPay = $(this).attr('payment');
		 	var line = $(this);//.parent().parent();

if (confirm("Confirmer la modification de cette transaction ?")) {
                //On envoie la requête
                nxAdminMain.ajaxRequest(url, {id_order: idPay}, function(json){
                    if(json.return == false){
                        if(json.url !== undefined){
                            //Redirection
                            document.location.href = json.url;
                        }
                    }else if(json.return == true){
                        line.hide();
                    }
                },'json');
}
        });


	$(document).on("click", ".payment_decla", function(e) {
            //On stop la propagation de l'event
            e.stopPropagation();
            var url = $(this).attr('href');
            var idPay = $(this).attr('payment');
		 	var line = $(this);//.parent().parent();

			//nxAdminMain.openUrlInModal(url+'/'+idPay);
			window.location = url+'/'+idPay;
			return false;

        });

	if($('#livecom_container').size() > 0){
			live_com();
			window.setInterval(function(){
			  live_com();
			}, 10000);
		}

	$('.deleted_watchmail').bind( "click",function(e) {
		e.preventDefault();
		var btn = $(this);
		if (confirm("Confirmer suppression de ce message ?")) {
			 nxAdminMain.ajaxRequest("/admins/deleteMessagePrivate", {id_message:$(this).attr('rel')}, function(t) {
					 btn.parent().parent().remove();

				});

		}

	});

	$('.archive_watchmail').bind( "click",function(e) {
		e.preventDefault();
		var btn = $(this);
		if (confirm("Confirmer annumlation de ce message ?")) {
			 nxAdminMain.ajaxRequest("/admins/archiveMessagePrivate", {id_message:$(this).attr('rel')}, function(t) {
					btn.remove();

				});

		}

	});

	$('.deleted_sponsorship').bind( "click",function(e) {
		e.preventDefault();
		var btn = $(this);
		if (confirm("Confirmer blocage de ce parrainage ?")) {
			 nxAdminMain.ajaxRequest("/sponsorship/deactivateSponsorship", {id_sponsorship:$(this).attr('rel')}, function(t) {
					 btn.hide();

				});

		}

	});
	$('.active_sponsorship').bind( "click",function(e) {
		e.preventDefault();
		var btn = $(this);
		if (confirm("Confirmer blocage de ce parrainage ?")) {
			 nxAdminMain.ajaxRequest("/sponsorship/activateSponsorship", {id_sponsorship:$(this).attr('rel')}, function(t) {
					 btn.hide();

				});

		}

	});



	$(document).on("change", "#status_dispo", function(e) {
    	load_agent_listing_comm();
		$("#tableexpertcomm").find('input[type=checkbox]').each(function() {
			$(this).prop('checked', false);
		});
	});
	$(document).on("change", "#status_indispo", function(e) {
    	load_agent_listing_comm();
		$("#tableexpertcomm").find('input[type=checkbox]').each(function() {
			$(this).prop('checked', false);
		});
	});
	$(document).on("change", "#status_consult", function(e) {
    	load_agent_listing_comm();
		$("#tableexpertcomm").find('input[type=checkbox]').each(function() {
			$(this).prop('checked', false);
		});
	});
	$(document).on("change", "#modes_tel", function(e) {
    	load_agent_listing_comm();
		$("#tableexpertcomm").find('input[type=checkbox]').each(function() {
			$(this).prop('checked', false);
		});
	});
	$(document).on("change", "#modes_chat", function(e) {
    	load_agent_listing_comm();
		$("#tableexpertcomm").find('input[type=checkbox]').each(function() {
			$(this).prop('checked', false);
		});
	});
	$(document).on("change", "#modes_mail", function(e) {
    	load_agent_listing_comm();
		$("#tableexpertcomm").find('input[type=checkbox]').each(function() {
			$(this).prop('checked', false);
		});
	});

	$(document).on("change", "#checkallexpert", function(e) {

		if($(this).is(':checked')) {
			var push = true;
		}
		if(!$(this).is(':checked')) {
			var push = false;
		}

		$(document).find('#allexpertbody input[type=checkbox]').each(function() {
				$(this).prop('checked', push);
			});

		pushContactExpert();
	});
	$(document).on("change", ".checkboxexpertcontact", function(e) {
		pushContactExpert();
	});


	$(document).on("click", ".the_mode_phone", function() {
			if($("#mode_phone").css('display') == 'none'){
			   	$("#mode_phone").show();
			} else{
			   	$("#mode_phone").hide();
			   }
		});
	$(document).on("click", ".the_mode_email", function() {
			if($("#mode_email").css('display') == 'none'){
			   	$("#mode_email").show();
			   }else{
			   	$("#mode_email").hide();
			   }
		});
		$(document).on("click", ".the_mode_tchat", function() {
			if($("#mode_tchat").css('display') == 'none'){
			   	$("#mode_tchat").show();
			  } else{
			   	$("#mode_tchat").hide();
			   }
		});

	$('.doc_delete').bind( "click",function(e) {
		e.preventDefault();
		var link = $(this).attr('href');
		if (confirm("Confirmer suppression de ce document ?")) {
			window.location = link;

		}

	});


	$('#AdminOrderValidAll').bind( "click",function(e) {
		e.preventDefault();
		var listing = new Array();

		$(document).find('.table tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked') && $(this).hasClass('AdminOrderCheckbox')) {
					listing.push($(this).attr('rel'));
				}
		});

		if(listing.length == 0){
			alert('Merci de sélectionner au moins 1 facture');
		}else{
			if (confirm("Confirmer la validation de ces factures sélectionnées ?")) {
			   nxAdminMain.ajaxRequest("/admin/agents/order_stripe_valid_group", {liste:listing}, function(t) {

				   /* if(json.msg != undefined){
						alert(json.msg);
						location.reload();
					}else{
						location.reload();
					} */
				   location.reload();

				});
			}
		}
	});

	$('#AdminTchatValidAll').bind( "click",function(e) {
		e.preventDefault();
		var listing = new Array();

		$(document).find('.table tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked') && $(this).hasClass('AdminTchatCheckbox')) {
					listing.push($(this).attr('rel'));
				}
		});

		if(listing.length == 0){
			alert('Merci de sélectionner au moins 1 tchat');
		}else{
			if (confirm("Confirmer l\'acceptation de ces tchats sélectionnés ?")) {
			   nxAdminMain.ajaxRequest("/admin/admins/tchat_accept_group", {liste:listing}, function(t) {
				   location.reload();
				});
			}
		}
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

	jQuery(document).on('change', '.VATAgent_obs', function() {
		jQuery.ajax({
					type        : "POST",
					cache       : false,
					asynchronous : false,
					url         : "/admin/agents/vatObservation",
					data        : {id:jQuery(this).attr('rel'),txt:jQuery(this).val(),
					   },
					success: function(data) {
						//alert(data);
					}
		  });
	  });

	if(jQuery(document).find('#SupportAdminFilForm').size()>0 && jQuery(document).find('.FormSupportFil').size() > 0){

		var action = jQuery(document).find('.FormSupportFil').attr('action').replace('supports','support');
		jQuery(document).find('.FormSupportFil').attr('action',action);
		jQuery('#SupportAdminFilForm .btnmessage').bind( "click",function(e) {
			e.preventDefault();
			var btn = $(this);
			if (confirm("Confirmer l\'envoi de ce message ?")) {
				 jQuery('.FormSupportFil').submit();

			}

		});


	}

	if(jQuery(document).find('#SupportAdminFilModerateForm').size()>0){

		var action = jQuery(document).find('.FormSupportFil').attr('action').replace('supports','support');
		jQuery(document).find('.FormSupportFil').attr('action',action);
		$('#SupportAdminFilModerateForm .btnmessage').bind( "click",function(e) {
			e.preventDefault();
			var btn = $(this);
			if (confirm("Confirmer l\'envoi de ce message ?")) {
				 $('.FormSupportFil').submit();

			}

		});

        $('.delete_messge_support').bind( "click",function(e) {
            var answer=confirm('Confirmer suppression de ce message ?');
            if(answer){
                return true;
            }
            else{
                e.preventDefault();
            }
        });

	}

	if(jQuery(document).find('#SupportAdminWriteForm').size()>0){

		$('#SupportAdminWriteForm .btnmessage').bind( "click",function(e) {
			e.preventDefault();
			var btn = $(this);
			if(jQuery(document).find('#SupportAdminWriteForm').find('#who_id').val() == '' && jQuery(document).find('#SupportAdminWriteForm').find('#SupportGuestmail').val() == ''){
				alert('Merci de selectionner un destinataire ou un guest');
			}else{
				if (confirm("Confirmer l\'envoi de ce message ?")) {
					 $('.FormSupportFil').submit();

				}
			}
		});
	}

	$(document).find('.recherche_upd').autocomplete({
					minLength : 2 ,
					source : '/admin/support/list_destinataire',
					select: function( event, ui ) {
						 event.preventDefault();
						$(this).val(ui.item.label);
						$(document).find('#who_id').val(ui.item.value);
					  },
					focus: function( event, ui ) {
					   event.preventDefault();
					  // $(document).find('.recherche').val(ui.item.label);
					},
				});

	$('.preview_invoice').bind( "click",function(e) {
		e.preventDefault();

		$(document).find('#InvoicesPreview').val(1);

		if($(document).find('#InvoicesAdminEditForm').size()>0){
			$(document).find('#InvoicesAdminEditForm').attr('target','_blank');
			$(document).find('#InvoicesAdminEditForm').submit();
		}else{
			$(document).find('#InvoicesAdminCreateForm').attr('target','_blank');
			$(document).find('#InvoicesAdminCreateForm').submit();
		}

	});

	$('.submit_invoice').bind( "click",function(e) {
		e.preventDefault();

		$(document).find('#InvoicesPreview').val(0);

		if($(document).find('#InvoicesAdminEditForm').size()>0){
			$(document).find('#InvoicesAdminEditForm').attr('target','');
			$(document).find('#InvoicesAdminEditForm').submit();
		}else{
			$(document).find('#InvoicesAdminCreateForm').attr('target','');
			$(document).find('#InvoicesAdminCreateForm').submit();
		}

	});

	$('.tabbable .load-next-tab-btn').click(function() {
		var el = $(this);
		var id = el.parents('.tab-pane').next().attr('id');
		el.parents('.tabbable').first().find('ul.nav a[href="#' + id + '"]').click();
	});
	
	if($('#support_message_classif').size()>0){
		$('#support_message_classif').selectize({
			sortField: 'text'
		});
	}

});

function textAreaAdjust(o) {
  o.style.height = "1px";
  o.style.height = (25+o.scrollHeight)+"px";
}

function showTextareaMessage(o) {
 	$(o).hide();
	$(o).parent().find('#AdminContent').show();
}

function updateRelanceMessageAdmin(id_message){
	var message = '';
	message += $(document).find('.ligne_'+id_message).find('.AdminContentTitre').val()+"<!---->";
	message += $(document).find('.ligne_'+id_message).find('.AdminContentBonjour').val()+"<!---->";
	message += $(document).find('.ligne_'+id_message).find('.AdminContent').val()+"<!---->";
	message += $(document).find('.ligne_'+id_message).find('.AdminContentSignature').val();

	nxAdminMain.ajaxRequest("/admins/modereRelanceAgent", {message:message, id_message:id_message}, function(t) {
				alert('Modification enregistrée.');
				});
}

function updateMesssageOpposition(id_order, message){

	nxAdminMain.ajaxRequest("/admins/save_declarer_incident", {message:message, id_order:id_order}, function(t) {
				alert('Modification enregistrée.');
				});
}
function updateEmailOpposition(id_order, email){

	nxAdminMain.ajaxRequest("/admins/save_declarer_incident_email", {email:email, id_order:id_order}, function(t) {
				alert('Modification enregistrée.');
				});
}

function live_com(){
	nxAdminMain.ajaxRequest("/admins/livecom", {}, function(t) {
				$('#livecom_container').html(t.html);
		$('.agent_in').html(t.dispo);
		$('.agent_busy').html(t.busy);
		$('.agent_ratio').html(t.ratio);
		$('.agent_need').html(t.need);
		if(t.need < t.dispo){
			$('#livecom_container').parent().parent().removeClass('red').addClass('green');
		}else{
			$('#livecom_container').parent().parent().removeClass('green').addClass('red');
		}

				});
}

function load_agent_listing_comm(){

	var dispo = 0;
	if(jQuery("#status_dispo").is(':checked')){
		dispo = 1;
	}
	var indispo = 0;
	if(jQuery("#status_indispo").is(':checked')){
		indispo = 1;
	}
	var consult = 0;
	if(jQuery("#status_consult").is(':checked')){
		consult = 1;
	}
	var tel = 0;
	if(jQuery("#modes_tel").is(':checked')){
		tel = 1;
	}
	var chat = 0;
	if(jQuery("#modes_chat").is(':checked')){
		chat = 1;
	}
	var mail = 0;
	if(jQuery("#modes_mail").is(':checked')){
		mail = 1;
	}

	nxAdminMain.ajaxRequest("/admins/getListingAgentsComm", {dispo:dispo,indispo:indispo,consult:consult,tel:tel,chat:chat,mail:mail}, function(t) {
		$('#allexpertbody').html(t.html);
	});

}

function pushContactExpert(){
	var experts = '';
	$(document).find('#allexpertbody input[type=checkbox]').each(function() {
		if($(this).is(':checked')) {
			experts = experts  + $(this).attr('rel')+ ',';
		}
	});

	$(document).find('#expertsmails').val(experts);
	$(document).find('#expertssms').val(experts);

}
