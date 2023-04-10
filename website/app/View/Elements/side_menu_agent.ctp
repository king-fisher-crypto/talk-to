<ul class="accordion-menu">
    <li>  <a  href="/agents/dashboard">
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
	   <li><a href="/accounts/profil"><?= __('mon profil') ?></a></li>
	   <li><a href="/accounts/profil/presentation"><?= __('ma présentation') ?></a></li>
	    <li><a href="/agents/my_billing"><?= __('Ma facturation & gains',null, true); ?></a></li>
	    <li><a href="/agents/rates"><?= __('mes tarifs') ?></a></li>
	    <li><a href="/agents/payment"><?= __('mes versemements') ?></a></li>
	    <li><a href="/accounts/payment_details"><?= __('mes coordonnées de paiement') ?></a></li>
	    <li><a href="/agents/payment_requests"><?= __('mes requetes paiements clients') ?></a></li>
	    <li><a href="/agents/tips"><?= __('mes pourboires') ?></a></li>
	    <li><a href="/agents/subscription"><?= __('mes abonnements') ?></a></li>
	    <li><a href="/accounts/promo_codes"><?= __('codes promo clients') ?></a></li>
	    <li><a href="/agents/certif_account"><?= __('certification du compte') ?></a></li>
		<li><a href="/agents/social_networks"><?= __('mes réseaux sociaux') ?></a></li>
	    <li><a href="/agents/qr_code"><?= __('mon QR code') ?></a></li>
	    <li><a href="/agents/oeuvres_caritatives"><?= __('oeuvres caritatives') ?></a></li> 
	</ul>
    </li>
    
    <li>
	<div class="dropdownlink">
	    <img src="/theme/black_blue/img/menu/history.svg" />
	    <?= __('historique'); ?>
	    <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	<ul class="submenuItems">
	    <li><a href="/agents/history"><?= __('mes communications') ?></a></li>
<!--	    <li><a href="/agents/history?media=phone"><?= __('par téléphone') ?></a></li>
	    <li><a href="/agents/history?media=email"><?= __('par écrit') ?></a></li>
	    <li><a href="/agents/history?media=chat"><?= __('par chat') ?></a></li>
	    <li><a href="#"><?= __('par webcam') ?></a></li>
	    <li><a href="#"><?= __('par SMS') ?></a></li>-->
	    <li><a href="/agents/my_sales"><?= __('mes ventes additionnelles') ?></a></li>
	    <li><a href="/agents/historylostcall"><?= __('mes appels perdus') ?></a></li>
	    <li><a href="/agents/historylostchat"><?= __('mes chats perdus') ?></a></li>
	    <li><a href="/agents/historylostemail"><?= __('mes emails perdus') ?></a></li>
	    <li><a href="/agents/historylostwebcam"><?= __('mes webcam perdues') ?></a></li>
	    <li><a href="/agents/historylostsms "><?= __('mes SMS perdus') ?></a></li>
	    <li><a href="/agents/notations"><?= __('mes notes') ?></a></li>
	    <li><a href="/agents/reviews"><?= __('mes avis') ?></a></li>
	    <!--<li><a href="/agents/my_biling"><?= __('ma facturation') ?></a></li>-->
	    <li><a href="/agents/clients_refund"><?= __('mes remboursements clients') ?></a></li>
	</ul>
    </li>
    
    
    <li>
	<div class="dropdownlink">
	    <img  src="/theme/black_blue/img/menu/heart.svg" />
	    <?= __('Parrainages & Affiliation') ?>
	   <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	
	<ul class="submenuItems">
	    <li><a href="/sponsorship/parrainage">
	    <?= __('Parrainages / Affiliation') ?></a></li>
	    <li>
		<!--<a href="/sponsorship/agent_gain">-->
		<a href="/accounts/loyalty">
	    <?= __('Mes Gains / Affiliation') ?></a></li>
	</ul>
    </li>

    <li>
	<div class="dropdownlink">
	    <img  src="/theme/black_blue/img/menu/messagerie.svg" /> 
	    <?= __('ma messagerie') ?>
	    <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	
	<ul class="submenuItems">
	    <li><a href="/accounts/paid_consultations_email"><?= __('consultation Email') ?></a></li>
	    <li><a href="/accounts/my_private_messages"><?= __('messages privés') ?></a></li>
	    <li><a href="/agents/mails_relance?private=1"><?= __('relance clients') ?></a></li>
	</ul>
	

    </li>
    
    
    <li>
	<div class="dropdownlink">
	    <img  src="/theme/black_blue/img/menu/calendrier.svg" />
	    <?= __('mon calendrier') ?>
	   <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	
	<ul class="submenuItems">
	    <li><a href="/agents/planning"><?= __('mon planning') ?></a></li>
	    <li><a href="/agents/appointments"><?= __('mes RDV') ?></a></li>
	</ul>
	
    </li>
    
    <li>
	<div class="dropdownlink">
	    <img  src="/theme/black_blue/img/menu/aide.svg" /> 
	    <?= __('aide & support') ?>
	    <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	
	<ul class="submenuItems">
	    <li><a href="/agents/mode"><?= __('options liviTalk') ?></a></li>
	    <li><a href="/agents/comment_communication"><?= __('développer votre activité') ?></a></li>
	    <li><a href="/agents/comment_general"><?= __('mode d\'emploi') ?></a></li>
	    <li><a href="/agents/cgu"><?= __('rémunération & reversion') ?></a></li>
	    <li><a href="/agents/gain"><?= __('conditions générales') ?></a></li>
	</ul>
	

    </li>
    
    <li>  <a  href="/agents/mod_consult">
	<div class="dropdownlink">
	 
	    <img  src="/theme/black_blue/img/menu/mode_consultation.svg" />
	    <?= __('mes modes de consultations') ?>
	  
	</div>  </a>
    </li>
    
    
    <li> <a  href="/agents/my_training_videos">
	<div class="dropdownlink">
	  
	    <img  src="/theme/black_blue/img/menu/video.svg" />
	    <?= __('mes vidéos formation') ?>
	   
	</div> </a>
    </li>
    
    
     <li><a  href="/accounts/masterclass">
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
	    <li><a href=""><?= __('documents-pdf à vendre') ?></a></li>
	    <li><a href=""><?= __('photos / vidéos à la demande') ?></a></li>
	</ul>
    </li>
      
    
    <li>  <a  href="/agents/my_private_content">
	<div class="dropdownlink">
	 
	    <img  src="/theme/black_blue/img/menu/contenus.svg" />
	    <?= __('mes contenus privés') ?>
	  
	</div>  </a>
    </li>
    
   
      
    
    <li>   <a  href="/logout">
	<div class="dropdownlink">
	
	    <img  src="/theme/black_blue/img/menu/deconnexion.svg" />
	    <?= __('Déconnexion') ?>
	  
	</div>  </a>
    </li>

</ul>