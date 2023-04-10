var nx_subscribe_agent = {
    formValid: false,

    init: function (){

        var url = $('form .btn-primary[href]').attr('href');

        if(url !== undefined){
            $("#myModalLoading").remove();
            nxMain.ajaxRequest(url, {}, function(json){
                if(json.return == true){
                    $("body").append(json.html);
                }
            });
        }

        $('#UserSubscribeAgentForm').submit(function(){
            $('.btn-primary').prop('disabled',true);
            //Modal traitement en cours
            $("#myModalLoading").modal({
                backdrop: 'static',
                keyboard: false,
                show: true
            });

            //Si le form n'est pas valider
            if(!nx_subscribe_agent.formValid){
                //Les données du formualaire
                var postVar = $(this).serialize();
                nxMain.ajaxRequest('/users/subscribe_agent_ajax', postVar, function(json){
                    if(json.return === false){
                        $("#myModalLoading").modal('hide');
                        $('.btn-primary').prop('disabled',false);
                        $("#myModal").remove();
                        $("body").append(json.content);
                        $("#myModal").modal({
                            backdrop: true,
                            keyboard: true,
                            show: true
                        });
                    }else{
                        nx_subscribe_agent.formValid = true;
                        $('#UserSubscribeAgentForm').submit();
                    }
                });
                return false;
            }
        });
    }
}

$(document).ready(function(){ nx_subscribe_agent.init(); });