<style>
    
	
/* POPUPS */
#modal-consult-pdf{
   width: calc(629px*var(--coef));
   height: calc(494px*var(--coef));
}

#modal-consult-pdf .modal-content{
   width: 100%;
   height: calc(445px*var(--coef));
   
   
   
}



#modal-consult-pdf .confirmez{
     width: calc(500px*var(--coef));
}

#modal-consult-pdf .montant {
    width: calc(173px*var(--coef));
    height: calc(60px*var(--coef));
 
}
	
#modal-consult-pdf .btn.validate {
    font-variant: small-caps;
    width: calc(303px*var(--coef));
    height: calc(52px*var(--coef));
   
}

#modal-consult-pdf .btn.validate .tel2{
    width: calc(23px*var(--coef));
    height: auto;
}
 

#modal-consult-pdf .underline{
     margin-bottom: calc(20px*var(--coef));
}


/* mobile ----------- */
@media only screen   and (max-width : 767px)
{

	#modal-consult-pdf{
	width: calc(347px*var(--coef));
	height: calc(414px*var(--coef));
	}

	#modal-consult-pdf .modal-content{
	width: 100%;
	height: calc(445px*var(--coef));
	}

    #modal-consult-pdf .confirmez{
	width: calc(307px*var(--coef));
    }

    #modal-consult-pdf .montant {
	width: calc(150px*var(--coef));
	height: calc(52px*var(--coef));

    }

    #modal-consult-pdf .btn.validate {
	width: calc(250px*var(--coef));
	height: calc(43px*var(--coef));
	padding:0;

    }



    #modal-consult-pdf .underline{
	margin-bottom: calc(20px*var(--coef));
    }

    
}



</style>
       
<div class="modal  fade" id="modal-consult-pdf"  role="dialog" >
    
    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>
    
    <div class="modal-content">

 
	<div class="fw400 p30 m18">
	    PDF/PPT <?=(__("document", null, true))."<br/> <span class='fw600'>".$User['pseudo']."</span>"; ?> ?
	
	</div>	
	
	<div class="confirmez  fw400 p28 m16">
	   <?=(__("PDF/PPT document formation sera disponible immédiatement après la confirmation de l'achat")) ; ?> ?
	</div>
	
	
	<div class="montant btn_like blue p25 m20">
	    9,99$
	</div>
	
	<div class=" btn validate white up_case p24 m18" onclick="dispatch_event()" href="#close-modal" rel="modal:close"><?= __('procéder au paiement') ?>  
</div>

	<a class="underline blue2 fw600 p18 m16" href="#close" rel="modal:close">
	    <?=(__("continuer sur", null, true))." ".Configure::read('Site.name'); ; ?> 
	</a>
	
    </div>   

</div>
