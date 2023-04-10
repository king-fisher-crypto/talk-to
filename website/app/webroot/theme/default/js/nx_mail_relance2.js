var date_min = '';
var date_max = '';
var trie_date_relance = 0;
var nx_datepickerrange = {
    monthFrench : ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],

    init: function(){
		if($('.date-range').size() > 0){
        $('.date-range').daterangepicker(
            {
                opens: (App.isRTL() ? 'left' : 'right'),
                ranges: {
                    'Aujourd\'hui': ['today', 'today'],
                    'Hier': ['yesterday', 'yesterday'],
                    'Les 7 derniers jours': [Date.today().add({
                        days: -6
                    }), 'today'],
                    'Les 29 derniers jours': [Date.today().add({
                        days: -29
                    }), 'today'],
                    'Ce mois-ci': [Date.today().moveToFirstDayOfMonth(), Date.today().moveToLastDayOfMonth()],
                    'Le mois dernier': [Date.today().moveToFirstDayOfMonth().add({
                        months: -1
                    }), Date.today().moveToFirstDayOfMonth().add({
                        days: -1
                    })]
                },
                format: 'dd-MM-yyyy',
                separator: ' au ',
                locale: {
                    applyLabel: 'Appliquer',
                    fromLabel: 'Du',
                    toLabel: 'Au',
                    customRangeLabel: 'Personnaliser',
                    daysOfWeek: ["D", "L", "Ma", "Me", "J", "V", "S"],
                    monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
                    monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Déc"],
                    firstDay: 1
                },
                buttonClasses: ['green'],
                startDate: Date.today().add({
                    days: -29
                }),
                endDate: Date.today()
            },
            function (start, end) {
               // $('#form-date-range').submit();
				date_min = start;
				date_max = end;
				select_client_relance();
            }
        );
		
		}
		
    }
}

$(document).ready(function(){
	
	nx_datepickerrange.init();

	//select all
	
	$(document).on("click", ".table_client_relance th input[type=radio]", function() {	
		$(document).find('.table tbody input[type=radio]').each(function() {
				if($(this).is(':checked')) {
					//$(this).attr('checked',false);
					$(this).prop('checked', false);
				}else{
					//$(this).attr('checked','checked');
					$(this).prop('checked', true);
				}
		});	
	});
	$(document).on("click", ".table_mail_relance_refus th input[type=checkbox]", function() {	
		$(document).find('.table tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked')) {
					//$(this).attr('checked',false);
					$(this).prop('checked', false);
				}else{
					//$(this).attr('checked','checked');
					$(this).prop('checked', true);
				}
		});	
	});
	$('#form-date-range').bind( "submit",function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
	});	
	
	$('#AgentMailsRelanceForm .btn').bind( "click",function(e) {
	//$('#AgentMailsRelanceForm').submit(function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
		
		var listing = new Array();
		var destinataire_mail = "";
		$(document).find('.table tbody input[type=radio]').each(function() {
				if($(this).is(':checked')) {
					
					listing.push($(this).attr('rel'));
					destinataire_mail += $(this).parent().parent().find(".resize-img").html()+", ";
					
				}
		});	
		
		if(listing.length == 0){
			alert('Merci de sélectionner au moins 1 client');
		}else{
			
			if($("#AgentTitle").val() == '' || $("#AgentBonjour").val() == '' ||$("#AgentContent").val() == ''||$("#AgentSignature").val() == '' ){
				alert('Merci de renseigner tous les champs.');
			}else{
			
				$("#AgentListingClient").val(listing);
				$(".nb_destinataires").html(listing.length);
				$(".destinataires_listing").html(destinataire_mail);
				
				//remplir le preview mail
				var contenu = $("#AgentBonjour").val()+ "<br /><br />"+  $("#AgentContent").val().replace(/\n/g,"<br>")+ "<br /><br />"+$("#AgentSignature").val().replace(/\n/g,"<br>");
				$("#message_relance_body").html(contenu);
				
				$( "#dialog-confirm-mail" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							"Refuser": function() {
							  $( this ).dialog( "close" );
							  return false;
							},
							"Valider": function() {
							  $( this ).dialog( "close" );
							  $('#AgentMailsRelanceForm').submit();
							}
						  }
				});
			}
		}
	});
	
	$(document).on("change", ".date-range", function(e) {
		if($(this).val() == ''){
			date_min='';
			date_max = '';
			select_client_relance();
		}
	});
	
	
	$(document).on("change", "#select_relance", function(e) {
		e.preventDefault();
		select_client_relance();
		
	});
	
	
	$(document).on("keyup", "#pseudo_relance", function(e) {
		e.preventDefault();
		e.isImmediatePropagationStopped(); 
		select_client_relance();
	});
	
	$(document).on("click", ".archive_relancemail", function() {	
		var btn = $(this);
		nxMain.ajaxRequest("/agents/closeMessageRelance", {id_message:btn.attr('mail')}, function(t) {
					 			btn.parent().parent().remove();
								});	
	
	});
	
	$(document).on("click", ".archive_all_relancemail", function() {	
		var btn = $(this);
		var listing = new Array();
		$(document).find('.table_mail_relance_refus tbody input[type=checkbox]').each(function() {
				if($(this).is(':checked')) {
					
					listing.push($(this).attr('rel'));
					
				}
		});	
		if(listing.length == 0){
			alert('Merci de sélectionnez au moins un message.');
		}else{
			
			if (confirm("souhaitez-vous confirmer la suppression des messages sélectionnés ?") == true) {
			
			
			
			nxMain.ajaxRequest("/agents/closeAllMessageRelance", {messages:listing}, function(t) {
									btn.parent().parent().parent().parent().find('tbody').remove();
									});	
			$(document).find('.table_mail_relance_refus tbody input[type=checkbox]').each(function() {
					if($(this).is(':checked')) {

						$(this).parent().parent().remove();
					}
				});	
				} 
		}
	});
	
	$(document).on("click", ".table_client_relance input[type=radio]", function() {	
		var bonjour = 'Bonjour ';
		var nbcheck = 0;
		$(document).find('.table tbody input[type=radio]').each(function() {
				if($(this).is(':checked')) {
					bonjour += $(this).parent().parent().find(".resize-img").html()+", ";
					nbcheck ++;
				}
		
		});	
			if(nbcheck <= 1){
						$("#AgentBonjour").removeAttr('readonly');;
					}else{
						$("#AgentBonjour").prop('readonly','true');
					}
		$("#AgentBonjour").val(bonjour);
	});
	
	$(document).on("click", ".daterelance_edit", function() {	
		
		var containeur = $(this).parent();
		var user_id = $(this).attr('rel');
		
		$( "#dialog-relance-date" ).dialog({
						  resizable: false,
						  height: "auto",
						  width: 400,
						  modal: true,
						  buttons: {
							"Annuler": function() {
							  $( this ).dialog( "close" );
							  return false;
							},
							"Enregistrer": function() {
							  $( this ).dialog( "close" );
								nxMain.ajaxRequest("/agents/save_relance_date", {date_relance:$("#relance_date_input").val(), user_id:user_id},
								function(t) {
									containeur.html('<span>'+$("#relance_date_input").val()+'</span> <i class="glyphicon glyphicon-remove lfloat daterelance_cancel" rel="'+user_id+'"></i>');
								$("#relance_date_input").val('');
								});	
								
							}
						  }
				});
	});
	
	$(document).on("click", ".daterelance_cancel", function() {	
		
		var containeur = $(this).parent();
		var user_id = $(this).attr('rel');
		
		nxMain.ajaxRequest("/agents/cancel_relance_date", {user_id:user_id},
								function(t) {
									containeur.html('<i class="glyphicon glyphicon-pencil lfloat daterelance_edit" rel="'+user_id+'"></i>');
								
								});	
	});
	
	$(document).on("click", ".trie_date_relance", function() {	
		trie_date_relance = 1;
		select_client_relance();
		});
	
	
});

function select_client_relance(){
	
	nxMain.ajaxRequest("/agents/mails_relance_filtre", {date_min_f:date_min,date_max_f:date_max, pseudo_f:$("#pseudo_relance").val(), deja_f:$("#select_relance").val(), trie_date_relance : trie_date_relance}, function(t) {
		$(".table_client_relance tbody").html(t.html);
		$(".nx_openlightbox").unbind("click").click(function() {
            return nxMain.openModal($(this).attr("href"), $(this).attr("param")), !1
        })
	});	
	trie_date_relance = 0;
}
