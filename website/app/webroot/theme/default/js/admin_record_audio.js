nx_admin_record_audio = {
    url: '',

    init: function(){
        nx_admin_record_audio.url = $('#btnSearch').attr('rel');

        $('#btnSearch').click(function(){
            var expert = $('#expert').val();
			var timing = $('#timing').val();
			var timing_min = $('#timing_min').val();
            if(expert !== '')
                nx_admin_record_audio.url+= '?expert='+expert+'&timing='+timing+'&timing_min='+timing_min;
			if(timing !== '')
				nx_admin_record_audio.url+= '?expert='+expert+'&timing='+timing+'&timing_min='+timing_min;
			if(timing_min !== '')
				nx_admin_record_audio.url+= '?expert='+expert+'&timing='+timing+'&timing_min='+timing_min;
			
            document.location.href = nx_admin_record_audio.url;
        });

        $('#expert').keyup(function(e){
            if(e.keyCode == 13){
                var expert = $(this).val();
                if(expert !== '')
                nx_admin_record_audio.url+= '?expert='+expert;

				document.location.href = nx_admin_record_audio.url;
            }
        });
		$('#timing').keyup(function(e){
            if(e.keyCode == 13){
                var timing = $(this).val();
				if(timing !== '')
					nx_admin_record_audio.url+= '?timing='+timing;
					
                document.location.href = nx_admin_record_audio.url;
            }
        });
		$('#timing_min').keyup(function(e){
            if(e.keyCode == 13){
                var timing_min = $(this).val();
				if(timing_min !== '')
					nx_admin_record_audio.url+= '?timing_min='+timing_min;
					
                document.location.href = nx_admin_record_audio.url;
            }
        })
    }
}

$(document).ready(function(){ nx_admin_record_audio.init(); });