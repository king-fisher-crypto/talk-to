var nx_message = {
    blockMail : '',
    blocAnswer : '',
    url: '',
    urlMail: '',
    urlUpdate: '',
    idUser: '',
    timer: '',
    interval: '',
    flagAnswer: false,

    init: function(){
        nx_message.blockMail = $('#mail-content');
        nx_message.blocAnswer = $('#mail-answer');
        nx_message.url = $('.mails').attr('url');
        nx_message.urlMail = $('.nav.nav-tabs').attr('rel');
        nx_message.urlUpdate = $('.nav.nav-tabs').attr('update');
        nx_message.idUser = $('.nav.nav-tabs').attr('user');

        $('.mails div.discussion table button.answer').click(function(e){
            
            var trElement = $(this).parent().parent().parent().parent().parent().parent();
            var idMail = trElement.attr('mail');
            var urlAnswerForm = $(this).attr('href');
			var url = $(this).attr('href');
			var dejaouvert = false;
			if(trElement.hasClass('selected')){
				dejaouvert = true;	
			}
			nx_message.blocAnswer.hide();
			$('.mails div.discussion').find('.answer').html('<i class="fa fa-plus-circle" aria-hidden="true"></i> Voir la discussion');
			if(!dejaouvert){
				nx_message.openDiscussion(trElement, urlAnswerForm, idMail);
				nx_message.answerForm(url, idMail);
				$(this).html('<i class="fa fa-minus-circle" aria-hidden="true"></i> Fermer la discussion');
				trElement.find('.msg-area-mobile').hide();
				nx_message.updateMailNoRead();
			}else{
				trElement.find('.msg-area-mobile').show();
				nx_message.closeDiscussion(trElement, urlAnswerForm, idMail);
				$(this).html('<i class="fa fa-plus-circle" aria-hidden="true"></i> Voir la discussion');
			}
        });

      /*  $('.answer').click(function(e){
            nx_message.flagAnswer = true;
			var trElement = $(this).parent().parent().parent().parent().parent().parent();
            var url = $(this).attr('href');
            var idMail = $(this).attr('mail');
			var dejaouvert = false;
			if(trElement.hasClass('selected')){
				dejaouvert = true;	
			}
            //On va chercher le formulaire de réponse
			if(!dejaouvert)
            nx_message.answerForm(url, idMail);

            nx_message.updateMailNoRead();
        });*/

        $('.archive').click(function(e){
            //On stop la propagation de l'event
            e.stopPropagation();
            var url = $(this).attr('href');
            var idMail = $(this).attr('mail');
            var label = $(this).attr('label');

            //Confirmation
            if(confirm(label)){
                //On envoie la requête
                nxMain.ajaxRequest(url, {id_mail: idMail}, function(json){
                    if(json.return == false){
                        if(json.url !== undefined){
                            //Redirection
                            document.location.href = json.url;
                        }
                    }else if(json.return == true){
                        $('div[mail="'+ idMail +'"]').hide();
                    }
                },'json');
            }

            nx_message.updateMailNoRead();
        });

        $('.customTab').unbind('click').click(function(){
            var param = $(this).attr('param');
            var postVar = {
                param: param,
                isAjax: true
            };
			
            //Les messages archivés
            if(param === 'archive'){
                //Le 1er onglet
                var elementLi = $('li.customTab').eq(0);
                //privé ??
                if(elementLi.hasClass('mailPrivate'))
                    postVar['archive'] = 'private';
                //ou non privé
                else if(elementLi.hasClass('mailConsult'))
                    postVar['archive'] = 'message';
            }
            //On va chercher les mails du type demandé
            nxMain.ajaxRequest(nx_message.urlMail, postVar, function(json){
				
                if(json.return == true){
                    //Supprime les blocks, par sécurité
                    $('.mails').remove();
                    $('.pagination-msg').remove();
                    nx_message.blockMail.remove();
                    nx_message.blocAnswer.remove();
                    //Si l'onglet demandé ,n'est pas celui retourné
                    if(param !== json.param){
                        //On supprime la class 'active'
                        $('li.customTab').removeClass('active');
                        $('li.customTab[param="'+json.param+'"]').addClass('active');
                    }

                    $('.mails_container').html(json.html);
                    nx_message.init();
                }
            },'json');

            nx_message.updateMailNoRead();
        });
    },

    inboxRefresh: function(interval){
        nx_message.interval = interval;
       /* nx_message.timer = setTimeout(function(){
            nx_message.updateInbox();
        }, interval);*/
    },

    getDiscussions : function(param){
        //On va chercher les mails du type demandé
        nxMain.ajaxRequest(nx_message.urlMail, {param: param, isAjax: true, onlyBlockMail: true}, function(json){
            if(json.return === true){
                //Supprime les blocks, par sécurité
                $('.mails').remove();
                $('.pagination-msg').remove();
                //Si l'onglet demandé ,n'est pas celui retourné
                if(param !== json.param){
                    //On supprime la class 'active'
                    $('li.customTab').removeClass('active');
                    $('li.customTab[param="'+json.param+'"]').addClass('active');
                }

                $(json.html).insertAfter('ul.nav.nav-tabs');
                nx_message.init();

                //La discussion en cours de lecture ??
                var idMail = $('#mail-content').attr('mail');

                if(idMail !== undefined){
                    $('div.discussion.noread').each(function(){
                        if(idMail === $(this).attr('mail')){
                            //On supprime les points d'exclamation
                            $('.customTab a i').remove();
                            //On supprime la class
                            $(this).removeClass('noread');
                            //On supprime l'enveloppe
                            $(this).find('td').eq(0).find('i').remove();
                            nx_message.readDiscussion(idMail);
                        }
                    });
                }
            }
        },'json');
    },

    readDiscussion : function(idMail){
        //On va chercher la conversation
        nxMain.ajaxRequest(nx_message.url, {id_mail: idMail}, function(json){
            if(json.return == true){
                nx_message.blockMail[0].innerHTML = json.html;
                nx_message.scrollDown(idMail);
            }
        },'json');

    },

    updateInbox: function(){
       /* nxMain.ajaxRequest(nx_message.urlUpdate, {user: nx_message.idUser}, function(json){
            if(json.return === true){
                var elementLi = $('li.customTab').eq(0);
                //Messages
                if(elementLi.hasClass('mailConsult')){
                    if(json.data.mailConsult === true){
                        //On supprime les points d'exclamation
                        $('.mailConsult a i').remove();
                        $('.mailConsult a').append('<i class="margin_left_5 glyphicon glyphicon-exclamation-sign"></i>');
                        //L'onglet est actif ??
                        if(elementLi.hasClass('active')){
                            //Alors on recharge les discussions
                            nx_message.getDiscussions('message');
                            nx_message.inboxRefresh(nx_message.interval);
                        }else
                            nx_message.inboxRefresh(nx_message.interval);
                    }else
                        nx_message.inboxRefresh(nx_message.interval);
                }
                //Ou messages privés
                else if(elementLi.hasClass('mailPrivate')){
                    if(json.data.mailPrivate === true){
                        $('.mailPrivate a i').remove();
                        $('.mailPrivate a').append('<i class="margin_left_5 glyphicon glyphicon-exclamation-sign"></i>');
                        //L'onglet est actif ??
                        if(elementLi.hasClass('active')){
                            //Alors on recharge les discussions
                            nx_message.getDiscussions('private');
                            nx_message.inboxRefresh(nx_message.interval);
                        }else
                            nx_message.inboxRefresh(nx_message.interval);
                    }else
                        nx_message.inboxRefresh(nx_message.interval);
                }else
                    nx_message.inboxRefresh(nx_message.interval);
            }
        },'json');*/
    },

    answerForm: function(url, idMail){
		$('.mail-content').css('display','none');
		$('.mail-answer').css('display','none');
		nx_message.blockMail = $('#mail-content-'+idMail);
        nx_message.blocAnswer = $('#mail-answer-'+idMail);
        nxMain.ajaxRequest(url, {id_mail: idMail}, function(json){
            if(json.return == false){
                if(json.url !== undefined){
                    //Redirection
                    document.location.href = json.url;
                }
            }else if(json.return == true){
				
                nx_message.blocAnswer.html(json.html);
                nx_message.blocAnswer.show();
            }
        },'json');
    },

    scrollDown: function(idMail){
		if($('button.answer[mail="'+idMail+'"]').size() > 0){
        var divMail = $('button.answer[mail="'+idMail+'"]');//document.getElementById('mail-content');
       // divMail.scrollTop = divMail.scrollHeight -100;
	   $('html, body').animate( { scrollTop: divMail.offset().top -100 }, 900 ); 
		}
    },

    //Pour savoir s'il y a des messages non lus
    updateMailNoRead: function(){
      /*  nxMain.ajaxRequest(nx_message.urlUpdate, {user: nx_message.idUser}, function(json){
            if(json.return === true){
                //On supprime les points d'exclamation
                $('.customTab a i').remove();

                if(json.data.mailConsult === true)
                    $('.mailConsult a').append('<i class="margin_left_5 glyphicon glyphicon-exclamation-sign"></i>');

                if(json.data.mailPrivate === true)
                    $('.mailPrivate a').append('<i class="margin_left_5 glyphicon glyphicon-exclamation-sign"></i>');
            }
        },'json');*/
    },

    //Permet de simuler le click pour ouvrir une discussion
    simulateOpenDiscussion: function(idMail){
        var trElement = $('tr[mail="'+idMail+'"]');
        var urlAnswerForm = $('button.answer[mail="'+idMail+'"]').attr('href');
		nx_message.blockMail = $('#mail-content-'+idMail);
        nx_message.blocAnswer = $('#mail-answer-'+idMail);
        nx_message.openDiscussion(trElement, urlAnswerForm, idMail);
    },
	
	closeDiscussion: function(trElement, urlAnswerForm, idMail){
        $('.mail-content').css('display','none');
		$('.mail-answer').css('display','none');
		nx_message.blockMail = $('#mail-content-'+idMail);
        nx_message.blocAnswer = $('#mail-answer-'+idMail);
        $('.mails div').removeClass("selected");
    },

    //Permet d'ouvrir une discussion
    openDiscussion: function(trElement, urlAnswerForm, idMail){
		$('.mail-content').css('display','none');
		$('.mail-answer').css('display','none');
		nx_message.blockMail = $('#mail-content-'+idMail);
        nx_message.blocAnswer = $('#mail-answer-'+idMail);
        $('.mails div').removeClass("selected");
        trElement.addClass("selected");
        //On va chercher la conversation
        nx_message.blockMail.html('<div id="loading"></div>');

        nxMain.ajaxRequest(nx_message.url, {id_mail: idMail}, function(json){
            if(json.return == false){
                if(json.url !== undefined){
                    //Redirection
                    document.location.href = json.url;
                }
            }else if(json.return == true){
                trElement.removeClass('noread');
                //On supprime l'enveloppe
                trElement.find('td').eq(0).find('i').remove();
                if(nx_message.flagAnswer === false){
                    //On va chercher le formulaire de réponse en même temps
                    nx_message.answerForm(urlAnswerForm, idMail);
                }
                nx_message.flagAnswer = false;
                nx_message.blockMail.html(json.html);
                nx_message.blockMail.attr('mail', idMail);
                nx_message.blockMail.show();
                nx_message.scrollDown(idMail);
				
            }
        },'json');

        nx_message.updateMailNoRead();
    }
}

$(document).ready(function(){ nx_message.init(); });