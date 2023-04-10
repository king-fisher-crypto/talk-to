<div class="fields"> 

			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/user_grey.svg" alt="<?= __('nom') ?>"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('nom') ?>"   name="data[User][lastname]">
			</div>
			
			
			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/user_grey.svg" alt="<?= __('prénom') ?>"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('prénom') ?>"  name="data[User][firstname]">
			</div>
			
			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/user_grey.svg" alt="<?= __('pseudo') ?>"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('pseudo') ?>"   name="data[User][pseudo]">
			</div>
			
			
			<div class="div-input-shadow">
			    
			    
<ul class="accordion-menu">
    <li>
	<div class="dropdownlink picto">
	    <img class="picto" src="/theme/black_blue/img/tel.svg" alt="<?= __('tél') ?>" />
	    <img  class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg" />
	</div>
	<ul class="submenuItems">
	    <li><a href="/accounts/profil">FR
	   </a></li>
	    
	    <li><a href="#">
	    EN</a></li>
	</ul>
    </li>
</ul>	    
			    
	<input class="input-shadow" type="text" placeholder="<?= __('tél') ?>"  name="data[User][phone_number]">
			</div>
			
			
			
			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/spot.svg" alt="<?= __('pays') ?>"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('pays') ?>"  name="data[User][country_id]">
			</div>
			
			
			
			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/courrier_grey.svg" alt="<?= __('E-mail') ?>"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('confirmez votre e-mail') ?>" name="data[User][email2]">
			</div>

			
			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/lock_grey.svg" alt="<?= __('E-mail') ?>"/>
			    <input class="input-shadow" type="text" placeholder="<?= __('créer un mot de passe') ?>" name="data[User][passwd_subscribe]">
			</div>
			
		    </div>
		    
	
<label class="checkbox_div text_s">
	<?= __('valider')." <span class='underline'>".__('les conditions générales d\'utilisation')."</span> <span class='blue2'>".Configure::read('Site.name')."</span> " ?>		    
  <input type="checkbox" >
  <span class="checkmark"></span>
</label>
		    
		    
		    
		    <br/>
		    <input class="btn_modal register large blue up_case" title="<?= __('s\'inscrire') ?>" type="submit" value="<?= __('s\'inscrire') ?>">