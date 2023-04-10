$(document).ready(function(){
    
    var Accordion = function (el, multiple){
        this.el = el || {};
        this.multiple = multiple || false;

        var btnselected = this.el.find('button');
        btnselected.on('click',
            {el: this.el, multiple: this.multiple},
            this.btn);
    };

    Accordion.prototype.btn = function (e)
    {
        
        var $el = e.data.el,
                    $this = $(this);
                   

        
        $this.toggleClass('selected').siblings().removeClass('selected');

        
        
    }

    var btnBlocks1 = new Accordion($('.pourcentage'),false);
    var btnBlocks2 = new Accordion($('.delai'),false);
    var Accordion2 = function (el, multiple){
        this.el = el || {};
        this.multiple = multiple || false;
        var optionSelected = this.el.find(".icon");
        optionSelected.on('click',
            {el: this.el, multiple: this.multiple},
            this.opt);
    };

    Accordion2.prototype.opt = function (e){
        var $el = e.data.el,
                    $this = $(this);
        $this.toggleClass('selected');
    };

    var optSelect = new Accordion2($('.option'),false)

    $(".select-all").click(function() {
        if($(".select-all").hasClass('checked')==false){
            $(".abled").removeClass("checked") 
        }else{
            $(".abled").addClass("checked")
        }
       
    })
    
    $(".abled").click(function(){
        if($(this).hasClass("checked")==false){
            $(".select-all").removeClass("checked");
        }
        var check= true;
        $(".abled").each(function(){
            
            if ($(this).hasClass('checked')==false){
               check=false
            };
        })
        if(check==false){
            $(".select-all").removeClass("checked")
        }else{
            $(".select-all").addClass("checked")
        }
    })

    $("#search").focus(function(){
        $(".search_suggestions").removeClass('hidden');
    })
    $("#search").focusout(function(){
        $(".search_suggestions").addClass('hidden');
    })


    $(".check-box").click(function(){
        $(".check-box").removeClass("checked");
        $(this).toggleClass("checked");
    })
    var n = $('.tgt_row').length;
    var i = 3;
    $(".arrow-btn").click(function(){
        if($(this).hasClass("down") == false){
            i=3;
            
            while(i<=n)
            {
                
                $("#row"+i).addClass("hidden");
                i++;
            }
            $(".arrow-btn").addClass('down');
        }
        else{
            i=3;
            while(i<=n)
            {
                $("#row"+i).removeClass("hidden");
                i++;
            }
            $(".arrow-btn").removeClass('down');
        }
    })
});




