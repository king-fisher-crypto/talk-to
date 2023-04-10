<style>
    
	
/* POPUPS */
#modal-consult-tel{
   width: calc(629px*var(--coef));
   height: calc(315px*var(--coef));
}

#modal-consult-tel .confirmez{
     width: calc(500px*var(--coef));
}

#modal-consult-tel .modal-content{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    
   width: 100%;
   height: calc(264px*var(--coef));
}
	
#modal-consult-tel .btn.validate {
    font-variant: small-caps;
    width: calc(303px*var(--coef));
    height: calc(52px*var(--coef));
    gap: calc(10px*var(--coef));
    margin-bottom: calc(10px*var(--coef));

}
#modal-consult-tel .btn.validate .tel2{
    width: calc(23px*var(--coef));
    height: auto;
}

 </style>
       
<div class="modal  fade" id="modal-consult-tel"  role="dialog" >
    
    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>
    
    <div class="modal-content">


	<div class="fw400 p28">
	    <?=(__("démarrez ma consultation avec ", null, true))."<br/> <span class='fw600'>".$User['pseudo']."</span>"; ?> ?
	
	</div>	
	
	<div class="confirmez blue2 fw500 p25">
	   <?=(__("confirmez-vous cette consultation par téléphone avec", null, true))." ".$User['pseudo']; ?> ?
	</div>
	
	<a class=" btn validate white up_case p24 " onclick="dispatch_event()" href="#close-modal" rel="modal:close"><?= __('valider') ?>  <img class="tel2" src="/theme/black_blue/img/medias/tel2.svg">
</a>

    </div>   

</div>
