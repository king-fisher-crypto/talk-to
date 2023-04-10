var nxfooter = {
    ajaxurl: '/admin/footers/ajax_get_elements',
    ajaxsaveurl: '/admin/footers/ajax_save_and_make',
    ajaxinittreeurl: '/admin/footers/ajax_init_tree',
    tree: [],
    idcolonne: 0,
    types: ['category','cms','link','block'],
    init: function(){
        for (i in this.types)
            this.getElements(this.types[i]);

        $(".elements_block .tools a.reload").click(function(){
            var name = $(this).parents(".elements_block").attr("id").replace("elements_of_","");
            nxfooter.getElements(name);
        });

        $("#savefooter").click(function(){
            if (confirm('Voulez-vous vraiment enregistrer le footer et publier ces modifications sur les fronts ?')){
                nxfooter.saveAndMake();
            }
        });

        $("#removeitem").unbind('click').click(function(){
            var id = $('#tree').find('div.tv_highlighted').attr('data-treeviewid');
            if(id !== undefined){
                id = id.substr(1);
                $('#tree').dasBaum('removeItem', id);
            }
        });

        $("#elements_of_sautdecolonne").find(".html_element").unbind("click").click(function(){
            nxfooter.elementClick($(this));
        });

        this.initTree();
    },
    getHtmlElement: function(datas){
        html = '<div class="html_element'+(datas['active']=='0'?' he_disabled':' he_enabled')+'" data-id="'+datas['id']+'" data-active="'+datas['active']+'">';
        html+= '<div class="he_title">'+datas['name']+'</div>';
        if (datas['active'] == 1)
            html+= '<div class="he_btns"><button type="button" class="btn btn-xs" id="genpassword"><i class=" icon-plus-sign"></i></button></div>';
        html+= '<div style="clear:both"></div>';
        html+= '</div>';
        return html;
    },
    getElements: function(element_type, callback){
        $("#elements_of_"+element_type).find(".portlet-body").html('<img src="/theme/default/images/ajax-loader.gif" />');
        nxAdminMain.ajaxRequest(nxfooter.ajaxurl, {
            'type': element_type
        }, function(json){
            html = '';
            for (i in json.items){
                html+= nxfooter.getHtmlElement(json.items[i]);
            }
            $("#elements_of_"+element_type).find(".portlet-body").html(html);
            $("#elements_of_"+element_type).find(".html_element").unbind("click").click(function(){
                nxfooter.elementClick($(this));
            });
            if (callback != undefined)
                callback(json);
        }, 'json');
    },
    elementClick: function(cible){
        var id = parseInt(cible.attr("data-id"));
        var active = parseInt(cible.attr("data-active"));
        var name = cible.find(".he_title").text();
        var type = cible.parents(".elements_block").attr("id").replace("elements_of_","");

        if (active == 0)return false;

        if (type == 'block')
            $('#tree').dasBaum('addItem',{id: type+'-'+id, label:name, items: []});
        else if(type == 'sautdecolonne'){
            $('#tree').dasBaum('addItem',{id: type+'-'+nxfooter.idcolonne, label:name});
            nxfooter.idcolonne++;
        }
        else
            $('#tree').dasBaum('addItem',{id: type+'-'+id, label:name});
    },
    refreshTree: function(){
        this.tree = [];
        var tree = $('#tree').dasBaum('getTree');
        for (i in tree.childs){
            nxfooter.tree.push(this.getElementForTree(tree.childs[i]));
        }
    },
    getElementForTree: function(node){
        var tmp = node.id.split("-");
        var element = {
            type: tmp[0],
            id: tmp[1],
            children: []
        };
        if (node.childs != null){
            var children = [];
            for (i in node.childs){
                children.push(this.getElementForTree(node.childs[i]));
            }
            element.children = children;
        }
        return element;
    },
    saveAndMake: function(){
        this.refreshTree();

        nxAdminMain.ajaxRequest(nxfooter.ajaxsaveurl, {
            'items': nxfooter.tree
        }, function(json){
            if(json.return){
                document.location.href = json.url;
            }

        }, 'json');
    },
    initTree: function(){
        nxAdminMain.ajaxRequest(nxfooter.ajaxinittreeurl, {}, function(json){
            if(json.data !== undefined){
                for(var i = 0; i < json.data.length; i++){
                    if(json.data[i].type === 'sautdecolonne')
                        nxfooter.idcolonne++;
                }
                $('#tree').dasBaum({
                    sort: false,
                    foldersOnTop: false,
                    allowRename: 	false,
                    items: nxfooter.setTree(json.data),
                    selected: 	function(){
                        //nxfooter.refreshTree();
                    },
                    renamed: 	function(){
                        //nxfooter.refreshTree();
                    },
                    moved: 		function(dd){
                        //nxfooter.refreshTree();
                    },
                    toggled: 	function(){
                        //nxfooter.refreshTree();
                    },
                    details: 	function(){
                        nxfooter.refreshTree();
                    }
                });
            }
            nxfooter.idcolonne++;
        },'json');
    },
    setTree: function(node){
        var items = [];
        for(var i=0; i < node.length; i++){
            if(node[i].children !== undefined){
                items.push({id: node[i].type+'-'+node[i].id, label:node[i].label, items: nxfooter.setTree(node[i].children)});
            }else{
                items.push({id: node[i].type+'-'+node[i].id, label:node[i].label});
            }
        }
        return items;
    }
}
$(document).ready(function(){
    nxfooter.init();
    $(document).unbind('keydown').keydown(function(e) {
        $('#tree').dasBaum('handleKey',e);
    });
    $('#btFolder').click(function() {
        $('#tree').dasBaum('addItem',{label:'New Entry',items:[]})
    });
    $('#btEntry').click(function() {
        $('#tree').dasBaum('addItem',{label:'New Entry'});
    });
    $('#btDelete').click(function() {
        nxfooter.refreshTree();
    });
    /*$('#tree').dasBaum({
     sort: false,
     foldersOnTop: false,
     allowRename: 	false,
     items: nxfooter.initTree(),
     selected: 	function(){
     //nxfooter.refreshTree();
     },
     renamed: 	function(){
     //nxfooter.refreshTree();
     },
     moved: 		function(dd){
     //nxfooter.refreshTree();
     },
     toggled: 	function(){
     //nxfooter.refreshTree();
     },
     details: 	function(){
     nxfooter.refreshTree();
     }
     });*/
});