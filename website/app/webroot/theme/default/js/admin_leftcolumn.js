var nx_leftcolumn = {
    init: function(){
        //Checkbox "Tous"
        $('#LeftColumnAlldomain').change(function(){
            if($(this).is(':checked') == true){
                $(':checkbox[id^="domain"]').attr('checked', true).parents('span').addClass('checked');
                $(':checkbox[id^="LeftColumnDomain"]').attr('checked', true).parents('span').addClass('checked');
            }else{
                $(':checkbox[id^="domain"]').attr('checked', false).parents('span').removeClass('checked');
                $(':checkbox[id^="LeftColumnDomain"]').attr('checked', false).parents('span').removeClass('checked');
            }
        });

        $(':checkbox[id^="domain"]').change(function(){
            if($(this).is(':checked') == false && $('#LeftColumnAlldomain').is(':checked') == true){
                $('#LeftColumnAlldomain').attr('checked', false).parents('span').removeClass('checked');
            }
        });

        $(':checkbox[id^="LeftColumnDomain"]').change(function(){
            if($(this).is(':checked') == false && $('#LeftColumnAlldomain').is(':checked') == true){
                $('#LeftColumnAlldomain').attr('checked', false).parents('span').removeClass('checked');
            }
        });
    }
}

$(document).ready(function(){ nx_leftcolumn.init(); });