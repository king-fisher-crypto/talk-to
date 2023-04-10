<?= $this->Html->css('/theme/black_blue/css/hayen.css'); ?>
<?= $this->Html->css('/theme/black_blue/css/swiper-bundle.min.css'); ?>
<?= $this->Html->script('/theme/black_blue/js/swiper-bundle.min.js?',array('block' => 'script2')); ?>

    
   

    <div  class="modal cs-agenda-container _form_input" id="modal-consult-agenda">
        <div class="cs-logo"><img src="/theme/black_blue/img/logo_blue.svg"></div>
        <i id="close-agenda" class="fa fa-xmark"></i>
        <div class="cs-agenda-section">
            <div class="cs-agenda-info">
                <div class="cs-avatar">
                    <img src="https://picsum.photos/200/300">
                </div>
                <span class="lh35-52"><?= __('agenda', null, true) ?></span>
                <span class="lh35-52 fw700"><?= $User['pseudo'] ?></span>
                <div class="swiper month-swiper">
                    <div class="swiper-wrapper" rel="">
			
			<?php
			$month_ar = [
			    __('jan', null, true),
			    __('feb', null, true),
			    __('mar', null, true),
			    __('apr', null, true),
			    __('may', null, true),
			    __('jun', null, true),
			    __('jul', null, true),
			    __('aug', null, true),
			    __('sep', null, true),
			    __('oct', null, true),
			    __('nov', null, true),
			    __('dec', null, true)
			];
			
			
			foreach($month_ar as $month) {
			?>
                        <div class="swiper-slide align-center">
                            <div class="cs-month-swiper-sg lh30-35 fw700"><?=$month;?></div>
                        </div>
			<?php 
			}
			?>
                       
                        
                    </div>
                    <div class="swiper-button-next"></div>
                    <div class="swiper-button-prev"></div>
                </div>
            </div>
            <div class="cs-agenda-schedule">
                <div class="swiper schedule-swiper">
                    <div class="swiper-wrapper" rel="">
                        <div class="swiper-slide">
                            <div class="cs-schedule-sg background-orange cs-has-backround border-orange">
                                <h4 class="lh16-24 fw500">Lundi, 01 Juin 2022</h4>
                                <p class="lh20-30">Masterclass thème</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cs-schedule-sg border-primary">
                                <h4 class="lh16-24 fw500">Mardi, 02 Juin 2022</h4>
                                <p class="lh20-30"></p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cs-schedule-sg border-orange">
                                <h4 class="lh16-24 fw500">Mercredi, 03 Juin 2022</h4>
                                <p class="lh20-30">Masterclass thème</p>
                            </div>
                        </div>
                        <div class="swiper-slide">
                            <div class="cs-schedule-sg background-grey cs-has-backround border-grey">
                                <h4 class="lh16-24 fw500">Jeudi, 04 Juin 2022</h4>
                                <p class="lh20-30">Indiponible</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="schedule-swiper-button-next swiper-button-next"></div>
                <div class="schedule-swiper-button-prev swiper-button-prev"></div>
            </div>
            <div class="cs-schedule-by-time">
                <div class="cs-utc-selector">
                    <div class="cs-utc lh16-24 blue2 fw500">
                        <span class="cs-current-utc">Modifier votre réseau UTC</span> <img class="fa-chevron-down fa-angle-down agenda" src="/theme/black_blue/img/menu/chevron.svg" style="z-index: 2">
                        <div class="cs-utc-list cs-utc-type-2">
                            <p class="lh18-27 fw600"><span>Regina</span><span>UTC-12:00</span></p>
                            <p class="lh18-27 fw600"><span>Paris</span><span>UTC-05:00</span></p>
                            <p class="lh18-27 fw600"><span>Berlin</span><span>UTC-12:00</span></p>
                        </div>
                    </div>
                </div>
                <div class="cs-schedule-items">
                    <div class="cs-scheudle-item">
                        <div class="cs-scheudle-time lh20-30 background-orange">
                            <span>09:00</span><span>Masterclass thème</span>
                        </div>
                    </div>
                    <div class="cs-scheudle-item">
                        <div class="cs-scheudle-time lh20-30 background-grey">
                            <span>10:00</span><span>Indisponible</span>
                        </div>
                    </div>
                    <div class="cs-scheudle-item">
                        <div class="cs-scheudle-time lh20-30 background-primary">
                            <span>11:00</span><span>Disponible pour rendez-vous</span>
                        </div>
                    </div>
                    <div class="cs-scheudle-item">
                        <div class="cs-scheudle-time lh20-30 background-grey">
                            <span>12:00</span><span>Indisponible</span>
                        </div>
                        <div class="cs-scheudle-time lh20-30 background-primary">
                            <span>12:15</span><span>Disponible pour rendez-vous</span>
                        </div>
                    </div>
                    <div class="cs-scheudle-item">
                        <div class="cs-scheudle-time lh20-30 background-black">
                            <span>13:00</span><span>Créneau réservé</span>
                        </div>
                    </div>
                    <div class="cs-scheudle-item">
                        <div class="cs-scheudle-time lh20-30 background-primary">
                            <span>14:00</span><span>Disponible pour rendez-vous</span>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>



 <style>
        /*Agenda*/
        .cs-agenda-container {
            max-width: calc(1030px*var(--coef));
            width: 100%;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            background: #FBFBFB;
            box-shadow: 0px 0px 35px rgb(0 0 0 / 8%);
            border-radius: 15px;
            padding: calc(25px*var(--coef));
            position: relative;
        }
        .cs-agenda-section .cs-agenda-info {
            text-align: center;
        }
        .cs-agenda-container .cs-logo {
            width: calc(100px*var(--coef));
            position: absolute;
            top: calc(25px*var(--coef));
            left: calc(25px*var(--coef));
        }
        .cs-agenda-container #close-agenda {
            position: absolute;
            right: calc(25px*var(--coef));
            color: #4CBBEC;
            top: calc(25px*var(--coef));
            font-size: calc(22px*var(--coef));
        }
        .cs-agenda-container img {
            max-width: 100%;
        }
        .cs-agenda-info .cs-avatar img {
            width: calc(105px*var(--coef));
            height: calc(105px*var(--coef));
            border-radius: 50%;
            border: 1px solid #4CBBEC;
        }
        .cs-agenda-section .cs-agenda-info > p {
            margin: 0px;
        }
        .swiper.month-swiper {
            max-width: calc(240px*var(--coef));
            margin-top: calc(10px*var(--coef));
            margin-bottom: calc(10px*var(--coef));
        }
        .month-swiper .swiper-button-next:after, .month-swiper .swiper-button-prev:after {
            font-size: calc(19px*var(--coef));
            font-weight: 700;
        }
        .cs-agenda-schedule {
            padding-left: calc(35px*var(--coef));
            padding-right: calc(35px*var(--coef));
            position: relative;
            /*margin-bottom: calc(20px*var(--coef));*/
        }
        .cs-schedule-items {
            margin-top: calc(10px*var(--coef));
        }
        .cs-schedule-sg {
            border-radius: 10px;
            padding: calc(10px*var(--coef));
        }
        .cs-schedule-sg > * {
            margin: 0px;
        }
        .cs-schedule-sg.cs-has-backround > * {
            color:#fff;
        }
        .cs-schedule-sg > h4 {
            margin-bottom: calc(10px*var(--coef));
        }
        .cs-schedule-sg > p {
            min-height: calc(60px*var(--coef));
        }
        .schedule-swiper-button-prev.swiper-button-prev {
            left: 0px;
        }
        .schedule-swiper-button-next.swiper-button-next {
            right: 0px;
        }
        .schedule-swiper-button-prev.swiper-button-prev:after, .schedule-swiper-button-next.swiper-button-next:after {
            font-size: calc(32px*var(--coef));
            font-weight: 700;
        }
        .cs-schedule-by-time {
            width: 100%;
            max-width: calc(730px*var(--coef));
            margin: auto;
        }
        .cs-scheudle-item {
            overflow: hidden;
            border-radius: 10px;
            display: flex;
            margin-bottom: calc(10px*var(--coef));
        }
        .cs-scheudle-item .cs-scheudle-time {
            transform: skew(-20deg);
            background: #b3b3b3;
            padding: calc(10px*var(--coef)) calc(30px*var(--coef));
            color: #fff;
            width: 100%;
            box-sizing: unset;
        }
        .cs-scheudle-item .cs-scheudle-time:not(:last-child) {
            width: 50%;
        }
        .cs-scheudle-item .cs-scheudle-time > * {
            display: inline-block;
            transform: skew(20deg);
        }
        .cs-scheudle-item .cs-scheudle-time:first-child {
            margin-left: calc(-20px*var(--coef));
        }
        .cs-scheudle-item .cs-scheudle-time:last-child {
            margin-right: calc(-20px*var(--coef));
        }
        .cs-scheudle-item .cs-scheudle-time > span:first-child {
            margin-right: calc(20px*var(--coef));
        }
        @media (max-width: 1024px){
            .cs-agenda-container{
                max-width: calc(645px*var(--coef));
                padding: calc(20px*var(--coef));
            }
            .cs-avatar {
                margin-top: calc(15px*var(--coef));
            }
            .cs-agenda-container #close-agenda{
                width: calc(20px*var(--coef));
            }
            .cs-agenda-info .cs-avatar img {
                width: calc(85px*var(--coef));
		height: calc(85px*var(--coef));;
            }
            .cs-agenda-section .cs-agenda-info > p {
                font-size: calc(25px*var(--coef));
                line-height: calc(30px*var(--coef));
            }
            .cs-month-swiper-sg {
                font-size: calc(20px*var(--coef));
                line-height: calc(23px*var(--coef));
            }
            .cs-schedule-sg > h4 {
                margin-bottom: calc(10px*var(--coef));
                font-size: calc(16px*var(--coef));
                line-height: calc(19px*var(--coef));
            }
            .cs-schedule-sg > p {
                min-height: calc(60px*var(--coef));
                font-size: calc(20px*var(--coef));
                line-height: calc(24px*var(--coef));
            }
            .cs-utc-selector .cs-utc{
                max-width: calc(205px*var(--coef));
            }
            .cs-participer .lh24-36 {
                font-size: calc(18px*var(--coef));
                line-height: calc(27px*var(--coef));
            }
            .cs-participer .cs-btn-1 {
                text-transform: uppercase;
                padding: calc(10px*var(--coef)) calc(20px*var(--coef));
                margin-top: 10px;
            }
            .cs-schedule-by-time img.fa-chevron-down.fa-angle-down {
                width: calc(19px*var(--coef));
            }
        }
        @media (max-width:767px){
            .cs-agenda-container{
                max-width: calc(347px*var(--coef));
                padding: calc(10px*var(--coef));
            }
            .cs-agenda-container .cs-logo {
                width: calc(72px*var(--coef));
                top: calc(15px*var(--coef));
                left: calc(15px*var(--coef));
            }
            .cs-agenda-container #close-agenda {
                right: calc(12px*var(--coef));
                top: calc(12px*var(--coef));
            }
            .cs-agenda-info .cs-avatar img{
                width: calc(72px*var(--coef));
                height: calc(72px*var(--coef));
            }
            .cs-agenda-section .cs-agenda-info > p {
                font-size: calc(18px*var(--coef));
                line-height: calc(21px*var(--coef));
            }
            .cs-month-swiper-sg {
                font-size: calc(16px*var(--coef));
                line-height: calc(19px*var(--coef));
            }
            .month-swiper .swiper-button-next:after, .month-swiper .swiper-button-prev:after {
                font-size: calc(16px*var(--coef));
            }
            .schedule-swiper-button-prev.swiper-button-prev:after, .schedule-swiper-button-next.swiper-button-next:after {
                font-size: calc(20px*var(--coef));
            }
        }
    </style>
    
    
    <script>
document.addEventListener("DOMContentLoaded", function() {
    
    
    
            let swiper = new Swiper(".month-swiper", {
                loop: true,
                navigation: {
                    nextEl: ".swiper-button-next",
                    prevEl: ".swiper-button-prev",
                },
            });
            let swiperSchedule = new Swiper(".schedule-swiper", {
                slidesPerView: 4,
                spaceBetween: 20,
                loop: true,
                navigation: {
                    nextEl: ".schedule-swiper-button-next",
                    prevEl: ".schedule-swiper-button-prev",
                },
                breakpoints: {
                    320: {
                        slidesPerView: 1,
                        spaceBetween: 10
                    },
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 15
                    },
                    992: {
                        slidesPerView: 4,
                        spaceBetween: 20
                    }
                }
            });
	    
	    
	      /* CLICK ANYWHERE TO CLOSE */
	
	 $(window).click(function() {
	   
	     let img = $(".cs-utc-selector .cs-utc > img");
	      $(img).removeClass('active');
              $(img).closest('.cs-dad-right').removeClass('cs-layer');
	  });  
        
        
        $('body').on('click', '#modal-consult-agenda .cs-utc-selector .cs-utc', function (e)
        {

            e.preventDefault();
	    e.stopPropagation();
            let img = $(this).find('> img');
            console.log("click #modal-consult-agenda .cs-utc-selector .cs-utc", img);
            if ($(img).hasClass('active'))
            {
                $(img).toggleClass('active');
                $(img).closest('.cs-dad-right').removeClass('cs-layer');
            } else
            {
                $(img).toggleClass('active');
                $(img).closest('.cs-dad-right').addClass('cs-layer');
            }
        });

	$('body').on('click','#modal-consult-agenda  .cs-utc-selector .cs-utc .cs-utc-list > p',function(e){
		e.preventDefault();

		let $parent = $(this).closest('.cs-utc-selector');
		let html = $(this).html();
		if($(this).hasClass('active')) return false;
		$parent.find('.cs-utc .cs-utc-list > p').removeClass('active');
		$(this).addClass('active');
		$(this).closest('.cs-utc').find('.cs-current-utc').html(html);
		
//		$(this).closest('.cs-utc').find('> img').click();
	});
	    
});
    </script>