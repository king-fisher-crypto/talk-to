$(document).ready(function(){ 
	if($( "#dialog-condplanning" ).size() > 0){
		$( "#dialog-condplanning" ).dialog({
					  resizable: false,
					  height: 400,
					  width: 550,
					  modal: true,
					  overlay: { backgroundColor: "#000000", opacity: 0.5 },
					  buttons: {
						"Aller sur mon planning": function() {
						  valider_condplanning();
							$( this ).dialog( "close" );
						}
					  }
		});
		 $(".dialog-close-box").click(function () {
  			$("#dialog-condplanning").dialog('close');
    });
	}
	
});

function valider_condplanning(){
	
	window.location = '/agents/planning';

}