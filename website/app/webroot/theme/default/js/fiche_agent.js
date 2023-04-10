var block_clic = null;
var nx_fiche_agent = {
    customerCode: 0,
    agentCode: 0,
    url: '',
    refreshInterval: 8000,
    timer: 0,
    urlUpdate: '',

    init: function(){

        nx_fiche_agent.agentCode = $('.block-agenda').find('.block-agenda-an').html();
        nx_fiche_agent.url = $('.block-agenda').find('.block-agenda-url').html();
        nx_fiche_agent.urlUpdate = $('.header_agent_display[rel]').find('.header_agent_display_rel').html();

        $('.block-agenda .block-hour').mouseleave(function(){
            $('.box-rdv').hide();
        });

        $('.block-agenda .block-hour').mouseenter(function(e){
            //Top et left de la cellule horaire
            var left = $(this)[0].offsetLeft;
            var top = $(this)[0].offsetTop;
            //La box pour la cellule en question
            var box = $(this).parent().find('.box-rdv');
			
			var box_parent = box.parent();
			var box_day = box_parent.find('.block-hour-day').html();
			var box_h = box_parent.find('.block-hour-h').html();
			var box_m = box_parent.find('.block-hour-m').html();
			
			if(box_m == "0")box_m = "00";
			
			//check le text a coller dans popup
			if(box.find('.box-rdv-txt-free').size() > 0){
				var texte = $(document).find('.agenda-text-def-free').html();
				texte = texte.replace("#DAY#", box_day);
				texte = texte.replace("#H#", box_h);
				texte = texte.replace("#M#", box_m);
				box.find('.box-rdv-txt-free').html(texte);	
				
			}
			if(box.find('.box-rdv-txt-busy').size() > 0){
				box.find('.box-rdv-txt-busy').html($(document).find('.agenda-text-def-busy').html());	
			}
			if(box.find('.box-rdv-txt-cancel').size() > 0){
				
				var texte2 = $(document).find('.agenda-text-def-cancel').html();
				texte2 = texte2.replace("#DAY#", box_day);
				texte2 = texte2.replace("#H#", box_h);
				texte2 = texte2.replace("#M#", box_m);
				box.find('.box-rdv-txt-cancel').html(texte2);
			}
			
            box.show();
            //Pour centrer la box par rapport au curseur
            left -=  (box[0].offsetWidth/2) - 10;
            top -= (box[0].offsetHeight+5);

            box.css({
                left: left+'px',
                top: top+'px'
            });
        });

        $('.block-agenda .block-hour').click(function(){
            postVars = {
                agent_number: nx_fiche_agent.agentCode,
                date: $(this).find('.block-hour-day').html(),
                h: $(this).find('.block-hour-h').html(),
                m: $(this).find('.block-hour-m').html()
            };
            var box = $(this).parent().find('.box-rdv');
			var box_parent = box.parent();
			var box_day = box_parent.find('.block-hour-day').html();
			var box_h = box_parent.find('.block-hour-h').html();
			var box_m = box_parent.find('.block-hour-m').html();
			if(box_m == '0')box_m = '00';
            var block = $(this);
			if(!block.hasClass('disabled') && ( block.hasClass('appointmentcancel') || block.hasClass('demand') || $('.block-agenda .block-agenda-connected').html() == '')){
				nxMain.ajaxRequest(nx_fiche_agent.url, postVars, function(json){
					//En cas d'erreur ou d'acces interdit
					if(json.return === false){
						if(json.url !== undefined){
							//Redirection page login
							document.location.href = json.url;
						}
						if(json.reload != undefined){
							//refresh la page
							document.location.reload();
						}
						if(json.modal !== undefined && json.modal === true){
							$("#myModal").remove();
							$("body").append(json.content);
							$("#myModal").modal({
								backdrop: true,
								keyboard: true,
								show: true
							});
						}
					}else if(json.return === true){
						//En fonction de l'action effectuée
						if(json.action === 'delete'){
							//On supprime la class
							block.removeClass('appointment');
							box.html('<p class="box-rdv-txt-free"></p>');
							var texte = $(document).find('.agenda-text-def-free').html();
							texte = texte.replace("#DAY#", box_day);
							texte = texte.replace("#H#", box_h);
							texte = texte.replace("#M#", box_m);
							box.find('.box-rdv-txt-free').html(texte);	
						}else if(json.action === 'add'){
							//On ajoute la class
						    block.addClass('appointment');
							box.html('<p class="box-rdv-txt-cancel"></p>');
							var texte2 = $(document).find('.agenda-text-def-cancel').html();
							texte2 = texte2.replace("#DAY#", box_day);
							texte2 = texte2.replace("#H#", box_h);
							texte2 = texte2.replace("#M#", box_m);
							box.find('.box-rdv-txt-cancel').html(texte2);
							document.location.href = '/accounts/appointments';
						}
					}
					if(json.html !== undefined){
						//On change le contenu de la box
						//box[0].innerHTML = json.html;
					}
				}, 'json');
			}else{
				if(!block.hasClass('disabled')){
					if(block.hasClass('appointment')){
						block_clic = block;
						var texte4 = $(document).find('.agenda-text-def-demandcancel').html();
								texte4 = texte4.replace("#DAY#", box_day);
								texte4 = texte4.replace("#H#", box_h);
								texte4 = texte4.replace("#M#", box_m);
							$("#myModal").remove();
								$("body").append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog" style="width:300px"><div class="modal-content"><div class="modal-header"><button type="button" class="appointmentConfirmClose close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="m-title" id="myModalLabel" style="margin:0">Annulation du rendez-vous</h4></div><div class="modal-body"><div class="" style="font-size:16px; margin:0;text-align:justify">' + texte4 + "</div><div></div>");
								$("#myModal").modal({
									backdrop: 1,
									keyboard: 0,
									show: 1
								});	
					}else{
						block_clic = block;
						var texte4 = $(document).find('.agenda-text-def-demand').html();
								texte4 = texte4.replace("#DAY#", box_day);
								texte4 = texte4.replace("#H#", box_h);
								texte4 = texte4.replace("#M#", box_m);
							$("#myModal").remove();
								$("body").append('<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true"><div class="modal-dialog" style="width:300px"><div class="modal-content"><div class="modal-header"><button type="button" class="appointmentConfirmClose close" data-dismiss="modal" aria-hidden="true">×</button><h4 class="m-title" id="myModalLabel" style="margin:0">Demande de rendez-vous</h4></div><div class="modal-body"><div class="" style="font-size:16px; margin:0;text-align:justify">' + texte4 + "</div><div></div>");
								$("#myModal").modal({
									backdrop: 1,
									keyboard: 0,
									show: 1
								});	
					}
				}
			}
			
		});
		
        nx_fiche_agent.initAgent();
    },
    initAgent: function(){
        nx_fiche_agent.timer = setTimeout(function(){
            nx_fiche_agent.updateAgent();
        }, nx_fiche_agent.refreshInterval);
    },
    updateAgent: function(){
        clearTimeout(nx_fiche_agent.timer);
        nxMain.ajaxRequest(nx_fiche_agent.urlUpdate, {}, function(json){
            if(json.return === true){
                $('.header_agent_display .agent_status').removeClass('busy available unavailable').addClass(json.datas.status);
                $('.header_agent_display .agent_status').empty().html(json.datas.label);

                $('.container_status').removeClass('busy available unavailable').addClass(json.datas.status);
                $('.container_status .agent_status').removeClass('busy available unavailable').addClass(json.datas.status);
                $('.container_status .agent_status').empty().html(json.datas.label);



                //Téléphone
                $('.consult_phone').removeClass('disabled').addClass(json.datas.phone.class);
                //Chat
                $('.consult_chat').removeClass('disabled').addClass(json.datas.chat.class);
                $('.consult_chat .consult_action').empty().html(json.datas.chat.html);
                //E-mail
                $('.consult_email').removeClass('disabled').addClass(json.datas.email.class);
                $('.consult_email .consult_action').empty().html(json.datas.email.html);

            }
            nxMain.initChat();
            nxMain.initEmail();
            nx_fiche_agent.initAgent();
        })
    }
}

$(document).ready(function(){ nx_fiche_agent.init(); 

	$(".block-agenda").css('display','block');
	$(document).on("click", ".appointmentBtnConfirm", function() {
			block_clic.addClass('demand').click();
			$("#myModal .close").click();
			
		});							 
	$(document).on("click", ".appointmentBtnCancel", function() {
			block_clic.removeClass('appointment').addClass('appointmentcancel').click();
			$("#myModal .close").click();
		});	
});