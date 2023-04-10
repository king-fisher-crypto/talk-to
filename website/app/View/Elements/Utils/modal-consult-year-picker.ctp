<?= $this->Html->css('/theme/black_blue/css/hayen.css'); ?>
 

<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?',array('block' => 'script2')); ?>

<script>
    /*
      let docStyle = getComputedStyle(document.documentElement);
      let cur_coef=docStyle.getPropertyValue('--coef');
      let coef_css =    cur_coef/2
            
            //set variable
	
      document.documentElement.setAttribute("style", "--coef: "+coef_css);
      console.log("cur_coef",cur_coef,"coef_css", coef_css);
      */
</script>

<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.hayen.css?a='.rand()); ?>
<script>
 
            
            //set variable
      //document.documentElement.setAttribute("style", "--coef: "+cur_coef);
</script>

<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>

    <style>
        
        #modal-consult-year-picker{

	}
	
	
	.blocker{
	    text-align: left;
	}
	
	#modal-consult-year-picker .cs-dad-right .text-uppercase:first-child{
	    width:calc(250px*var(--coef)); 
	}
	
	#modal-consult-year-picker a.close-modal{
	   right: calc(15px*var(--coef)); 
	}

        #cs-datetime-picker-avec-durees .dcalendarpicker {
            visibility: visible;
            opacity: 1;
            position: unset;
        }
    
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__wrapper {
            position: unset;
            transform: unset;
            box-shadow: unset;
            font-size: calc(1*18px*var(--coef));
            font-weight: 100;
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container{
            width: calc(1*298px*var(--coef));
            background: #000;
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays span {
            font-size: calc(1*18px*var(--coef));
            font-weight: 500;
            width: calc(1*40px*var(--coef));
            line-height: calc(1*30px*var(--coef));
            text-transform: uppercase;
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date{
            width: calc(1*40px*var(--coef));
            line-height: calc(1*40px*var(--coef));
            height: calc(1*40px*var(--coef));
            color: #ffffffcc;
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev, #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next{
            font-size: calc(1*40px*var(--coef));
            font-weight: 400;
            top: calc(1*15px*var(--coef));
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date.dudp__pm, #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date.dudp__nm {
            color: #969696;
            opacity: 0;
            visibility: hidden;
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-month, #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-year {
            font-size: calc(1*22px*var(--coef));
            font-weight: 400;
            text-transform: capitalize;
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year {
            padding-top: calc(1*15px*var(--coef));
            padding-bottom: calc(1*15px*var(--coef));
        }
        #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__buttons {
            opacity: 0;
            visibility: hidden;
        }

        .cs-datetime-picker-avec-durees-section {
            display: flex;
            padding: calc(1*20px*var(--coef));
            background: #FBFBFB;
            box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
            border-radius: calc(1*15px*var(--coef));
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }
        .cs-dad-right {
            width: calc(1*318px*var(--coef));
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            padding: calc(1*10px*var(--coef)) calc(1*20px*var(--coef));
            border-radius: calc(1*15px*var(--coef));
            position: relative;
        }
        .cs-dad-right.cs-layer:before {
            background: linear-gradient(181.57deg, rgba(0, 0, 0, 0.45) -94.89%, rgba(43, 43, 43, 0.227463) 96.49%, rgba(0, 0, 0, 0) 116.85%);
            filter: drop-shadow(0px 3px 35px rgba(0, 0, 0, 0.08));
            border-radius: calc(1*15px*var(--coef));
            position: absolute;
            width: 100%;
            height: 100%;
            z-index: 1;
            content: "";
            left: 0;
            top: 0;
        }
        .cs-dad-left {
            position: relative;
            padding: calc(1*20px*var(--coef));
            box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
            border-radius: calc(1*15px*var(--coef));
        }
        .cs-dad-left .cs-logo {
            position: absolute;
            left: calc(1*20px*var(--coef));
            top: calc(1*20px*var(--coef));
            width: calc(1*72px*var(--coef));
        }
        .cs-dad-left .cs-avatar {
            text-align: center;
            width: calc(1*51px*var(--coef));
            margin: auto;
            border-radius: 50%;
            overflow: hidden;
            border: 1px var(--blue) solid;
            margin-top: calc(1*30px*var(--coef));
            margin-bottom: calc(1*10px*var(--coef));
        }
        .cs-dad-left > p {
            margin: 0px;
        }

        .cs-dad-right > .cs-setting {
            display: flex;
            justify-content: space-between;
            margin-top: calc(1*25px*var(--coef));
            margin-bottom: calc(1*25px*var(--coef));
            flex-wrap: wrap;
        }
        .cs-dad-right > .cs-setting > label {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            max-width: calc(1*80px*var(--coef));
            text-align: center;
        }
        .cs-dad-right > .cs-setting > label span {
            background: #EAEAEA;
            box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
            border-radius: calc(1*10px*var(--coef));
            color: #4CBBEC;
            font-weight: 500;
            font-size: calc(1*18px*var(--coef));
            line-height: calc(1*21px*var(--coef));
            padding: calc(1*5px*var(--coef)) calc(1*12px*var(--coef));
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            transition: 0.3s all;
        }
        .cs-dad-right > .cs-setting > label input:checked + span {
            background: #4CBBEC;
            color: #fff;
        }

        .cs-dad-right > .cs-setting.cs-list-time > label {
            max-width: 100%;
            width: 100%;
        }
        .cs-dad-right > .cs-setting.cs-list-time > label:not(:last-child){
            margin-bottom:calc(1*15px*var(--coef));
        }
        .cs-dad-right > .cs-setting.cs-list-time > label span {
            display: block;
            background: #EAEAEA;
            box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
            border-radius: calc(1*10px*var(--coef));
            padding: calc(1*6px*var(--coef)) calc(1*12px*var(--coef));
            color: #000;
        }
        .cs-dad-right > .cs-setting.cs-list-time > label input[name="cs-time-none"] + span {
            background: #F5F5F5;
            color: #B3B3B3;
            font-weight: 100;
        }
        #cs-dad-submit {
            background: #FFFFFF;
            box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
            border-radius: calc(1*15px*var(--coef));
            color: #4CBBEC;
            display: block;
            font-weight: 600;
            font-size: calc(1*24px*var(--coef));
            line-height: 150%;
            padding: calc(1*5px*var(--coef));
            transition: 0.3s all;
        }
        #cs-dad-submit:hover {
            background: #4CBBEC;
            color: #fff;
        }
	
        @media (max-width:1024px){
            .cs-datetime-picker-avec-durees-container {
                max-width: calc(1*629px*var(--coef));
            }
            .cs-datetime-picker-avec-durees-section{
                padding: calc(1*15px*var(--coef));
            }
            .cs-datetime-picker-avec-durees-section > div {
                /*width: 50%;*/
                width: calc(1*50%*var(--coef));
            }
            .cs-dad-left > p {
                font-size: calc(1*21px*var(--coef));
                line-height: calc(1*24px*var(--coef));
            }
            #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev, #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
                font-size: calc(1*34px*var(--coef));
            }
            #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays span {
                font-size: calc(1*18px*var(--coef));
                width: calc(1*38px*var(--coef));
                line-height: calc(1*24px*var(--coef));
            }
            #cs-datetime-picker-avec-durees .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
                width: calc(1*38px*var(--coef));
                line-height: calc(1*38px*var(--coef));
                height: calc(1*38px*var(--coef));
                color: #ffffffcc;
            }
        }
        @media (max-width:767px){
            .cs-datetime-picker-avec-durees-container {
                max-width: calc(1*330px*var(--coef));
            }
            .cs-datetime-picker-avec-durees-section {
                flex-wrap: wrap;
            }
            .cs-datetime-picker-avec-durees-section > div {
                width: 100%;
            }
            .cs-dad-left {
                margin-bottom: calc(1*25px*var(--coef));
            }
            .cs-dad-right {
                padding: calc(1*10px*var(--coef)) calc(1*0px*var(--coef));
            }
            #cs-dad-submit {
                background: #FFFFFF;
                box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
                border-radius: calc(1*15px*var(--coef));
                color: #4CBBEC;
                display: block;
                font-weight: 600;
                font-size: calc(1*16px*var(--coef));
                line-height: calc(1*24px*var(--coef));
                padding: calc(1*5px*var(--coef));
                transition: 0.3s all;
                max-width: calc(1*220px*var(--coef));;
                margin: auto;
                text-transform: uppercase;
            }
            .cs-dad-right > .cs-setting{
                margin-bottom: calc(1*20px*var(--coef));
            }
	    
	    
	
	.cs-utc > i, .cs-utc > img {
	    margin-left: calc(5px*var(--coef));
	    width: calc(24px*var(--coef));
	}
	    
	    
        }
	
    </style>

    
    
    
    
    
    
    <div id="modal-consult-year-picker" class="modal cs-datetime-picker-avec-durees-container _form_input">
        <div class="cs-datetime-picker-avec-durees-section">
            <div class="cs-dad-left background-black">
                <div class="cs-logo"><img src="/theme/black_blue/img/logo-white.png"></div>
                <div class="cs-avatar">
                    <img src="https://picsum.photos/200/300">
                </div>
                <p class="lh22-28 fw400 color-white align-center"><?= __('consultation avec', null, true) ?></p>
                <p class="lh22-28 fw500 color-white align-center"><?= $User['pseudo'] ?></p>
                <div class="cs-datetimepicker">
                    <input type="text" id="mod_cons_year_picker" readonly value="" style="display: none;">
                    <div id="cs-datetime-picker-avec-durees"></div>
                </div>
            </div>
            <div class="cs-dad-right">
                <p class="lh20-30 text-uppercase cs-margin-0 fw500"><?= __('de combien de temps avez-vous besoin?', null, true) ?></p>
                <div class="cs-setting cs-radio">
                    <label class="cs-control">
                        <input type="radio" name="cs-min" id="cs-15min" value="15 min" checked>
                        <span>15 <?=  __('mn') ?></span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-min" id="cs-30min" value="30 min">
                        <span>30 <?=  __('mn') ?></span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-min" id="cs-60min" value="60 min">
                        <span>60 <?=  __('mn') ?></span>
                    </label>
                </div>
                <p class="lh20-30 text-uppercase cs-margin-0 fw500"><?=  __('choisissez l\'heure de rendez-vous', null, true) ?></p>
                <div class="cs-utc-selector">
                    <div class="cs-utc lh16-24 blue2 fw500">
                        <span class="cs-current-utc"><?= __('modifier votre réseau UTC', null, true) ?></span> <img class="fa-chevron-down fa-angle-down picker" src="/theme/black_blue/img/menu/chevron.svg">
                        <div class="cs-utc-list cs-utc-type-2">
                            <p class="lh18-27 fw600"><span>Regina</span><span>UTC-12:00</span></p>
                            <p class="lh18-27 fw600"><span>Paris</span><span>UTC-05:00</span></p>
                            <p class="lh18-27 fw600"><span>Berlin</span><span>UTC-12:00</span></p>
                            <p class="lh18-27 fw600"><span>Regina</span><span>UTC-12:00</span></p>
                        </div>
                    </div>
                </div>
                <div class="cs-setting cs-radio cs-list-time">
                    <label class="cs-control">
                        <input type="radio" name="cs-time" value="11:00 am">
                        <span>11:00 am</span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-time" value="11:15 am">
                        <span>11:15 am</span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-time-none" value="11:30 am">
                        <span>11:30 am</span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-time" value="11:45 am">
                        <span>11:45 am</span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-time" value="12:00 am">
                        <span>12:00 am</span>
                    </label>
                    <label class="cs-control">
                        <input type="radio" name="cs-time-none" value="12:15 am">
                        <span>12:15 am</span>
                    </label>
                </div>
                <div class="cs-submit-button align-center">
                    <a id="cs-dad-submit" href="javascript:void(0);"><?=  __('valider ma sélection', null, true) ?></a>
                </div>
            </div>
        </div>
    </div>


<script type="text/javascript">
document.addEventListener("DOMContentLoaded", function() { 
	
        duDatepicker('#mod_cons_year_picker', {
            format: 'mmm d, yyyy', //range: true,
            i18n: 'fr',
            firstDay: 1,
            events: {
                dateChanged: function (data) {
                    console.log(data)
                    console.log('Date: ' + data.date)
                }
            },
            root: '#cs-datetime-picker-avec-durees'
        })
        duDatepicker('#mod_cons_year_picker','show')
        
        
        
        
         /* CLICK ANYWHERE TO CLOSE */
	
	 $(window).click(function() {
	   
	     let img = $(".cs-utc-selector .cs-utc > img");
	      $(img).removeClass('active');
              $(img).closest('.cs-dad-right').removeClass('cs-layer');
	  });  
        
        
        $('body').on('click', '#modal-consult-year-picker .cs-utc-selector .cs-utc', function (e)
        {

            e.preventDefault();
	    e.stopPropagation();
            let img = $(this).find('> img');
            console.log("click img", img);
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

	$('body').on('click','#modal-consult-year-picker .cs-utc-selector .cs-utc .cs-utc-list > p',function(e){
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