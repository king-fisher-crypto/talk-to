<style>

    
#year-picker.modal  
{
border-radius:   calc(15px*var(--coef));   
}

 #year-picker .modal-content{
    background: var(--main-bg-color);
    align-items: center;
    justify-content: space-around;
    width: calc(400px*var(--coef));;
    height: calc(250px*var(--coef));
    border-bottom-left-radius: calc(15px*var(--coef));
    border-bottom-right-radius: calc(15px*var(--coef));
    
}  
    
#year-picker .header{
    /*position:absolute;*/
    background: var(--second-bg-color);
    height:  calc(80px*var(--coef));
    width:100%;
    border-top-left-radius: calc(15px*var(--coef));
    border-top-right-radius: calc(15px*var(--coef));
}

#year-picker .logo{
    position: absolute;
    width:calc(150px*var(--coef));
    height:calc(22px*var(--coef));
    top:calc(30px*var(--coef));
    left:calc(26px*var(--coef));
}



#year-picker .btn.year{
    padding: 0 calc(45px*var(--coef)) 0 calc(43px*var(--coef));
    border-radius:5px;
}


#year-picker .prev, 
#year-picker .next{
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor:pointer;
    width: calc(90px*var(--coef));
    height:  calc(90px*var(--coef));
    position: relative;
   
}

#year-picker .fa-chevron-down{
    width: calc(25px*var(--coef));
    height:  calc(15px*var(--coef));
    position:absolute;
    z-index: 5;
}


#year-picker .prev .fa-chevron-down{
  
   transform: rotate(90deg);
}

#year-picker .next .fa-chevron-down{
  
   transform: rotate(-90deg);
}


#year-picker .disk_over{
position:absolute;

width: 100%;
height: 100%;
border-radius: 50%;
opacity: 0;
transition: opacity 0.25s cubic-bezier(0, 0, 0.2, 1), background-color 0.25s linear;
will-change: opacity, background-color;

background: var(--grey);
}

#year-picker .disk_over:hover {
    opacity: 1;
}
    


/* tablets ----------- */
@media only screen and (max-width : 1024px) {
    
    #year-picker .header{
    height:  calc(50px*var(--coef));
    }
    
    #year-picker .logo{
    width: calc(100px*var(--coef));
    height:calc(14px*var(--coef));
    top:calc(18px*var(--coef));
    left:calc(14px*var(--coef));
    }

}


@media only screen   and (max-width : 767px)
{
    #year-picker .modal-content{
    width: calc(300px*var(--coef));;
    height: calc(150px*var(--coef));  
    }  
    
    
    
    #year-picker .header{
    height:  calc(43px*var(--coef));
    }
    
    #year-picker .logo{
    width: calc(70px*var(--coef));
    height: calc(11px*var(--coef));
    top:calc(18px*var(--coef));
    left:calc(14px*var(--coef));
    }
    
    #year-picker .fa-chevron-down{
    width: calc(20px*var(--coef));
    height:  calc(10px*var(--coef));
    position:absolute;
    z-index: 5;
    }

}

</style>
    
<div class="modal  fade" id="year-picker" tabindex="-1" role="dialog" >
    
    <div class="header"><div class="logo"></div></div>
    
    <div class="modal-content">

	
	
	<div class="prev"><img class="fa-chevron-down " src="/theme/black_blue/img/menu/chevron.svg">
	<span class="disk_over"></span>
	</div>
	
	<div>
	    <a class="btn year blue h85d lh40-46" href="" title="<?= __('year') ?>"><?=date("Y");?></a>    
    </div>
	
	<div class="next"><img class="fa-chevron-down " src="/theme/black_blue/img/menu/chevron.svg">
	<span class="disk_over"></span>
	</div>

    </div>   

</div>


<script>

document.addEventListener("DOMContentLoaded", function() { 
    
    
 $(".modal-content .prev").click(function ()
    {
      let year = $(".modal-content .btn.year").text();
      year=parseInt(year)
      year--
      $(".modal-content .btn.year").text(year);
    });
    
    $(".modal-content .next").click(function ()
    {
      let year = $(".modal-content .btn.year").text();
      year=parseInt(year)
      year++
      $(".modal-content .btn.year").text(year);
    });     
    
});
</script>