var nx_slide = {
    init: function(){
        //Checkbox "Tous"
        $('#SlideAlldomain').change(function(){
            if($(this).is(':checked') == true){
                $(':checkbox[id^="domain"]').attr('checked', true).parents('span').addClass('checked');
                $(':checkbox[id^="SlideDomain"]').attr('checked', true).parents('span').addClass('checked');
            }else{
                $(':checkbox[id^="domain"]').attr('checked', false).parents('span').removeClass('checked');
                $(':checkbox[id^="SlideDomain"]').attr('checked', false).parents('span').removeClass('checked');
            }
        });

        $(':checkbox[id^="domain"]').change(function(){
            if($(this).is(':checked') == false && $('#SlideAlldomain').is(':checked') == true){
                $('#SlideAlldomain').attr('checked', false).parents('span').removeClass('checked');
            }
        });

        $(':checkbox[id^="SlideDomain"]').change(function(){
            if($(this).is(':checked') == false && $('#SlideAlldomain').is(':checked') == true){
                $('#SlideAlldomain').attr('checked', false).parents('span').removeClass('checked');
            }
        });
    }
}

$(document).ready(function(){ nx_slide.init(); });