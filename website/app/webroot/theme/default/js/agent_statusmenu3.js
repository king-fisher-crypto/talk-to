var nx_agent_statusmenu = {
    urlChangeStatus: '',
    urlGetStatus: '',
    idTimer: 0,
    timerRefreshInterval: 30000,
    agent_number: 0,

    init: function (){
        nx_agent_statusmenu.urlChangeStatus = $('div.agent_statusmenu').attr('urlChangeStatus');
        nx_agent_statusmenu.urlGetStatus = $('div.agent_statusmenu').attr('urlGetStatus');
        nx_agent_statusmenu.timerRefreshInterval = $('div.agent_statusmenu').attr('timer');
        nx_agent_statusmenu.agent_number = $('div.agent_statusmenu').attr('an');

        /* Click sur un des choix */
        $('.agent_statusmenu li').click(function() {
				//On récupère le status cliqué
				var status = $(this).attr("rel");
				//On stop le refresh
				clearTimeout(nx_agent_statusmenu.idTimer);
				//On envoie le status demandé
				nx_agent_statusmenu.sendStatus(status);
				//On relance le refresh
				nx_agent_statusmenu.startTimer();
        });

        nx_agent_statusmenu.startTimer();
    },
    sendStatus : function(agentStatus){
        nxMain.ajaxRequest(nx_agent_statusmenu.urlChangeStatus, {status: agentStatus, agent_number: nx_agent_statusmenu.agent_number}, function(json){
            if(json.status != false){
                //On supprime la classe active
                $('.agent_statusmenu li.active').removeClass('active');
                //On rajoute la class active sur le status en question
                $('.agent_statusmenu li.'+json.status).addClass('active');


				var phrase = '<span style="color:#00891d;font-weight:bold">Votre compte est désormais Disponible</span>';
				if(json.status == 'unavailable'){
					var phrase = '<span style="color:#af0200;font-weight:bold">Votre compte est désormais Indisponible</span>';
				}
				if(json.status == 'empty'){
					var phrase = '<span style="color:#af0200;font-weight:bold;font-size:10;">Vous devez activer au moins l\'un des 3 modes de consultation.</span>';
				}
				$( "<div id=\"statusalert\" style=\"display:none\">"+phrase+"</div>" ).appendTo( ".agent_statusmenu" );

				$( "#statusalert" ).dialog({
					  resizable: false,
					  height: 75,
					  width: 400,
					  modal: true,
					  open : function(eve, ui) {
							   var item = $(this);
								 window.setTimeout(function() {
								   item.dialog('close');
								 },
								 2000);
							  },
					 close : function() {
							  $( "#statusalert" ).remove();
							  },
					});



                //On relance le hasSession pour le chat si available
                if(json.status === 'available'){
                    nx_chat.hasSession();
                }
                if(json.apiConnect != undefined){
                    alert(json.apiConnect)
                }
                if(json.apiDeconnect != undefined){
                    alert(json.apiDeconnect)
                }
            }
        },'json');
    },
    updateStatus : function(){
        clearTimeout(nx_agent_statusmenu.idTimer);
        nxMain.ajaxRequest(nx_agent_statusmenu.urlGetStatus, {agent_number: nx_agent_statusmenu.agent_number}, function(json){
            if(json.status != false){
                //On supprime la class active
                $('.agent_statusmenu li.active').removeClass('active');
                //On rajoute la class active sur le status en question
                $('.agent_statusmenu li.'+json.status).addClass('active');


            }
        },'json');

        nx_agent_statusmenu.startTimer();
    },
    startTimer : function(){
        nx_agent_statusmenu.idTimer = setTimeout(function(){
            nx_agent_statusmenu.updateStatus();
        },nx_agent_statusmenu.timerRefreshInterval);
    }
}

$(document).ready(function(){

	if($('div.agent_statusmenu').size() > 0){
		nx_agent_statusmenu.init();
	}
	$(document).on("click", "#login_modal a.list-group-item-block", function() {
		if(!$(this).hasClass('disabled')){
			if($(this).hasClass('active')){
				$(this).prev().attr('checked', false);
				$(this).removeClass('active');
			}else{
				$(this).prev().attr('checked', true);
				$(this).addClass('active');
			}

			$( "#login_modal" ).submit();
		}
	});
	$(".icon_alert-factured").mouseleave(function(){$(".box-factured").hide()});
	$(".icon_alert-factured").mouseenter(function(t){var e=$(this)[0].offsetLeft,n=$(this)[0].offsetTop,a=$(this).parent().find(".box-factured");a.show(),e-=a[0].offsetWidth/2-10,n-=a[0].offsetHeight+5,a.css({left:e+"px",top:n+"px"})});

    $(document).on("click", "#modal_num a.list-group-item-block", function() {
        var idInput=$(this).attr('id_input');
        if(!$(this).hasClass('disabled')){
            if($(this).hasClass('active')){
                $(this).prev().attr('checked', false);
                $(this).removeClass('active');
            }else{
                $(this).prev().attr('checked', true);
                $(this).addClass('active');
            }

            $('.input_num').attr('name', 'data[Agent][phone_number_to_use][]');
            $('#'+idInput).attr('name', 'data[Agent][phone_number_to_use][use]');
            $( "#modal_num" ).submit();
        }
    });

 });
