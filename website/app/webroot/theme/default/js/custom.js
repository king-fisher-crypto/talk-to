$(document).ready(function(){

	$('.mobile-header .top-mobile-login span.lclick').click(function(){
    	$(this).find('a.login-a').toggleClass('login-slide');
    	$(this).find('i.glyphicon-chevron-down').toggleClass('glyphicon-chevron-up');
});

	$('.mobile-filter .search').click(function(){
    	$(this).toggleClass('search-active');
});

	jQuery(window).load(function() {
		jQuery("#preloader").delay(100).fadeOut("slow");
		jQuery("#load").delay(100).fadeOut("slow");
	});


	//jQuery to collapse the navbar on scroll
	// $(window).scroll(function() {
	// 	if ($(".navbar").offset().top > 50) {
	// 		$(".navbar-fixed-top").addClass("top-nav-collapse");
	// 	} else {
	// 		$(".navbar-fixed-top").removeClass("top-nav-collapse");
	// 	}
	// });

	//backToTop

	$('body').append('<div id="toTop" class="btn"><span class="glyphicon glyphicon-chevron-up"></span></div>');
    	$(window).scroll(function () {
			if ($(this).scrollTop() != 0) {
				$('#toTop').fadeIn();
			} else {
				$('#toTop').fadeOut();
			}
		});
    $('#toTop').click(function(){
        $("html, body").animate({ scrollTop: 0 }, 600);
        return false;
    });

if($(window).width() >= 767){
    $(".mega-dropdown").hover(
        function() {
			$('.dropdown-menu', this).delay( 200 ).slideDown("fast");
           // $('.dropdown-menu', this).stop( true, true ).slideDown("fast");
            // $(this).toggleClass('open');
            $(this).addClass('active');
        },
        function() {
			//$('.dropdown-menu', this).slideUp("fast");
			//alert($(".mega-dropdown").ismouseover());
		/*	if($(".navbar-main").is(':hover')){
				$('.dropdown-menu', this).css('display','none');
			}else{
				$('.dropdown-menu', this).slideUp("fast");
			}*/

            $('.dropdown-menu', this).stop( true, true ).slideUp("fast");
		   //
            // $(this).toggleClass('open');
            $(this).removeClass('active');
        }
    );
	$(".small-dropdown").hover(
        function() {
            $('.dropdown-menu', this).delay( 200 ).slideDown("fast");
            // $(this).toggleClass('open');
            $(this).addClass('active');
        },
        function() {
            $('.dropdown-menu', this).stop( true, true ).slideUp("fast");
            // $(this).toggleClass('open');
            $(this).removeClass('active');
        }
    );
  }

    $('[data-toggle="tooltip"]').tooltip();

    //carousel
     $("#carousel").carousel({
         interval : 6000,
         // pause: true
     });

     //fade-quote-carousel
     //carousel
     $("#template_3 #fade-quote-carousel").carousel({
         interval : 5000,
         //pause: false
     });

    $("#template_3 #fade-quote-carousel-for-mobile").carousel({
        interval : 5000,
        //pause: false
    });

// in template page show 3 item into slider
    $('#template_3 #fade-quote-carousel .item').each(function(){
        var next = $(this).next();
        if (!next.length) {
            next = $(this).siblings(':first');
        }
        next.children(':first-child').clone().appendTo($(this));

        if (next.next().length>0) {
            next.next().children(':first-child').clone().appendTo($(this));
        } else {
            $(this).siblings(':first').children(':first-child').clone().appendTo($(this));
        }
    });

    $('.filter-advance').click(function(e) {
    	$(e.target).find('.fa-plus-circle, .fa-minus-circle').toggleClass("fa-plus-circle fa-minus-circle");
	});

    $('.search a').click(function(){
      $(this).toggleClass("active-search");
  });



    //user-logged dropdown
    // $(".user-logged").hover(
    //     function() {
    //         $('.dropdown-menu', this).stop( true, true ).slideDown("fast");
    //         $(this).toggleClass('open');
    //     },
    //     function() {
    //         $('.dropdown-menu', this).stop( true, true ).slideUp("fast");
    //         $(this).toggleClass('open');
    //     }
    // );

    $('.collapse').on('shown.bs.collapse', function(){
	$(this).parent().find(".fa-sort-asc").removeClass("fa-sort-asc").addClass("fa-sort-desc");
	}).on('hidden.bs.collapse', function(){
	$(this).parent().find(".fa-sort-desc").removeClass("fa-sort-desc").addClass("fa-sort-asc");
	});

/**message Collapse**/

      $('.collapse').on('shown.bs.collapse', function(){
  $(this).parent().find(".fa-plus-circle").removeClass("fa-plus-circle").addClass("fa-minus-circle");
  }).on('hidden.bs.collapse', function(){
  $(this).parent().find(".fa-minus-circle").removeClass("fa-minus-circle").addClass("fa-plus-circle");
  });





    // Collapse accordion every time dropdown is shown
$('.dropdown-accordion').on('show.bs.dropdown', function (event) {
  var accordion = $(this).find($(this).data('accordion'));
  accordion.find('.panel-collapse.in').collapse('hide');
});

// Prevent dropdown to be closed when we click on an accordion link
$('.dropdown-accordion').on('click', 'a[data-toggle="collapse"]', function (event) {
  event.preventDefault();
  event.stopPropagation();
  $($(this).data('parent')).find('.panel-collapse.in').collapse('hide');
  $($(this).attr('href')).collapse('show');
})



//nav-tabs scroll

    $('a.scroll[href^="#"]').bind('click.smoothscroll',function (e) {
        e.preventDefault();
        var target = this.hash,
        $target = $(target);

      /* $('html, body').stop().animate( {
            'scrollTop': $target.offset().top-100
        }, 900, 'swing', function () {
            window.location.hash = target;
        } );*/
		$('html, body').animate( { scrollTop: $(target).offset().top -150 }, 900 );
    } );


	var elem = $('#' + window.location.hash.replace('#', ''));
    if(elem.size() > 0) {
		$('html, body').animate( { scrollTop: elem.offset().top -150 }, 900 );
    }



//iconified

$('.color-icon ').focus(function() {
    var input = $(this);
    if(input.val().length === 0) {
        input.prev( ".fa").addClass('icon-colored');
    } else {
        input.prev( ".fa").addClass('icon-colored');
    }
});

//model-collapse plus-minus

  $('.collapse').on('shown.bs.collapse', function(){
  $(this).parent().find(".fa-plus-circle").removeClass("fa-plus-circle").addClass("fa-minus-circle");
  }).on('hidden.bs.collapse', function(){
  $(this).parent().find(".fa-minus-circle").removeClass("fa-minus-circle").addClass("fa-plus-circle");
  });


  //header-color

    $(window).scroll(function() {
        var scroll = $(window).scrollTop();

        if (scroll >= 85) {
             $(".navbar").addClass("darkHeader");
        } else {
             $(".navbar").removeClass("darkHeader");
        }
    });


/***star rating***/

$('.rank-half').hover(function() {
    var thisIndex = $(this).index(),
        parent = $(this).parent(),
        parentIndex = parent.index(),
        ranks = $('.rank-container');
    for (var i = 0; i < ranks.length; i++) {
        if(i < parentIndex) {
          $(ranks[i]).removeClass('half').addClass('full');
        } else {
            $(ranks[i]).removeClass('half').removeClass('full');
        }
    }
    if(thisIndex == 0) {
    parent.addClass('half');
    } else {
        parent.addClass('full');
    }
});

$('.rank-half').click(function() {
    var thisIndex = $(this).index(),
        parent = $(this).parent(),
        parentIndex = parent.index(),
        rating = parentIndex;
    rating += thisIndex ? 1 : 0.5;
    // $('.foo').text(rating);
});



/**date chooser***/
/**ref: http://www.daterangepicker.com/#ex4**/

    var start = moment().subtract(29, 'days');
    var end = moment();

    function cb(start, end) {
        $('#reportrange span').html(start.format('DD-MM-YYYY') + ' - ' + end.format('DD-MM-YYYY'));
    }

    /*$('.date-range').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Aujourd\'hui': [moment(), moment()],
           'Hier': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Les 7 derniers jours': [moment().subtract(6, 'days'), moment()],
           'Les 29 derniers jours': [moment().subtract(29, 'days'), moment()],
           'Ce mois-ci': [moment().startOf('month'), moment().endOf('month')],
           'Le mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        format: 'DD-MM-YYYY',
        separator: ' au ',
        locale: {
                    applyLabel: 'Appliquer',
                    fromLabel: 'Du',
                    toLabel: 'Au',
                    customRangeLabel: 'Personnaliser',
                    daysOfWeek: ["D", "L", "Ma", "Me", "J", "V", "S"],
                    monthNames: ["Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"],
                    monthsShort: ["Jan", "Fév", "Mar", "Avr", "Mai", "Jui", "Jul", "Aou", "Sep", "Oct", "Nov", "Déc"],
                    firstDay: 1
                },

    }, cb);

    cb(start, end);*/


});






