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
$this->set('valider', __('continuer sur livitalk'));
$this->set('class', 'custom-modal');

echo $this->element('Utils/modal-confirmation');

?>

<section class="planning-page">
    <?= $this->element('Utils/planning-picker'); ?>
    <div class="planning-btn-container">
        <button id="submitBtn">
            Enregister
        </button>
    </div>
</section>
<script type="text/javascript">
    $("#submitBtn").click(function() {
        showModal('submit')
    });

    const options = document.querySelectorAll('a.cs-status-option[data-type]');

    options.forEach(option => {
        option.addEventListener('click', () => {
            const type = option.getAttribute('data-type');
            console.log(`Option with type ${type} clicked!`);
            const dataValue = document.querySelector("#datepicker4").value;
            if (!dataValue) {
                showModal('planing')
            }
            // Do something with the selected type
        });
    });

    function showModal(type) {
        if (type === 'planing') {
            document.querySelector('.modal .message-title').innerHTML = '<?= __("Vous devez sélectionner un ou plusieurs horaires pour modifier le statut.") ?>'
            document.querySelector('.modal .btn.validate').innerHTML = '<?= __('j’ai compris') ?>'
        } else {
            document.querySelector('.modal .message-title').innerHTML = '<?= __("Vos modifications ont été enregistrées") ?>'
            document.querySelector('.modal .btn.validate').innerHTML = '<?= __('continuer sur livitalk') ?>'
        }
        $("#modal-confirmation").modal()
    }
</script>