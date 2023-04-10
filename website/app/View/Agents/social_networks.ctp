<?php
$socialLinks = [
	[
		'name' => 'Instagram',
		'icon' => 'instagram'
	],
	[
		'name' => 'Facebook',
		'icon' => 'facebook'
	],
	[
		'name' => 'Linkedin',
		'icon' => 'Linkedin'
	],
	[
		'name' => 'Youtube',
		'icon' => 'Youtube'
	],
	[
		'name' => 'Twitter',
		'icon' => 'twitter'
	],
	[
		'name' => 'Snapchat',
		'icon' => 'Snapchat'
	],
	[
		'name' => 'Pinterest',
		'icon' => 'Pinterest'
	],
	[
		'name' => 'Twitch',
		'icon' => 'Twitch'
	],
	[
		'name' => 'Tiktok',
		'icon' => 'tiktok'
	],
	[
		'name' => 'WeChat',
		'icon' => 'WeChat'
	]
];
?>
<section class="social_networks-page page jswidth marg180">
	<article class="header-section">
		<h1 class="article-header">
			<?= __('Mes Réseaux sociaux') ?>
		</h1>
		<p>
			<?= __("Donnez confiance à vos futurs clients: Activez la vérification de sécurité de vos comptes et affichez les liens sur votre profil LiviTalk. Nous n'aurons pas la possibilité de publier sur  vos comptes réseaux sociaux.") ?>
		</p>
	</article>

	<div class="container">
		<div class="btns social-bloc flex-col">
			<?php foreach ($socialLinks as $socialLink): ?>
				<div class="social">
					<div class="social-media" title="<?= __('agent') ?>">
						<img class="w-40 h-40" src="/theme/black_blue/img/social_net/<?= $socialLink['icon'] ?>.svg">
						<h2>
							<?= $socialLink['name']; ?>
						</h2>
					</div>
					<div class="link-div">
						<a href="">se connecter</a>
					</div>
				</div>
			<?php endforeach; ?>
		</div>
		<article class="mb-106 social-indication">
			<?= __("Indiquer vos réseaux sociaux permet de rediriger vos clients vers ceux-ci mais également de vous proposer très prochainement quelques surprises ! Faites le vous avez tout à y gagner.") ?>
		</article>
	</div>
	<div class="social-form">
		<form action="">
			<div class="social-links_header">
				<div class="text-description">
					<div class="desc-link text">
						Collez le lien de votre compte pour rediriger vos clients vers celui-ci
					</div>
					<div class="desc-abonner text">
						Soyez honnêtes, nous vous <br> proposerons des surprises !
					</div>
				</div>
			</div>
			<?php foreach ($socialLinks as $socialLink): ?>
				<div class="row-social">
					<div class="table-social-icon">
						<img class="w-40 h-40 p-13 shadow br-15"
							src="/theme/black_blue/img/social_net/<?= $socialLink['icon'] ?>.svg">
						<h2>
							<?= $socialLink['name']; ?>
						</h2>
					</div>

					<div class="link-desc ">
						<input type="text" placeholder="https://">
					</div>
					<div class="desc-abonner">
						
							<input type="text" placeholder="nombre abonnés">
							<img src="/theme/black_blue/img/Group-icon.svg" alt="">
						
					</div>
				</div>
			<?php endforeach; ?>

			<div class="save-btn">
				<button type="submit" class="br-10">ENREGISTRER</button>
			</div>
		</form>
	</div>
</section>