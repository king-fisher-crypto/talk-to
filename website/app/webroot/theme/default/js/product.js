var nx_product = {
    init: function(){
        $('.show-description').click(function(e){
            $(this).hide();
            $(this).parents(".product").find(".description").show();
        });

        $('.product').mouseleave(function(){
            var el = $(this).find('.show-description');
            if(el.css('display') === 'none'){
                el.show();
                $(this).find('.description').hide();
            }
        });
    }
}

$(document).ready(function(){ nx_product.init(); });