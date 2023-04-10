$(document).ready(function(){ 
	if($( "#dialog-cgv" ).size() > 0){
		$( "#dialog-cgv" ).dialog({
					  resizable: false,
					  height: "auto",
					  width: 550,
					  modal: true,
					  overlay: { backgroundColor: "#000000", opacity: 0.5 },
					  buttons: {
						"Valider": function() {
							if (confirm("Valider les CGU et accéder aux Gains & Chiffre d\'affaire Expert.")) {
						   		valider_cgv();
									$( this ).dialog( "close" );
						}
							
							
						  
						}
					  }
		});
	}
	
});

function valider_cgv(){
	nxMain.ajaxRequest("/agents/validcgv", {}, function(t) {
				document.location.href = '/agents/gain';
			});	

}