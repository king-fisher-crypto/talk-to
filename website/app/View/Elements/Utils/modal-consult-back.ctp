<?php 
$log_user = $this->Session->read('Auth.User');

?>

<style>
	
/* POPUPS */
#modal-consult-back{
   width: calc(629px*var(--coef));
   height: calc(332px*var(--coef));
}

#modal-consult-back .confirmez{
     /*width: calc(500px*var(--coef));*/
}

#modal-consult-back .fa-chevron-down{
    width: calc(20px*var(--coef));
    height: auto;
    transform: rotate(-90deg);
    position: relative;
    top:calc(-5px*var(--coef));
}


#modal-consult-back .modal-content{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;
    
   width: 100%;
   height: calc(282px*var(--coef));
}
	
#modal-consult-back .btn.validate {
    font-variant: small-caps;
    width: calc(303px*var(--coef));
    height: calc(52px*var(--coef));
    gap: calc(10px*var(--coef));
    margin-bottom: calc(10px*var(--coef));

}

 </style>
       
<div class="modal  fade" id="modal-consult-back"  role="dialog" >
    
    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>
    
    <div class="modal-content">


	<div class="fw400 p28">
	    <?=(__("notification SMS du retour de", null, true))."<br/> <span class='fw600'>".$User['pseudo']."</span>"; ?> ?
	
	</div>	
	
	<div class="confirmez  fw500 p25">
	   <img class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg"> <?=(__("je veux recevoir le SMS au", null, true))." ".$log_user['phone_number']; ?> 
	</div>
	
	<a class="fw600 p18 blue2 underline">
	    <?=(__("Modifier mon numéro ", null, true)); ?> 
	</a>	
	
	
	<a class=" btn validate white up_case p24 " onclick="dispatch_event()" href="#close-modal" rel="modal:close"><?= __('valider') ?>  
</a>

    </div>   

</div>
