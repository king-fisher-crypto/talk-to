<?php
$data = [
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '24/04/22 15:11:25',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
    ],
    [
        'agent' => 'Lorem Ipsum ',
        'date_consultation' => '24/04/22 15:11:25',
        'date_envoie' => '',
        ]
    ];

$message =  __("<div class='message-title'>Vous devez sélectionner un client </div>"); // on peut injecter du HTML
$this->set('message', $message);
$this->set('valider', __('sélectionner'));
echo $this->element('Utils/modal-confirmation');
?>


<style>
    .selection-title {
        text-align: left;
    }
    
    input {
        -webkit-appearance: none;
    }

    #modal-confirmation {
        width: 33%;
    }
    #modal-confirmation.modal .modal-content {
        width: 100%;
        padding: calc(50px * var(--coef)) 0;
    }
    #modal-confirmation.modal .modal-content #message {
        padding: 0px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
    }

    #modal-confirmation.modal .modal-content #message .message-title {
        margin-bottom: calc(32px * var(--coef));
    }

    @media screen and (max-width: 1024px) {
        #modal-confirmation {
            width: 50%;
        }
        .selection-title {
            text-align: center;
        }
    }

    @media screen and (max-width: 767px) {
        #modal-confirmation {
            width: 100%;
        }
        #modal-confirmation.modal .modal-content {
            padding: calc(30px * var(--coef)) 0;
            height: fit-content;
        }
        #modal-confirmation.modal .btn.validate {
            height: calc(61px * var(--coef));
        }
        .selection-title {
            font-size: calc(16px * var(--coef));
        }
    }
</style>

<section class="mails-relance page agent_cadre_table">
	<div class="first-bloc">
		<h1>Relance clients</h1>
		<p>Grâce à cet outils, vous avez la possibilité de faire un message groupé à un ou plusieurs clients. Par mesure de sécurité cet envoi est limité à 1 par semaine pour éviter de sur-solliciter ces derniers, nous vous invitons à ne leur écrire que si votre message à de l'intérêt pour eux, sans quoi ils spameront vos prochains envois. Attention, ces derniers n'auront pas la possibilité de répondre directement à votre message.</p>
       <div class="title-input  lh22-26">
        <input type="text"  placeholder="Titre"   class="title-writer">
       </div>
       <div class="message-input">
        <textarea name="" id=""   placeholder="taper un message"  class="lh22-26" ></textarea>
       </div>
	
	           <div class="mails-relance_search">
            <div class="search_container">
                <div class="search_modal">
                    <div class="search_input">
                        <a href="#"><img src="\theme\black_blue\img\promo_code_agent\icon_chercher_chercher_default.svg" alt=""></a>
                        <div class="lh22-26 input-controller">
                            <input type="text" name="" id="search" placeholder="<?= __('Client') ?>">
                        </div>
                    </div>
                    <div class="search_suggestions hidden">
                        <div class="clients_suggestions">
                            <p>Tokyo</p>
                            <p>Berlin</p>
                            <p>Paris</p>
                        </div>
                    </div>
                </div>
                <button><?= __('chercher') ?></button>
            </div>
     </div>

        <h2 class="selection-title">Sélectionnez le(s) client(s) pour cet envoi</h2>
     <div class="mails_relance table-picker-container">
        <div class="cadre_table ">
            <div class="overflow jswidth stries-table-container">
            <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

                <table class="stries">
                    <thead>
                        <tr>
                            <th class="agent">client</th>
                            <th class="date">
                                <div>Dernière <br>consultation/Achat</div>
                            </th>
                            <th class="date-envoi">Date dernier <br>envoi</th>
                            <th class="icon"><div class="checkbox-wrapper-19" >
                                    <input type="checkbox" />
                                    <label for="cbtest-19" class="check-box check abled" id="header-checkbox"
                                    onclick=" $(this).toggleClass('checked');">
                            </div></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($data as $value): ?>
                        <tr>
                            <td class="agent"><?= $value['agent'] ?> </td>
                            <td class="date1"><?= $value['date_consultation'] ?></td>
                            <td class="date"><?= $value['date_envoie'] ?> </td>
                            <td class="Icon"><div class="checkbox-wrapper-19" >
                                    <input type="checkbox" />
                                    <label for="cbtest-19" class="check-box check abled"
                                    onclick=" $(this).toggleClass('checked');">
                                </div></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="submitBtn">
            <button id="submitBtn">
                Envoyer
            </button>
        </div>
          
	</div>
</section>
<script>
document.getElementById("header-checkbox").addEventListener("click", function() {
    var checkboxes = document.querySelectorAll('tbody label.check-box');
    for (var i = 0; i < checkboxes.length; i++) {
        if (this.classList.contains('checked')) {
            !checkboxes[i].classList.contains('checked') && checkboxes[i].classList.add('checked');
        } else {
            checkboxes[i].classList.remove('checked');
        }
    }
});

$( "#submitBtn" ).click(function() { 
     if (document.querySelectorAll('tbody label.checked').length > 0) {
        document.querySelector('.modal .message-title').innerHTML = '<?= __("Confirmez-vous l\'envoi de ce message?") ?>'
        document.querySelector('.modal .btn.validate').innerHTML = '<?= __('valider') ?>'
    } else {
        document.querySelector('.modal .message-title').innerHTML = '<?= __("Vous devez sélectionner un client") ?>'
        document.querySelector('.modal .btn.validate').innerHTML = '<?= __('sélectionner') ?>'
    }

     $("#modal-confirmation").modal()
});
</script>
