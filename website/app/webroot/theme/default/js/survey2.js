var nx_plannif = {
    plannif_intervalles : {},
    ondrag: false,
    init: function(){
        /* Clic sur une colonne */
            $('#planning th').click(function(){
                var index = ($(this).index()-1) * 2;
                $('#planning tbody tr').each(function(){
                    $(this).find('td.date_checkbox:eq('+ index +')').each(function(){
                        nx_plannif.clickHoraire($(this), true, true);
                    });
                    $(this).find('td.date_checkbox:eq('+ (index+1) +')').each(function(){
                        nx_plannif.clickHoraire($(this), true, true);
                    });
                });
            });

        /* Clic sur une date */
            $('td.date_label').click(function(){
                $(this).parents("tr").find('td:has(:input[type="hidden"])').each(function(){
                    nx_plannif.clickHoraire($(this), true, true);
                });
            });

        /* On affiche les intervalles déjà cochés */
            $('td:has(:input[checked="checked"])').addClass("date_checked");

        /* On passe sur une colonne */
            $("table#planning th").hover(function(){
                nx_plannif.ondrag = false;
            });
        /* On passe sur une date */
            $("table#planning td.date_label").hover(function(){
                nx_plannif.ondrag = false;
            });

        /* On interdit le dragndrop */
            $("table#planning").mousedown(function(){
                return false;
            });
        /* On sort de la table */
            $("table#planning").hover(function(){}, function(){
                nx_plannif.ondrag = false;
            });

        /* On clic */
            $('td:has(:input[type="hidden"])').click(function(){
                nx_plannif.ondrag = false;
                nx_plannif.clickHoraire($(this), true, false);

            });


        $('td:has(:input[type="hidden"])').mouseover(function(){
            if (nx_plannif.ondrag == true){
                nx_plannif.clickHoraire($(this), false, false)
            }
        });
        $('td:has(:input[type="hidden"])').mousedown(function(){
            nx_plannif.ondrag = true;
        });
        $('td:has(:input[type="hidden"])').mouseup(function(){
            nx_plannif.ondrag = false;
        });

        $('#AgentPlanningForm').submit(function(){
            $('.btn').prop('disabled',true);
            nx_plannif.generationIntervalle();
            $('input[type="hidden"]').remove();

            $('<input>').attr({
                type: 'hidden',
                name: 'data[Agent][planning]',
                value: JSON.stringify(nx_plannif.getIntervalles())
            }).appendTo('#AgentPlanningForm');
        });
    },

    getIntervalles: function(){
        return this.plannif_intervalles;
    },

    clickHoraire: function(tdElement, with_uncheck, force_check){
        var checkbox = tdElement.find("input[type=hidden]");
        if (force_check == true){
            checkbox.val(1);
            tdElement.addClass("date_checked");
        }else{
            if(tdElement.hasClass("date_checked")){
                checkbox.val('');
                tdElement.removeClass("date_checked");
            }else{
                checkbox.val(1);
                tdElement.addClass("date_checked");
            }
        }
    },

    addHoraire: function (i, dateIntervalle, h, m, type){
        if (nx_plannif.plannif_intervalles[dateIntervalle] === undefined)
            nx_plannif.plannif_intervalles[dateIntervalle] = {};

        if (nx_plannif.plannif_intervalles[dateIntervalle][i] == undefined)
            nx_plannif.plannif_intervalles[dateIntervalle][i] = {};

        nx_plannif.plannif_intervalles[dateIntervalle][i]['h'] = h;
        nx_plannif.plannif_intervalles[dateIntervalle][i]['m'] = m;
        nx_plannif.plannif_intervalles[dateIntervalle][i]['type'] = type;
    },

    nextDay: function(date){
        var splitDate = date.split('-');
        var myDate = new Date(splitDate[2]+'-'+splitDate[1]+'-'+splitDate[0]);
        myDate.setDate(myDate.getDate() + 1);
        if((myDate.getMonth()+1) < 10){
            var month = '0'+(myDate.getMonth()+1);
        }else var month = myDate.getMonth()+1;

        if((myDate.getDate()+1) < 10){
            var day = '0'+myDate.getDate();
        }else var day = myDate.getDate();

        return day+'-'+month+'-'+myDate.getFullYear();
    },

    generationIntervalle : function(){
        $('#planning tbody tr').each(function(){
            var inIntervalle = false;
            var dateIntervalle = $(this).attr('date');
            var i = 0;
            $(this).find('td:has(:input[type="checkbox"])').each(function(){
                var checkbox = $(this).find("input[type=checkbox]");
                var checked = checkbox.is(":checked");
                if(checked){
                    //Si on n'est pas dans un intervalle
                    if(!inIntervalle){
                        nx_plannif.addHoraire(i,dateIntervalle,checkbox.attr('h'),checkbox.attr('m'),'debut');
                        inIntervalle = true;
                        i++;
                    }
                }else{
                    if(inIntervalle){
                        nx_plannif.addHoraire(i,dateIntervalle,checkbox.attr('h'),checkbox.attr('m'),'fin');
                        inIntervalle = false;
                        i++;
                    }
                }

                //Gestion du cas de fin de journée
                //Si dernière checkbox de la journée
                if(checkbox.attr('h') == 23 && checkbox.attr('m') == 30){

                    dateIntervalle = nx_plannif.nextDay(dateIntervalle);
                    //Si on est dans un intervalle
                    if(inIntervalle){
                        nx_plannif.addHoraire(-1,dateIntervalle,0,0,'fin');
                    }
                }
            });
        });
    }
}

$(document).ready(function(){ 
	nx_plannif.init();
	$('.btn-survey').bind( "click",function(e) {
		
		
		//verif si planning saisie
		var planning_check = false;
		$(document).find("#planning").find("input").each(function() {
            var domclic = $(this);
			if(domclic.val() == 1){
				planning_check = true;
			}
        });
		if(planning_check){
			if (confirm("Valider les informations saisies dans ce questionnaire ?")) {
				$('#AgentsSurveyAgentForm').submit();			   		
			}
		}else{
			alert("Merci de remplir le planning.");
			e.preventDefault();
			e.isImmediatePropagationStopped(); 
		}
		
	});
});