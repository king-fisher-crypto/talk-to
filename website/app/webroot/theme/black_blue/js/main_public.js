$(function ()
{


    /* POUR QUE LE CONTENT NE SOIT VERTICALEMENT PAS PLUS PETIT QUE LE MENU */
//var menu = document.getElementById("accordion-menu");
/*
    var menu = document.querySelector(".accordion-menu");
    //var menuHeight = parseInt(window.getComputedStyle(menu).height) + "px";
    var menuHeight = $(menu).height()  + "px";
    //menuHeight= "800px";
    console.log("menuHeight",menuHeight);
    $("main>.content").css("min-height", menuHeight)
    */

    $(".pictos .loupe").click(function ()
    {
        $(".search_mobile").slideToggle( 'fast');
    });
  /*—————————————————————————————————
     ONLOAD, ON RESIZE	
     —————————————————————————————————*/

  

    /* CALCULE ET APPLIQUE LE COEF D HOMOTHETIE PAR RAPPORT AU DESIGN FIGMA BASE SUR 1920PX*/ 
    function coef_homothetie()
    {
            let w = getDocWidth()// - 40;
             var coef
             
            if(w>1024)// PC
                coef  = w/1920; 
            else // MOBILE
            if(w<768) 
                coef  = w/375;
            else // TABLETTE
                coef  = w/875;
     
            $(":root").css("--coef", coef);
            console.log("coef 2",coef);
            
            let docStyle = getComputedStyle(document.documentElement);

            //get variable
            let cur_coef=docStyle.getPropertyValue('--coef');
            console.log("w",w);
            
            //set variable
            document.documentElement.setAttribute("style", "--coef: "+coef);
        
            
    }
    

    /* IMPOSE LARGEUR REELLE D UN ELEMENT */
    function jswidth(){
        
          if ($(".jswidth").length == 0) return;
        
            let w = getDocWidth() ;   
           // console.log("jswidth",w);
            
            //if(w>1024) return;
            let no_arrow  = true;
         
            /* BOUCLE SUR LES ELEMENTS A AJUSTER */
            $( ".jswidth" ).each(function( index ) {

                let cur_elmt = $(this);
                
                let pad_w = cur_elmt.innerWidth() - cur_elmt.width();
                let mar_w = cur_elmt.outerWidth(true) - cur_elmt.outerWidth();
                
                /* ON EVALUE LES PADDING ET MARGIN ENGLOBANTS, c a d ceux des parents */
                $( cur_elmt ).parentsUntil( "body" ).each(function( index ) {
                    let cur_parent = $(this);
                      // console.log(cur_parent, (cur_parent.innerWidth() - cur_parent.width() ) );
                       
                       pad_w += cur_parent.innerWidth() - cur_parent.width();
                       mar_w += cur_parent.outerWidth(true) - cur_parent.outerWidth();
                })
                
              
                let final_w = w-pad_w-mar_w
                console.log(cur_elmt,final_w);
                console.log("this width",$(this).width());
                
                let proxy_final_w = final_w-final_w*0.01
                if($(this).width()>proxy_final_w)no_arrow=false;
                  console.log("this width",$(this).width(), "proxy_final_w", proxy_final_w);
                cur_elmt.css("max-width", final_w + "px");
               
               // console.log(cur_elmt, "max-width", cur_elmt.css("max-width") );
                
          });

            if(no_arrow) $(".cadre_table  .arrow_right").css("display", "none");
           else $(".cadre_table  .arrow_right").css("display", "block");
    }


    function  resize_menu_closer()
    {
            let w = getDocWidth();
            let h = getDocHeight();
            $("#menu_closer").css("width", w+"px")
//            $("#menu_closer").css("height", h+"px")        
    }
    /* F° LANCEE AU DEMARRAGE */
   function at_start()
    {
        coef_homothetie()
        jswidth()
//      resize_menu_closer()
    }

    $(window).resize(function ()
    {
        //console.log("resize");
        at_start()
    })

    at_start()

    /*———————————————————————————————————
     LOGIN REGISTER POPUPS
     ————————————————————————————————————— */

    function hide_login_steps()
    {
        $("#connection.modal .step").hide();
    }

    function hide_login_screens()
    {
        hide_login_steps()
        $("#connection.modal .screen").hide();
    }


    function show_login_screen()
    {
        hide_login_screens()
        $("#connection.modal .login.screen").css("display", "flex")
//       $(".modal .login.screen").animate({display:"flex"}, 1000);
        $("#connection.modal .login .step1").fadeIn();
    }

    function show_register_screen()
    {
        hide_login_screens()
        $("#connection.modal .register.screen").css("display", "flex")
//       $(".modal .login.screen").animate({display:"flex"}, 1000);
        $("#connection.modal .register .step1").fadeIn();

    }


    $("#connection.modal .login .btn_register").click(function ()
    {
        show_register_screen()
    });

    $("#connection.modal .register .btn_login").click(function ()
    {
        show_login_screen();
    });


    $("#connection.modal .login .step1 .client").click(function ()
    {
        hide_login_steps()
        $("#connection.modal .login .step2").fadeIn();
    });

    $("#connection.modal .login .step1 .livimaster").click(function ()
    {
        hide_login_steps()
        $("#connection.modal .login .step3").fadeIn();
    });

    $("#connection.modal .register .step1 .client").click(function ()
    {
        hide_login_steps()
        $("#connection.modal .register .step2").fadeIn();
    });

    $("#connection.modal .register .step1 .livimaster").click(function ()
    {
        hide_login_steps()
        $("#connection.modal .register .step3").fadeIn();
    });

    $("#connection.modal .login .step .left_arrow").click(function ()
    {
        hide_login_steps()
        $("#connection.modal .login .step1").fadeIn();
    });



    $("#connection.modal .register .step .left_arrow").click(function ()
    {
        show_register_screen()
    });


    $("#connection.modal .register .step2 .continuer").click(function ()
    {
        hide_login_steps()
        $(".modal .register .step2_2").fadeIn();
    });

    $("#connection.modal .register .step3 .continuer").click(function ()
    {
        hide_login_steps()
        $(".modal .register .step3_2").fadeIn();
    });

    //  show_login_screen();

    /* 
     * //pour faire afficher la modal at start et en mode register
     $('#connection').modal();
     hide_login_screens()
     $(".modal .register.screen").css("display","flex")
     //       $(".modal .login.screen").animate({display:"flex"}, 1000);
     $(".modal .register .step2_2").fadeIn();
     */

    /*—————————————————————————————————
     SIDE MENU
     —————————————————————————————————*/
    var Accordion = function (el, multiple)
    {
        this.el = el || {};
        // more then one submenu open?
        this.multiple = multiple || false;

        var dropdownlink = this.el.find('.dropdownlink');
        dropdownlink.on('click',
                {el: this.el, multiple: this.multiple},
                this.dropdown);
    };

    Accordion.prototype.dropdown = function (e)
    {
        var $el = e.data.el,
                $this = $(this),
                //this is the ul.submenuItems
                $next = $this.next();

        $next.slideToggle();
        $this.parent().toggleClass('open');

        if (!e.data.multiple)
        {
            //fadeIn only one menu at the same time
            $el.find('.submenuItems').not($next).slideUp().parent().
                    removeClass('open');
        }
    }

    var accordion = new Accordion($('.accordion-menu'), false);


    function toggle_menu()
    {
//      $(".left-column #side-menu").toggle( "fast", function() {   });



        if ($(".left-column .div_vignette").hasClass("menu_closed"))
        {
            $(".left-column .div_vignette").removeClass("menu_closed");
            $(".left-column #side-menu").removeClass("menu_closed");
        } else
        {
            $(".left-column .div_vignette").addClass("menu_closed");
            $(".left-column #side-menu").addClass("menu_closed");
        }

    }



    $(".left-column .div_vignette .btn_menu, .left-column #menu_closer ").click(function ()
    {
        console.log("click");
        toggle_menu();
    });

    toggle_menu();
   


  $( ".only_numbers" ).keydown(function(e) {
			var x = e.which || e.keycode;
			
			console.log("x",x);
			
			if ((x >= 48 && x <= 57)) /* nombres du haut*/
			    return true;
			else
			if ((x >= 96 && x <= 105)) /* nombres du pavé */
			    return true;
			else
			if (x ===188 ) /* comma */
			    return true;
			else    
			if (x ===110 ||  x ===59) /* dot */
                        {
                                 $(this).val($(this).val()+",")
                                 return false;
                        }
			else    
			if (x ===13 ) /* enter */
			    return true;
			else
			if (x ===46 ||  x ===8) /* delete */
			    return true;
			    else
			if (x ===37 ) /* ArrowLeft */
			    return true;
			    else
			if (x ===39 ) /* ArrowRight */
			    return true;
			else
			    return false;
	      });


})