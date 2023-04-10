var nx_tinymce = {
    urlDialog: '/dialog_tinymce',

    init: function(){

        var element = "";
        //On récupère les éléments qui ont l'attribut tinymce
        $('[tinymce]').each(function() {
            tinyMCE.init({
                mode : "exact",
                elements: $(this).attr('id'),
                theme: "modern",
                language: "fr_FR",
                content_css : '/theme/default/css/admin.css' ,
                //width: 680,
                width: '100%',
                autoresize_max_width: 800,
                paste_as_text: true,
                height: $(this).attr('data-tinymce-height') || 400,
                plugins: [
                    "advlist autolink image lists preview hr link",
                    "searchreplace wordcount insertdatetime",
                    "save table contextmenu directionality paste textcolor pagebreak code"
                ],
                pagebreak_separator: "<!--PAGEBREAK-->",
                toolbar1: "undo redo | styleselect | bold italic underline | alignleft aligncenter alignright alignjustify | numlist outdent indent | table | ",
                toolbar2: "fontsizeselect forecolor backcolor | pagebreak | link image | preview | code ",
               // toolbar: "",
                relative_urls: false,
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
        });
    }
}

$(document).ready(function(){ nx_tinymce.init(); });
