<footer >
    <div class="top">
	<div class="content">
	    <div class="div_logo">   
		<div class="logo"></div>
	    </div> 

	    <div class="dropdown mobile-flag pc mobile  block">
		<?= $this->FrontBlock->getHeaderCountryBlockMobile(); ?>
	    </div>
	</div>
    </div>

    

<div class="middle ">
    
    <div class="content pc">
	
	<ul>
<li>	<a href="" class="" ><?= __('comment ça marche') ?></a></li>
<li>	<a href="" class=""><?= __('informations légales') ?></a></li>
</ul> 
	
	
 <ul>
 <li>	<a href="" class=""><?= __('questions fréquentes') ?></a></li>
 <li>	<a href="../contact_support" class=""><?= __('contact support') ?></a></li>
 <div class="copyright">
	<?= __('Copyright') . " " . Configure::read('Site.name') . " " . date("Y"); ?></div>
    </ul> 

 <ul>
 <li>	<a href="../recrutement_agent" class=""><?= __('devenir livimaster') ?></a></li>
 <li>	<a href="../affiliation_generale" class=""><?= __('parrainage & affiliation') ?></a></li>
 </ul> 
 
 <ul>
 <li>	<a href="../oeuvres_caritatives" class=""><?= __('oeuvres caritatives') ?></a></li>
 <li>	<a href="../videos_livimaster" class=""><?= __('vidéos')." ".__('LiviMasters'); ?></a>	</li>

 <li class="social_net">
     <ul>
		<li class="instagram"></li>    
		<li class="tiktok"></li>    
		<li class="facebook"></li>    
		<li class="twitter"></li>    
	       
    </ul>  
 </li>
  <li class="powered">
		<?= __('Powered by XX Limited') ?>
 </li>
 
</ul> 
	
	</div>  
  
  
    
<div class="content tablet">
	
<ul>
<li>	<a href="" class="" ><?= __('comment ça marche') ?></a></li>
<li>	<a href="" class=""><?= __('informations légales') ?></a></li>
<li>	<a href="" class=""><?= __('questions fréquentes') ?></a></li>
<div class="copyright">
	<?= __('Copyright') . " " . Configure::read('Site.name') . " " . date("Y"); ?></div>
</ul> 
    
<ul>
 <li>	<a href="../contact_support" class=""><?= __('contact support') ?></a></li>
 <li>	<a href="../recrutement_agent" class=""><?= __('devenir livimaster') ?></a></li>
 <li>	<a href="../affiliation_generale" class=""><?= __('parrainage & affiliation') ?></a></li>   
 <li>
	 <ul class="social_net tablet mobile">
			<li class="instagram"></li>    
			<li class="tiktok"></li>    
			<li class="facebook"></li>    
			<li class="twitter"></li>    

	    </ul>  
 </li>   
</ul> 
 
 <ul>
 <li>	<a href="../oeuvres_caritatives" class=""><?= __('oeuvres caritatives') ?></a></li>
 <li>	<a href="../videos_livimaster" class=""><?= __('vidéos')." ".__('LiviMasters'); ?></a>	</li>

 <li>	<a href="" class="">
	 <div class="lang_pow tablet mobile block">
		 <div class="dropdown mobile-flag">
		<?= $this->FrontBlock->getHeaderCountryBlockMobile(); ?>
	    </div>
		
	    <div class="powered">
		<?= __('Powered by XX Limited') ?>
	    </div>
		
	 </div></a>	</li>
</ul>
    
</div>    
    
    
<div class="content mobile">
	
	<ul>
<li>	 <div class="div_logo">   
		<div class="logo"></div>
	    </div> 
	</li>
<li>	<a href="" class="" ><?= __('comment ça marche') ?></a></li>
<li>	<a href="" class=""><?= __('informations légales') ?></a></li>
 <li>	<a href="" class=""><?= __('questions fréquentes') ?></a></li>
 <li>	<a href="../contact_support" class=""><?= __('contact support') ?></a></li>
    </ul> 

 <ul>
 <li>	 <div class="dropdown mobile-flag pc mobile  block">
		<?= $this->FrontBlock->getHeaderCountryBlockMobile(); ?>
	    </div></li>
 <li>	<a href="../recrutement_agent" class=""><?= __('devenir livimaster') ?></a></li>
 <li>	<a href="../affiliation_generale" class=""><?= __('parrainage & affiliation') ?></a></li><!-- comment -->   
 <li>	<a href="../oeuvres_caritatives" class=""><?= __('oeuvres caritatives') ?></a></li>
 <li>	<a href="../videos_livimaster" class=""><?= __('vidéos')." ".__('LiviMasters'); ?></a>	</li>

</ul> 
</div>    
    
    
    
</div>  

    

    <div class="bottom ">
	<div class="content mobile   ">

	    <div class="copyright ">
		<?= __('Copyright') . " " . Configure::read('Site.name') . " " . date("Y"); ?>
	    </div>

	    
	
	     <ul class="social_net tablet mobile">
			<li class="instagram"></li>    
			<li class="tiktok"></li>    
			<li class="facebook"></li>    
			<li class="twitter"></li>    

	    </ul>  
	
	    

		
	    <div class="powered mobile">
		<?= __('Powered by XX Limited') ?>
	    </div>
		
	
	    

	</div>
    </div>


</footer>

<?php
echo $this->element('Users/login_modal');
?> 


