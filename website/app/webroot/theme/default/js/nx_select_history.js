var nx_select_history = {
    init: function (){
        $('#select_history').unbind('change').change(function(){
            document.location.href = $(this).val();
        });
    }
}

$(document).ready(function(){ nx_select_history.init();});