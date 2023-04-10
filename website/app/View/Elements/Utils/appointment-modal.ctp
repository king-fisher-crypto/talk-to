<?php
$message =  __("<div class='message-title'>Vos propositions d'horaires ont été envoyées </div>"); // on peut injecter du HTML
$this->set('message', $message);
$this->set('valider', __('continuer sur livitalk'));
$this->set('class', 'custom-modal');
$this->set('id', 'modal-appointment-confirmation');

$this->set('modal', true);
echo $this->element('Utils/modal-confirmation');

?>
<style>
    #modal-appointment.modal {
        border-radius: calc(15px*var(--coef));
    }


    #modal-appointment.modal .modal-content {
        padding: 5px calc(20px * var(--coef)) calc(20px * var(--coef)) calc(20px * var(--coef));
    }

    #modal-appointment.modal #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
        width: calc(81.5px*var(--coef));
    }

    #modal-appointment.modal .btn.validate {
        padding: 0 calc(80px*var(--coef)) 0 calc(78px*var(--coef));
        text-transform: uppercase;
        width: calc(300px * var(--coef));
        border: none;
        font-weight: 500;
        margin-top: calc(30px * var(--coef));
    }

    #modal-appointment.modal .cs-mon-calendrier-section,
    #modal-appointment.modal .cs-selecteur-horaires {
        box-shadow: 0px 3px 35px rgb(0 0 0 / 15%);
    }


    /* tablets ----------- */
    @media only screen and (max-width : 1024px) {

        .modal#modal-appointment .dcalendarpicker .dudp__wrapper {
            flex-direction: column;
        }

        #modal-appointment.modal #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
            width: calc(77px * var(--coef));
            margin-bottom: calc(20px * var(--coef));
        }

        .modal#modal-appointment .cs-selecteur-horaires {
            max-width: calc(629px * var(--coef));
            padding: 0 calc(100px * var(--coef));
        }

        #modal-confirmation.custom-modal,
        #modal-refusal.custom-modal,
        #modal-warning.custom-modal,
        #modal-appointment-confirmation.custom-modal {
            width: 51%;
        }

        .modal#modal-appointment .cs-selecteur-horaires-timepicker .cs-time-sg {
            max-width: calc(77px * var(--coef));
            height: calc(50px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays span {
            font-size: calc(18px * var(--coef));
            width: calc(77px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar-views {
            width: calc(2714px * var(--coef));
        }

    }


    @media only screen and (max-width : 767px) {
        #modal-appointment.modal .cs-selecteur-horaires-timepicker {
            margin: 0;
        }


        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
            width: calc(40px * var(--coef));
            font-size: calc(16px * var(--coef));
            margin-bottom: calc(10px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container {
            width: calc(280px * var(--coef));
            background: transparent;
        }

        .modal#modal-appointment #cs-mon-calendrier {
            padding: calc(10px * var(--coef)) calc(9px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays span {
            font-size: calc(16px * var(--coef));
            width: calc(40px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar-views {
            width: calc(1428px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev,
        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
            font-size: calc(36px * var(--coef));
            top: 3.2%;
        }

        .modal#modal-appointment .cs-mon-calendrier-bottom {
            padding: 0px;
        }

        .modal#modal-appointment .title {
            margin-left: calc(15px * var(--coef));
            margin-right: calc(15px * var(--coef));
        }

        .modal#modal-appointment .title h1 {
            font-size: calc(18px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev {
            left: 17%;
        }

        .modal#modal-appointment .title p {
            font-size: calc(15px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-month,
        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-month {
            font-size: calc(20px * var(--coef));
        }

        .modal#modal-appointment .cs-selecteur-horaires {
            padding: 0px;
        }

        .modal#modal-appointment .cs-selecteur-horaires-header {
            margin-left: 0px;
            margin-right: 0px;
        }

        .modal#modal-appointment .cs-selecteur-horaires-timepicker .cs-time-sg {
            max-width: calc(70px * var(--coef));
            height: calc(42px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
            font-size: calc(70px * var(--coef));
            font-weight: 300;
            top: 3.2%;
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
            right: 17%;
        }

        #modal-appointment.modal #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
            width: calc(41px * var(--coef)) !important;
            margin-bottom: calc(5px * var(--coef));
        }

        .modal#modal-appointment #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
            font-size: calc(34px * var(--coef));
        }

        #modal-confirmation.custom-modal,
        #modal-refusal.custom-modal,
        #modal-warning.custom-modal,
        #modal-appointment-confirmation.custom-modal {
            width: 90%;
        }

        .jquery-modal.blocker.current {
            overflow-x: hidden;
        }

    }
</style>

<div class="modal  fade <?= $class ?? ''; ?>" id="modal-appointment" role="dialog">
    <a href="#close-modal" rel="modal:close" class="close-modal "></a>

    <div class="modal-content">
        <?= $this->element('Utils/planning-picker'); ?>
        <button class="btn blue h70 lh24-36 validate" id="validerBtn" data-type="valider" href="#close-modal" rel="modal:close">Valider</button>
    </div>
</div>



<script>
    
    $("#validerBtn").click(function() {
        $("#modal-appointment-confirmation").modal()
        var event = new Event('appointment-confirm');
        event.initEvent('appointment-confirm', true, true);
        dispatchEvent(event);
    });
</script>