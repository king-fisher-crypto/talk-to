<?php
//var_dump($User);

echo $this->Html->css('/theme/black_blue/css/home.css' . "?a=" . rand(), null,
	array('inline' => false));
echo $this->Html->css('/theme/black_blue/css/owl.carousel/owl.carousel.css',
	null, array('inline' => false));
echo $this->Html->css('/theme/black_blue/css/owl.carousel/owl.theme.black_blue.css',
	null, array('inline' => false));

echo $this->Html->css('/theme/black_blue/css/swiper-bundle.min.css', null,
	array('inline' => false));
echo $this->Html->css('/theme/black_blue/css/hayen.css'.'?a='.rand(), null,
	array('inline' => false));
echo $this->Html->css('https://fonts.googleapis.com/css?family=Montserrat:500,600', null,
	array('inline' => false));

//echo $this->Html->script('/theme/black_blue/js/helpers.js');
echo $this->Html->script('/theme/black_blue/js/owl.carousel/owl.carousel.js');
echo $this->Html->script('/theme/black_blue/js/swiper-bundle.min.js');
//echo $this->Html->script('/theme/black_blue/js/jquery-1.10.2.js');
//echo $this->Html->script('/theme/black_blue/js/owl.carousel/owl.carousel.js', array('block' => 'script'));

echo $this->element('Utils/modal-confirmation');

echo $this->element('Utils/modal-consult-tel');
echo $this->element('Utils/modal-consult-pdf');
echo $this->element('Utils/modal-consult-chat');
echo $this->element('Utils/modal-consult-sms');

echo $this->element('Utils/modal-consult-back');
echo $this->element('Utils/modal-consult-year-picker');
echo $this->element('Utils/modal-consult-agenda');
echo $this->element('Utils/modal-consult-video-form');
echo $this->element('Utils/modal-consult-masterclass');
echo $this->element('Utils/modal-consult-rates');
echo $this->element('Utils/modal-payment');


// url vers l image de User
$avatar = $this->FrontBlock->getAvatar($User, false);

//var_dump($user);
$logged = empty($user) ? false : true;
$credits = 1;

$is_favorite;




?>
<?php ?>

<div class="display-page  page">

    <section class="header">

	<div class="div_pictos">
	    <div>
		<img src="/theme/black_blue/img/social_net/icon_social_media_profile_deafault_cadre.svg" class="social_media" />
	    </div>
<?php
// AJOUT FAVORI SE FAISAIT EN AJAX PAR accounts/add_favorite/$User['id']
// ds display.ctp, chercher la classe icon-expert-favoris	 
?>
	    <div>
		<img src="/theme/black_blue/img/heart_blue.svg" class="heart_blue" />
	    </div>

	    <div>
		<img src="/theme/black_blue/img/world.svg" class="world" />
		<div class="agent_langs">
		    <table>
<?php
foreach ($langsAgent as $code => $name) :
    if ($code != 'frc' && $code != 'frs' && $code != 'frb' && $code != 'frl')
    //   echo '<div><div class="lang p20 fw300" title="'.$name.'">'.$name.'</div><div class="level p20 fw400 blue2">'.__('parlé couramment').'</div></div>';
	    echo "<tr><td class='lang p20 fw300 lgrey2'>" . $name . "</td><td class='level p20 fw400 blue2'>" . __('parlé couramment') . "</td></tr>";
endforeach;
?>
		    </table>									
		</div>
	    </div>
	</div>
    </section>

    <div class="sidebar ">

	<div class="agent_img">
	    <img src="/theme/black_blue/img/profile_ok.svg" class="profile_ok" />
	</div>

	<div class="padding">
	    
	    <div class="left">

	    <div class="div_abonnes">

		<div class="nbre_abonnes p23 t14"> <img src="/theme/black_blue/img/heart_red.svg" class="heart fw500" />1,5 <?= __("mln") ?></div>

	    </div>

	    <div class="pseudo p75 t24 fw400">
<?= $User['pseudo']; ?>   
	    </div>


	    <div class="categ_pro p30 t22 fw300">
		Photographe
	    </div>
	    <div class="categ_form p25 t20 fw300">
		Formation photo   
	    </div>
		
 <div class="div_a_propos">
	    <div class="a_propos p30 t22 fw600 ucfirst">
<?= __("à propos de moi") ?> 
	    </div>
	    <div class="a_propos2 p24 t18 fw400">
<?php // echo $this->Text->truncate("Lorem ipsum dolor sit amet, consectetur adipiscing elit. Velit, vulputate placerat elit habitant ", 80); ?> 

		<?php
		if (empty($UserPresentLang['texte']))
			echo __('L\'expert n\'a pas renseigné de présentation pour cette langue.');
		else echo nl2br($UserPresentLang['texte']);
		?>


	    </div>
	    <div class="lire_suite p24 t16 fw500 blue2">
		<?= __("Lire la suite", null, true) ?> <img class="arrow_right " src="/theme/black_blue/img/arrow_right.svg" >
	    </div>
</div>
		
	    <div class="social_net">
		<img class="" src="/theme/black_blue/img/social_net/facebook.svg" >
		<img class="" src="/theme/black_blue/img/social_net/instagram.svg" >
		<img class="" src="/theme/black_blue/img/social_net/Linkedin.svg" >
		<img class="" src="/theme/black_blue/img/social_net/Youtube.svg" >
		<img class="" src="/theme/black_blue/img/social_net/twitter.svg" >
		<img class="" src="/theme/black_blue/img/social_net/Pinterest.svg" ><br/>
		<img class="" src="/theme/black_blue/img/social_net/Twitch.svg" >
		<img class="" src="/theme/black_blue/img/social_net/tiktok.svg" >
		<img class="" src="/theme/black_blue/img/social_net/WeChat.svg" >
		<img class="" src="/theme/black_blue/img/social_net/Snapchat.svg" >

	    </div>

	    <div class="prestas fw300 p28 t16  t18">

		<div class="presta"> <span>  <?= __("masterclass disponibles"); ?></span> <span class="qty">5</span>	</div>
		<div class="presta"> <span>  <?= __("documents formation"); ?></span> <span class="qty">11</span>	</div>
		<div class="presta"> <span>  <?= __("vidéos formations"); ?></span> <span class="qty">5</span>	</div>
		<div class="presta"> <span>  <?= __("contenus privés"); ?></span> <span class="qty">25</span>	</div>

	    </div>
	    </div>
	    
	    <div class="a_propos_slide">
		<img class="lire_suite  arrow_right " src="/theme/black_blue/img/arrow_right.svg">
		<h3>À propos de moi</h3>
		Lorem ipsum dolor sit amet, consectetur adipiscing elit. Massa velit metus etiam convallis scelerisque. Non cursus blandit fermentum ipsum. Ut placerat eu adipiscing quisque. Ac, tortor nunc augue et sed quam neque nulla nunc. Id ullamcorper tempor eget commodo ultrices fusce eros elementum. Mattis dis libero consectetur viverra. Integer praesent cursus nam odio dignissim velit urna fermentum diam.
Egestas tristique leo ac cursus id purus hac et elit. Cursus ut enim ut vitae augue nisi, feugiat at. Aenean condimentum lacus, volutpat a quis mauris ipsum lorem. Quam sociis a, dictumst ut at volutpat. Habitasse vel aliquam ut euismod a.
Diam nunc sit gravida erat sed eu mauris. Venenatis venenatis, a, etiam odio. Feugiat sagittis, ac, et cras lectus non ac. Sagittis sit porttitor neque turpis euismod ridiculus. 
Fringilla pellentesque malesuada sit nisl. Duis purus, egestas suspendisse in mattis tempor ut lectus pellentesque. Blandit pretium, risus, amet curabitur enim amet, quam. Scelerisque sed enim, nam ligula placerat at ultricies augue.
Consectetur integer ac et eu, ullamcorper accumsan. Neque dui arcu pharetra ut leo porttitor elit sed velit. Nisi in id proin tempor, pharetra. Et, elementum suspendisse turpis cras sed nunc sem purus hendrerit.
Sit eu mus ac hendrerit risus, blandit. Suspendisse et scelerisque fermentum feugiat.
	    </div>
	</div><?php // FIN padding  ?>


    </div><?php // FIN SIDEBAR  ?>

    
    <div class="scroll_bloc ">
	
	<div class="div_agent_img">
	    <div class="agent_img">
		<img src="/theme/black_blue/img/profile_ok.noir.svg" class="profile_ok" />
	    </div>   
	</div>
	
	<div class="top_links">
	    <a  href="" class="p25 t16 m14 fw400"><?= __("messages privés") ?></a>
	    <a  href="#mes-video-formation" class="p25 t16 m14 fw400"><?= __("vidéos Formation") ?></a>
	    <a  href="#contenu_od" class="p25 t16 m14 fw400"><?= __("contenus à la demande") ?></a>
	    <a  href="#priv_cont" class="p25 t16 m14 fw400"><?= __("contenus privés") ?></a>
	    <a  href="" class="p25 t16 m14 fw400"><?= __("pourboire") ?></a>
	</div>
	
	
	
	<div class="pictos ">
	    <div class="img" >  <img  class="tel" src="/theme/black_blue/img/medias/tel2.svg"  ></div>
	    <a class="img" href="#modal-consult-chat" rel="modal:open">  <img class="chat" src="/theme/black_blue/img/medias/chat2.svg"  ></a>
	    <div class="img" >  <img class="webcam" src="/theme/black_blue/img/medias/webcam2.svg"  ></div>
	    <div class="img" >  <img  class="sms" src="/theme/black_blue/img/medias/sms2.svg"  ></div>
	    <div class="img" >  <img class="email" src="/theme/black_blue/img/medias/email2.svg"  ></div>
	</div>
	
	
    </div> 
    
    
    <div class="top_bloc">



	<div class="owl-carousel carousel_top_links owl-theme">
	    <a  href="" class="p25 t16 m14 fw400"><?= __("messages privés") ?></a>
	    <a  href="#mes-video-formation" class="p25 t16 m14 fw400"><?= __("vidéos Formation") ?></a>
	    <a  href="#contenu_od" class="p25 t16 m14 fw400"><?= __("contenus à la demande") ?></a>
	    <a  href="#priv_cont" class="p25 t16 m14 fw400"><?= __("contenus privés") ?></a>
	    <a  href="" class="p25 t16 m14 fw400"><?= __("pourboire") ?></a>
	</div>


	
	
	<div class="contactez fw500 p25 t22">
<?= __("contactez"); ?>	
<?php if ($is_favorite) echo 'favorite'; ?>
<?= $User['pseudo']; ?>    
<?= __("par") ?>

	</div>


	<div class="pictos">
	   

		<img  class="tel " src="/theme/black_blue/img/medias/tel.svg"  />
		<img class="chat" src="/theme/black_blue/img/medias/chat.svg"  />
		<img class="webcam" src="/theme/black_blue/img/medias/webcam.svg"  />
		<img class="sms" src="/theme/black_blue/img/medias/sms.svg"  />
		<img class="email" src="/theme/black_blue/img/medias/email.svg"  />

	   

	</div>

	<div class="pictos mobile">

	    <div class="img" >  <img  class="tel" src="/theme/black_blue/img/medias/tel2.svg"  ><?= __("tel",
	null, true) ?></img></div>
	    <a class="img" href="#modal-consult-chat" rel="modal:open">  <img class="chat" src="/theme/black_blue/img/medias/chat2.svg"  ><?= __("chat",
	null, true) ?></img></a>
	    <div class="img" >  <img class="webcam" src="/theme/black_blue/img/medias/webcam2.svg"  ><?= __("webcam",
	null, true) ?></img></div>
	    <div class="img" >  <img  class="sms" src="/theme/black_blue/img/medias/sms2.svg"  ><?= __("sms",
	null, true) ?></img></div>
	    <div class="img" >  <img class="email" src="/theme/black_blue/img/medias/email2.svg"  ><?= __("email",
	null, true) ?> </img></div>

	</div>

	<div>
<a href="#modal-consult-rates" rel="modal:open" class="blue2 fw400 p22 t22">
<?= __("voir les tarifs de") ?> <?= $User['pseudo']; ?>    
</a></div>


	<div class="btns">
	    <div class="btn blue2 back pc"><?= ucfirst(__("m’alerter de son retour<br/>par SMS")) ?></div>
	    <div class="btn blue2 back mobile"><?= ucfirst(__("alerte retour<br/>par SMS")) ?></div>
	    <div class="btn blue2 rdez_vous pc"><?= ucfirst(__("prendre rendez-vous")) ?></div>
	    <div class="btn blue2 rdez_vous mobile"><?= ucfirst(__("prendre<br/>rendez-vous")) ?></div>
	    <div class="btn blue2 agenda pc"><?= ucfirst(__("voir son agenda")) ?></div>
	    <div class="btn blue2 agenda mobile"><?= ucfirst(__("voir son<br/>agenda")) ?></div>
	</div>


	

    </div><?php //fin top bloc   ?>

    <div class="top_bloc2">
	
	<div class="a_la_demande ucfirst blue2 p25 t18 fw500">
<?= (__("à la demande de ce LiviMaster, ses gains sont reversés à des oeuvres caritatives.")) ?>
	</div>

	<div class="_masterclass bordark blue2 fw400 p35 t22">
<?= ucfirst(__("3 MasterClass disponibles")) ?>
	</div>

	<div class="div_chevron">
	    <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">
	</div>
	
	
    </div>

    <div id="mes-video-formation" class="cs-mes-video-formation-section bordark">
	<h2 class="p50 t22 fw700 color-black ucfirst"><?= __("mes vidéos formation") ?></h2>
	<div class="cs-main-video-box">
	    <div class="cs-video ">
		<img src="<?= $avatar ?>">


		<div class="video">
		    <img class=" play" src="/theme/black_blue/img/btn/play_red.svg">
		    <video>

			<source src="/img/effacer/Videoarte.mp4#t=1" type="video/mp4">
			<source src="maVideo.webm" type="video/webm">
			<p> <?=__('Votre navigateur ne prend pas en charge les vidéos HTML5.') ?></p>
		    </video>

		</div>



		<div class="rating-stars">
<?php
$note = 3.5;

$full_stars = floor($note);
$demi_star = $note - $full_stars;
$empty_stars = 5 - ceil($note);

for ($s = 0; $s < $full_stars; $s++)
    {
    echo '<span class="rating-full"></span>';
    }

if ($demi_star > 0) echo '<span class="rating-half"></span>';


for ($s = 0; $s < $empty_stars; $s++)
    {
    echo '<span class="rating-empty"></span>';
    }
?>


		</div>
	    </div>
	    <div class="cs-video-content">
		<div class="cs-title">
		    <h3 class="p30 t22 ucfirst"><?= __("présentation de ma formation") ?></h3>

		    <div class="cs-mmd">   
			<div class="cs-utc-selector" data-tx_conv=""> 



			    <div class="cs-utc lh16-24 blue2 fw500">

				<span class="cs-current-utc p15 t15">
<?= __("modifier ma devise") ?> (<span class="currency_symbol">$</span>)</span> 
				<img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">



				<div class="cs-utc-list cs-utc-type-2">

<?php
foreach ($currencies as $key => $currency)
    {
    echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
    }
?>
				</div>
				
			    </div>
			</div>
			
			
			<p class="cs-mmd-price color-orange lh35-52 fw700">299,00$</p>
		    </div>	
		</div>
		
		<div class="cs-content">
		    <p class="p24 t18 m16">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
		    <div class="cs-mmd">
			<p class="cs-mmd-modifier-ma-device lh18-27 blue2">Modifier ma device ($) <img class="fa-chevron-down fa-angle-down" src="/theme/black_blue/img/chevron.svg" style="z-index: 2"></p>
			<p class="cs-mmd-price color-orange lh35-52 fw700">299,00$</p>
		    </div>
		</div>
	    </div>
	</div>
	<div class="cs-video-list">
	    <div class="swiper video-swiper">
		<div class="swiper-wrapper" rel="">
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog01.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog02.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog03.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog04.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog05.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog06.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		    <div class="swiper-slide">
			<div class="cs-video-swiper-sg" style="background-image:url('/theme/black_blue/img/effacer/blog07.jpg');">
			    <div class="cs-video-info-sg cs-locked-video">
				<h3 class="lh24-28 fw300 color-white">Video Title</h3>
				<p class="lh20-30 color-white fw300" style="opacity: 0.7;">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua</p>
			    </div>
			</div>
		    </div>
		</div>
		<div class="video-swiper-button-next swiper-button-next"></div>
		<div class="video-swiper-button-prev swiper-button-prev"></div>
	    </div>
	</div>
	
    </div>

    
    
    
    
    
    <div class="owl-carousel carousel_fiche owl-theme">
	    
	
	<div class="pub">
	
	<div class="left">
	    <div class="up_case fw600 p24 t22 title"><?= __("livitalk affiliation") ?></div>
	    <div class=" p20 t18 ucfirst"><?= __("devenez ambassadeur auprès de nouveaux influenceurs et micro-influenceurs et gagnez") ?></div>
	    <div class="fw500 p35 t30 blue2">10%</div>
	     <div class=" p20 t18 ucfirst"><?= __("des revenus générés par les nouveaux <span class='blue2'>LiviMasters</span>.") ?></div>
	</div>
	
	<div class="right" style="background-image: url(/theme/black_blue/img/effacer/1st_page-1.jpg);">
	    <!--<img class="" src="/theme/black_blue/img/effacer/1st_page-1.jpg">-->
	    <a href="#" class="btn up_case blue2 promouvoir p18"> <?= __("promouvoir") ?> </a>
	</div>
	
    </div>
    
    
    <div class="pub">
	
	<div class="left">
	    <div class="up_case fw600 p24 t22 title"><?= __("affiliation<br/>
vidéo/formation") ?></div>
	    <div class=" p20 t18 ucfirst"><?= __("gagnez de l'argent en faisant la promotion des Vidéos Formations
de vos <span class='blue2'>LiviMasters</span> auprès de vos Followers.") ?></div>
	   
	     <div class=" p20 t18 ucfirst"><?= __("gagnez jusqu'à <span class='blue2'>300$</span> par vidéo formation vendue.") ?></div>
	</div>
	
	<div class="right" style="background-image: url(/theme/black_blue/img/effacer/1st_page-2.jpg);">
	    <!--<img class="" src="/theme/black_blue/img/effacer/1st_page-1.jpg">-->
	    <a href="#" class="btn up_case blue2 promouvoir p18"> <?= __("voir les formations") ?> </a>
	</div>
	
    </div>
    
    
    <div class="pub">
	<div class="center">
	 <div class="up_case fw600 p24 t22 title"><?= __("recrutez des livimatsers<br/>
vidéo/formation") ?></div>
	    <div class=" p20 t18 ucfirst"><?= __("partagez ce lien pour recruter un maximum de <span class='blue2'>Livimasters</span> et créateurs de contenus et gagnez <span class='big_blue'>10%</span> des revenus générés par tous ceux que vous aurez recruté.") ?></div>
	   
	 <a href="#" class="btn up_case blue2 promouvoir p18"> <?= __("voir programme affiliation") ?> </a>
	 </div>
    </div>
    
    
    <div class="pub">
	<div class="center">
	 <div class="up_case fw600 p24 t22 title"><?= __("recrutez des livimatsers") ?></div>
	    <div class=" p20 t18 ucfirst"><?= __("partagez ce lien pour recruter un maximum de <span class='blue2'>Livimasters</span> et créateurs de contenus et gagnez <span class='big_blue'>10%</span> des revenus générés par tous ceux que vous aurez recruté.") ?></div>
	   
	 <a href="#" class="btn up_case blue2 promouvoir p18"> <?= __("voir programme affiliation") ?> </a>
	 </div>
    </div>
    
    <div class="pub">
	<div class="center">
	 <div class="up_case fw600 p24 t22 title"><?= __("recrutez des ambassadeurs") ?></div>
	    <div class="  p20 t18 ucfirst"><?= __("partagez ce lien pour recruter un maximum d'Ambassadeurs  possible. Ils gagneront <span class='big_blue'>10%</span> des revenus générés par les Livimasters qu'ils recruteront. Vous gagnerez <span class='big_blue'>50%</span> de ce montant.") ?></div>
	   
	 <a href="#" class="btn up_case blue2 promouvoir p18"> <?= __("voir programme affiliation") ?> </a>
	 </div>
    </div>
	
	


	</div>
    

    <section id="contenu_od" class="contenu_od bordark">
	
	
	
	<h2 class="p50 t22 m22 fw700 color-black"><?= __("Contenus à la demande") ?></h2>
	<div class="ss_titre fw400 p24 t16 m14">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Turpis cras fermentum fermentum.
	</div>
	
	
	
	<div class="bordark video_conf masterclass">
	    
	    
	    <div class="top">
	    
	    <div class="div_img">
		<div class="imgs">
		<img class="user rounded" src="<?= $avatar ?>">
		<img class="tv" src="/theme/black_blue/img/menu/masterclass.svg">
		</div>
		<div class="btn buy blue white2 p22"><?= __("regarder la vidéo") ?></div>
	    </div>
	    
	    
	    
	    <div class="div_txts">
		<div class="title p28 t22 m18 fw500 ">
		    <div class="imgs">
		    <img class="user rounded" src="<?= $avatar ?>">
		    <img class="tv" src="/theme/black_blue/img/menu/masterclass.svg">
		    </div>
		
		    <div class="title2"><?= __("vidéo conference et masterclass") ?></div>
		</div>
		<div class="ss_title p23 t20 m16 fw500"><?= __("masterclass titre - lorem ipsum") ?></div>
		
		<div class="txt p24 t18 m14 fw400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at arcu laoreet dignissim pellentesque lacus. Sodales nulla molestie dignissim sit. Faucibus dolor tellus id.</div>
	    
	     <div class="lire_suite p24 t18 m16 fw400 blue2">
		<?= __("Lire la suite", null, true) ?> <img class="arrow_right " src="/theme/black_blue/img/arrow_right.svg" >
	    </div>
	    </div>
	    
	    
	    
	    <div class="div_convert">
		
		
		<div class="cs-utc-selector" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p15 t15 m12">

<?= __("modifier ma devise") ?>(<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

		<div class="cs-utc-list cs-utc-type-2">

		    <?php
		    foreach ($currencies as $key => $currency)
			{

			echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
			}
		    ?>

		</div>
	    </div>
	</div>	
		
		
		
		<div class="montant orange2 p35 t20 m18 fw700 ">99,00$</div>
		
	    </div>
	    </div>
	    
	    
	    <div class="bas">
		
		<div class="room full up_case "><?= __("complet") ?> 3/02/22 16:00</div>
		<div class="room      up_case "><?= __("participer") ?> 3/02/22 16:00</div>
		<div class="room full up_case "><?= __("complet") ?> 3/02/22 16:00</div>
		<div class="room      up_case "><?= __("participer") ?> 3/02/22 16:00</div>
		<div class="room      up_case "><?= __("participer") ?> 3/02/22 16:00</div>
		<div class="room      up_case "><?= __("participer") ?> 3/02/22 16:00</div>
		<div class="room      up_case "><?= __("participer") ?> 3/02/22 16:00</div>
		<div class="room      up_case "><?= __("participer") ?> 3/02/22 16:00</div>
		
		
		
	    </div>
	    
	</div>
	
	
	
	<div class="bordark video_conf video_od other_cont">
	    
	    
	    <div class="top">
	    
	    <div class="div_img">
		<div class="imgs">
		<img class="user rounded" src="<?= $avatar ?>">
		<img class="tv" src="/theme/black_blue/img/medias/video_od.svg">
		</div>
	
	    </div>
	    
	    
	    
	    <div class="div_txts">
		<div class="title p28 t22 m18 fw500 ">
		    <div class="imgs">
		    <img class="user rounded" src="<?= $avatar ?>">
		    <img class="tv" src="/theme/black_blue/img/medias/video_od.svg">
		    </div>
		
		    <div class="title2"><?= __("Videos à la demande") ?></div>
		</div>
		 
		
		<div class="txt p24 t18 m14 fw400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at arcu laoreet dignissim pellentesque lacus. Sodales nulla molestie dignissim sit.</div>
	    
	     <div class="lire_suite p24 t18 m16 fw400 blue2">
		<?= __("Lire la suite", null, true) ?> <img class="arrow_right " src="/theme/black_blue/img/arrow_right.svg" >
	    </div>
	    </div>
	    
	    
	    
	    <div class="div_convert">
		
		
	    <div class="cs-utc-selector" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p15 t15 m12">

<?= __("modifier ma devise") ?>(<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

		<div class="cs-utc-list cs-utc-type-2">

		    <?php
		    foreach ($currencies as $key => $currency)
			{

			echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
			}
		    ?>

		</div>
	    </div>
	</div>	
		
		
		
		<div class="montant orange2 p35 t20 m18 fw700 ">99,00$</div>
		
	    </div>
		
		
		
		
	    </div>
	    
	    <div class="div_btns">
		    
		    
		    <div class="btn request blue white2 up_case"><?= __("envoyer une requête") ?></div>
		    <div class="btn buy blue white2 up_case"><?= __("acheter") ?></div>
		</div>
	    
	</div>
	
	
	
	
	
	
	
	
	
	<div class="bordark video_conf photo_od other_cont">
	    
	    
	    <div class="top">
	    
	    <div class="div_img">
		<div class="imgs">
		<img class="user rounded" src="<?= $avatar ?>">
		<img class="tv" src="/theme/black_blue/img/menu/contenus.svg">
		</div>
	
	    </div>
	    
	    
	    
	    <div class="div_txts">
		<div class="title p28 t22 m18 fw500 ">
		    <div class="imgs">
		    <img class="user rounded" src="<?= $avatar ?>">
		    <img class="tv" src="/theme/black_blue/img/menu/contenus.svg">
		    </div>
		
		    <div class="title2"><?= __("photos à la demande") ?></div>
		</div>
		 
		
		<div class="txt p24 t18 m14 fw400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at arcu laoreet dignissim pellentesque lacus. Sodales nulla molestie dignissim sit.</div>
	    
	     <div class="lire_suite p24 t18 m16 fw400 blue2">
		<?= __("Lire la suite", null, true) ?> <img class="arrow_right " src="/theme/black_blue/img/arrow_right.svg" >
	    </div>
	    </div>
	    
	    
	    
	    <div class="div_convert">
		
		
		<div class="cs-utc-selector" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p15 t15 m12">

<?= __("modifier ma devise") ?>(<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

		<div class="cs-utc-list cs-utc-type-2">

		    <?php
		    foreach ($currencies as $key => $currency)
			{

			echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
			}
		    ?>

		</div>
	    </div>
	</div>	
		
		
		
		<div class="montant orange2 p35 t20 m18 fw700 ">99,00$</div>
		
	    </div>
		
		
		
		
	    </div>
	    
	    <div class="div_btns">
		    
		    
		    <div class="btn request blue white2 up_case"><?= __("envoyer une requête") ?></div>
		    <div class="btn buy blue white2 up_case"><?= __("acheter") ?></div>
		</div>
	    
	</div>
	
	<div class="bordark video_conf pdf_od other_cont">
	    
	    
	    <div class="top">
	    
	    <div class="div_img">
		<div class="imgs">
		<img class="user rounded" src="<?= $avatar ?>">
		<img class="tv" src="/theme/black_blue/img/medias/pdf2.svg">
		</div>
	
	    </div>
	    
	    
	    
	    <div class="div_txts">
		<div class="title p28 t22 m18 fw500 ">
		    <div class="imgs">
		    <img class="user rounded" src="<?= $avatar ?>">
		    <img class="tv" src="/theme/black_blue/img/medias/pdf2.svg">
		    </div>
		
		    <div class="title2"><?= __("PDF/PPT document formation") ?></div>
		</div>
		 
		
		<div class="txt p24 t18 m14 fw400">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Morbi at arcu laoreet dignissim pellentesque lacus. Sodales nulla molestie dignissim sit.</div>
	    
	     <div class="lire_suite p24 t18 m16 fw400 blue2">
		<?= __("Lire la suite", null, true) ?> <img class="arrow_right " src="/theme/black_blue/img/arrow_right.svg" >
	    </div>
	    </div>
	    
	    
	    
	    <div class="div_convert">
		
		
		<div class="cs-utc-selector" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p15 t15 m12">

<?= __("modifier ma devise") ?>(<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

		<div class="cs-utc-list cs-utc-type-2">

		    <?php
		    foreach ($currencies as $key => $currency)
			{

			echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
			}
		    ?>

		</div>
	    </div>
	</div>	
		
		
		
		<div class="montant orange2 p35 t20 m18 fw700 ">59,00$</div>
		
	    </div>
		
		
		
		
	    </div>
	    
	    <div class="div_btns">
		    <div class="btn buy blue white2 up_case"><?= __("acheter") ?></div>
		</div>
	    
	</div>
	
    </section>
    
        
    

 <section id="priv_cont" class="priv_cont">
	
	
	
	<h2 class="p50  t22 m22 fw700 color-black"><?= __(" Contenus privés") ?></h2>
	<div class="div_ss_titre center_v">
	<div class="ss_titre center_h fw300 p30 t16 m14 ucfirst">
			<?= __("<span class='blue2'>abonnez</span> vous pour accéder aux contenus privés, photo et vidéos de Owen Simonin.") ?>  
	</div>
	    
	      <div class="div_convert">
		
		
	    <div class="cs-utc-selector" data-tx_conv="" > 
	    <div class="cs-utc lh16-24 blue2 fw500">

		<span class="cs-current-utc p15 t15 m12">

<?= __("modifier ma devise") ?>(<span class="currency_symbol">$</span>)</span> <img class="fa-chevron-down fa-angle-down " src="/theme/black_blue/img/menu/chevron.svg">

		<div class="cs-utc-list cs-utc-type-2">

		    <?php
		    foreach ($currencies as $key => $currency)
			{

			echo '<p class="lh18-27 fw600" data-currency="' . $currency["Currency"]["label"] . '"><span>' . $currency["Currency"]["code"] . ' (' . $currency["Currency"]["label"] . ')</span><span></span></p>';
			}
		    ?>

		</div>
	    </div>
	</div>	
		<div class="montant orange2 p36 t20 m18 fw700 ">14,90$/mois</div>
		
	    </div>
	    
	</div>

	
	
	
	
	<div class="grid">
	      <img class="user rounded" src="<?= $avatar ?>"> 
	      
	      <?php 
	      for($i=0;$i<16;$i++ ){
	      ?>
	      
	      <div class="div_flou">
		   <img class="lock" src="https://moigioi.hoieothon.com/wp-content/plugins/k-dev-expand/assets/design-assets/images/lock.png"> 
		    <img class="flou" src="/theme/black_blue/img/effacer/flou.jpg"> 
		    
		    <div class="txts">
			<div class="fw300 p28 t20 ">Photo Title</div>
			<div class="fw100 p28 t16 ">Lorem ipsum dolor </div>
		    </div>
	      </div>
	      
	    
	     <?php 
	      }
	      ?>
	    
	    
	</div>
	

	<div class="voir_plus blue2 up_case underline lh22-28"><?= __('Voir plus') ?> <img class="arrow" src="/theme/black_blue/img/arrow_right.svg"></div>
</section>



   


    <a href="#">
<img class="chevron bas" src="/theme/black_blue/img/menu/chevron.svg"></a>


    <style>

	html {
	    scroll-behavior: smooth;
	}

	.display-page {
	    width:100%;
	    text-align: center;
	}

	.display-page section.header{
	    background: url(/theme/black_blue/img/cover_image.jpg) no-repeat center center;
	    background-size: cover;
	    text-align: center;

	}



	.display-page  .agent_img{
	    background: url(<?= $avatar ?>) no-repeat center center;
	    background-size: cover;
	}


	/*  Tablet Small OR Mobile Extra Large  */
	@media only screen   and (max-width : 767px)
	{
	    .display-page section.header{
		background: url(theme/black_blue/img/cover_image.jpg) no-repeat center center;
		background-size: cover;
		text-align: center;

	    }
	}





    </style>

<style>
        /*Mes vidéos formation*/
        .cs-mes-video-formation-container {
            max-width: 100%;
        }
        .cs-mes-video-formation-section {
            overflow: hidden;
        }
        .cs-mes-video-formation-section > * {
            margin-bottom: calc(60px*var(--coef))!important;
        }
        .cs-mes-video-formation-section > h2 {
            margin-bottom: calc(120px*var(--coef))!important;
        }
        .rating-stars {
            display: flex;
            justify-content: space-between;
            width: calc(150px*var(--coef));
            margin-top: calc(10px*var(--coef));
            margin-bottom: calc(5px*var(--coef));
        }
        .rating-stars span {
            display: inline-block;
            width: calc(25px*var(--coef));
            height: calc(25px*var(--coef));
            background-size: 100% 100%;
        }
        .rating-full {
            background-image: url('https://moigioi.hoieothon.com/wp-content/plugins/k-dev-expand/assets/design-assets/images/full-star.png');
        }
        .rating-half {
            background-image: url('https://moigioi.hoieothon.com/wp-content/plugins/k-dev-expand/assets/design-assets/images/half-star.png');
        }
        .rating-empty {
            background-image: url('https://moigioi.hoieothon.com/wp-content/plugins/k-dev-expand/assets/design-assets/images/empty-star.png');
        }
        .cs-mes-video-formation-section .cs-main-video-box {
            max-width: calc(1500px*var(--coef));
            margin: auto;
            display: flex;
            width: 90%;
        }
        .cs-main-video-box .cs-video {
            position: relative;
            width: 40%;
            padding-right: calc(30px*var(--coef));
        }
        .cs-main-video-box .cs-video-content {
            width: 60%;
        }
        .cs-main-video-box .cs-video > img {
            position: absolute;
            border-radius: 50%;
            width: calc(100px*var(--coef));
            top: calc(-25px*var(--coef));
            left: calc(-25px*var(--coef));
	    z-index: 10;
	    border:2px solid white;
        }
        .cs-main-video-box .cs-video > iframe {
            border-radius: 35px;
            border: unset;
            width: 100%;
            margin-bottom: calc(15px*var(--coef));
        }
        .cs-main-video-box .cs-video-content .cs-title {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: flex-end;
            position:relative;
        }
	
	
	
	
	.cs-main-video-box .cs-video-content .cs-content {
	    text-align:left;
	}
	
	
        .cs-main-video-box .cs-video-content .cs-title h3, .cs-main-video-box .cs-video-content .cs-title p {
            margin: 0px;
        }
        .cs-video-content .cs-mmd-price {
            text-align: right;
        }
	
	.swiper{
	    overflow: inherit;;
	}
	
	
        .cs-video-swiper-sg {
            overflow: hidden;
            border: 3px solid #4CBBEC;
            border-radius: calc(50px*var(--coef));
            height: calc(245px*var(--coef));
            position: relative;
        }
        .cs-video-swiper-sg .cs-video-info-sg {
            padding: calc(10px*var(--coef)) calc(30px*var(--coef));
            display: flex;
            flex-direction: column;
            height: 100%;
            justify-content: end;
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
            cursor: pointer;
	    text-align: left;

        }
        .cs-video-info-sg.cs-locked-video > * {
            margin: 0px;
            white-space: nowrap;
            overflow: hidden;
            display: block;
            text-overflow: ellipsis;
            z-index: 2;
        }
        .cs-video-swiper-sg .cs-video-info-sg.cs-locked-video:before {
            position: absolute;
            content: "";
            width: 100%;
            left: 0;
            top: 0;
            height: 100%;
            z-index: 0;
            background: linear-gradient(181.57deg, rgba(98, 97, 97, 0.75) -94.89%, rgba(43, 43, 43, 0.379104) 96.49%, rgba(0, 0, 0, 0) 116.85%);
        }
        .cs-video-swiper-sg .cs-video-info-sg.cs-locked-video:after {
            content: "";
            background-image: url(https://moigioi.hoieothon.com/wp-content/plugins/k-dev-expand/assets/design-assets/images/lock.png);
            width: calc(30px*var(--coef));
            height: calc(50px*var(--coef));
            position: absolute;
            background-size: 100% auto;
            background-repeat: no-repeat;
            background-position: center;
            left: 0;
            right: 0;
            margin: auto;
            z-index: 1;
            top: 50%;
            transform: translate(0,-50%);
        }
        .cs-video-list .video-swiper {
            margin-left: calc((-10% - 40px)*var(--coef));
            margin-right: calc((-10% + 40px)*var(--coef));
            padding-top: calc(30px*var(--coef));
            padding-bottom: calc(30px*var(--coef));
        }
        .video-swiper-button-next.swiper-button-next {
            right: calc((10% + 10px)*var(--coef));
        }
        .video-swiper-button-prev.swiper-button-prev {
            left: calc((10% + 120px)*var(--coef));
        }
        .video-swiper-button-next:after, .video-swiper-button-prev:after {
            font-weight: 700;
            color: var(--blue);
        }
        .video-swiper .swiper-slide {
            scale: 1;
            transition: 0.3s all;
        }
        .cs-main-video-box .cs-video-content .cs-content .cs-mmd {
            display: none;
        }
	
	.swiper-slide-next{
	    position: relative;
	    left:calc(-20px*var(--coef));
	}
	
	.swiper-slide-next + .swiper-slide + .swiper-slide{
	    position: relative;
	    left:calc(20px*var(--coef));
/*	    border: red solid 3px;*/
	}
	
	
        @media(min-width: 768px){
            .video-swiper .swiper-slide.swiper-slide-next + .swiper-slide.swiper-slide-duplicate, .video-swiper .swiper-slide-next + .swiper-slide {
                scale: 1.3;
                padding-left: calc(20px*var(--coef));
                padding-right: calc(20px*var(--coef));
            }
        }
        @media (min-width:1600px){
            .video-swiper .swiper-slide.swiper-slide-next + .swiper-slide.swiper-slide-duplicate, .video-swiper .swiper-slide-next + .swiper-slide {
                padding-left: calc(40px*var(--coef));
                padding-right: calc(40px*var(--coef));
            }
        }
        @media (max-width:1024px){
	    
	   /* lock on images */ 
.cs-video-swiper-sg .cs-video-info-sg.cs-locked-video::after {
    width: calc(22px*var(--coef));
    height: calc(30px*var(--coef));
}
	    
.cs-video-swiper-sg .cs-video-info-sg >h3,
.cs-video-swiper-sg .cs-video-info-sg >p
{
    position: relative;
    top:calc(5px*var(--coef));
    left:  calc(-10px*var(--coef));
}
	    
            .cs-main-video-box .cs-video {
                width: 50%;
                padding-right: calc(18px*var(--coef));
            }
            .cs-main-video-box .cs-video-content {
                width: 50%;
            }
            .cs-mes-video-formation-section > h2 {
		margin-top: calc(42px*var(--coef));
                margin-bottom: calc(52px*var(--coef))!important;
                font-size: calc(22px*var(--coef));
                line-height: calc(33px*var(--coef));
            }
            .cs-main-video-box .cs-video > iframe{
                height: 260px;
            }
            .rating-stars {
                width: calc(100px*var(--coef));
                margin-top: calc(20px*var(--coef));
                margin-bottom: calc(5px*var(--coef));
            }
            .rating-stars span {
                display: inline-block;
                width: calc(17px*var(--coef));
                height: calc(17px*var(--coef));
                background-size: 100% 100%;
            }
            .cs-main-video-box .cs-video-content .cs-title h3 {
                font-size: calc(20px*var(--coef));
                line-height: calc(30px*var(--coef));
            }
            .cs-title .cs-mmd {
                position: absolute;
                right: 0;
                bottom: 100%;
            }
            .cs-main-video-box .cs-video-content .cs-title p {
                font-size: calc(15px*var(--coef));
                line-height: calc(18px*var(--coef));
                margin-bottom: 5px;
            }
            .cs-title .cs-mmd img.fa-chevron-down {
                width: calc(16px*var(--coef));
                margin-left: 5px;
            }
            .cs-main-video-box .cs-video-content .cs-title p.cs-mmd-price {
                font-size: calc(20px*var(--coef));
                line-height: calc(24px*var(--coef));
                margin-bottom: 15px;
            }
            .cs-main-video-box .cs-video-content .cs-content p {
                margin: calc(5px*var(--coef)) 0px;
            }
            .cs-video-info-sg.cs-locked-video h3 {
                font-size: calc(16px*var(--coef));
                line-height: calc(24px*var(--coef));
            }
            .cs-video-info-sg.cs-locked-video p {
                font-size: calc(11px*var(--coef));
                line-height: calc(16px*var(--coef));
            }
            .cs-video-swiper-sg {
                border: 2px solid #4CBBEC;
                height: calc(130px*var(--coef));
            }
            .cs-mes-video-formation-section > * {
                margin-bottom: calc(40px*var(--coef))!important;
            }
            .cs-mes-video-formation-section .cs-submit-button a {
                font-size: calc(35px*var(--coef));
                line-height: calc(45px*var(--coef));
            }
            .cs-main-video-box .cs-video > img {
                width: calc(70px*var(--coef));
            }
        }
        @media(max-width:767px){
            .cs-video-list .video-swiper {
                margin-left: -10%;
                margin-right: -10%;
                padding-top: calc(30px*var(--coef));
                padding-bottom: calc(30px*var(--coef));
            }
            .cs-mes-video-formation-section .cs-main-video-box {
                flex-direction: column;
                max-width: inherit;
                min-width: 335px;
            }
            .cs-main-video-box .cs-video {
                width: 80%;
                padding-right: calc(0px*var(--coef));
                margin: auto;
                min-width: 245px;
                margin-bottom: 20px;
            }
            .cs-mes-video-formation-section > h2 {
                margin-bottom: calc(29px*var(--coef))!important;
                font-size: calc(20px*var(--coef));
                line-height: calc(30px*var(--coef));
            }
            .cs-main-video-box .cs-video > img {
                width: calc(47px*var(--coef));
                top: calc(-15px*var(--coef));
                left: calc(-15px*var(--coef));
            }
            .cs-main-video-box .cs-video > iframe {
                height: 230px;
            }
            .cs-main-video-box .cs-video-content {
                width: 100%;
            }
            .cs-main-video-box .cs-video-content .cs-title h3 {
                font-size: calc(18px*var(--coef));
                line-height: calc(27px*var(--coef));
            }
            .cs-main-video-box .cs-video-content .cs-content p {
                margin: calc(5px*var(--coef)) 0px;
                font-size: calc(16px*var(--coef));
                line-height: calc(24px*var(--coef));
            }
            .cs-title .cs-mmd {
                display: none;
            }
            .cs-main-video-box .cs-video-content .cs-content .cs-mmd {
                display: flex;
                justify-content: center;
                margin-top: 10px;
            }
            .cs-main-video-box .cs-video-content .cs-content .cs-mmd img {
                width: 18px;
                margin: 0px 5px 0px 5px;
            }
            .cs-main-video-box .cs-video-content .cs-content p.cs-mmd-price {
                font-size: calc(18px*var(--coef));
                line-height: calc(21px*var(--coef));
            }
            .cs-main-video-box .cs-video-content .cs-content .cs-mmd-modifier-ma-device {
                font-size: calc(12px*var(--coef));
                line-height: calc(14px*var(--coef));
                font-weight: 500;
                display: flex;
                align-items: center;
            }
            .cs-mes-video-formation-section > * {
                margin-bottom: calc(20px*var(--coef))!important;
            }
            .cs-video-info-sg.cs-locked-video h3 {
                font-size: calc(14px*var(--coef));
                line-height: calc(21px*var(--coef));
            }
            .cs-video-swiper-sg .cs-video-info-sg.cs-locked-video:after{
                width: calc(18px*var(--coef));
                height: calc(40px*var(--coef));
            }
            .cs-mes-video-formation-section .cs-submit-button a {
                font-size: calc(26px*var(--coef));
                line-height: calc(30px*var(--coef));
            }
            .cs-video-swiper-sg {
                height: calc(115px*var(--coef));
                border-radius: calc(30px*var(--coef));
            }
        }
    </style>

    <script>
        window.onload = function ()
        {
            
	/* VOLET "à propos de moi" */
	
        $('body').on('click',
            ' .display-page  .sidebar  .lire_suite  ',
            function (e)
            {
		console.log(".display-page  .a_propos_slide  .lire_suite");
		if($('.display-page  .sidebar').hasClass('open'))
		{
		    $('.display-page  .sidebar').removeClass('open')
		    $(".display-page  .sidebar .a_propos_slide").hide("slide", { direction: "left" }, 300);
		    $(".display-page  .sidebar .div_a_propos").show("slide", { direction: "right" }, 300);

		}
		else
		{ 
		   $('.display-page  .sidebar').addClass('open')
		   $(".display-page  .sidebar .a_propos_slide").show("slide", { direction: "left" }, 300);
		   $(".display-page  .sidebar .div_a_propos").hide("slide", { direction: "right" }, 300);
		    
		
		}
	    })
	    
       /* ON SCROLL */
	var offset = 800
	$(window).on('load scroll', function(){

	   // console.log("scroll", $(window).scrollTop());
	    if( $(window).scrollTop() > offset ){
		$('.scroll_bloc').addClass('open')
	    }else{
		$('.scroll_bloc').removeClass('open')
	    }
	})
       
       
       
       
       /*  SLIDERS  owlCarousel */
            function page_start()
            {
                console.log("home_start");
     
                let items_car1 = 4;
                let items_car2 = 2;
		let dots1 = false;
		let loop1 = false

                let margin_car1 = 20
                let margin_car2 = 10

     


                //let support_type = device_type();

                let w = getDocWidth()// - 40;
                let support_type

                if (w > 1024)// PC
                    support_type = "pc"
                else // MOBILE
                if (w < 768)
                    support_type = "mobile"
                else // TABLETTE
                    support_type = "tablet"


                switch (support_type)
                {
                    case "mobile":
                        items_car1 = 4;
                        items_car2 = 1;
                        nav1 = false;
                        dots1 = true;
                        margin_car1 = 4;
                        margin_car2 = 10
			loop1 = true
                        break;
                    case "tablet":
                        items_car1 = 3;
                        items_car2 = 1;
			margin_car2 = 0
			dots1 = true;
			loop1 = true
                        break;
                }



                $('.carousel_top_links').owlCarousel('destroy');
                $('.carousel_top_links').owlCarousel({
                    margin: margin_car1,
                    center: false,
                    loop: loop1,
                    autoWidth: true,
                    navText: [
                        '<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">',
                        '<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">'
                    ],
                    mouseDrag: true,
                    touchDrag: true,
                    nav: false,
                    dots: dots1,
                    checkVisibility: false,
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
	    nav:true,
	    dots:true,
	    checkVisibility:false,
            items: items_car2
        })

    }

		
	    $(window).resize(function ()
    {
        console.log("resize");

        page_start()
    })	
		

            


            /* POPUP */
	    
$(" .display-page .div_btns .btn.buy, .display-page 	.cs-video-list	 .swiper-slide, #modal-consult-video-form .btn.validate, #modal-consult-masterclass .btn.validate").click(function () {
   
	$("#modal-payment").modal();
    });	  
	

	
<?php
$logged = true;
if ($logged)
    {
    ?>

$(".display-page  .pictos .tel").click(function (){$("#modal-consult-tel").modal();});
$(".display-page  .pictos .chat").click(function (){$( "#modal-consult-chat").modal();});
$(".display-page  .pictos .sms").click(function (){ $("#modal-consult-sms").modal();});
$(".display-page .top_bloc .back").click(function (){$("#modal-consult-back").modal();});
$(".display-page .top_bloc .rdez_vous").click(function (){$("#modal-consult-year-picker").modal();});
$(".display-page .top_bloc .agenda").click(function () {$("#modal-consult-agenda").modal();});
$(".display-page .cs-mes-video-formation-section .swiper-slide").click(function () {$("#modal-consult-video-form").modal();});
$(".display-page .room:not(.full)").click(function () {$("#modal-consult-masterclass").modal();});



<?php }
else
    {
    ?>


$(".display-page .top_bloc .pictos .tel").click(function (){$("#connection").modal();});
$(".display-page .top_bloc .pictos .chat").click(function (){$("#connection").modal();});
$(".display-page .top_bloc .pictos .sms").click(function (){$("#connection").modal();});
$(".display-page .top_bloc .back").click(function (){$("#connection").modal();});
$(".display-page .top_bloc .agenda").click(function (){$("#connection").modal();});
$(".display-page .cs-mes-video-formation-section .swiper-slide").click(function (){$("#connection").modal();});
$(".display-page .room:not(.full)").click(function () {$("#connection").modal();});

<?php } ?>


            /* CLICK ANYWHERE TO CLOSE */
            $(window).click(function ()
            {
		$(".display-page .header .div_pictos .agent_langs").hide();
            });


            $(".display-page .header .div_pictos .world").click(function ()
            {
                console.log("(world).click");
                event.stopPropagation();
		$(".agent_langs").toggle();
            });

            /* DIAPORAMA MES VIDEOS FORMATION  */

            let swiperVideo = new Swiper(".video-swiper", {
                slidesPerView: 5,
                spaceBetween: 30,
                loop: true,
                navigation: {
                    nextEl: ".video-swiper-button-next",
                    prevEl: ".video-swiper-button-prev",
                },
                breakpoints: {
                    320: {
                        slidesPerView: 3,
                        spaceBetween: 10
                    },
                    768: {
                        slidesPerView: 5,
                        spaceBetween: 20
                    },
                    1601: {
                        slidesPerView: 5,
                        spaceBetween: 30
                    }
                }
            });

            /* VIDEO PLAYER */

            let media
            $('body').on('click', '.cs-video video, .cs-video .play',
                    function ()
                    {

                        let cadre = $(this).closest(".cs-video");
                        let btn_play = cadre.find(".play")
                        media = cadre.find("video")[0]
                        //console.log("media", media);
                        if (media.paused)
                        {
                            media.play();
                            btn_play.fadeOut('fast');
                        } else
                        {
                            btn_play.fadeIn('fast');
                            media.pause();
                        }
                    })


            $('body').on('mousedown', '.close-modal',
                    function ()
                    {
                        if (typeof media != 'undefined')
                        {
                            media.pause();
                        }

                    })

            /* CONVERTISSEUR POUR MES FORMATIONS VIDEO */
	    let conversion_ar = [];

    <?php
    foreach ($currencies as $key => $currency)
	{
	// echo '<p class="lh18-27 fw600"><span>'.$currency["Currency"]["code"].' ('.$currency["Currency"]["label"].')</span><span></span></p>';
	echo "conversion_ar['" . $currency["Currency"]["label"] . "']=" . $currency["Currency"]["amount"] . ";" . chr(13) . chr(10);
	}
    ?>
        //let tx_conv;
        //let currency = "$";
	    
	    
	    

            /* CLICK ANYWHERE TO CLOSE */
            $(window).click(function ()
            {

                let img = $(".cs-utc-selector .cs-utc > img");
                $(img).removeClass('active');
                $(img).closest('.cs-dad-right').removeClass('cs-layer');
            });


            $('body').on('click',
                    '.cs-mes-video-formation-section .cs-utc-selector .cs-utc',
                            function (e)
                            {

                                e.preventDefault();
                                e.stopPropagation();
                                let img = $(this).find('> img');
                            //    console.log("click img", img);
                                if ($(img).hasClass('active'))
                                {
                                    $(img).toggleClass('active');
                                    $(img).closest('.cs-dad-right').
                                            removeClass('cs-layer');
                                } else
                                {
                                    $(img).toggleClass('active');
                                    $(img).closest('.cs-dad-right').addClass(
                                            'cs-layer');
                                }
                            });

           $('body').on('click', '.cs-mes-video-formation-section .cs-utc-selector .cs-utc .cs-utc-list > p',
                function (e)
                {
                    e.preventDefault();
		    let $parent = $(this).closest('.cs-utc-selector');
		    // OLD
		    let currency = $parent.find('.currency_symbol').html();
                    let prev_tx_conv = conversion_ar[currency]
		    console.log("OLD currency",currency);
		    // NEW 
                    currency = $(this).data("currency")
		    console.log("NEW currency",currency);
                    tx_conv = conversion_ar[currency] / prev_tx_conv
		    $parent.data("tx_conv", tx_conv);
			
		    
		    // CHANGE LE SYMBOLE EN HAUT DU COMPOSANT		    
//                    let html = $(this).html();
                    if ($(this).hasClass('active'))
                        return false;
                    $parent.find('.cs-utc .cs-utc-list > p').removeClass(
                            'active');
                    $(this).addClass('active');
                    $(this).closest('.cs-utc').find('.currency_symbol').html(
                            currency);
			   
		    convert_video_formation();
                });

				     
		function convert_video_formation()
		{
			let new_val
			let cur_val=  $(".cs-mes-video-formation-section .cs-mmd-price").html();
			cur_val = cur_val.replace(',', '.')
			cur_val = parseFloat(cur_val)
			let tx_conv = $(".cs-mes-video-formation-section .cs-utc-selector").data("tx_conv")
			new_val = cur_val * tx_conv;
			new_val = new_val.toFixed(2)
			new_val = new_val+""; 
			new_val = new_val.replace('.', ',')
			$(".cs-mmd-price").html(new_val+currency)
			
		}
		
		

		
		/* CONVERTISSEUR  DU BLOC  Contenus à la demande*/
		
		/* AFFICHE LES LISTE DEROULANTE OPTIONS currencies  */
	    $('body').on('click', '.div_convert .cs-utc-selector .cs-utc',
            function (e)
            {

                e.preventDefault();
		e.stopPropagation();
                let img = $(this).find('> img');
                console.log("click .div_convert .cs-utc-selector .cs-utc", img);
                if ($(img).hasClass('active'))
                {
                    $(img).toggleClass('active');
                    $(img).closest('.cs-dad-right').removeClass('cs-layer');
                } else
                {
                    $(img).toggleClass('active');
                    $(img).closest('.cs-dad-right').addClass('cs-layer');
                }
		
            });




    /* CHOIX D UNE AUTRE CURRENCY */
    $('body').on('mousedown',
            '.div_convert .cs-utc-selector .cs-utc .cs-utc-list > p',
            function (e)
            {
		e.preventDefault();
		let $parent = $(this).closest('.cs-utc-selector');
		// OLD
		
		let currency = $parent.find('.currency_symbol').html();
		let prev_tx_conv = conversion_ar[currency]
		// NEW 
		currency = $(this).data("currency")
		tx_conv = conversion_ar[currency] / prev_tx_conv
		$parent.data("tx_conv", tx_conv);


                // CHANGE LE SYMBOLE EN HAUT DU COMPOSANT
                if ($(this).hasClass('active'))
                    return false;
                $parent.find('.cs-utc .cs-utc-list > p').removeClass('active');
                $(this).addClass('active');
                $(this).closest('.cs-utc').find('.currency_symbol').html(
                        currency);
		
		
		//CHANGE LE MONTANT
		let $div_convert = $(this).closest('.div_convert');
		// OLD
		
		let new_val
		let cur_val=  $div_convert.find('.montant').html();;
		cur_val = cur_val.replace(',', '.')
		cur_val = parseFloat(cur_val)

		new_val = cur_val * tx_conv;
		new_val = new_val.toFixed(2)
		new_val = new_val+""; 
		new_val = new_val.replace('.', ',')
		$div_convert.find('.montant').html(new_val+currency)
		
		
            });
	    
	    
	   
	    
		
		
    page_start()
};// fin  DOMContentLoaded
    </script>

