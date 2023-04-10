<div class="fields"> 

			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/courrier_grey.svg" alt="alt"/>
			    <input class="input-shadow" type="email" placeholder="<?= __('E-mail') ?>" required name="data[User][email]" id="UserEmail" value="<?=$mail?>">
			</div>

			<div class="div-input-shadow">
			    <img class="picto" src="/theme/black_blue/img/lock_grey.svg" alt="alt"/>
			    <!--<div class="courrier" ></div>-->
			    <input class="input-shadow" placeholder="<?= __('Mot de passe') ?>" name="data[User][passwd]" required="required" type="password" id="UserPasswd" value="<?=$mdp?>">
			</div>
			
			<input id="data[User][compte]" name="data[User][compte]" type="hidden" value="<?=$statu?>">

		    </div>
		    
		    <div class="pictos text_s"> 
			<?= __('ou') ?> 
			<?= __('connectez-vous avec') ?>
			<br/>

			<img  src="/theme/black_blue/img/social_net/facebook.svg" alt="facebook"/>
			<img  src="/theme/black_blue/img/google.svg" alt="google"/>
			<img  src="/theme/black_blue/img/apple.svg" alt="apple"/>
		    </div>	 
<br/><br/>

		    <input class="btn_modal large blue up_case"  title="<?= __('Se Connecter') ?>" type="submit" value="<?= __('Se Connecter') ?>"></input> 