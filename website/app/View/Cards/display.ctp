<div class="tarot-game tarot-game-<?php
switch ($card['Card']['game_type']) {
	case Card::GAME_TYPE_YES_NO: echo 'yesno'; break;
	case Card::GAME_TYPE_SINGLE: echo 'single'; break;
	case Card::GAME_TYPE_FORTUNE: echo 'fortune'; break;
	case Card::GAME_TYPE_LOVE: echo 'love'; break;
    default: echo $card['Card']['game_type'];
}
?>">
	<div class="tarot-game-step tarot-game-step-choose">
		<div class="container">
			<div class="tarot-game-step-title">
				<h1><?php echo htmlspecialchars($card['CardLang']['step_choose_title']); ?></h1>
			</div>
			<div class="tarot-game-step-desc">
				<?php echo htmlspecialchars($card['CardLang']['step_choose_description']); ?>
			</div>
			<div class="tarot-game-step-cont">
			</div>
		</div>
	</div>
</div>

<div class="tarot-game-main-desc"><div class="container"><?php echo $card['CardLang']['description']; ?></div></div>

<script>
	window.TarotConfig = {
		selector: '.tarot-game',
		card: <?php echo json_encode($card); ?>,
		cardItems: <?php echo json_encode($cardItems); ?>,
		cardImagesUrl: <?php echo json_encode('/' . Configure::read('Site.cardImages') . '/'); ?>,
		cardItemImagesUrl: <?php echo json_encode('/' . Configure::read('Site.cardItemImages') . '/'); ?>,
		tr: <?php echo json_encode([
			'shuffle_btn_text' => __('Mélanger les cartes'),
		]); ?>
	};
</script>
