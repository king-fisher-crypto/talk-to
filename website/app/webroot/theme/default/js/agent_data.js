$(document).ready(function(){ 
	if($( "#dialog-conddata" ).size() > 0){
		$( "#dialog-conddata" ).dialog({
					  resizable: false,
					  height: "auto",
					  width: 550,
					  modal: true,
					  overlay: { backgroundColor: "#000000", opacity: 0.5 },
						open: function () 
					  { 

						  $('#dialog-conddata div').scrollTop(0);
					  },
			
					  buttons: {
						"Ok": function() {
							//document.location.href = '/agents/profil#mvp';
							
							$(document).find('.account-tabs').find('li').removeClass('active');
							$(document).find('.tab-content').find('.tab-pane').removeClass('active');
							$(document).find('.account-tabs').find('li.mvp').addClass('active');
							$(document).find('.tab-content').find('.tab-pane#mvp').addClass('active');
							
						  	$( this ).dialog( "close" );
						}
					  }
		});
	}
	
});