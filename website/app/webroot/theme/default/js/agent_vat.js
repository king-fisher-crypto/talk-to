$(document).ready(function(){ 
	if($( "#dialog-vat" ).size() > 0){
		$( "#dialog-vat" ).dialog({
					  resizable: false,
					  height: "auto",
					  width: 550,
					  modal: true,
					  overlay: { backgroundColor: "#000000", opacity: 0.5 },
					  buttons: {
						"Voir": function() {
						  voir_vat();
							$( this ).dialog( "close" );
						}
					  }
		});
	}
	
});

function voir_vat(){
	/*nxMain.ajaxRequest("/agents/validconditionutilisation", {}, function(t) {
				
			});	*/
	window.location.href = "/agents/vatnum";
}