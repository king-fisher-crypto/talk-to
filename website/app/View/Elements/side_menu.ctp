<ul class="accordion-menu">
    
    <li>  <a  href="/dashboard">
	<div class="dropdownlink">
	 
	    <img  src="/theme/black_blue/img/menu/dashboard.svg" />
	    <?= __('dashboard ') ?>
	  
	</div>  </a>
    </li>
    
    <li>
	<div class="dropdownlink">
	    <img  src="/theme/black_blue/img/menu/user_blue.svg" />
	    <?= __('mon compte') ?>


	    <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	<ul class="submenuItems">
	    <li><a href="/accounts/profil">
	    <?= __('mon profil') ?></a></li>
	    
	    <li><a href="/accounts/favorites">
	    <?= __('mes favoris') ?></a></li>
	</ul>
    </li>
    
    <li>  <a href="/accounts/history">
	<div class="dropdownlink">
	  
		<img src="/theme/black_blue/img/menu/phone_plus.svg" />
		<?= __('mes consultations') ?>
	</div></a>
    </li>
    
    <li>
	<div class="dropdownlink">
	  
		<img  src="/theme/black_blue/img/menu/achats.svg" /> <?= __('Mes achats') ?>
		<img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	   
	</div> 

	<ul class="submenuItems">
	    <li><a href="/accounts/buycredits"><?= __('Mes Dépôts/Crédits') ?></a></li>
	    <li><a href="/accounts/masterclass">Masterclass</a></li>
	    <li><a href="/accounts/photos_od"><?= __('photos à la demande') ?></a></li>
	    <li><a href="/accounts/videos_od"><?= __('vidéos à la demande') ?></a></li>
	    <li><a href="/accounts/pdf_od"><?= __('Documents-Pdf') ?></a></li>
	    <li><a href="/accounts/subscription"><?= __('abonnements') ?></a></li>
	    <li><a href="/accounts/payment_request"><?= __('requêtes de paiement') ?></a></li>
	</ul>


    </li>
    
    <li>
	<div class="dropdownlink">
	    <img  src="/theme/black_blue/img/menu/heart.svg" />

	    <?= __('parrainages & affiliation') ?>
	    <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	<ul class="submenuItems">
	    
	    <li><a href="/sponsorship/parrainage">
		    <?= __('parrainages / affiliation') ?></a></li>
	    <li><a href="/accounts/loyalty"><?= __('Mes Gains / Affiliation') ?></a></li>
	    <li><a href="/accounts/payment_details"><?= __('mes coordonnées de paiement') ?></a></li>
	    <li><a href="/accounts/certif_account"><?= __('certification du compte') ?></a></li>
	    
	    <li><a href="/accounts/affiliate_payment"><?= __('mes Versements') ?></a></li>
	</ul>
    </li>

    <li>  
	<div class="dropdownlink">
	
		<img  src="/theme/black_blue/img/menu/messagerie.svg" /> 
		<?= __('ma messagerie') ?>
		<img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	 
	</div> 

	<ul class="submenuItems">
	    <li><a href="/accounts/paid_consultations_email"><?= __('consultation email') ?></a></li>
	    <li><a href="/accounts/my_private_messages"><?= __('messages privés') ?></a></li>
	</ul>


    </li>

    <li>  <a  href="/accounts/appointments">
	<div class="dropdownlink">
	  
		<img  src="/theme/black_blue/img/menu/calendrier.svg" />
		<?= __('mes rdv') . " " . __('LiviMasters') ?>
	  
	</div>  </a>
    </li>

    <li>  <a  href="/accounts/loyalty_bonus">
	<div class="dropdownlink">
	  
		<img  src="/theme/black_blue/img/menu/bonus.svg" />
		<?= __('mes bonus fidélité') ?>
	  
	</div>  </a>
    </li>


    <li>  <a  href="/accounts/my_videos">
	<div class="dropdownlink">
	  
		<img  src="/theme/black_blue/img/menu/video.svg" />
		<?= __('mes vidéos') ?>
	  
	</div>  </a>
    </li>

    <li>  <a  href="/accounts/my_masterclass">
	<div class="dropdownlink">
	  
		<img  src="/theme/black_blue/img/menu/masterclass.svg" />
		<?= __('mes') ?> masterclass
	  
	</div>  </a>
    </li>

  <li>  
	<div class="dropdownlink">
	  
	<img  src="/theme/black_blue/img/menu/contenus.svg" />
		<?= __('mes contenus à la demande') ?>
	      <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div> 

	<ul class="submenuItems">
	    <li><a href="/accounts/docs_pdf"><?= __('documents-pdf') ?></a></li>
	    <li><a href="/accounts/photos_videos_od"><?= __('photos / vidéos à la demande') ?></a></li>
	</ul>
    </li>


    <li> <a  href="/accounts/promo_codes">
	<div class="dropdownlink">
	   
		<img  src="/theme/black_blue/img/menu/codes.svg" />
		<?= __('mes codes promos') ?>
	    
	</div></a>
    </li>


    <li> <a  href="/logout">
	<div class="dropdownlink">
	   
		<img  src="/theme/black_blue/img/menu/deconnexion.svg" />
		<?= __('Déconnexion') ?>
	   
	</div> </a>
    </li>

</ul>