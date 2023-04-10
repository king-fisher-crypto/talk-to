var nx_rate_select = {
    init: function(){
        var collecStars = $('#review_stars .bigStar_rate');
        var inputRate = document.getElementById('AccountRate');
        $('#evaluation_expert').html(inputRate.value+'/5');

        collecStars.hover(function(e){
            var span = e.currentTarget;
            var star = span.attributes['star'].value;
            for(var i=0; i<5; i++){
                if(collecStars[i].attributes['star'].value <= star)
                    collecStars[i].className = 'bigStar_rate bigStar_enabled';
                else collecStars[i].className = 'bigStar_rate bigStar_disabled';
            }
        },function(e){
            for(var i=0; i<5; i++){
                if(collecStars[i].attributes['star'].value <= inputRate.value)
                    collecStars[i].className = 'bigStar_rate bigStar_enabled';
                else collecStars[i].className = 'bigStar_rate bigStar_disabled';
            }
        });

        collecStars.click(function(e){
            inputRate.value = e.currentTarget.attributes['star'].value;
            $('#evaluation_expert').html(inputRate.value+'/5');
        });
    }
}

$(document).ready(function(){ nx_rate_select.init(); });