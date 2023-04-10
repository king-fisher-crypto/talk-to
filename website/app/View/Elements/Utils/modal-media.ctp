<style>
    #modal-media.modal
    {
	border-radius:  calc(30px*var(--coef)) ;
	color: var(--light-grey);
	padding: calc(35px*var(--coef)) calc(166px*var(--coef)) calc(35px*var(--coef)) calc(80px*var(--coef));
	background: var(--main-bg-color);
	width: calc(1036px*var(--coef));
	height: calc(468px*var(--coef));
    }

    
    #modal-media  .vignette_btns_txt{
	display: inline-flex;
	align-items: center;
	justify-content: space-between;
	gap:calc(40px*var(--coef));
	width:100%;
	height: 100%;
    }
    
   #modal-media .txts{
	text-align: left;
    }

    #modal-media .description{
	line-height: calc(23px*var(--coef));
	margin-top: calc(25px*var(--coef));
    }


    #modal-media .close-modal{
	width: calc(22px*var(--coef));
	height: calc(22px*var(--coef));
	top:calc(48px*var(--coef));
	right:calc(49px*var(--coef));
    }



    /* tablets ----------- */
    @media only screen and (max-width : 1024px) {
	
	#modal-media.modal
	{
	    padding: calc(25px*var(--coef));
	    width: calc(724px*var(--coef));
	    height: calc(335px*var(--coef));
	}
	
	#modal-media.modal img.vignette{
	    border-radius:  calc(25px*var(--coef)) ;
	}
	
	#modal-media .description{
	    line-height: calc(21px*var(--coef));
	}
	
	
	
    }


    @media only screen   and (max-width : 767px)
    {
	
	#modal-media.modal
	{
	    padding: calc(20px*var(--coef));
	    width: calc(347px*var(--coef));
	    height: auto;
	}
	#modal-media  .vignette_btns_txt{
	  gap:calc(15px*var(--coef));
	  flex-direction: column;
	}

	#modal-media .description{
	    line-height: calc(19px*var(--coef));
	    margin-top:calc(10px*var(--coef));
	    /*margin-bottom: calc(20px*var(--coef));*/
	}
	
	#modal-media .close-modal{
	top:calc(29px*var(--coef));
	right:calc(26px*var(--coef));
	}

    }

</style>

<div class="modal  fade" id="modal-media"  role="dialog" >
    <a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    <div class="vignette_btns_txt">

	<div class="vignette_btns">
	    <img class="vignette" src="/img/effacer/Rectangle-4314.jpg">
	</div>   
	<div class="txts">
	    <div class="title p28 t20 m18 fw500">Titre du photo / vidéo </div>
	    <div class="description p20 t18 m16 fw400">
		Lorem ipsum dolor sit amet consectetur. Enim duis aliquam hac fames non velit faucibus. Fermentum semper amet fermentum dictum lorem cras parturient. In sed tellus dignissim vulputate egestas. Lorem pellentesque magna sed feugiat elementum. Viverra ornare et sed nulla porttitor. Dolor amet viverra mattis id eget.
	    </div>
	</div>




    </div>

</div>



<script>





</script>

