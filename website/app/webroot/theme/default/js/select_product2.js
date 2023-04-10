var nx_select_product = {
    btnTitle: '',
    nocgv: '',
    noproductmsg: '',
    init: function(){
        this.btnTitle = $("table.table_products .btn_product:eq(0)").html();
		
        //$('.price-table button').click(function(){
		$( document ).on( "click", ".price-table .row li", function() {	
           var trElement = $(this);
            var value = parseInt(trElement.find(".btn").attr('param'));
			$('#produit').attr('value', value);
			$(document).find("form#AccountCartForm").delay( 800 ).submit();

        });
		
		$( document).on( "click", ".table_mobile_products a", function() {
            var trElement = $(this);
            var value = parseInt(trElement.attr('param'));
			$('#produit').attr('value', value);
			$("form#AccountCartForm").delay( 800 ).submit();
        });
		
    }
}

$(document).ready(function(){ nx_select_product.init();});