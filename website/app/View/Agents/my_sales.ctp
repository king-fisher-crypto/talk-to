<?php //$this->Html->script('/theme/black_blue/js/calendar/i18n.js'); 
?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js'); ?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand()); 
?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); 
?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>
<?php
//echo $this->Html->script('/theme/default/js/nx_select_history', array('block' => 'script'));

$message = __("En validant cette demande de remboursement je confirme que cette somme sera déduite de mon compte LiviMaster, que le client sera remboursé et que je ne pourrais plus me rétracter.");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');

echo $this->Session->flash();
?>

<?php
$appointments = [];

$appointment = [];
$appointment['date'] = "24/04/22";
$appointment['Agent']["pseudo"] = "Lorem Ipsum";

$appointment['duree'] = "5%";

$appointment['statut'] = [];

$mois = [1, 2, 3, 1, 2, 3, 1, 2, 3, 1, 2, 3];

$montants = ["30,00$", "50,00$", "75,00$", "Via requête Paiement ", "99,00$", "30,00$", "50,00$", "75,00$", "Via requête Paiement ", "99,00$", "50,00$", "75,00$",];
$duree = ["15", "30", "30", "60", "15", "15", "60", "30", "30", "60", "30", "30"];

$statuts = ["En cours", "En cours", "Validé", "En cours", "Validé", "En cours", "En cours", "Arrêté", "Arrêté", "Arrêté", "Arrêté"];

$fin = ["", "", "Mettre fin", "", "Mettre fin", "", "", "", "", "", ""];

$statuts_color = ["", "", "blue2", "", "blue2", "", "", "orange2", "orange2", "orange2", "orange2"];

$k = 0;
for ($j = 1; $j <= 3; $j++) {
    for ($i = 1; $i <= 6; $i++) {
        $appointment['duree'] = $duree[$k] . " min";
        $appointment['statut']["label"] = $statuts[$k];
        $appointment['mois'] = $mois[$k];
        $appointment['fin'] = $fin[$k];

        $appointment['statut']["color"] = $statuts_color[$k];
        $k++;
        if ($k > 10)
            $k = 0;


        $appointments[] = $appointment;
    }
}


?>

<section class="my-sales page">
    <div class=" my-sales title">
        <h1>
            Mes Ventes additionnelles
        </h1>
        <p>
            Cette page répertorie l'ensemble de vos ventes additionnelles, Vidéos formation, MasterClass, Photos à la
            demande et Vidéos à la demande. Les consultations par téléphone, Chat, Webcam, SMS ou Email ne sont pas
            répertoriées ici.

        </p>
    </div>


    <div class="div_criteres">

        <div id="tabs_k2" class="cs-selecteur-de-criteres-container _form_input">
            <div class="cs-selecteur-de-criteres">
                <div class="cs-sdc-date cs-search">
                    <img src="/theme/black_blue/img/loupe_bleu.svg">
                    <input type="text" id="datepicker1" class="form-control lh22-33 no_select" placeholder="date" readonly="readonly">
                </div>
                <div class="cs-sdc-client cs-search">
                    <input name="client-search" class="lh22-33" placeholder="client">
                </div>
                <div class="cs-sdc-mode cs-search"><span class="lh22-33">mode</span>

                    <img class="fa-chevron-down" src="/theme/black_blue/img/menu/chevron.svg">

                </div>
            </div>
            <div class="cs-mode-list cs-hover-type">

                <p class="lh20-30">Téléphone</p>
                <p class="lh20-30">Chat</p>
                <p class="lh20-30">Email</p>
                <p class="lh20-30">SMS</p>
                <p class="lh20-30">Webcam</p>
                <p class="lh20-30">Masterclass</p>
                <p class="lh20-30">vidéos formation</p>
                <p class="lh20-30">Photos à la demande</p>
                <p class="lh20-30">Documents-PDF</p>
            </div>
        </div>

        <div class="btn chercher h70 lh24-28 p20spe blue2 up_case" title="<?= __('chercher') ?>">
            <?= __('chercher') ?>
        </div>
    </div>
    <div class="my-sales table-picker-container">
        <div class="cadre_table ">
            <div class="overflow jswidth stries-table-container">
                <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">
                <table class="stries">
                    <thead>
                        <tr>
                            <th class="agent">client</th>
                            <th class="date">
                                <div>Prestation</div>
                            </th>
                            <th class="date">Date</th>
                            <th class="montant">Montant</th>
                        </tr>
                    </thead>
                    <tbody>

                        <tr>
                            <td class="agent">Lorem Ipsum </td>
                            <td class="prestation">Vidéos formation</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">25,22$</td>
                        </tr>

                        <tr>
                            <td class="agent">Lorem Ipsum </td>
                            <td class="prestation">Vidéos formation</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">126,12$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Vidéos formation</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Vidéos formation</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">25,22$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Vidéos formation</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">126,12$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Documents-PDF</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Masterclass</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">25,22$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Photos à la demande</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">126,12$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Photos à la demande</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Videos à la demande</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">25,22$</td>
                        </tr>
                        <tr>
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Videos à la demande</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">126,12$</td>
                        </tr>
                        <tr class="last">
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Masterclass</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr class="last">
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Masterclass</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr class="last">
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Masterclass</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr class="last">
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Documents-PDF</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr class="last">
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Vidéos à la demande</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                        <tr class="last">
                            <td class="agent">Lorem Ipsum</td>
                            <td class="prestation">Masterclass</td>
                            <td class="date">24/04/22 15:11:25 </td>
                            <td class="montant">2 452,12$</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="facturation">
            <a href="/agents/order"> Retourner sur la page " Ma Facturation " pour déclencher un paiement</a>
        </div>
    </div>
    <input type="hidden" id="daterange" class="form-control">
</section>

<style>
    #modal-confirmation .modal-content {
        width: calc(629px*var(--coef));
        height: calc(270px*var(--coef));
    }

    #modal-confirmation #message {
        font-size: calc(24px*var(--coef));
        line-height: calc(28px*var(--coef));
    }

    @media only screen and (max-width : 767px) {


        #modal-confirmation .modal-content {
            width: calc(347px*var(--coef));
            height: calc(270px*var(--coef));
        }

        #modal-confirmation #message {
            font-size: calc(18px*var(--coef));
            line-height: calc(21px*var(--coef));
        }

    }
</style>

<script>
    window.onload = function() {


        $('body').on('click', '.cs-selecteur-de-criteres .cs-sdc-mode', function(e) {
            e.preventDefault();
            let $this = $(this);
            let $parent = $(this).closest('.cs-selecteur-de-criteres-container');
            if ($this.find('> img').hasClass('active')) {
                $parent.find('.cs-mode-list').removeClass('active');
                $this.find('> img').removeClass('active');

            } else {

                $parent.find('.cs-mode-list').addClass('active');
                $this.find('> img').addClass('active');
            }
        });

        $('body').on('click', '.cs-selecteur-de-criteres-container .cs-mode-list.active > p', function(e) {
            e.preventDefault();
            let $this = $(this);
            let $parent = $(this).closest('.cs-selecteur-de-criteres-container');
            let value = $this.html();
            $parent.find('.cs-selecteur-de-criteres .cs-sdc-mode > span').html(value);
            $this.closest('.cs-mode-list').removeClass('active');
            $parent.find('.cs-selecteur-de-criteres .cs-sdc-mode > img').removeClass('fa-angle-up').addClass('fa-angle-down');
        });



        $("table.stries .action a").click(function() {
            $("#modal-confirmation").modal();

        });


        /////////// DATE PICKER //////////

        //var btn_datepicker = document.getElementById('.cs-selecteur-de-criteres-container .cs-sdc-date');
        var btn_datepicker = $(".cs-selecteur-de-criteres-container .cs-sdc-date")[0]

        btn_datepicker.addEventListener('click', function() {
            duDatepicker('#daterange', 'show')
        }, false);


        duDatepicker('#daterange', {
            range: true,
            events: {
                onRangeFormat: function(from, to) {
                    var fromFormat = 'mmmm d, yyyy',
                        toFormat = 'mmmm d, yyyy';

                    console.log("from", from, "to", to);

                    if (from.getMonth() === to.getMonth() && from.getFullYear() ===
                        to.getFullYear()) {
                        fromFormat = 'mmmm d'
                        toFormat = 'd, yyyy'
                    } else if (from.getFullYear() === to.getFullYear()) {
                        fromFormat = 'mmmm d'
                        toFormat = 'mmmm d, yyyy'
                    }

                    return from.getTime() === to.getTime() ?
                        this.formatDate(from, 'mmmm d, yyyy') : [this.formatDate(from, fromFormat),
                            this.formatDate(to, toFormat)
                        ].join('-');
                }
            }
        });
    }
</script>