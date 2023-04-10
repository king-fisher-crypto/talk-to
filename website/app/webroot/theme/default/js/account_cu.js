$(document).ready(function(){ 
	if($( "#dialog-cu" ).size() > 0){
		$( "#dialog-cu" ).dialog({
					  resizable: false,
					  height: "auto",
					  width: 550,
					  modal: true,
					  overlay: { backgroundColor: "#000000", opacity: 0.5 },
					  buttons: {
						"Valider": function() {
						  valider_cu();
							$( this ).dialog( "close" );
						}
					  }
		});
	}
	
});

function valider_cu(){
	nxMain.ajaxRequest("/accounts/validconditionutilisation", {}, function(t) {
				
			});	

}