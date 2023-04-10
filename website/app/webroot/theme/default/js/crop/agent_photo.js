var nx_agent_photo = {
    model: '',
    hauteurPhotoSite : 0,
    largeurPhotoSite : 0,

    init : function(){
        var allowedTypes = ['png', 'jpg', 'jpeg', 'gif'];

        var fileInput = document.getElementById('UserPhoto');
        if(fileInput == undefined) fileInput = document.getElementById('AgentPhoto');
        var url = fileInput.getAttribute('url');

        fileInput.addEventListener('change', function(e) {
            var file = fileInput.files[0];
            if (file == undefined) return false;
            var fileData = file.name.split('.');
            var filetype = fileData[fileData.length - 1].toLowerCase();

            if(allowedTypes.indexOf(filetype) != -1){
                $(".loading-crop").show();
                nx_agent_photo.getSettings();

                var reader = new FileReader();

                reader.onload = function(e) {
                    $("#myModal").remove();
                    nxMain.ajaxRequest(url,{image: reader.result},function(html){
                        $("body").append(html);
                        $("#myModal").modal({
                            backdrop: 'static',
                            keyboard: false,
                            show: true
                        });

                        $("#myModal").bind('shown.bs.modal', function (e) {
                            $(".loading-crop").hide();

                            //Event click sur les buttons
                            document.getElementById('myModal').onclick = function(e){
                                var el = e ? e.target : event.srcElement;
                                if(el.type == 'button'){
                                    for (var key = 0; key < el.attributes.length; key++){
                                        if(el.attributes[key].name == 'data-dismiss'){
                                            fileInput.value = '';
                                            return false;
                                        }
                                    }

                                    if(el.id == 'saveCrop') $('#myModal').modal('hide');
                                }
                            };

                            if($("#cropImg")[0] != undefined){
                                //Footer de la modal
                                var footer = $("#myModal .modal-footer")[0];
                                //nouveau bouton pour save la sélection
                                var newButton = document.createElement('button');
                                newButton.type = 'button';
                                newButton.className = 'btn btn-pink btn-pink-modified';
                                newButton.id = 'saveCrop';
                                newButton.innerHTML = 'Enregistrer la sélection';
                                //on ajoute le bouton
                                footer.appendChild(newButton);
                                //On ajoute l'image à la div preview
                                $('#previewCrop').attr('src',reader.result);

                                var imgHeight = ($("#cropImg")[0].height);
                                var imgWidth = ($("#cropImg")[0].width);

                                var naturalWidth = $('#cropImg')[0].naturalWidth;
                                var naturalHeight = $('#cropImg')[0].naturalHeight;

                                //Les coordonnées du select par défaut
                                var selectX = (naturalWidth/2) - 300;
                                var selectY = (naturalHeight/2) - 300;
                                var selectX2 = (naturalWidth/2) + 300;
                                var selectY2 = (naturalHeight/2) + 300;

                                //Dimension du crop minimum
                                var minHeight = nx_agent_photo.hauteurPhotoSite;
                                var minWidth = nx_agent_photo.largeurPhotoSite;

                                //Si l'image est plus petite que 300 (choix arbitraire)
                                if(naturalWidth < 300){
                                    selectX = imgWidth/2 - 95;
                                    selectY = imgHeight/2 - 95;
                                    selectX2 = imgWidth/2 + 95;
                                    selectY2 = imgHeight/2 + 95;
                                }

                                //Si l'image est plus petite que le crop minimum
                                if(naturalHeight <= minHeight && naturalWidth <= minWidth){
                                    minHeight = naturalWidth/4;
                                    minWidth = naturalWidth/4;
                                }

                                //On initialise le crop
                                $.Jcrop('#cropImg', {
                                    bgColor:    'black',
                                    onSelect: nx_agent_photo.saveCoordCrop,
                                    aspectRatio: 1,
                                    trueSize: [naturalWidth,naturalHeight],
                                    keySupport : false,
                                    minSize: [minWidth,minHeight],
                                    maxSize: [1000,1000],
                                    bgOpacity:  .3,
                                    boxWidth:   imgWidth,
                                    boxHeight:  imgHeight,
                                    setSelect: [selectX,
                                        selectY,
                                        selectX2,
                                        selectY2
                                    ]
                                });
                            }
                        });
                    },'html');
                }
                reader.readAsDataURL(file);
            } else {
                fileInput.value = '';
                alert("File not supported!");
            }
        });
    },

    saveCoordCrop : function(c){
        var rx = $('.photo_agent')[0].clientWidth / c.w;
        var ry = $('.photo_agent')[0].clientHeight / c.h;

        //Preview du crop
        $('#previewCrop').css({
            width: Math.round(rx * $('#cropImg')[0].naturalWidth) + 'px',
            height: Math.round(ry * $('#cropImg')[0].naturalHeight) + 'px',
            marginLeft: '-' + Math.round(rx * c.x) + 'px',
            marginTop: '-' + Math.round(ry * c.y) + 'px'
        });

        $('#'+nx_agent_photo.model+'CropX')[0].value = c.x;
        $('#'+nx_agent_photo.model+'CropY')[0].value = c.y;
        $('#'+nx_agent_photo.model+'CropW')[0].value = c.w;
        $('#'+nx_agent_photo.model+'CropH')[0].value = c.h;
    },

    getSettings : function(){
        var div = $('div[model]');
        nx_agent_photo.model = div.attr('model');
        nx_agent_photo.hauteurPhotoSite = div.attr('croph');
        nx_agent_photo.largeurPhotoSite = div.attr('cropw');
    }
}

$(document).ready(function(){ nx_agent_photo.init(); });
