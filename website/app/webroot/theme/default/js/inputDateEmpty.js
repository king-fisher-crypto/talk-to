var nx_inputDate = {
    init: function (){
        $("select[id*='Birthdate']").each(function(){
            $(this).find("option:first").text('-');
        });
    }
}

$(document).ready(function(){ nx_inputDate.init(); });