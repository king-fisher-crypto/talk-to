var nx_agent_audio = {
    init: function (){
        var audioValidation = document.getElementById('presentationValidation');

        $('#AgentAudio').change(function(){
            if(audioValidation != undefined){
                audioValidation.hidden = true;
            }
        });
    }
}

$(document).ready(function(){ nx_agent_audio.init(); });