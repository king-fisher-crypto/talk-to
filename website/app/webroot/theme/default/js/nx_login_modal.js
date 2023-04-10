var nx_login_modal = {

    init : function(){

        $('#login_modal').unbind('submit').submit(function(){
            console.log('submit');
            console.log($(this).attr('action'));
            var postVars = {
                User: {
                    compte: $(this).find('#UserCompte').val(),
                    email: $(this).find('#UserEmail').val(),
                    passwd: $(this).find('#UserPasswd').val(),
                    isAjax: 1
                }
            }
            nxMain.ajaxRequest($(this).attr('action'), postVars, function(json){
                if(json.return === false){
                    if(json.msg !== undefined)
                        alert(json.msg);
                }else if(json.return === true)
                    window.location.reload();
            });
            return false;
        });
    }
}

$(document).ready(function(){ nx_login_modal.init(); });