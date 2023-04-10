var nx_admin_message = {
    blockMail : '',
    blocAnswer : '',
    url: '',
    urlreadswitch: '',

    init: function(){
        nx_admin_message.blockMail = $('#mail-content');
        nx_admin_message.blocAnswer = $('#mail-answer');
        nx_admin_message.url = $('.nx_mail').attr('url');

        $('.nx_mail tr.discussion').click(function(e){
            $('.nx_mail tr.discussion').removeClass("selected");
            $(this).addClass("selected");
            nx_admin_message.blocAnswer.hide();
            var trElement = $(this);
            var idMail = trElement.attr('mail');
            //On va chercher la conversation
            nxAdminMain.ajaxRequest(nx_admin_message.url, {id_mail: idMail}, function(json){
                if(json.return == false){
                    if(json.url !== undefined){
                        //Redirection
                        document.location.href = json.url;
                    }
                }else if(json.return == true){
                    trElement.removeClass('noread');
                    nx_admin_message.blockMail[0].innerHTML = json.readMail;
                    nx_admin_message.blocAnswer[0].innerHTML = json.answerForm;
                    nx_admin_message.blockMail.show();
                    nx_admin_message.blocAnswer.show();
                    nx_admin_message.urlreadswitch = json.switchReadedUrl;
                    nx_admin_message.initMessagesEvents();
					
                }
				tinyMCE.remove('textarea');
				var element = "";
					$('[tinymce]').each(function(){
						if(element === "")
							element = $(this).attr('id');
						else
							element+= ',' + $(this).attr('id');
					});
				 tinyMCE.init({
						mode : "exact",
						elements: element,
						selector: "textarea.tinymce",
						theme: "modern",
						language: "fr_FR",
						content_css : '/theme/default/css/admin.css' ,
						//width: 680,
						width: '100%',
						autoresize_max_width: 800,
						paste_as_text: true,
						height: 400,
						plugins: [
							"advlist autolink image lists preview hr link",
							"searchreplace wordcount insertdatetime",
							"save table contextmenu directionality paste textcolor pagebreak code"
						],
						pagebreak_separator: "<!--PAGEBREAK-->",
						toolbar1: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | numlist outdent indent | table | ",
						toolbar2: "fontsizeselect forecolor backcolor | pagebreak | link image | preview | code ",
					   // toolbar: "",
						relative_urls: true,
						paste_data_images: true,
						menubar: false,
						//toolbar_items_size: 'small',

						file_browser_callback: function(field, url, type, win) {
							tinyMCE.activeEditor.windowManager.open({
								file: '/theme/default/js/kcfinder/browse.php?opener=tinymce4&field=' + field + '&type=' + type,
								title: 'KCFinder',
								width: 700,
								height: 500,
								inline: true,
								close_previous: false
							}, {
								window: win,
								input: field
							});
							return false;
						},
						image_advtab: true,
					});
				
				
				
            },'json');
        });
    },
    initMessagesEvents: function(){
        $("#notreadaction").unbind("click").click(function(){
            nxAdminMain.ajaxRequest(nx_admin_message.urlreadswitch, {id_mail: parseInt($(this).attr("msg-id"))}, function(json){
                if (json.return == true){
                    $("tr.discussion.selected").removeClass("selected").addClass("noread");
                    $("#mail-content").hide();
                    $("#mail-answer").hide();
                }
            });
        });
    }
}

$(document).ready(function(){ nx_admin_message.init(); 
	$(document).on("click", "#AdminAdminMailsForm .btn", function(e) {						
							
		$("#mail-answer").find("#AdminAdminMailsForm").submit();	
	
	
	});
	
});