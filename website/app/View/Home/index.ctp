<?php
echo $this->Html->css('/theme/black_blue/css/home.css'."?a=".rand(),
	null, array('inline' => false));
echo $this->Html->css('/theme/black_blue/css/owl.carousel/owl.carousel.css',
	null, array('inline' => false));
echo $this->Html->css('/theme/black_blue/css/owl.carousel/owl.theme.black_blue.css'."?a=".rand(),
	null, array('inline' => false));
//echo $this->Html->script('/theme/black_blue/js/helpers.js');
echo $this->Html->script('/theme/black_blue/js/owl.carousel/owl.carousel.js'."?a=".rand());
//echo $this->Html->script('/theme/black_blue/js/jquery-1.10.2.js');
//echo $this->Html->script('/theme/black_blue/js/owl.carousel/owl.carousel.js', array('block' => 'script'));
?>
<div class="home-page">


    <section class="header">

	<div class="liviTalk blue2 lh50-75 fw700"><?= __("LiviTalk") ?><span class="white2"> <?= __(" : La première plateforme mondiale de mise en relation") ?></span> </div>

	<div class="pictos">
	    <a href ="#contact_liviMaster">
	    <img src="/theme/black_blue/img/medias/tel.svg"  />
	    <img src="/theme/black_blue/img/medias/chat.svg"  />
	    <img src="/theme/black_blue/img/medias/webcam.svg"  />
	    <img src="/theme/black_blue/img/medias/sms.svg"  />
	    <img src="/theme/black_blue/img/medias/email.svg"  />
	    <img src="/theme/black_blue/img/medias/plus.svg"  />
	    </a>

	</div>

	<div class="pictos mobile">
	    
<div class="img">  <img src="/theme/black_blue/img/medias/tel2.svg"  ><?= __("Tel") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/chat2.svg"  ><?= __("Chat") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/webcam2.svg"  ><?= __("Webcam") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/sms2.svg"  ><?= __("SMS") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/email2.svg"  ><?= __("Email") ?> </img></div>



	</div>

	
	
	
	<div class="presentation white2 lh30-45b "><?= __("Vous avez rêvé de pouvoir parler seul à seul avec des Influenceurs et Micro-influenceurs jusque là inaccessibles, ou d'échanger avec des professionnels et experts dans un domaine bien précis ? LiviTalk offre à tous, la possibilité de communiquer avec ses Followers, sa communauté, ses clients et bien plus encore !") ?> </div>

	<div class="presentation2 white2 lh30-45b up_case"><?= __("Achetez du temps de communication ET DISCUTEZ AVEC VOTRE LIVIMASTER !") ?> </div>
	
	
	<a class="  inscrire lh24-28i " href="" title="<?= __("s'inscrire") ?>"><?= __("s'inscrire") ?></a>

    </section>

    <section class="categories">

	<div class="txt1  lh29-43 fw500"><?= __("Le monde change et vous avez le besoin ou l'envie de communiquer instantanément <span class='mobile'><br/></span> 24/24 et 7/7 avec vos influenceurs Micro-influenceurs ou un professionnel ? <span class='blue2'>LiviTalk</span> <wbr> ré‑invente le monde de la mise en relation en vous offrant la première MarketPlace Mondiale de communication pour tous les métiers. Des milliers de professions sont représentées sur <span class='blue2'>LiviTalk</span>&nbsp;!") ?> </div>

	<?php
	$categories_ar = ["Influenceurs", "Professeur de langue", "Instagrameur", "Micros-influenceurs",
	    "Designer", "Professeur de Musique", "Youtubeurs", "Sportifs", "Nutritionniste",
	    "Crypto-influenceurs", "Coach Sportif", "Trader", "Psychologues", "Coach de vie",
	    "TikTokeur","Influenceurs", "Professeur de langue", "Instagrameur", "Micros-influenceurs",
	    "Designer", "Professeur de Musique", "Youtubeurs", "Sportifs", "Nutritionniste",
	    "Crypto-influenceurs", "Coach Sportif", "Trader", "Psychologues", "Coach de vie",
	    "TikTokeur"];
	?>

	<div class="owl-carousel carousel_categories owl-theme">
	    <?php
	    $i = 0;
	    foreach ($categories_ar as $categ)
		{
		$i++;
		if ($i == 1)
		    {
		    echo"<div class='categories owl_col'>";
		}

		echo"<div class='category black '>$categ</div>";

		if ($i == 3)
		    {
		    echo"</div>";
		    $i = 0;
		    }
		}
	    ?>


	</div>
    </section>

    <section id="contact_liviMaster" class="contact_liviMaster black white2">
	
	<div class="top">
	    
	    <img src="/theme/black_blue/img/fotos/home_contact.jpg">
	    
	    <div class="txt_pictos">
		
		<div class="txt1 lh35-52 fw600"><?= __("Contactez votre LiviMaster de 5 façons") ?> </div>
		
		<div class="pictos"><img src="/theme/black_blue/img/medias/tel.svg"> <img src="/theme/black_blue/img/medias/chat.svg"> <img src="/theme/black_blue/img/medias/webcam.svg"> <img src="/theme/black_blue/img/medias/sms.svg"> <img src="/theme/black_blue/img/medias/email.svg"> </div>
		
		
		
		<div class="pictos mobile">
	    
<div class="img">  <img src="/theme/black_blue/img/medias/tel2.svg"  ><?= __("Tel") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/chat2.svg"  ><?= __("Chat") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/webcam2.svg"  ><?= __("Webcam") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/sms2.svg"  ><?= __("SMS") ?></img></div>
<div class="img">  <img src="/theme/black_blue/img/medias/email2.svg"  ><?= __("Email") ?> </img></div>



	</div>
		
		
		
		<img class="mobil" src="/theme/black_blue/img/fotos/home_contact_mob.jpg">
		
		
		<div class="txt2 lh29-43b fw500"><?= __("Achetez du temps de communication et discutez instantanément avec votre LiviMaster par Téléphone, Chat, WebCam, SMS ou Email") ?> </div>
	    </div>
	    
	    
	    
	</div>
	<hr>
	
	<div class="bottom">
	    
	    <div class="txt_pictos">
		
		<div class="txt1 lh35-52b fw600"><?= __("Vos LiviMasters peuvent également proposer ") ?> </div>
		
		
		<div class="pictos_img">
		    
		<div class="pictos lh22-26c">
		    <div class="img_txt ">
		    <img src="/theme/black_blue/img/medias/video_clap.svg"> <br/>
		    <?= __("Formations Vidéos") ?>
		    </div>
		    <div class="img_txt">
		      <img src="/theme/black_blue/img/medias/masterclass.svg"> <br/>
		    <?= __("MasterClass") ?>
		    </div>
		    <div class="img_txt">
		     <img src="/theme/black_blue/img/medias/picture.svg"> <br/>
		    <?= __("Contenus à la demande") ?>
		    </div>
		    <div class="img_txt pdf">
		     <img src="/theme/black_blue/img/medias/pdf.svg">  <br/>
		    <?= __("Documents PDF") ?>
		    </div>
		    <div class="img_txt">
		     <img src="/theme/black_blue/img/medias/prives.png">  <br/>
		    <?= __("Contenus privés") ?>
		    </div>
		
		</div>	
		    		    
		    
		
		<img class="mobil" src="/theme/black_blue/img/fotos/home_contact2_mob.jpg">
		</div>
		
		<div class="txt2 lh29-43b fw500"><?= __("Vos LiviMasters peuvent également proposer différentes options, Vidéos Formation, MasterClass ou Visioconférences, Contenus à la demandes tels que Photos, Vidéos ou Documents et même des Contenus Privés.") ?> </div>
		
		
		
	    </div>
	    
	     <img src="/theme/black_blue/img/fotos/home_contact2.jpg">
  
	</div>              
	<a href="/mode" class="blue2 savoir underline lh35-52c fw600"><?= __("En savoir plus") ?></a>
</section>

    
    
<section class="trouver_liviMaster ">
	     	<div class="txt1 lh35-52c fw600"><?= __("TROUVEZ VOTRE LIVIMASTER ET S'IL N'EST PAS ENCORE SUR <span class='blue2'>LIVITALK</span>") ?><br/> <?= __("FAITES LUI CONNAITRE LE SITE POUR DISCUTER AVEC LUI") ?></div>
    

    
    <a class="btn spe2  p22spe chercher transparent bord2 lgrey2" title="<?= __("chercher") ?>"> <img class="img_btn2" src="/theme/black_blue/img/loupe_bleu.svg"><form> <input class="lh22-33 lgrey2" type="text" placeholder="<?= __("chercher") ?>"></form> </a>
    
    
    
    <div class="owl-carousel carousel_fiche owl-theme">
	    <?php
	    
	    $fiches_ar=["/theme/black_blue/img/fotos/agent_min1b.jpg","/theme/black_blue/img/fotos/agent_min2b.jpg", "/theme/black_blue/img/fotos/agent_min3b.jpg", "/theme/black_blue/img/fotos/agent_min4b.jpg", "/theme/black_blue/img/fotos/agent_min1b.jpg","/theme/black_blue/img/fotos/agent_min2b.jpg", "/theme/black_blue/img/fotos/agent_min3b.jpg", "/theme/black_blue/img/fotos/agent_min4b.jpg", "/theme/black_blue/img/fotos/agent_min1b.jpg","/theme/black_blue/img/fotos/agent_min2b.jpg", "/theme/black_blue/img/fotos/agent_min3b.jpg", "/theme/black_blue/img/fotos/agent_min4b.jpg"];
	    
	    foreach ($fiches_ar as $fiche)
		{
		?>
		<div class='fiche blue2'><img src='<?=$fiche ?>' />
		<a  href="" class="blue2 voir lh24-28b fw500"><?= __("Voir +") ?></a>
		</div>
	    <?php
		}
	    ?>


	</div>
	    
</section>

    
<section class="gagner_argent black white2">

    	<div class="txt1 lh35-52 fw600 up_case">
		<?= __("Particuliers ou professionnels, gagnez de l'argent en faisant connaître LiviTalk auprès de ceux que vous aimeriez contacter") ?> </div>
	
    <div class="">
    
	<div class="cadre cadre1 white">
	     <div class="num_div lh40-60 fw400">1</div>
	     <div class="title up_case lh27-40 fw600">
		 <?= __("GAGNEZ")." <span class='blue2 lh38-57 fw800'>10%</span><span class='mobile'><br/></span> ".__("DES REVENUS GÉNÉRÉS") ?>
	     </div>
	     <div class="txt lh22-33c fw400">
		 <?= __("En faisant connaître LiviTalk à de futurs LiviMasters qui s'inscriront grâce à vous, vous gagnez 10% de ce qu'ils gagnent.") ?>
	     </div>
	</div>
	
    <div class="cadre white cadre2">
	     <div class="num_div lh40-60 fw400">2</div>
	     <div class="title up_case lh27-40 fw600">
		 <?= __("GAGNEZ")." <span class='blue2 lh38-57 fw800'>50%</span><span class='mobile'><br/></span> ".__("DES REVENUS GÉNÉRÉS") ?>
	     </div>
	     <div class="txt lh22-33c fw400">
		 <?= __("En faisant connaître LiviTalk à de futurs Ambassadeurs, vous gagnez 50% de ce qu'ils gagnent lorsqu'ils recrutent de futurs LiviMasters.") ?>
	     </div>
	</div>
    
    
    <div class="cadre white cadre3">
	     <div class="num_div lh40-60 fw400">3</div>
	     <div class="title up_case lh27-40 fw600">
		 <?= __("GAGNEZ JUSQU'À")." <span class='blue2 lh38-57 fw800'>300$</span><span class='mobile'><br/></span> ".__("PAR VENTE GÉNÉRÉE") ?>
	     </div>
	     <div class="txt lh22-33c fw400">
		 <?= __("En faisant la promotion des vidéos formation d'autres LiviMasters auprès de votre communauté, de vos proches ou de vos clients.
") ?>
	     </div>
	</div>
    
    
    <div class="cadre white cadre4">
	     <div class="num_div lh40-60 fw400">4</div>
	     <div class="title up_case lh27-40 fw600">
		 <?= __("GAGNEZ JUSQU'À")." <span class='blue2 lh38-57 fw800'>90$</span> <span class='mobile'><br/></span>  ".__("PAR VENTE GÉNÉRÉE") ?>
	     </div>
	     <div class="txt lh22-33c fw400">
		 <?= __("En faisant la promotion des MasterClass/Visioconférence d'autres LiviMasters auprès de votre communauté, de vos proches ou de vos clients.
") ?>
	     </div>
	</div>
    </div>
    

    
    
    <a href="" class="voir_prgm blue2 lh24-28d fw500"><?= __("Voir les programmes d'affiliation en détail") ?> <img class="arrow" src="/theme/black_blue/img/arrow_right.svg"></a>
    
</section>
    
    
    <div class="ouvert lh35-52 fw500 up_case">
		<?= __("<span class='blue2'>LiviTalk</span> est ouvert à tous et révolutionne le monde de la mise en relation") ?> </div>
    
    
  
    <a class="  inscrire bis lh24-28i " href="" title="<?= __("s'inscrire") ?>"><?= __("s'inscrire") ?></a>
    
    <a href="#"><img class="chevron_bas" src="/theme/black_blue/img/menu/chevron.svg"></a>
    
    
</div> <!--fin homepage-->



<style>
   
    html { scroll-behavior: smooth; }
    
 .home-page {
    width:100%;
    text-align: center;
}
    
    .home-page section.header{
	background: url(theme/black_blue/img/Mask-group.jpg) no-repeat center center;
	background-size: cover;
	text-align: center;

    }
  
    /*  Tablet Small OR Mobile Extra Large  */
@media only screen   and (max-width : 767px)
{
    .home-page section.header{
	background: url(theme/black_blue/img/2.png) no-repeat center center;
	background-size: cover;
	text-align: center;

    }
}
    

</style>



<script>


window.onload = function () {

function home_start(){
    console.log("home_start");
    let items_car1 = 5;
    let items_car2 = 4;
    let margin_car1 = 1
    let margin_car2 = 10
    
    let nav1=true;
    let dots1=false;
   
    
    //let support_type = device_type();
    
      let w = getDocWidth()// - 40;
      let support_type
             
            if(w>1024)// PC
                support_type  = "pc" 
            else // MOBILE
            if(w<768) 
               support_type = "mobile"
            else // TABLETTE
               support_type = "tablet"
    
    
    switch(support_type){
	case "mobile":
	    items_car1 = 2;
	    items_car2 = 2;
	    nav1 = false;
	    dots1 = true;
	    margin_car1 = 4;
	    margin_car2 = 10
	    break;
	case "tablet":
	    items_car1 = 4;
	    items_car2 = 2;
	    
	    break;
    }

	$('.carousel_categories').owlCarousel('destroy');
     $('.carousel_categories').owlCarousel({
	    margin: margin_car1,
	    center: false,
            loop: true,
	    autoWidth: false,
	    navText:['<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">','<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">'],
	    mouseDrag:true,
	    touchDrag:true,
	    nav:nav1,
	    dots:dots1,
	    checkVisibility:false,
            items: items_car1
        })


	$('.carousel_fiche').owlCarousel('destroy');
            $('.carousel_fiche').owlCarousel({
            margin: margin_car2,
	    center: false,
            loop: true,
	    autoWidth: false,
	    navText:['<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">','<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">'],
	    mouseDrag:true,
	    touchDrag:true,
	    nav:false,
	    dots:true,
	    checkVisibility:false,
            items: items_car2
        })

    }

/*
    $('.owl-item').hover(function() {
	console.log("fiche_hover");
	let target = $(this).find(".voir");
	target.addClass("fiche_hover");
});
    $(' .owl-item').mouseout(function() {
	 console.log("fiche_mouseout");
	    let target = $(this).find(".voir");
	    target.removeClass("fiche_hover");
});
*/


    $(window).resize(function ()
    {
        console.log("resize");

        home_start()
    })

    home_start()

     };// fin  DOMContentLoaded
</script>