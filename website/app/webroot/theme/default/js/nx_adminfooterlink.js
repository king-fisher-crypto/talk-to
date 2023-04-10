var nx_adminfooterlink = {
    init : function(){
        $('.edit_link').unbind('click').click(function(){
            var postVars = {
                FooterLink: {},
                FooterLinkLang: {}
            };

            if($('#FooterLinkTarget').is(':checked'))
                postVars.FooterLink['target_blank'] = 1;
            else
                postVars.FooterLink['target_blank'] = 0;
            postVars.FooterLink['id'] = $('input[id=FooterLinkId]').val();

            var i = 0;
            $('.tab-pane.tab-link').each(function(){
                var lang = $(this).attr('lang');
                postVars.FooterLinkLang[i] = {};

                postVars.FooterLinkLang[i]['lang_id'] = $(this).find('input[id=FooterLinkLang'+lang+'LangId]').val();
                postVars.FooterLinkLang[i]['title'] = $(this).find('input[id=FooterLinkLang'+lang+'Title]').val();
                postVars.FooterLinkLang[i]['link'] = $(this).find('input[id=FooterLinkLang'+lang+'Link]').val();
                i++;
            });
            nxAdminMain.ajaxRequest($(this).attr('href'), postVars, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean)
                        $('#block-link').empty();
                    //On met à jour le select_link
                    nx_adminfooterlink.updateSelectLink(json.update);
                    //Message pour l'utilisateur
                    if(json.msg != undefined)
                        alert(json.msg);
                }else{
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean){
                        $('#block-link').empty();
                        //On met à jour le select_link, si l'url update existe
                        if(json.update !== undefined)
                            nx_adminfooterlink.updateSelectLink(json.update);
                    }
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

        $('#select_link').unbind('change').change(function(){
            //On efface les boutons et le block link
            $('.menu-link a').remove();
            $('#block-link').empty();
            if($(this).val() !== ''){
                //On va charcher les boutons
                nx_adminfooterlink.updateLinkAction($(this).attr('update'), $(this).val());
                //On va chercher les datas pour le lien sélectionner
                nx_adminfooterlink.getDataLink($(this).attr('update'), $(this).val());
            }
        });

        $('.add_link').unbind('click').click(function(){
            nxAdminMain.ajaxRequest($(this).attr('href'), {idLink: 0}, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement
                    $('#block-link').empty();
                    //On ajoute le nouveau block
                    $('#block-link').append(json.html);

                    nx_adminfooterlink.init();
                }else{
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

        $('.create_link').unbind('click').click(function(){
            var postVars = {
                FooterLink: {},
                FooterLinkLang: {}
            };

            if($('#FooterLinkTarget').is(':checked'))
                postVars.FooterLink['target_blank'] = 1;
            else
                postVars.FooterLink['target_blank'] = 0;

            var i = 0;
            $('.tab-pane.tab-link').each(function(){
                var lang = $(this).attr('lang');
                postVars.FooterLinkLang[i] = {};

                postVars.FooterLinkLang[i]['lang_id'] = $(this).find('input[id=FooterLinkLang'+lang+'LangId]').val();
                postVars.FooterLinkLang[i]['title'] = $(this).find('input[id=FooterLinkLang'+lang+'Title]').val();
                postVars.FooterLinkLang[i]['link'] = $(this).find('input[id=FooterLinkLang'+lang+'Link]').val();
                i++;
            });
            nxAdminMain.ajaxRequest($(this).attr('href'), postVars, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement
                    if(json.clean)
                        $('#block-link').empty();
                    //On met à jour le select_link
                    nx_adminfooterlink.updateSelectLink(json.update);
                    //Message pour l'utilisateur
                    if(json.msg != undefined)
                        alert(json.msg);
                }else{
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean)
                        $('#block-link').empty();
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

    },
    updateSelectLink: function(url){
        nxAdminMain.ajaxRequest(url, {mode: 'footer_link'}, function(json){
            if(json.return){
                //On supprime ce qu'il y a dans le listing des liens
                $('.menu-link').empty();
                //On ajoute la nouvelle liste
                $('.menu-link').append(json.html);
                nx_adminfooterlink.init();
            }
        });
    },
    updateLinkAction: function(url, idLink){
        nxAdminMain.ajaxRequest(url, {mode: 'link_action', idLink: idLink}, function(json){
            if(json.return){
                //On ajoute le nouveau contenu
                $('.menu-link').append(json.html);
                nx_adminfooterlink.init();
            }
        });
    },
    getDataLink: function(url, idLink){
        nxAdminMain.ajaxRequest(url, {mode: 'data', idLink: idLink}, function(json){
            if(json.return){
                //On supprime le contenu du block link
                $('#block-link').empty();
                //On ajoute le nouveau contenu
                $('#block-link').append(json.html);
                nx_adminfooterlink.init();
            }
        });
    }
}

$(document).ready(function(){ nx_adminfooterlink.init(); });