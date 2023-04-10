var nx_topmenu = {
    listmenu: {},

    init: function(){
        $('.choice').unbind('click').click(function(){
            if($(this).hasClass('selected'))
                $(this).removeClass('selected');
            else{
                if($(this).hasClass('disabled') == false)
                    $(this).addClass('selected');
            }
        });

        $('.add').unbind('click').click(function(){
            $('#menu_add').find('li.selected').each(function(){
                $('#menu_list').append(nx_topmenu.generatorLi($(this).attr('contenu_id'),$(this).attr('contenu_type'),$(this).attr('name')));
                $(this).removeClass('selected');
            });
            nx_topmenu.init();
            return false;
        });

        $('.delete').unbind('click').click(function(){
            $('#menu_delete').find('li.selected').remove();
            return false;
        });

        $('.up').unbind('click').click(function(){
            $('#menu_list').find('li.selected').each(function(){
                if($(this).prev().is('li')){
                    $(this).insertBefore($(this).prev());
                    if(!$(this).prev().is('li'))
                        $(this).removeClass('selected');
                }else
                    $(this).removeClass('selected');
            });
            return false;
        });

        $('.down').unbind('click').click(function(){
            $($('#menu_list li.selected').get().reverse()).each(function(){
                if($(this).next().is('li')){
                    $(this).insertAfter($(this).next());
                    if(!$(this).next().is('li'))
                        $(this).removeClass('selected');
                }else
                    $(this).removeClass('selected');
            });
            return false;
        });

        $('form').submit(function(){
            nx_topmenu.generatorList();

            $('<input>').attr({
                type: 'hidden',
                name: 'data[Menu]',
                value: JSON.stringify(nx_topmenu.listmenu)
            }).appendTo('#MenuAdminMenuForm');
            //return false;
        });
    },
    initLink : function(){
        $('.edit_link').unbind('click').click(function(){
            var postVars = {
                MenuLink: {},
                MenuLinkLang: {}
            };

            if($('#MenuLinkTarget').is(':checked'))
                postVars.MenuLink['target_blank'] = 1;
            else
                postVars.MenuLink['target_blank'] = 0;
            postVars.MenuLink['id'] = $('input[id=MenuLinkId]').val();

            var i = 0;
            $('.tab-pane').each(function(){
                var lang = $(this).attr('lang');
                postVars.MenuLinkLang[i] = {};

                postVars.MenuLinkLang[i]['lang_id'] = $(this).find('input[id=MenuLinkLang'+lang+'LangId]').val();
                postVars.MenuLinkLang[i]['title'] = $(this).find('input[id=MenuLinkLang'+lang+'Title]').val();
                postVars.MenuLinkLang[i]['link'] = $(this).find('input[id=MenuLinkLang'+lang+'Link]').val();
                i++;
            });
            nxAdminMain.ajaxRequest($(this).attr('href'), postVars, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean)
                        $('#block-link').empty();
                    //On met à jour la liste des liens
                    nx_topmenu.updateListingLink(json.update);
                    //On met à jour le select_link
                    nx_topmenu.updateSelectLink(json.update);
                    //On met à jour le lien, s'il se trouve dans le block menu_list
                    nx_topmenu.updateMenuList(json.id, json.name);
                    //Message pour l'utilisateur
                    if(json.msg != undefined)
                        alert(json.msg);
                }else{
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean){
                        $('#block-link').empty();
                        //On met à jour le select_link, si l'url update existe
                        if(json.update !== undefined)
                            nx_topmenu.updateSelectLink(json.update);
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
                nx_topmenu.updateLinkAction($(this).attr('update'), $(this).val());
                //On va chercher les datas pour le lien sélectionner
                nx_topmenu.getDataLink($(this).attr('update'), $(this).val());
            }
        });

        $('.add_link').unbind('click').click(function(){
            nxAdminMain.ajaxRequest($(this).attr('href'), {idLink: 0}, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement
                    $('#block-link').empty();
                    //On ajoute le nouveau block
                    $('#block-link').append(json.html);

                    nx_topmenu.initLink();
                }else{
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

        $('.create_link').unbind('click').click(function(){
            var postVars = {
                MenuLink: {},
                MenuLinkLang: {}
            };

            if($('#MenuLinkTarget').is(':checked'))
                postVars.MenuLink['target_blank'] = 1;
            else
                postVars.MenuLink['target_blank'] = 0;

            var i = 0;
            $('.tab-pane').each(function(){
                var lang = $(this).attr('lang');
                postVars.MenuLinkLang[i] = {};

                postVars.MenuLinkLang[i]['lang_id'] = $(this).find('input[id=MenuLinkLang'+lang+'LangId]').val();
                postVars.MenuLinkLang[i]['title'] = $(this).find('input[id=MenuLinkLang'+lang+'Title]').val();
                postVars.MenuLinkLang[i]['link'] = $(this).find('input[id=MenuLinkLang'+lang+'Link]').val();
                i++;
            });
            nxAdminMain.ajaxRequest($(this).attr('href'), postVars, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement
                    if(json.clean)
                        $('#block-link').empty();
                    //On met à jour la liste des liens
                    nx_topmenu.updateListingLink(json.update);
                    //On met à jour le select_link
                    nx_topmenu.updateSelectLink(json.update);
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
    generatorLi: function(id, type, name){
        var html = '<li class="choice" contenu_id="'+id+'" contenu_type="'+type+'">'+name+'</li>'
        return html;
    },
    generatorList: function(){
        var i=1;
        $('#menu_list li').each(function(){
            nx_topmenu.listmenu[i] = {};
            nx_topmenu.listmenu[i]['contenu_id'] = $(this).attr('contenu_id');
            nx_topmenu.listmenu[i]['contenu_type'] = $(this).attr('contenu_type');
            nx_topmenu.listmenu[i]['position'] = i;
            i++;
        });
    },
    updateListingLink: function(url){
        nxAdminMain.ajaxRequest(url, {mode: 'listing_link'}, function(json){
            if(json.return){
                //On supprime ce qu'il y a dans le listing des liens
                $('#liste_link').empty();
                //On ajoute la nouvelle liste
                $('#liste_link').append(json.html);
                nx_topmenu.init();
            }
        });
    },
    updateSelectLink: function(url){
        nxAdminMain.ajaxRequest(url, {mode: 'menu_link'}, function(json){
            if(json.return){
                //On supprime ce qu'il y a dans le listing des liens
                $('.menu-link').empty();
                //On ajoute la nouvelle liste
                $('.menu-link').append(json.html);
                nx_topmenu.initLink();
            }
        });
    },
    updateLinkAction: function(url, idLink){
        nxAdminMain.ajaxRequest(url, {mode: 'link_action', idLink: idLink}, function(json){
            if(json.return){
                //On ajoute le nouveau contenu
                $('.menu-link').append(json.html);
                nx_topmenu.initLink();
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
                nx_topmenu.initLink();
            }
        });
    },
    updateMenuList: function(idLink, name){
        $('#menu_list li').each(function(){
            if($(this).attr('contenu_type') === 'link' && $(this).attr('contenu_id') === idLink)
                $(this).html(name);
        });

    }
}

$(document).ready(function(){ nx_topmenu.init(); nx_topmenu.initLink(); });