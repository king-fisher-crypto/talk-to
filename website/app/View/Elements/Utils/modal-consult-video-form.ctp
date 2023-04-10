<style>
    
	
/* POPUPS */
#modal-consult-video-form {
   width: calc(629px*var(--coef));
   height: calc(494px*var(--coef));
}

#modal-consult-video-form .modal-content{
   width: 100%;
   height: calc(445px*var(--coef));
   display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    text-align: center;

}



#modal-consult-video-form .confirmez{
     width: calc(500px*var(--coef));
}

#modal-consult-video-form .montant {
    width: calc(173px*var(--coef));
    height: calc(60px*var(--coef));
 
}
	
#modal-consult-video-form .btn.validate {
    font-variant: small-caps;
    width: calc(303px*var(--coef));
    height: calc(52px*var(--coef));
   
}

#modal-consult-video-form .btn.validate .tel2{
    width: calc(23px*var(--coef));
    height: auto;
}
 

#modal-consult-video-form .underline{
     margin-bottom: calc(20px*var(--coef));
}

#modal-consult-video-form  .form_video{
    text-transform: capitalize;
}



/* mobile ----------- */
@media only screen   and (max-width : 767px)
{

	#modal-consult-video-form {
	width: calc(347px*var(--coef));
	height: calc(414px*var(--coef));
	}

	#modal-consult-video-form  .modal-content{
	width: 100%;
	height: calc(445px*var(--coef));
	}

    #modal-consult-video-form  .confirmez{
	width: calc(307px*var(--coef));
    }

    #modal-consult-video-form  .montant {
	width: calc(150px*var(--coef));
	height: calc(52px*var(--coef));

    }

    #modal-consult-video-form  .btn.validate {
	width: calc(250px*var(--coef));
	height: calc(43px*var(--coef));
	padding:0;

    }



    #modal-consult-video-form  .underline{
	margin-bottom: calc(20px*var(--coef));
    }


</style>
       
<div class="modal  fade" id="modal-consult-video-form"  role="dialog" >
    
    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>
    
    <div class="modal-content">


	<div class="fw400 p30 m18 form_video">
	   <?=(__("formation vidéo", null, true))."<br/> <span class='fw600'>".$User['pseudo']."</span>"; ?> ?
	
	</div>	
	
	<div class="confirmez  fw400 p28 m16">
	   <?=(__("la formation vidéo sera disponible immédiatement après la confirmation de l'achat", null, true)) ; ?> ?
	</div>
	
	
	<div class="montant btn_like blue p25 m20">
	    9,99$
	</div>
	
	<div class=" btn validate white  p24 m18" onclick="dispatch_event()" href="#close-modal" rel="modal:close"><?= __('procéder au paiement') ?>  
</div>

	<a class="underline blue2 fw600 p18 m16" href="#close" rel="modal:close">
	    <?=(__("continuer sur", null, true))." ".Configure::read('Site.name'); ; ?> 
	</a>
	
    </div>   

</div>
