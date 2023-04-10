<div class="modal  fade" id="connection"  role="dialog" >
    <div class="modal-content">


	<!--———————————————————————————————————————
				LOGIN  SIDE ———————————————————————————————————————---->

	<div class="login screen ">

	    <div class="left">
		<div class="logo_blue"> </div>

		<div class="step step1">
		    <h1><?= __('Se Connecter') ?></h1>
		    <div class="je_suis"> 
			Je suis
		    </div>
		    <div class="fields"> 
			<a class="btn_modal client large grey"    title="<?= __('Client') ?>"><?= __('Client') ?></a> 

			<a class="btn_modal livimaster large grey"   title="<?= __('LiviMaster') ?>"><?= __('LiviMaster') ?></a> 
		    </div>

		    <div class="_bottom"> 
			<input class="btn_modal large blue up_case"  title="<?= __('Se Connecter') ?>" type="submit" value="<?= __('Se Connecter') ?>"></input> 
		    </div>


		</div>                

		<div class="step step2">

		    <div class="haut">
			<h1><?= __('Se Connecter en tant que') . " " . __('Client') ?> </h1>
			<img class="left_arrow" src = "/theme/black_blue/img/left_arrow.svg" alt="left arrow"/>
		    </div>  

		    <form action="/users/login" class="form-horizontal" id="UserLoginForm" method="post" accept-charset="utf-8">
			<?php 
			$this->set('statu', 'client');
			$this->set('mail', 'ademus@free.fr');
			$this->set('mdp', 'password');
			echo $this->element('Users/modal_login_form'); ?>
		    </form>
		    
		</div>


		<div class="step step3">

		    <div class="haut">
			<h1><?= __('Se Connecter en tant que') . " " . __('Livimaster') ?> </h1>
			<img class="left_arrow" src = "/theme/black_blue/img/left_arrow.svg" alt="left arrow"/>
		    </div> 
		    
		    <form action="/users/login" class="form-horizontal" id="AgentLoginForm" method="post" accept-charset="utf-8">

			<?php 
			$this->set('statu', 'agent');
			//$this->set('mail', 'degrefinance2@protonmail.com');
			$this->set('mail', 'thomastrass@protonmail.com');
			$this->set('mdp', 'password');
			echo $this->element('Users/modal_login_form'); ?>

		    </form>
		</div>


	    </div>

	    <div class="aside">
		<h1><?= __('Bonjour') ?> </h1>
		<DIV class="txt">
		    <?= __('Entrez vos données personnelles et commencez votre voyage avec nous.') ?>

		</DIV>

		<DIV  class=" txt2 _bottom">
		    <div class="text_s">
			<?= __('Vous n\'avez pas encore de compte ?') ?> 

		    </div>


		    <a class="btn_modal btn_register large  transparent up_case"   title="<?= __('S\'inscrire') ?>"><?= __('S\'inscrire') ?></a>   
		</DIV> 



	    </div>

	</div>

	<!--———————————————————————————————————————
				REGISTER  SIDE ———————————————————————————————————————---->
	<div class="register screen ">



	    <div class="aside">
		<div class="  "> </div>
		<h1><?= __('Bienvenue') ?> </h1>
		<DIV class="txt">
		    <?= __('Pour rester en contact avec nous, veuillez vous connecter avec vos informations personnelles') ?>

		</DIV>

		<DIV  class=" txt2 _bottom">
		    <div class="text_s">
			<?= __('Déjà inscrit ?') ?> 

		    </div>


		    <a class="btn_modal btn_modal_login large transparent up_case"   title="<?= __('Se Connecter') ?>"><?= __('Se Connecter') ?></a>   
		</DIV> 



	    </div>

	    <div class="left">

		<div class="step step1">
		    <h1><?= __('Créer un compte') ?> </h1>
		    <div class="je_suis"> 
			Je suis
		    </div>
		    <div class="fields"> 
			<a class="btn_modal client large grey"   title="<?= __('Client') ?>">Client</a> 

			<a class="btn_modal livimaster large grey"   title="<?= __('LiviMaster') ?>"><?= __('LiviMaster') ?></a> 
		    </div>

		    <div class="_bottom"> 
			<a class="btn_modal large blue up_case"   title="<?= __('S\'inscrire') ?>"><?= __('S\'inscrire') ?></a> 
		    </div>

		</div>      


		<div class="step step2">

		    <div class="haut">
			<h1><?= __('Créer un compte') . " " . __('Client') ?> </h1>
			<img class="left_arrow" src = "/theme/black_blue/img/left_arrow.svg" alt="left arrow"/>
		    </div>  


		    <div class="fields"> 
	    <form action="/users/subscribe" class="form-horizontal" id="AgentLoginForm" method="post" accept-charset="utf-8">
			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/courrier_grey.svg" alt="Email"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('E-mail') ?>" name="data[User][email_subscribe]">
			</div>

		    </div>

		    <div class="pictos text_s"> 
			<?= __('ou') ?> 
			<?= __('connectez-vous avec') ?>
			<br/>

			<img  src="/theme/black_blue/img/social_net/facebook.svg" alt="facebook"/>
			<img  src="/theme/black_blue/img/google.svg" alt="google"/>
			<img  src="/theme/black_blue/img/apple.svg" alt="apple"/>
		    </div>	 

		    <div class="_bottom"> 
			<a class="btn_modal continuer large blue up_case"    title="<?= __('Continuer') ?>"><?= __('Continuer') ?></a> 
		    </div>
		</div>


		<div class="step step2_2">
		    <div class="haut">
			<h1><?= __('Créer un compte') ?> </h1>
			<img class="left_arrow" src = "/theme/black_blue/img/left_arrow.svg" alt="left arrow"/>
		    </div> 
	

		    <?php echo $this->element('Users/modal_register_form'); ?>
</form>
		</div>
		
		
 <form action="/users/subscribe_agent" class="form-horizontal" id="AgentLoginForm" method="post" accept-charset="utf-8">		
		
		<div class="step step3">

		    <div class="haut">
			<h1><?= __('Créer un compte') . " " . __('LiviMaster') ?> </h1>
			<img class="left_arrow" src = "/theme/black_blue/img/left_arrow.svg" alt="left arrow"/>
		    </div>  


		    <div class="fields"> 

			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/courrier_grey.svg" alt="Email"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('E-mail') ?>" name="data[User][email_subscribe]">
			</div>

		    </div>

		    <div class="pictos text_s"> 
			<?= __('ou') ?> 
			<?= __('connectez-vous avec') ?>
			<br/>

			<img  src="/theme/black_blue/img/social_net/facebook.svg" alt="facebook"/>
			<img  src="/theme/black_blue/img/google.svg" alt="google"/>
			<img  src="/theme/black_blue/img/apple.svg" alt="apple"/>
		    </div>	 

		    <div class="_bottom"> 
			<a class="btn_modal continuer large blue up_case"    title="<?= __('Continuer') ?>"><?= __('Continuer') ?></a> 
		    </div>
		</div>
		
		
		<div class="step step3_2">
		    <div class="haut">
			<h1><?= __('Créer un compte') ?> </h1>
			<img class="left_arrow" src = "/theme/black_blue/img/left_arrow.svg" alt="left arrow"/>
		    </div> 


		    <?php echo $this->element('Users/modal_register_form'); ?>

		</div>
	 </form>	

	    </div> 



	</div>


    </div>   

</div>


<style>
    
    

/* SOFTPEOPLE */
/* POPUP  LOGIN & REGISTER */

#connection .modal-content{
    display: flex;
    width:570px;
    align-items: stretch;
    height: 400px;
    border-radius: calc(15px*var(--coef));
    /*   
        line-height: 2rem;*/
}

#connection.modal  .logo_blue{
    position: absolute;
    left:21px;
    top:10px;
    width:calc(var(--header-btns_height)*82/22);
    /*height: var(--header-btns_height);*/
}

#connection.modal .left_arrow{
    cursor: pointer;
}

#connection.modal .left{
    flex: 67%;
    /*height: 100%;*/
    position: relative;
    text-align: center;
    padding: 1.5em;
    background-color: var(--main-bg-color);
    overflow-y: auto;
    
      -ms-overflow-style: none; /* for Internet Explorer, Edge */
    scrollbar-width: none; /* for Firefox */
}

#connection.modal .left::-webkit-scrollbar{
    display: none; /* for Chrome, Safari, and Opera */ 
}


#connection.modal .login .left{
    border-bottom-left-radius: var(--border-radius);
    border-top-left-radius:var(--border-radius);
}


#connection.modal .login .left{
    border-bottom-left-radius: var(--border-radius);
    border-top-left-radius:var(--border-radius);
}
#connection.modal .register .left{
 border-bottom-right-radius: var(--border-radius);
    border-top-right-radius: var(--border-radius); 
}

#connection.modal .left h1{
    font-size: 30px;
    font-weight: 500;
    color: var(--blue);
}

/*.modal .left .step2 h1{
    font-size: 30px;
    font-weight: 500;
    color: var(--blue);
}*/

#connection.modal .left .je_suis{
    margin: 2rem;
    font-weight: 400;
}

#connection.modal .left .fields > .btn_modal{
    margin:0.6rem;
}


#connection.modal .aside{
    position: relative;
    color: var(--second-color);
    text-align: center;
    background-color: var(--second-bg-color);
    flex: 33%;
    padding: 1.3em;
    padding-top: 3em;
    /*    height: 100%;*/

}


#connection.modal .login .aside{
    border-bottom-left-radius: var(--border-radius);
    border-top-left-radius: var(--border-radius); 
}

#connection.modal .register .aside{
    border-bottom-right-radius: var(--border-radius);
    border-top-right-radius: var(--border-radius); 
}

#connection.modal .aside h1{
    font-weight: 600;
    /*color:var(--blue);*/
}


#connection.modal .aside .txt{
    line-height: 22px;
    font-weight: 100;
    font-size: 12px;
    margin-bottom: 14px ;
}

#connection.modal .aside .txt2{
    height:25%;
}

#connection.modal .aside .text_s{
    margin-bottom: 0.5rem;
    padding: 1.5em;
    padding-bottom: 0;
    font-size: 10px;
}

#connection.modal  .screen{
    display: flex;
    align-items: stretch;
}

#connection.modal .step{
}

#connection.modal .step1{z-index: 1}
#connection.modal .step2{z-index: 2}
#connection.modal .step3{z-index: 3}
#connection.modal .step4{z-index: 4}


#connection.modal .step .fields{
    display: inline-block;
}


#connection.modal .haut{
    position: relative;
}
#connection.modal .haut .left_arrow{
    position: absolute;
    left: 0;
    bottom: 0.5rem; 
    width:1.5rem;
    height:auto;
}

#connection.modal  .login.screen{
/*z-index: 1;*/
}
#connection.modal .register.screen{
   /*z-index: 2;*/ 
}

#connection .div-input-shadow
{
  display: -ms-flexbox; /* IE10 */
  display: flex;
  align-items: center;
  /*width: 100%;*/
  background: #F5F5F5;
    /* drop */
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.08);
  border-radius: 5px; 
}


#connection .div-input-shadow .picto{
    width:17px;
    height:17px;
    box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.08);
    border-radius: 5px;
    padding: 7px;
    background-color: #FBFBFB;
}



#connection .div-input-shadow .input-shadow{
    color: #878787;
    border: 0;
    background: 0;
    outline: none;
    transition: color 0.1s ease;
    min-width: 150px;
     
    padding-left: 15px;
}


#connection.modal .fields .div-input-shadow{
    margin-bottom: 15px;
}


#connection.modal .left .pictos{
  color: #878787;
 
}
#connection.modal .left .pictos img{
    display: inline-block;
    margin: 10px;
    width:30px;
    height:30px;
}



#connection.modal .register .left .step2 .pictos {
    margin-top: 2rem;
}

#connection.modal .register{
   /*display: none;*/
}


#connection.modal .screen, .modal .step{
    display: none;
} 
#connection.modal .screen.login{
    display: flex;
} 
#connection.modal .screen.login .step1{
    display: inline-block;
} 


#connection.modal .login{
   display: none;
}
#connection.modal .step1{
    display: none;
}


/* MENU DEROULANT FLAG DS MODAL */ 
#connection.modal  .submenuItems{
    position:absolute;
}

#connection.modal  .fa-chevron-down{
   
}

#connection.modal .div-input-shadow{
    max-height: 31px;
}

#connection .checkbox_div{
    color: var(--light-grey); 
}

#connection.modal .div-input-shadow .picto{
    width: 27px;
}



#connection.modal  .dropdownlink img{
    left: 0;
    top: initial;
}
#connection.modal  .dropdownlink .fa-chevron-down{
    bottom: -5px;
    right: 0 !important;
    left: initial;
    top: initial;
    width: 15px;
}

#connection.modal .submenuItems{
      bottom: -75px;
      z-index: 10;
}

#connection.modal .submenuItems a{
    padding: 5px 5px 5px 15px;
}

#connection.modal  .dropdownlink{
    position: relative;
}

#connection.modal .fields .div-input-shadow{
    position: relative;
}

#connection.modal .ul.accordion-menu, .modal .ul.accordion-menu>li {
     position: relative;
}



/* BOUTONS SPECIFIQUE A LA MODALE */

#connection.modal .btn_modal{
    border-radius: 15px;
    padding: 5px 10px 5px 10px ;
    text-decoration: none;
    font-weight: 500;

    white-space: nowrap;

    text-align: center;
    cursor: pointer;
    
    display: inline-block;
    
    box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.08);
    transition: color 300ms ease-out 100ms;
    transition: background-color 300ms ease-out 100ms;

}

#connection.modal .btn_modal.large{
/* line-height: 30px;*/
  min-width: 100px;
  padding: 10px 20px 10px 20px ;
}

#connection.modal .btn_modal.small{
  padding: 8px 55px 9px 29px;
  font-size: 24px;
  line-height: 24px;
  height: 50px;
}

#connection.modal .btn_modal img{
    vertical-align: inherit;
    height: 20px;
    margin-right: 10px;

}


#connection.modal .btn_modal.transparent{
   border: 2px solid var(--blue); 
   background-color: none;

}









/* ***************************************** */
/* MEDIA QUERIES */
/* Smartphones (portrait and landscape) ----------- */
/* iPhone 4 ----------- */
@media
only screen and (-webkit-min-device-pixel-ratio : 1.5),
only screen and (min-device-pixel-ratio : 1.5) {
    /* Styles */
}

/*  Tablet  */
@media only screen and (max-width : 1100px) {

    #connection.modal .left .je_suis {
        margin: 1.5rem;
    }

    #connection.modal .aside {
        padding-top: 1.3em;
    }

    #connection.modal .aside .txt2{
        height: auto;
    }

}

/*  Tablet Small OR Mobile Extra Large  */
@media only screen   and (max-width : 767px)
{

}

@media only screen   and (max-width : 767px) and (orientation: landscape)
{
    #connection .modal-content{
        width:100%;
    }
}

/* Mobile Medium PORTRAIT */
@media only screen  and (max-width : 570px ){

    #connection .modal-content{
        width:100%;
    }

    #connection.modal .left .fields .btn_modal{
        min-width:70px;
    }

    #connection.modal .aside .btn_modal{
        font-size: 10px;
    }

     #connection.modal .left .step2 h1 {
        font-size: 20px;
    }
    
     #connection.modal  .div-input-shadow .input-shadow{
        max-width: 100px;
        min-width: initial;
        }
        
        #connection.modal .left .pictos img{
            margin-left: 3px;
            margin-right: 3px;
        }
        
        #connection.modal .login .haut .left_arrow {
            /*left: -10px;*/
            bottom: -10px;
        }
   
}




/* Mobile Medium & Small ( < 400px ) */
/* Mobile Medium LANDSCAPE */
@media only screen  and (max-height : 400px ) {
    
    .blocker{
         /*align-items: initial;*/
    }
    
    
    #connection .modal-content{
        height: 280px;
    }
    
    #connection.modal .left .step2 h1 {
        font-size: 18px;
    }
    
    #connection.modal .left  ._bottom{
        bottom: 5px !important;
    }
    
    #connection.modal .haut .left_arrow{
        bottom: -20px;
    }
    
    
}

/* Mobile Medium & Small ( < 400px ) */
/* Mobile Medium PORTRAIT */
@media only screen  and (max-width : 400px ) {
    
    #connection.modal .left,  .modal .aside  {
        padding: 0.5rem;
    }
}



/* Mobile Medium PORTRAIT & LANDSCAPE */
@media only screen  and (max-width : 570px ), only screen  and (max-height : 400px ) {

    #connection.modal .left h1 {
        font-size: 25px;
    }

   
    
    #connection.modal .left .je_suis {
        margin: 1rem;
    }



    #connection.modal .aside h1 {
        font-weight: 500;
        font-size: 27px;

    }


    #connection.modal .left .logo_blue {
        left: 13px;
        width: calc(var(--header-btns_height)*82/17);
    }




    #connection.modal .aside .txt {
        line-height: 15px;
    }


}


/* iPads (portrait and landscape) ----------- */
@media only screen and (max-width : 768px)  {


}
/* iPads (landscape) ----------- */
@media only screen and (min-width : 768px) and (max-width : 1024px) and (orientation : landscape),
/* iPads (portrait) ----------- */
only screen and (min-width : 768px) and (max-width : 1024px) and (orientation : portrait)
{

}
/* iPads (portrait) ----------- */
@media only screen and (min-width : 768px) and (max-width : 1024px) and (orientation : portrait) {
    /* Styles */
}



/* Large screens ----------- */
@media only screen and (min-width : 1824px) {
    /* Styles */
}


    
</style>   