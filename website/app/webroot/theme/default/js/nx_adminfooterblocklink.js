var nx_adminfooterblocklink = {

    init : function(){
        $('.edit_block_link').unbind('click').click(function(){
            var postVars = {FooterBlockLang: {}};

            postVars.FooterBlockLang['footer_block_id'] = $('input[id=FooterBlockLangFooterBlockId]').val();

            var i = 0;
            $('.tab-pane.tab-block').each(function(){
                var lang = $(this).attr('lang');
                postVars.FooterBlockLang[i] = {};

                postVars.FooterBlockLang[i]['lang_id'] = $(this).find('input[id=FooterBlockLang'+lang+'LangId]').val();
                postVars.FooterBlockLang[i]['title'] = $(this).find('input[id=FooterBlockLang'+lang+'Title]').val();
                i++;
            });
            nxAdminMain.ajaxRequest($(this).attr('href'), postVars, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean)
                        $('#block-link-2').empty();
                    //On met à jour le select_link
                    nx_adminfooterblocklink.updateSelectLink(json.update);
                    //Message pour l'utilisateur
                    if(json.msg != undefined)
                        alert(json.msg);
                }else{
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean){
                        $('#block-link-2').empty();
                        //On met à jour le select_link, si l'url update existe
                        if(json.update !== undefined)
                            nx_adminfooterblocklink.updateSelectLink(json.update);
                    }
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

        $('#select_block_link').unbind('change').change(function(){
            //On efface les boutons et le block link
            $('.menu-block-link a').remove();
            $('#block-link-2').empty();
            if($(this).val() !== ''){
                //On va chercher les boutons
                nx_adminfooterblocklink.updateLinkAction($(this).attr('update'), $(this).val());
                //On va chercher les datas pour le bloc sélectionner
                nx_adminfooterblocklink.getDataLink($(this).attr('update'), $(this).val());
            }
        });

        $('.add_block_link').unbind('click').click(function(){
            nxAdminMain.ajaxRequest($(this).attr('href'), {idBlockLink: 0}, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement
                    $('#block-link-2').empty();
                    //On ajoute le nouveau block
                    $('#block-link-2').append(json.html);

                    nx_adminfooterblocklink.init();
                }else{
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

        $('.create_block_link').unbind('click').click(function(){
            var postVars = {FooterBlockLang: {}};

            var i = 0;
            $('.tab-pane.tab-block').each(function(){
                var lang = $(this).attr('lang');
                postVars.FooterBlockLang[i] = {};

                postVars.FooterBlockLang[i]['lang_id'] = $(this).find('input[id=FooterBlockLang'+lang+'LangId]').val();
                postVars.FooterBlockLang[i]['title'] = $(this).find('input[id=FooterBlockLang'+lang+'Title]').val();
                i++;
            });
            nxAdminMain.ajaxRequest($(this).attr('href'), postVars, function(json){
                if(json.return){
                    //On supprime ce qu'il y a actuellement
                    if(json.clean)
                        $('#block-link-2').empty();
                    //On met à jour le select_block_link
                    nx_adminfooterblocklink.updateSelectLink(json.update);
                    //Message pour l'utilisateur
                    if(json.msg != undefined)
                        alert(json.msg);
                }else{
                    //On supprime ce qu'il y a actuellement dans le block-link
                    if(json.clean)
                        $('#block-link-2').empty();
                    if(json.msg != undefined)
                        alert(json.msg);
                }
            });
            return false;
        });

    },
    updateSelectLink: function(url){
        nxAdminMain.ajaxRequest(url, {mode: 'footer_block_link'}, function(json){
            if(json.return){
                //On supprime ce qu'il y a dans le listing des blocks
                $('.menu-block-link').empty();
                //On ajoute la nouvelle liste
                $('.menu-block-link').append(json.html);
                nx_adminfooterblocklink.init();
            }
        });
    },
    updateLinkAction: function(url, idLink){
        nxAdminMain.ajaxRequest(url, {mode: 'block_link_action', idLink: idLink}, function(json){
            if(json.return){
                //On ajoute le nouveau contenu
                $('.menu-block-link').append(json.html);
                nx_adminfooterblocklink.init();
            }
        });
    },
    getDataLink: function(url, idLink){
        nxAdminMain.ajaxRequest(url, {mode: 'block_data', idLink: idLink}, function(json){
            if(json.return){
                //On supprime le contenu du block link
                $('#block-link-2').empty();
                //On ajoute le nouveau contenu
                $('#block-link-2').append(json.html);
                nx_adminfooterblocklink.init();
            }
        });
    }
}

$(document).ready(function(){ nx_adminfooterblocklink.init(); });