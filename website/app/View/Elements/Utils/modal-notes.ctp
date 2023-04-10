<style>

    .modal{
	/*display:block !important;*/
    }

    #modal-notes.modal
    {
	border-radius:   calc(15px*var(--coef));
	/*display: block !important;*/
	width:calc(1047px*var(--coef));
	height: calc(686px*var(--coef));
	background: white;
    }

    #modal-notes .modal-content{
	width: 100%;
	height: calc(400px*var(--coef));
	display: flex;

	align-items: stretch;

    }

    #modal-notes .header{
	/*position:absolute;*/
	background: var(--second-bg-color);
	height:  calc(70px*var(--coef));
	width:100%;
	border-radius: calc(15px*var(--coef));

    }

    #modal-notes img.rounded{
	position: absolute;
	width:calc(50px*var(--coef));
	height:calc(50px*var(--coef));
	top:calc(10px*var(--coef));
	left:calc(40px*var(--coef));
	border: none;
    }

    #modal-notes .header .name{
	position: absolute;
	top:calc(20px*var(--coef));
	left:calc(110px*var(--coef));
	font-weight: 500;
	color:white;

    }

    #modal-notes .close-modal{
	width: calc(22px*var(--coef));
	height: calc(22px*var(--coef));
	top:calc(24px*var(--coef));
	right:calc(40px*var(--coef));

    }

    #modal-notes    img.notes{
	position: absolute;
	width: calc(28px*var(--coef));
	height: calc(28px*var(--coef));
	top:calc(19px*var(--coef));
	right:calc(77px*var(--coef));
    }


    #modal-notes  .time_infos{
	width:100%;
	position: relative;
	height: calc(50px*var(--coef));
	;
    }

    #modal-notes .date{
	position: absolute;
	top:calc(10px*var(--coef));
	left:calc(20px*var(--coef));
    }

    #modal-notes .time{
	position: absolute;
	top:calc(10px*var(--coef));
	right:calc(23px*var(--coef));
    }



    #modal-notes #notes{
	margin:  0 auto  calc(40px*var(--coef)) auto;
	color:var(--main-color);
	overflow-y: auto;
	;
	padding: 0 5% 0 5%;
	line-height: calc(50px*var(--coef));
	height: 100%;
	width:100%;
	text-align: left;
	font-weight: 400;
    }

    #modal-notes .last_modif{
	color:var(--light-grey);
	font-weight: 400;
	position: absolute;
	left:calc(40px*var(--coef));
	bottom:calc(33px*var(--coef));
    }


    #modal-notes.modal .btn.validate{
	position: absolute;
	padding: 0 calc(80px*var(--coef)) 0 calc(78px*var(--coef));
	right:calc(23px*var(--coef));
	bottom:calc(33px*var(--coef));

    }



    /* tablets ----------- */
    @media only screen and (max-width : 1024px) {

	#modal-notes.modal
	{
	    width:calc(809px*var(--coef));
	    height: calc(650px*var(--coef));
	}

	#modal-notes .modal-content{

	    height: calc(400px*var(--coef));

	}

	#modal-notes .header{
	}

	#modal-notes img.rounded{
	    left:calc(20px*var(--coef));

	}

	#modal-notes .header .name{
	    left:calc(90px*var(--coef));
	}

	#modal-notes .close-modal{
	    top:calc(24px*var(--coef));
	    right:calc(23px*var(--coef));
	}

	#modal-notes    img.notes{
	    right:calc(60px*var(--coef));
	}


	#modal-notes  .time_infos{
	    height: calc(50px*var(--coef));
	    ;
	}

	#modal-notes .date{
	    left:calc(20px*var(--coef));
	}

	#modal-notes .time{
	    right:calc(23px*var(--coef));
	}



	#modal-notes #notes{

	    line-height: calc(40px*var(--coef));

	}

	#modal-notes .last_modif{

	    left:calc(5px*var(--coef));
	    bottom:calc(71px*var(--coef));
	}


	#modal-notes.modal .btn.validate{
	    height: calc(50px*var(--coef));
	    padding: 0 calc(27px*var(--coef)) 0 calc(27px*var(--coef));
	    right:calc(5px*var(--coef));
	    bottom:calc(20px*var(--coef));

	}


    }


    @media only screen   and (max-width : 767px)
    {
	#modal-notes.modal
	{
	  
	    width:calc(360px*var(--coef));
	    height: calc(550px*var(--coef));
	
	}

	#modal-notes .modal-content{
	    width: 100%;
	    height: calc(330px*var(--coef));

	}


	#modal-notes img.rounded{
	    left:calc(5px*var(--coef));
	}

	#modal-notes .header .name{
	    left:calc(75px*var(--coef));
	}

	#modal-notes .close-modal{
	    width: calc(22px*var(--coef));
	    height: calc(22px*var(--coef));
	    top:calc(24px*var(--coef));
	    right:calc(15px*var(--coef));

	}

	#modal-notes    img.notes{
	    width: calc(28px*var(--coef));
	    height: calc(28px*var(--coef));
	    top:calc(19px*var(--coef));
	    right:calc(77px*var(--coef));
	}

	#modal-notes .date{
	    top:calc(10px*var(--coef));
	    left:calc(5px*var(--coef));
	}

	#modal-notes .time{
	    top:calc(10px*var(--coef));
	    right:calc(5px*var(--coef));
	}

	#modal-notes #notes{
	    margin:  0 auto  calc(40px*var(--coef)) auto;
	    padding: 0 5% 0 5%;
	    line-height: calc(30px*var(--coef));
	}

	#modal-notes .last_modif{

	    left:calc(5px*var(--coef));
	    bottom:calc(80px*var(--coef));
	}


	#modal-notes.modal .btn.validate{
	    height: calc(36px*var(--coef));
	    padding: 0 calc(27px*var(--coef)) 0 calc(27px*var(--coef));
	    right:calc(5px*var(--coef));
	    bottom:calc(20px*var(--coef));

	}


    }

</style>

<div class="modal  fade" id="modal-notes"  role="dialog" >

    <div class="header">
	<img src="https://picsum.photos/200/300" class="rounded">
	<div class="name p26 m22">James Potter</div>
	<img class="notes" src="/theme/black_blue/img/notes.svg" alt="See">
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>


    <div class="time_infos">
	<div class="date"><?= date("j M Y"); ?></div>
	<div class="time"><?= date("h:i"); ?> gmt+2</div>
    </div>


    <div class="modal-content">

	<div id="notes" contentEditable="true" class="p22 t18 m14">
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Enim, tortor eget potenti varius. Aliquet.
	</div>	

	<div class="last_modif p22 t15 m12">Dernière modification : 12/04/2022 à 12h 52 min gmt+2</div>   

	<a class=" btn validate white up_case h85e lh25-29b" onclick="dispatch_event()" href="#close-modal" rel="modal:close"><?= __('enregistrer') ?></a>

    </div>   

</div>



<script>

    function dispatch_event()
    {
        var event = new Event('save');
        event.initEvent('save', true, true);
        dispatchEvent(event);
        //console.log("confirm");
    }




</script>

