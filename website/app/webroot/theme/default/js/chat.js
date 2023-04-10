var nx_chat = {
    lastIdEvent : '',
    lastIdMsg : '',
    nameClass: '',
    agent: false,
    idTimer: 0,
    idTimerInactif: 0,
    writting : 0,
    postMsg: 0,
    postStatus: 0,
    session: '',
    timerRefreshInterval: 2000,
    maxDisplayMsg: 50,
    url: {
        startSession: '',
        update: '',
        setStatus: '',
        postMessage: '',
        stopSession: '',
        hasCredit: '',
        getMessage: '',
        hasSession: '/chats/hasSession'
    },

    init: function(url, session, otherUrl, lastIdMsg, lastIdEvent){
        //url = ChatsController -> getTemplateChat()
        nxMain.ajaxRequest(url, {}, function(json){

            $('.chatbox').remove();
            //On Ajoute le chat
            $('body').append(json.html);
            //On affecte l'id à la div
            $('#chatbox_0').attr('id', session);
			
			
			
            //Les différentes url pour le chat
            if(otherUrl !== undefined){
                nx_chat.initUrl(otherUrl);
            }
            //L'id du dernier message affiché
            nx_chat.lastIdMsg = lastIdMsg;
            //L'id du dernier event
            nx_chat.lastIdEvent = lastIdEvent;
            //On stocke la session
            nx_chat.session = session;

            //On récupère la valeur de la class de la div
            nx_chat.nameClass = $('#'+nx_chat.session).attr('class');
            nx_chat.startSession();
            nx_chat.initEvent();
			
			$('.phoneboxnote').remove();
			if(json.html_note != ''){
				$('body').append(json.html_note);
				$('body').find('.phoneboxnote .name').html(json.phone_note_title);
				$('body').find('.phoneboxnote .content').val(json.phone_note_text);
				$('body').find('.phoneboxnote #phone_note_birthday_day option[value="'+json.phone_note_birthday_day+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_month option[value="'+json.phone_note_birthday_month+'"]').prop('selected', true);
					$('body').find('.phoneboxnote #phone_note_birthday_year option[value="'+json.phone_note_birthday_year+'"]').prop('selected', true);
					
					$('body').find('.phoneboxnote #phone_note_sexe option[value="'+json.phone_note_sexe+'"]').prop('selected', true);
				
				
				$('body').find('.phoneboxnote #phone_note_tchat').val(json.phone_note_tchat);
				$('body').find('.phoneboxnote #phonenoteagent').val(json.phone_note_agent);
				$('body').find('.phoneboxnote .nx_openlightbox_note').attr('param',json.phone_id_client);
				//$('body').find('.phoneboxnote .nx_openlightbox_note').remove();
				//$('.phoneboxnote').css('left','20px');
				$('.phoneboxnote').draggable();
			}
			
			
        });
    },
    
    initUrl : function(urls){
        nx_chat.url.startSession = urls.urlStartSession;
        nx_chat.url.update = urls.urlUpdate;
        nx_chat.url.setStatus = urls.urlSetStatus;
        nx_chat.url.postMessage = urls.urlPostMessage;
        nx_chat.url.stopSession = urls.urlStopSession;
        nx_chat.url.hasCredit = urls.urlHasCredit;
        nx_chat.url.getMessage = urls.urlGetMessage;
    },
    initEvent : function(){
        /* On initialise les events sur les chatbox */

        //Lorsqu'une touche est pressée
        $('.chatbox textarea').unbind('keypress').keypress(function(event){
            //Touche Entree, submit le formulaire
            if(event.keyCode == 13 && event.shiftKey == false){
                //S'il y a un message a envoyer
                if($(this).val() !== '')
                    $('.chatbox form').submit();

                return false;
            }

            //Touche différente de supprimer ou retour arrière
            if(event.keyCode != 8 && event.keyCode != 46){
                //S'il n'écrivait pas et si on peut poster un statut
                if(nx_chat.writting == 0 && nx_chat.postStatus == 0){
                    nx_chat.writting = 1;
                    nx_chat.postStatus = 1;
                    nx_chat.setStatus({writting: 1, session: nx_chat.session, status: 'Online'});
                    nx_chat.initTimerInactif();
                }else{
                    clearTimeout(nx_chat.idTimerInactif);
                    nx_chat.initTimerInactif();
                }
            }

            return true;
        });

        //Lorsqu'une touche est relachée
        $('.chatbox textarea').unbind('keyup').keyup(function(event){
            //Les touches supprimer et retour en arrière uniquement
            if(event.keyCode == 8 || event.keyCode == 46){
                //Si plus de texte et qu'il était en train d'écrire et qu'on peut poster un statut
                if($(this).val() === '' && nx_chat.writting == 1 && nx_chat.postStatus == 0){
                    clearTimeout(nx_chat.idTimerInactif);
                    nx_chat.writting = 0;
                    nx_chat.postStatus = 1;
                    nx_chat.setStatus({writting: 0, session: nx_chat.session, status: 'Online'});
                }else if($(this).val() !== ''){
                    if(nx_chat.writting == 1){
                        clearTimeout(nx_chat.idTimerInactif);
                        nx_chat.initTimerInactif();
                    }else if(nx_chat.postStatus == 0){
                        nx_chat.writting = 1;
                        nx_chat.postStatus = 1;
                        nx_chat.setStatus({writting: 1, session: nx_chat.session, status: 'Online'});
                        nx_chat.initTimerInactif();
                    }
                }
            }

            return true;
        });

        //Lorsque la zone textarea perd le focus
        $('.chatbox textarea').unbind('focusout').focusout(function(){
            //Si il était en train d'écrire et si on peut poster
            if(nx_chat.writting == 1 && nx_chat.postStatus == 0){
                clearTimeout(nx_chat.idTimerInactif);
                nx_chat.writting = 0;
                nx_chat.postStatus = 1;
                nx_chat.setStatus({writting: 0, session: nx_chat.session, status: 'Online'});
            }
        });

        //Lorsque qu'on submit le form
        $('.chatbox form').unbind('submit').submit(function(){
            clearTimeout(nx_chat.idTimerInactif);
            //Si il n'y a pas un submit en cours
            if(nx_chat.postMsg == 0){
                nx_chat.postMsg = 1;
                //On récupère l'élément textarea
                var textaera = $(this).find('textarea')[0];
                //On récupère le message
                var msg = textaera.value;
                //Si le msg est vide
                if(msg !== ''){
                    //On envoie le message
                    nx_chat.setMessage({msg: msg, session: nx_chat.session, status: 'Online'});
                }else
                    nx_chat.postMsg = 0;
            }
            return false;
        });

        //Lorsque qu'on ferme la chatbox
        $('.chatbox .cb_close').unbind('click').click(function(){
            nxMain.ajaxRequest(nx_chat.url.stopSession, {}, function(json){
                if(json.msg !== undefined){
                    if(confirm(json.msg)){
                        clearTimeout(nx_chat.idTimer);
                        nx_chat.stopSession(nx_chat.session);
                    }
                }else{
                    clearTimeout(nx_chat.idTimer);
                    nx_chat.stopSession(nx_chat.session);
                }
            });
        });
		
		//Lorsque qu'on agrandit fenetre
        $('.chatbox .cb_growup').unbind('click').click(function(){
           
		   if($(".chatbox").css('width') != '600px'){
			   $(".chatbox").css('width','600px');
			   $(".chatbox").css('height','auto');
			   $(".chatbox .cb_history").css('min-height','230px');
			   $(".chatbox textarea").css('width','588px');
			   $(".chatbox .cb_history").css('font-size','15px');
		   }else{
				$(".chatbox").css('width','398px');
			   $(".chatbox").css('height','auto');
			   $(".chatbox .cb_history").css('min-height','100px');
			   $(".chatbox textarea").css('width','388px');   
			   $(".chatbox .cb_history").css('font-size','12px');
		   }
		   
        });
    },
    startSession : function(){
        //On va chercher les datas de l'agent
        //urlStartSession = ChatsController -> start_session()
        nxMain.ajaxRequest(nx_chat.url.startSession, {}, function(json){
            if(json.return === true){
				
                var chatbox = $('#'+json.data.session);
                if(chatbox.length !== 0){
                    //Le pseudo
                    chatbox.find('.cb_pseudo .name').html(json.data.pseudo);
                    //Le message
                    chatbox.find('.cb_pseudo .msg').html(json.data.msg);
                    if(json.data.agent === undefined){
                        //La photo
                        chatbox.find('.cb_pseudo .avatar img').attr('src', '/'+json.data.photo);
                    }
                }

                nx_chat.maxDisplayMsg = json.data.maxDisplay;
                //Un agent ??
                if(json.data.agent !== undefined && json.data.agent === true){
                    nx_chat.agent = true;
                }

                //On affiche le chat
                chatbox.show();
				/*$.playSound("https://www.talkappdev.com/media/sonnerie/0159");
				document.getElementById("audiosonnerie").play();*/

                //On affiche les derniers messages
                nx_chat.getLastMessage();
                chatbox.find('textarea').focus();
            }
        });
    },
    getLastMessage : function(){
        nxMain.ajaxRequest(nx_chat.url.getMessage, {session: nx_chat.session}, function(json){
            if(json.return === true){
                //On met à jour l'id du dernier message affiché
                if(json.lastIdMsg !== undefined){
                    nx_chat.lastIdMsg = json.lastIdMsg;
                }
				var classcss = '';
                //On affiche les messages
                for(var key in json.messages){
					if(json.messages[key].name != 'Moi')classcss = 'txt_grey'; else classcss = '';;
                    nx_chat.addMessage(json.messages[key].time, json.messages[key].name, json.messages[key].content, true, classcss);
                }
                //On ajoute le dernier event
                $('#'+nx_chat.session+' .cb_pseudo .msg').html(json.event);
                //On descend la scrollbar
                nx_chat.moveScroll();
            }
            //On lance le timer
            nx_chat.initTimer();
        });
    },
    initTimer : function(){
        nx_chat.idTimer = setTimeout(function(){
            nx_chat.hasUpdate();
        },nx_chat.timerRefreshInterval);
    },
    initTimerInactif : function(){
        nx_chat.idTimerInactif = setTimeout(function(){
            nx_chat.postStatus = 1;
            nx_chat.setStatus({writting: 0, session: nx_chat.session, status: 'Online'});
        },4000);
    },
    setStatus : function(param){
        nxMain.ajaxRequest(nx_chat.url.setStatus, param, function(json){
            if(json.return === true){
                if(param.writting !== undefined){
                    nx_chat.writting = param.writting;
                }
            }
            //On peut de nouveau poster un status
            nx_chat.postStatus = 0;
        });
    },
    setMessage : function(param){
        nxMain.ajaxRequest(nx_chat.url.postMessage, param, function(json){
            if(json.return === true){
                //On vide le textarea
                $('.chatbox textarea').val('');
                nx_chat.writting = 0;
                //On ajoute le message
                nx_chat.addMessage(json.message.time, json.message.name, json.message.content, undefined, 'waiting');
                nx_chat.moveScroll();
            }else if(json.return === false){
                switch (json.typeError){
                    case 'save' :
                        alert(json.value);
                        break;
                }
            }
            //L'utilisateur peut de nouveau faire un submit
            nx_chat.postMsg = 0;
        });
    },
    hasUpdate : function(){
        //On arrete le timer
        clearTimeout(nx_chat.idTimer);
        //Assez de crédit ??
        nx_chat.hasCredit();

        nxMain.ajaxRequest(nx_chat.url.update, {lastIdEvent: nx_chat.lastIdEvent, lastIdMsg: nx_chat.lastIdMsg}, function(json){
			if(json === false){
				nx_chat.initTimer();
			}
            else if(json.return === true){
                //S'il y a des nouvelles données à afficher
                nx_chat.refreshData(json);
                //On relance le timer
                nx_chat.initTimer();
            }else if(json.return === false){
                if(json.hasSession == true || (json.hasSession == undefined && json.msg == undefined)){
                    //On relance le timer
                   nx_chat.initTimer();
                }else if(json.msg !== undefined){
                    alert(json.msg);
                }else{
                    nx_chat.deactivateChatbox(json.status);
                    nx_chat.addInfo(json.info);
                    nx_chat.addEvent('');
                    nx_chat.moveScroll();
                    $('.chatbox .cb_time').remove();
                    if(json.agent != undefined && json.agent == true){
                        nx_chat.hasSession();
                    }
                }
            }
        });
        return true;
    },
    refreshData: function(chatData){
        if(chatData.return === true){
            //On met à jour les id
            nx_chat.lastIdEvent = chatData.lastIdEvent;
            nx_chat.lastIdMsg = chatData.lastIdMsg;
            //On affiche les dernières données
            nx_chat.refreshChatbox(chatData.data);
        }
    },
    refreshChatbox : function(data){
        //Gestion de l'evenement
        if(data['Event'] !== undefined){
            nx_chat.addEvent(data['Event'].writting.msg);
            nx_chat.addInfo(data['Event'].status.msg, data.session);
        }
        //Gestion des messages
        for(var key in data['Message']){
			if(data['Message'][key].name == 'Moi')
            nx_chat.addMessage(data['Message'][key].time, data['Message'][key].name, data['Message'][key].content, undefined, 'new_moi');
			else
			nx_chat.addMessage(data['Message'][key].time, data['Message'][key].name, data['Message'][key].content, undefined, 'new');	
        }
        nx_chat.displayNewMsg();
        nx_chat.disconnect(data);
        nx_chat.clearHistory();
        nx_chat.moveScroll();
    },
    displayNewMsg : function(){
		if(nx_chat.session !== undefined){
			//On supprime les message avec la classe "waiting"
			$('#'+nx_chat.session+' .cb_history ul li.waiting').remove();
			//On affiche les messages avec la classe "new"
			$('#'+nx_chat.session+' .cb_history ul li.new').removeClass('new').addClass('txt_grey');
			$('#'+nx_chat.session+' .cb_history ul li.new_moi').removeClass('new_moi');
		}
    },
    /**
     * Ajoute un message sur le chat (html)
     *
     * @param time
     * @param name
     * @param content
     * @param prepend
     * @param nameClass
     */
    addMessage : function(time, name, content, prepend, nameClass){
        if(prepend == undefined){
            if(time == undefined || name == undefined){
                $('#'+nx_chat.session+' .cb_history ul').append('<li'+(nameClass !== undefined ?' class="'+nameClass+'"':'')+'>'+content+'</li>');
            }else{
                $('#'+nx_chat.session+' .cb_history ul').append('<li'+(nameClass !== undefined ?' class="'+nameClass+'"':'')+'><span>'+time+' - '+name+'</span>'+content+'</li>');
            }
        }else{
            if(time == undefined || name == undefined){
                $('#'+nx_chat.session+' .cb_history ul').prepend('<li'+(nameClass !== undefined ?' class="'+nameClass+'"':'')+'>'+content+'</li>');
            }else{
                $('#'+nx_chat.session+' .cb_history ul').prepend('<li'+(nameClass !== undefined ?' class="'+nameClass+'"':'')+'><span>'+time+' - '+name+'</span>'+content+'</li>');
            }
        }
    },
    addInfo : function(msg, session){
        if(session != undefined){
            $('#'+session+' .msg').html(msg);
        }else{
            $('.chatbox .msg').html(msg);
        }
    },
    addEvent : function(msg){
        $('#'+nx_chat.session+' .event').html(msg);
    },
    /**
     * Met la scrollbar à niveau
     */
    moveScroll: function(){
        var divEl = $('#'+nx_chat.session+' .cb_history')[0];
        divEl.scrollTop = divEl.scrollHeight;
    },
    stopSession : function(session){
        nxMain.ajaxRequest(nx_chat.url.stopSession, {session : session}, function(json){
            if(json.return === true){
                clearTimeout(nx_chat.idTimer);
                $('#'+session).remove();
                if(json.agent != undefined && json.agent == true){
                    nx_chat.hasSession();
                }
            }else if(json.return === false){
                switch (json.typeError){
                    case 'update' :
                        alert(json.value);
                        break;
                }
            }
        });
    },
    /**
     * Désactive le chat si le status est Déconnecté
     * @param data
     */
    disconnect : function(data){
        if(data['Event'] !== undefined && data['Event'].status.value === 'Disconnecting'){
            nx_chat.deactivateChatbox(data['Event'].status.msg);
        }
    },
    deactivateChatbox : function(event){
        clearTimeout(nx_chat.idTimer);
        if(nx_chat.session != undefined){
            var chatbox = $('#'+nx_chat.session);
        }else{
            var chatbox = $('.chatbox');
            nx_chat.session = chatbox.attr('id');
        }
        //Message de l'event
        if(event != undefined){
            nx_chat.addMessage(undefined, undefined, event, undefined, undefined);
        }
        var formH = chatbox.find('form').height();
        var divH = chatbox.find('.cb_history').height();
        chatbox.find('form').remove();
        chatbox.find('.cb_history').height(formH + divH - 33);
        chatbox.find('.hide_btn_close').show();
        //On supprime les events
        nx_chat.rmEvent();
    },
    rmEvent : function(){
        //Lorsqu'une touche est pressée
        $('.chatbox textarea').unbind('keypress');

        //Lorsque la zone textarea perd le focus
        $('.chatbox textarea').unbind('focusout');

        //Lorsque qu'on submit le form
        $('.chatbox form').unbind('submit');

        //Lorsque qu'on ferme la chatbox
        $('.chatbox .cb_close').unbind('click').click(function(){
            $('#'+nx_chat.session).remove();
        });
    },
    /**
     *  Supprime les anciens si le nombre de message affiché dépasse le max autorisé
     */
    clearHistory : function(){
        var history = $('#'+nx_chat.session+' .cb_history');
        var nbrLi = history.find('li').length;
        if(nbrLi > nx_chat.maxDisplayMsg){
            $('#'+nx_chat.session+' .cb_history li').slice(0,15).remove();
        }
    },
    changeCounterStatus: function(status, chat){
        var shortcut = chat.find('.cb_time .cb_time_left');
        if (shortcut.hasClass(status))return false;
        shortcut.removeClass('danger');
        shortcut.removeClass('warning');

        if (status == 'danger'){
            shortcut.addClass('danger');
        }else if (status == 'warning'){
            shortcut.addClass('warning');
        }
    },
    hasCredit : function(){
        nxMain.ajaxRequest(nx_chat.url.hasCredit, {}, function(json){
            if(json.return === true){
                if(json.value !== undefined){
                    var chat = $('#'+json.session);
                    //1min
                    if(json.value <= 120){
                        nx_chat.changeCounterStatus('danger', chat);
                        if(nx_chat.agent)
                            chat.find('.cb_time .cb_time_left').html(json.msg.agent);
                        else
                            chat.find('.cb_time .cb_time_left').html(json.msg.client);
                    }else if(json.value <= 180){ //3min
                        nx_chat.changeCounterStatus('warning', chat);
                        if(nx_chat.agent)
                            chat.find('.cb_time .cb_time_left').html(json.msg.agent);
                        else
                            chat.find('.cb_time .cb_time_left').html(json.msg.client);
                    }else if(json.value <= 300){ //5min
                        nx_chat.changeCounterStatus('warning', chat);
                        if(nx_chat.agent)
                            chat.find('.cb_time .cb_time_left').html(json.msg.agent);
                        else
                            chat.find('.cb_time .cb_time_left').html(json.msg.client);
                    }else{
                        nx_chat.changeCounterStatus('', chat);
                        if(nx_chat.agent)
                            chat.find('.cb_time .cb_time_left').html(json.msg.agent);
                        else
                            chat.find('.cb_time .cb_time_left').html(json.msg.client);
                    }

                    if(nx_chat.agent)
                        chat.find('.time_consult').html(json.msg.time_value);
					
					if(nx_chat.agent && json.alert_time && json.alert_time.agent)
						chat.find('.alert_time').html(json.alert_time.agent);
					
					if(!nx_chat.agent && json.alert_time && json.alert_time.client)
						chat.find('.alert_time').html(json.alert_time.client);
					
					if(json.picture)
						chat.find('.cb_pictures').html(json.picture);
						
                }
            }else if(json.return === false){
                if(json.value != undefined)
                    alert(json.value);
            }
        });
    },
    hasSession : function(){
        nxMain.ajaxRequest(nx_chat.url.hasSession, {}, function(json){
            if(json.return === true){
                nx_chat.init(json.url, json.session, json.otherUrl, json.lastIdMsg, json.lastIdEvent);
            }else if(json.return === false){
                if(json.agent != undefined && json.agent == true){
                    setTimeout(function(){
                        nx_chat.hasSession();
                    },5000);
                }
            }
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
            async: true,
            success: function(datas){
                if (callback != undefined)
                    callback(datas);
            }
        });
    }
}