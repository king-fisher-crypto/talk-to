var nx_voice_call = {
    init: function (){
        nx_voice_call.ajaxRequest( '/webhooks/jwt', {
            id :  $("input[name='current_user_id']").val()
        }, function(json){
            if(json.status === 'success'){
                new NexmoClient({ debug: true })
                    .login(json.data)
                    .then(app => {
                        var ringback = null;
                        $(document).on("click", "#call-voip", function(e) {
                            e.preventDefault();
                            const to = $("input[name='to']").val();
                            app.callServer(to, 'app').then((nxmCall) => {
                                console.log('Calling  '+to);

                              }).catch((error) => {
                                console.error(error);
                              });

                        });
                        $(document).on("click", "#call-phone", function(e) {
                            e.preventDefault();
                            app.newConversationAndJoin().then((conversation) => {
                                console.log("Con ne` " ,conversation);
                                nx_voice_call.ajaxRequest( '/webhooks/call', {
                                    to :  '84983819089',
                                    con_name: conversation.name
                                }, function(json){

                                });
                              }).catch((error) => {
                                console.error("Error creating a conversation and joining ", error);
                              });

                        });
                        app.on("member:call", (member, call) => {
                            var ringtone = null;
                            console.log("NXMCall ", app.me.name);
                            if (!isNaN(call.from)  && call.from !== app.me.name) {
                                ringtone = new Audio('/media/ringtone/ring-tone-voip.mp3');
                                ringtone.play();
                                nx_voice_call.ajaxRequest( '/home/voice_call', {
                                        user_name: call.to.entries().next().value[1].user.name
                                    },
                                    function(json){
                                        $("#myModal").remove();
                                        $("body").append(json.html);
                                        $("#myModal").modal({
                                            backdrop: !0,
                                            keyboard: !0,
                                            show: !0
                                        });
                                    });
                            }

                            $(document).on("hidden.bs.modal", "#myModal", function(e) {
                                call.hangUp();
                                if(ringtone) {
                                    ringtone.pause();
                                    ringtone.currentTime = 0;
                                }
                            });

                            $(document).on("click", "#reject", function(e) {
                                e.preventDefault();
                                call.reject();
                                if(ringtone) {
                                    ringtone.pause();
                                    ringtone.currentTime = 0;
                                }
                                $("#myModal").close();
                            });

                            $(document).on("click", "#answer", function(e) {
                                e.preventDefault();
                                call.answer();
                                if(ringtone) {
                                    ringtone.pause();
                                    ringtone.currentTime = 0;
                                }
                            });

                            $(document).on("click", "#hangup", function(e) {
                                e.preventDefault();
                                console.log("Hanging up...");
                                call.hangUp();
                                console.log("You ended the call")

                            });

                        });
                        app.on("call:status:changed",(call) => {
                            const statusElement = $(".status-voip");
                            const hangupButton = document.getElementById("hangup");
                            const callButton = document.getElementById("call-voip");
                            const answerButton = document.getElementById("answer");
                            const rejectButton = document.getElementById("reject");
                            if (statusElement) {
                                statusElement.text(`Call status: ${call.status}`);
                            }
                            if (call.status === call.CALL_STATUS.RINGING){
                                // ringback = new Audio('/media/ringtone/ring-back-tone.wav');
                                // ringback.play();

                            }
                            if (call.status === call.CALL_STATUS.STARTED){
                                if(callButton) {
                                    callButton.style.display = "none";
                                }
                                if(hangupButton) {
                                    hangupButton.style.display = "inline";
                                }
                            }
                            if (call.status === call.CALL_STATUS.ANSWERED){
                                if(answerButton) {
                                    answerButton.style.display = "none";
                                }
                                if(rejectButton) {
                                    rejectButton.style.display = "none";
                                }

                                if(ringback) {
                                    ringback.pause();
                                    ringback.currentTime = 0;
                                }
                            }
                            if ([call.CALL_STATUS.COMPLETED,
                                call.CALL_STATUS.REJECTED,
                                call.CALL_STATUS.TIMEOUT,
                                call.CALL_STATUS.BUSY,
                                call.CALL_STATUS.UNANSWERED ].includes(call.status)){
                                if(ringback) {
                                    ringback.pause();
                                    ringback.currentTime = 0;
                                }
                                if(hangupButton) {

                                    hangupButton.style.display = "none";
                                }
                                if(rejectButton) {
                                    rejectButton.style.display = "none";
                                }
                            }
                        });
                    })
                    .catch(console.error);
            }
        });

    },
    ajaxRequest: function(url, postVars, callback, dataType){
        if (dataType == undefined)
            dataType = 'json';
        $.ajax({
            type: 'POST',
            dataType: dataType,
            url: url,
            data: postVars,
            success: function(datas){
                if (callback != undefined)
                    callback(datas);
            }
        });
    }
}

$(document).ready(function(){ nx_voice_call.init(); });