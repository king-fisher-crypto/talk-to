<div class="sidebar">
	<div class="inner">
		<div><h2>Rappel de vos cartes:</h2></div>
		<?php
		foreach ( $cardInformation as $cardItem ){
			?>
			<h3><?php echo $cardItem[0]['CardItem']['name']?></h3>
			<img src="/<?php echo Configure::read('Site.cardItem');?>/<?php echo $cardItem[0]['CardItem']['image']?>" alt="<?php echo $cardItem[0]['CardItem']['name']?>">
			<?php
		}
		?>

	</div>
</div>
<div class="content">
	<?php
	 echo 'text par carte';
	?>
</div>
<div class="content mt20">
	<?php
	 echo $result['text'];
	?>
</div>
