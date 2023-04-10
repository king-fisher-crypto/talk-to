<style>

    .modal{
	/*display:block !important;*/
    }
    
#modal-confirmation.modal  
{
border-radius:   calc(15px*var(--coef));   
}

#modal-confirmation.modal   .modal-content{
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-around;

    background: var(--main-bg-color);
   
    width: calc(400px*var(--coef));;
    /*height: calc(250px*var(--coef));*/
    border-bottom-left-radius: calc(15px*var(--coef));
    border-bottom-right-radius: calc(15px*var(--coef));
    
}  
    
#modal-confirmation.modal   .header{
    /*position:absolute;*/
    background: var(--second-bg-color);
    height:  calc(50px*var(--coef));
    width:100%;
    border-top-left-radius: calc(15px*var(--coef));
    border-top-right-radius: calc(15px*var(--coef));
}

#modal-confirmation.modal   .logo{
    position: absolute;
    width:calc(72px*var(--coef));
    height:calc(13px*var(--coef));
    top:calc(19px*var(--coef));
    left:calc(25px*var(--coef));
}

#modal-confirmation.modal   a.close-modal{	
     width: calc(22px*var(--coef));
    height: calc(22px*var(--coef));
    top:calc(14px*var(--coef));
    right:calc(25px*var(--coef));
    }

   #modal-confirmation.modal   .message{
	margin:  calc(40px*var(--coef)) auto  calc(40px*var(--coef)) auto;
	color:var(--main-color);
    }
    
  #modal-refusal.modal  .btn.validate{
	padding: 0 calc(80px*var(--coef)) 0 calc(78px*var(--coef))
    }

    
 #modal-confirmation.modal   .modal-content #message{
    padding: 0 5% 0 5%;
}
   
    

/* tablets ----------- */
@media only screen and (max-width : 1024px) {
  
    /*
    #modal-confirmation .logo{
    width: calc(100px*var(--coef));
    height:calc(14px*var(--coef));
    top:calc(18px*var(--coef));
    left:calc(14px*var(--coef));
    }
*/
}


@media only screen   and (max-width : 767px)
{
    
    #modal-confirmation.modal   .modal-content{
    width: calc(300px*var(--coef));;
    height: calc(150px*var(--coef));  
    }   
    /*
    #modal-confirmation .header{
    height:  calc(43px*var(--coef));
    }
    
    #modal-confirmation .logo{
    width: calc(70px*var(--coef));
    height: calc(11px*var(--coef));
    top:calc(18px*var(--coef));
    left:calc(14px*var(--coef));
    }
     
    
 #modal-confirmation .close-modal{	
     width: calc(20px*var(--coef));
    height: calc(20px*var(--coef));
    top:calc(14px*var(--coef));
    right:calc(25px*var(--coef));
    }
    */
}

</style>
    
<div class="modal  fade <?=$class ?? '';?>" id="<?=$id ?? 'modal-refusal';?>"  role="dialog" >
    
    <div class="header">
	<div class="logo"></div>
	<a href="#close-modal" rel="modal:close" class="close-modal ">Close</a>
    </div>
    
    <div class="modal-content">


	<div id="message" class=" lh30-35b"><?=$message;?></div>	
	
	
	<a class=" btn validate white up_case h85e lh25-29b" onclick="dispatch_event()" href="#close-modal" rel="modal:close" ><?= $valider ?? __('valider') ?></a>

    </div>   

</div>



<script>
    
    function dispatch_event(){
	var event = new Event('confirm');
	event.initEvent('confirm', true, true);
	dispatchEvent(event);
	//console.log("confirm");
    }
    
    
    
    
</script>

