<video width="100%" height="600" id="videoID" controls  muted>
	<source src="" type="video/mp4">
</video>
<script>

	$(document).ready(() => {
		var video = document.getElementById('videoID');
		const videoPath = '/'+pathVideo+'/'+ card.CardLang.video;
		video.src = videoPath;
		video.load();
		video.play();
		window.setTimeout(() => {
			$('.card_page').empty();
			$('.card_page').load('/cards/result', selectedCardsInput );
		}, 5000);
	});

</script>
