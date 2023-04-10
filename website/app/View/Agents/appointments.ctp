<?php $this->Html->script('/theme/black_blue/js/calendar/i18n.js');
?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js'); ?>

<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand()); 
?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); 
?>
<?= $this->Html->css('/theme/black_blue/css/hayen.css'); ?>
<script src="https://cdn.jsdelivr.net/npm/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<?php
$message =  __("<div class='message-title'>Vos modifications ont été enregistrées </div>"); // on peut injecter du HTML
$this->set('message', $message);
$this->set('valider', __('valider'));
$this->set('class', 'custom-modal');

echo $this->element('Utils/modal-confirmation');
echo $this->element('Utils/modal-refusal');
echo $this->element('Utils/appointment-modal');
?>



<?php
$message =  __("<div class='message-title'>Vous devez selectionner au moins un client </div>"); // on peut injecter du HTML
$this->set('message', $message);
$this->set('valider', __('valider'));
$this->set('class', 'custom-modal');
$this->set('id', 'modal-warning');

echo $this->element('Utils/modal-confirmation');

?>
<section class="appointments-page page marg180">
	<article>
		<h1 class=""> <?= __('Mes RDV') ?></h1>
		<p>
			<?= __("Les clients effectuant une demande de rendez-vous ont automatiquement crédité leur compte client LiviTalk au préalable. Lorsqu'un horaire vous est proposé, celui-ci correspond à l'un des horaires pour lesquels vous vous êtes déclarés \" Disponible \" via votre agenda LiviTalk. Il vous faut donc valider celui-ci pour que le client reçoive à son tour une confirmation, mais vous avez malgré tout la possibilité de le refuser en proposant d'autres horaires. Afin de limiter les interactions, nous vous conseillons de valider l'horaire proposé et qui correspond aussi et surtout à la disponibilité de votre client.") ?>
		</p>
	</article>

	<div class="cadre_table">
		<?php

		$appointments = [];
		$appointment = [];
		$appointment['date'] = "24/04/22 15:30";
		$appointment['client']["pseudo"] = "Lorem Ipsum";

		$appointment['duree'] = "15min";

		$statuts = ["En attente", "En attente", "Validé", "En attente", "Validé", "En attente", "En attente", "En attente", "Refusé", "Refusé"];

		$k = 0;
		for ($j = 1; $j <= 3; $j++) {
			for ($i = 1; $i <= 4; $i++) {
				$appointment['statut']  = $statuts[$k];
				$k++;
				if ($k > 10) $k = 0;

				$appointments[] = $appointment;
			}
		}

		?>
		<?php if (empty($appointments)) : ?>
			<p class="txt_cent">
				<?php echo __('Vous n\'avez aucune demande de consultation.'); ?>
			</p>
		<?php else : ?>
			<div class="overflow jswidth">
				<img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">
				<table class="stries">
					<thead class="">
						<tr>
							<th>
								<div class="checkbox-wrapper-19">
									<input type="checkbox" />
									<label for="cbtest-19" class="check-box check abled" id="header-checkbox" onclick=" $(this).toggleClass('checked');">
								</div>
							</th>
							<th class="agent"><?php echo __('Client'); ?></th>
							<th class="date">
								<strong><?php echo __('Date'); ?></strong>
								<span class="lgrey2 lh18-27">+05:00 UTC Paris</span>
							</th>
							<th class="duree"><?php echo __('Durée'); ?></th>
							<th class="statu"><?php echo __('Statut'); ?></th>

						</tr>
					</thead>
					<tbody>
						<?php foreach ($appointments as $appointment) : ?>
							<tr>

								<?php if (!($appointment['statut'] === "Refusé")) : ?>
									<td>
										<div class="checkbox-wrapper-19">
											<input type="checkbox" />
											<label for="cbtest-19" class="check-box check abled" onclick=" $(this).toggleClass('checked');">
										</div>
									</td>
								<?php elseif (($appointment['statut'] === "Refusé")) : ?>
									<td></td>
								<?php endif; ?>

								<td>Lorem Ipsum</td>
								<td class="montant"><?php echo $appointment['date']; ?></td>
								<td class=""><?php echo $appointment['duree']; ?></td>


								<?php if ($appointment['statut'] === "En attente") : ?>
									<td><?php echo $appointment['statut']; ?></td>
								<?php elseif ($appointment['statut'] === "Validé") :  ?>
									<td class="blue2"><?php echo $appointment['statut']; ?></td>
								<?php elseif ($appointment['statut'] === "Refusé") :  ?>
									<td class="orange2"><?php echo $appointment['statut']; ?></td>
								<?php endif; ?>
							</tr>

						<?php endforeach; ?>

					</tbody>
				</table>
			</div>
	</div> <!-- End of create_table div -->

	<div class="btn-group">
		<button class="btn btn-grey h70 lh24-36" id="date-btn">Proposer autre date</button>
		<button class="btn blue h70 lh24-36" id="valider-btn" data-type="valider">Valider</button>
		<button class="btn orange h70 lh24-36" id="cancel-btn">Refuser</button>
	</div>
</section>

<p>

	<?php if ($this->Paginator->param('pageCount') > 1) echo $this->FrontBlock->getPaginateObj($this->Paginator); ?>
<?php endif; ?>
</p>

<script type="text/javascript">
	let inputStatus;
	var checkboxes = document.querySelectorAll('label.check-box');

	function checkedStatus() {
		inputStatus = false
		var checkboxes = document.querySelectorAll('label.check-box');
		for (var i = 0; i < checkboxes.length; i++) {
			if (checkboxes[i].classList.contains('checked')) {
				inputStatus = true;
			}
		}
	}
	let doc = document.querySelector('body');

	$("#valider-btn").click(function() {
		checkedStatus()
		if (inputStatus) {
			showConfirmationModal()
			let doc = document.querySelector('.appointments-page');
		} else {
			$("#modal-warning").modal()
		}
	});

	$("#date-btn").click(function() {
		checkedStatus()
		if (inputStatus) {
			showAppointmentModal()
		} else {
			$("#modal-warning").modal()
		}
	});


	$("#cancel-btn").click(function() {
		checkedStatus()
		if (inputStatus) {

			showRefusalModal()
		} else {
			$("#modal-warning").modal()
		}
	});


	function showConfirmationModal() {
		document.querySelector('.modal .message-title').innerHTML = '<?= __("Votre validation est enregistrée et votre client en est averti") ?>'
		document.querySelector('.modal .btn.validate').innerHTML = '<?= __('valider') ?>'
		$("#modal-confirmation").modal();

	}

	function showRefusalModal() {
		document.querySelector('.modal#modal-refusal .message-title').innerHTML = '<?= __("Confirmez-vous le refus à cet horaire ? Votre client en sera averti sans indications") ?>'
		document.querySelector('.modal#modal-refusal .btn.validate').innerHTML = '<?= __('valider') ?>'
		$("#modal-refusal").modal()
	}

	function showAppointmentModal() {
		$("#modal-appointment").modal()
	}


	let chk = document.getElementById("header-checkbox")

	document.getElementById("header-checkbox").addEventListener("click", function() {
		var checkboxes = document.querySelectorAll('tbody label.check-box');
		for (var i = 0; i < checkboxes.length; i++) {
			if (this.classList.contains('checked')) {
				!checkboxes[i].classList.contains('checked') && checkboxes[i].classList.add('checked');
			} else {
				checkboxes[i].classList.remove('checked');
				inputStatus = false
			}
		}
	});
	let newTitle = document.querySelector(".modal#modal-appointment .slider-small h1");

	newTitle.style.display = "none"

	let newPara = document.querySelector(".modal#modal-appointment .slider-small p");
	newPara.style.display = "none"
</script>