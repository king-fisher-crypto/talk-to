<style>
    /*Mon Calendrier*/
    .cs-mon-calendrier-container {
        width: calc(700px*var(--coef));
        max-width: 100%;
        position: relative;
    }

    .cs-mon-calendrier-container #close-mon-calendrier {
        position: absolute;
        right: calc(20px*var(--coef));
        color: #4CBBEC;
        top: calc(10px*var(--coef));
        font-size: calc(40px*var(--coef));
    }

    #cs-mon-calendrier .dcalendarpicker {
        visibility: visible;
        opacity: 1;
        position: unset;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__wrapper {
        position: unset;
        transform: unset;
        box-shadow: unset;
        font-size: calc(18px*var(--coef));
        font-weight: 100;
    }

    #cs-mon-calendrier .dcalendarpicker.dp__open {
        background: transparent;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar-views {
        width: calc(2000px*var(--coef));
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container {
        width: calc(570px*var(--coef));
        background: transparent;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__calendar-header {
        display: flex;
        background: transparent;
        padding: 0;
        align-items: center;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__calendar-header .dudp__sel-year {
        display: inline-block;
        color: #787878;
        font-weight: 500;
        font-size: calc(20px*var(--coef));
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__calendar-header .dcp_sel-date {
        display: none;
    }

    #cs-mon-calendrier .cs_dudp__btn-cal-prev,
    #cs-mon-calendrier .cs_dudp__btn-cal-next {
        display: block;
        text-align: center;
        font-size: calc(50px*var(--coef));
        line-height: calc(38px*var(--coef));
        width: calc(55px*var(--coef));
        height: calc(43px*var(--coef));
        font-weight: 300;
        cursor: pointer;
        border-radius: 50%;
        opacity: 1;
        transition: opacity 0.25s cubic-bezier(0, 0, 0.2, 1), background-color 0.25s linear;
        will-change: opacity, background-color;
        color: var(--blue);
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays span {
        font-size: calc(20px*var(--coef));
        font-weight: 500;
        width: calc(82px*var(--coef));
        line-height: calc(35px*var(--coef));
        text-transform: uppercase;
        color: #000;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
        width: calc(82px*var(--coef));
        line-height: calc(50px*var(--coef));
        height: calc(50px*var(--coef));
        color: #000;
        font-weight: 300;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date:before {
        width: 30px;
        height: 30px;
        text-align: center;
        margin: auto;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date.selected:before {
        background: #000;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__months-view .dudp__month {
        color: #000;
        font-size: calc(20px*var(--coef));
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev,
    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
        font-size: calc(70px*var(--coef));
        font-weight: 300;
        top: calc(10px*var(--coef));
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date.dudp__pm,
    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date.dudp__nm {
        color: #969696;
        opacity: 0;
        visibility: hidden;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-month,
    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-year {
        font-size: calc(27px*var(--coef));
        font-weight: 500;
        text-transform: capitalize;
        color: var(--blue);
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year,
    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays {
        background: transparent;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year .cal-year {
        display: none;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-month-year {
        padding-top: calc(15px*var(--coef));
        padding-bottom: calc(15px*var(--coef));
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__buttons {
        opacity: 0;
        visibility: hidden;
    }

    .cs-mon-calendrier-section {
        padding: calc(25px*var(--coef)) calc(35px*var(--coef));
        background: #FBFBFB;
        box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
        border-radius: calc(15px*var(--coef));
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
    }

    .cs-mon-calendrier-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: calc(15px*var(--coef)) 0;
    }

    .cs-mon-calendrier-section .cs-title {
        padding-bottom: calc(20px*var(--coef));
    }

    #cs-mon-calendrier {
        padding: calc(10px*var(--coef)) calc(30px*var(--coef));
    }

    .cs-mon-calendrier-bottom {
        padding: 0 calc(30px*var(--coef));
    }

    .cs-mon-calendrier-bottom>p {
        max-width: calc(370px*var(--coef));
        width: 100%;
        margin-bottom: calc(10px*var(--coef)) !important;
        display: flex;
    }

    .cs-mon-calendrier-bottom>p>.cs-note-deja {
        background: #fff;
        border: 2px var(--blue) solid;
    }

    .cs-mon-calendrier-bottom>p>.cs-note-nous {
        background: #b3b3b3;
        border: 2px #b3b3b3 solid;
    }

    .cs-mon-calendrier-bottom>p>span:first-child {
        width: calc(28px*var(--coef));
        height: calc(28px*var(--coef));
        display: inline-block;
        border-radius: 50%;
        margin-right: calc(15px*var(--coef));
        position: relative;
        top: 3px;
    }

    .cs-mon-calendrier-bottom>p>span:last-child {
        width: calc(100% - 43px*var(--coef));
    }

    #cs-mon-calendrier .dudp__date.cs-completed:before {
        border: 2px var(--blue) solid;
    }

    #cs-mon-calendrier .dudp__date.cs-none:before {
        border: 2px #b3b3b3 solid;
        background: #b3b3b3;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date.cs-none {
        color: #fff;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev {
        left: 25%;
    }

    #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
        right: 25%;
    }

    @media (max-width:1024px) {
        .cs-mon-calendrier-container {
            max-width: calc(629px*var(--coef));
        }

        .cs-mon-calendrier-section {
            padding: calc(15px*var(--coef));
        }

        .cs-mon-calendrier>p {
            font-size: calc(21px*var(--coef));
            line-height: calc(24px*var(--coef));
        }

        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-prev,
        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__btn-cal-next {
            font-size: calc(34px*var(--coef));
        }

        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__weekdays span {
            font-size: calc(18px*var(--coef));
            width: calc(38px*var(--coef));
            line-height: calc(24px*var(--coef));
        }

        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__calendar .dudp__cal-week .dudp__date {
            width: calc(38px*var(--coef));
            line-height: calc(38px*var(--coef));
            height: calc(38px*var(--coef));
        }
    }

    /*Sélecteur d’horaires*/
    .cs-selecteur-horaires {
        max-width: calc(950px*var(--coef));
        margin: calc(30px*var(--coef)) auto;
        background: #FBFBFB;
        box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
        border-radius: 15px;
        padding: calc(20px*var(--coef)) calc(30px*var(--coef));
    }

    .cs-selecteur-horaires-header>p {
        margin: 0px;
    }

    .cs-selecteur-horaires-header {
        margin-bottom: calc(20px*var(--coef));
        padding-top: calc(20px*var(--coef));
        padding-left: calc(20px*var(--coef));
    }

    .cs-selecteur-horaires-header .cs-current-utc {
        margin-right: 5px;
    }

    .cs-selecteur-horaires-header .cs-utc-list {
        padding-left: 10px;
        padding-right: 10px;
    }

    .cs-selecteur-horaires-header .cs-utc-list p {
        padding: calc(10px*var(--coef)) calc(10px*var(--coef));
        cursor: pointer;
    }

    .cs-selecteur-horaires-header .cs-utc-list p.active {
        background: #ebebeb;
        border-radius: 10px;
    }

    .cs-selecteur-horaires-body {
        display: flex;
        justify-content: space-between;
        flex-wrap: wrap;
        align-items: self-start;
    }

    .cs-selecteur-horaires-timepicker {
        width: 60%;
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
    }

    .cs-selecteur-horaires-options {
        width: 40%;
        position: sticky;
        top: 30%;
        margin-top: 20%;
        margin-bottom: 20%;
    }

    .cs-selecteur-horaires-timepicker {
        width: calc(60% + 30px);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
        margin-left: calc(-15px*var(--coef));
        margin-right: calc(-15px*var(--coef));
    }

    .cs-selecteur-horaires-timepicker .cs-time-sg {
        background: #FBFBFB;
        box-shadow: 0px 3px 35px rgb(0 0 0 / 8%);
        border-radius: 15px;
        width: 100%;
        max-width: calc(105px*var(--coef));
        display: flex;
        justify-content: center;
        align-items: center;
        height: calc(50px*var(--coef));
        margin: calc(20px*var(--coef)) calc(15px*var(--coef));
        cursor: pointer;
    }

    .cs-selecteur-horaires-timepicker .cs-time-sg:hover {
        background: #dfdfdf;
    }

    .cs-selecteur-horaires-timepicker .cs-time-sg.active {
        background: #8FDDFF;
        color: #fff;
    }

    .cs-selecteur-horaires-timepicker .cs-disponible {
        background: var(--blue);
        color: #fff;
    }

    .cs-selecteur-horaires-timepicker .cs-indisponible {
        background: #070707;
        color: #fff;
    }

    .cs-selecteur-horaires-timepicker .cs-occupe {
        background: #FF9800;
        color: #fff;
    }

    .cs-status-options {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .cs-status-option {
        max-width: calc(200px*var(--coef));
        padding: calc(10px*var(--coef));
        width: 100%;
        border-radius: 15px;
        background: transparent;
        transition: 0.2s all;
        display: flex;
        align-items: center;
    }

    .cs-status-option:hover {
        background: #e8e8e8;
    }

    .cs-status-option img {
        width: calc(50px*var(--coef));
        margin-right: calc(30px*var(--coef));
    }

    .cs-status-options>* {
        margin-top: calc(15px*var(--coef));
        margin-bottom: calc(15px*var(--coef));
    }

    .cs-status-delete {
        text-decoration: underline;
    }

    @media (max-width:1024px) {
        .cs-selecteur-horaires {
            max-width: calc(622px*var(--coef));
        }

        .cs-selecteur-horaires-timepicker,
        .cs-selecteur-horaires-options {
            width: 100%;
        }

        .cs-selecteur-horaires-body {
            flex-direction: column-reverse;
        }

        .cs-selecteur-horaires-header,
        .cs-status-options {
            margin-left: calc(15px*var(--coef));
            margin-right: calc(15px*var(--coef));
        }

        .cs-status-options {
            flex-direction: row;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .cs-status-option {
            max-width: calc(130px*var(--coef));
            flex-direction: column;
        }

        .cs-status-options .cs-status-delete {
            width: calc(100px*var(--coef));
            font-size: calc(20px*var(--coef));
            line-height: calc(30px*var(--coef));
        }

        .cs-status-option>span {
            font-size: calc(20px*var(--coef));
            line-height: calc(30px*var(--coef));
        }

        .cs-status-option img {
            margin-right: 0px;
            width: calc(35px*var(--coef));
            margin-bottom: calc(5px*var(--coef));
        }
    }

    @media (max-width:767px) {
        .cs-mon-calendrier-container {
            max-width: calc(330px*var(--coef));
        }

        .cs-mon-calendrier-section {
            flex-wrap: wrap;
        }

        .cs-selecteur-horaires-options {
            position: inherit;
            top: 0px;
            margin-top: 0px;
            margin-bottom: 0px;
        }

        .cs-selecteur-horaires {
            max-width: calc(335px*var(--coef));
            padding: calc(15px*var(--coef)) calc(0px*var(--coef));
        }

        .cs-selecteur-horaires-header {
            margin-bottom: calc(10px*var(--coef));
        }

        .cs-selecteur-horaires-header>p,
        .cs-selecteur-horaires-header .cs-current-utc {
            font-size: calc(18px*var(--coef));
            line-height: calc(27px*var(--coef));
        }

        .cs-status-option img {
            width: calc(30px*var(--coef));
            margin-bottom: calc(2px*var(--coef));
        }

        .cs-status-option>span,
        .cs-status-options .cs-status-delete {
            font-size: calc(14px*var(--coef));
            line-height: calc(21px*var(--coef));
        }

        .cs-status-option {
            max-width: calc(75px*var(--coef));
            background: transparent;
            box-shadow: unset;
        }

        .cs-status-options .cs-status-delete {
            max-width: calc(75px*var(--coef));
        }

        .cs-status-options {
            margin-left: calc(10px*var(--coef));
            margin-right: calc(10px*var(--coef));
        }

        .cs-status-options>* {
            margin-top: calc(10px*var(--coef));
            margin-bottom: calc(10px*var(--coef));
        }

        .cs-selecteur-horaires-timepicker .cs-time-sg {
            font-size: calc(16px*var(--coef));
            line-height: calc(24px*var(--coef));
            max-width: calc(72px*var(--coef));
            margin: calc(5px*var(--coef)) calc(4px*var(--coef));
            height: calc(45px*var(--coef));
        }

        .cs-selecteur-horaires-timepicker {
            padding-left: 5px;
            padding-right: 5px;
        }

        #cs-mon-calendrier .dcalendarpicker .dudp__cal-container .dudp__buttons {
            display: none;
        }
    }
</style>

<div class="">
    <section class="slider-small">
        <div class="title">
            <h1>Mon planning</h1>
        </div>
        <div class="title p-container">
            <p>Veuillez indiquer vos plages horaires de disponibilité pour être consulté.</p>
        </div>
        <div id="tabs_k12" class="cs-mon-calendrier-container _form_input">
            <i id="close-mon-calendrier" class="fa fa-xmark"></i>
            <div class="cs-mon-calendrier-section">
                <div class="mask-div">
                    <div class="title">
                        <h1>Proposer autre date / horaires</h1>
                    </div>
                    <div class="title p-container">
                        <p>Cliquez sur le ou les horaires que vous souhaitez proposer à votre client, il recevra ces informations afin de choisir l'un d'entre eux et vous en serez averti en retour via la page " Mes RDV "</p>
                    </div>
                </div>
                <div class="cs-datetimepicker">
                    <input type="text" id="datepicker4" readonly value="" style="display: none;">
                    <div id="cs-mon-calendrier"></div>
                </div>
            </div>
        </div>
        <div class="second-title">
            <h1> Mes horaires de présence</h1>
            <p>Veuillez cliquer sur vos horaires puis sur le statut bleu, noir ou orange correspondant.</p>
        </div>
    </section>

    <div class="block2">
        <div class="_utilities_tabs" style="opacity: 0;">
            <ul class="frtab_k clearfix">
                <li><a class="tabs_k cs-btn-1 active" rel="tabs_k3">Sélecteur d’horaires</a></li>
            </ul>
        </div>
        <div id="tabs_k3" class="cs-selecteur-horaires _form_input">
            <div class="cs-selecteur-horaires-header">
                <p class="cs-date lh24-36 fw500">11 Mai 2022</p>
                <div class="cs-utc lh20-30">
                    <span class="cs-current-utc">UTC−12:00 Paris</span> <img class="fa-chevron-down fa-angle-down" src="/theme/black_blue/img/planning/chevron.svg" style="z-index: 2">
                    <div class="cs-utc-list">
                        <p class="lh18-27 fw300"><span>UTC-15:00</span> Lille</p>
                        <p class="lh18-27 fw300"><span>UTC-05:00</span> Marseille</p>
                        <p class="lh18-27 fw300"><span>UTC-15:00</span> Lille</p>
                    </div>
                </div>
            </div>
            <div class="cs-selecteur-horaires-body">
                <div class="cs-selecteur-horaires-timepicker">

                    <?php
                    $hours = [
                        '0:00',   '00:15',  '00:30',  '00:45',  '01:00',   '01:15',  '01:30',
                        '01:45',  '02:00',   '02:15',  '02:30',  '02:45',  '03:00',   '03:15',
                        '03:30',  '03:45',  '04:00',   '04:15',  '04:30',  '04:45',  '05:00',
                        '05:15',  '05:30',  '05:45',  '06:00',   '06:15',  '06:30',  '06:45',
                        '07:00',   '07:15',  '07:30',  '07:45',  '08:00',   '08:15',  '08:30',
                        '08:45',  '09:00',   '09:15',  '09:30',  '9:45',  '10:00',  '10:15',
                        '10:30', '10:45', '11:0',  '11:15', '11:30', '11:45', '12:0',
                        '12:15', '12:30', '12:45', '13:00',  '13:15', '13:30', '13:45',
                        '14:0',  '14:15', '14:30', '14:45', '15:0',  '15:15', '15:30',
                        '15:45', '16:0',  '16:15', '16:30', '16:45', '17:00',  '17:15',
                        '17:30', '17:45', '18:0',  '18:15', '18:30', '18:45', '19:0',
                        '19:15', '19:30', '19:45', '20:00',  '20:15', '20:30', '20:45',
                        '21:00',  '21:15', '21:30', '21:45', '22:0',  '22:15', '22:30',
                        '22:45', '23:00',  '23:15', '23:30', '23:45'
                    ]
                    ?>

                    <?php foreach ($hours as $hour) : ?>
                        <div class="cs-time-sg lh24-36"><?php echo $hour; ?></div>
                    <?php endforeach; ?>

                </div>
                <div class="cs-selecteur-horaires-options">
                    <div class="cs-status-options">
                        <a class="cs-status-option" href="javascript:void(0);" data-type="disponible">
                            <img src="/theme/black_blue/img/planning/disponible.png">
                            <span class="lh20-30" style="color:#4CBBEC;">disponible</span>
                        </a>
                        <a class="cs-status-option" href="javascript:void(0);" data-type="indisponible">
                            <img src="/theme/black_blue/img/planning/indisponible.png">
                            <span class="lh20-30" style="color:#070707;">indisponible</span>
                        </a>
                        <a class="cs-status-option" href="javascript:void(0);" data-type="occupe">
                            <img src="/theme/black_blue/img/planning/occupe.png">
                            <span class="lh20-30" style="color:#FF9800;">occupé</span>
                        </a>
                        <a class="cs-status-delete blue2 lh20-30" href="javascript:void(0);">Supprimer mon choix</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<script type="text/javascript">
    jQuery(document).ready(function($) {
        const scroll = new SmoothScroll();
        const isModal = <?= isset($modal) ? 1 : 0 ?>;
        const scheduleData = [{
                date: 1,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 2,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 3,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 4,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 5,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 6,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 7,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 8,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 9,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 10,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 11,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 12,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 13,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 14,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 15,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 16,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 17,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 18,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 19,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 20,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 21,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 22,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 23,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 24,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 25,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 26,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 27,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
            {
                date: 28,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 29,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 30,
                month: 3,
                year: 2023,
                status: 'cs-none'
            },
            {
                date: 31,
                month: 3,
                year: 2023,
                status: 'cs-completed'
            },
        ];
        duDatepicker('#datepicker4', {
            // format: 'mm/dd/yyyy',
            i18n: 'fr',
            auto: true,
            // firstDay: 2,
            events: {
                ready: function() {
                    setTimeout(function() {
                        add_date_status();
                    }, 100);
                    jQuery('#cs-mon-calendrier .dudp__wrapper').prepend('<div class="cs-mon-calendrier-header"></div>');
                    jQuery('#cs-mon-calendrier .dudp__wrapper').append('<div class="cs-mon-calendrier-bottom">' +
                        '<p class="lh20-30 cs-margin-0 fw400" style="color:#787878"><span class="cs-note-deja"></span><span>déjà complétés</span></p>' +
                        '<p class="lh20-30 cs-margin-0 fw400" style="color:#787878"><span class="cs-note-nous"></span><span>nous n\'avons aucune information sur votre emploi du temps ces jours-ci</span></p>' +
                        '</div>');
                    jQuery('#cs-mon-calendrier .dudp__calendar-header').prependTo('#cs-mon-calendrier .cs-mon-calendrier-header');
                    jQuery('#cs-mon-calendrier .dudp__calendar-header').prepend('<div class="cs_dudp__btn-cal-prev" data-status="prev">‹</div>');
                    jQuery('#cs-mon-calendrier .dudp__calendar-header').append('<div class="cs_dudp__btn-cal-next" data-status="next">›</div>');
                    jQuery('#cs-mon-calendrier .dudp__wrapper .cs-mon-calendrier-header').prepend('<p class="lh27-40 fw500 color-black cs-margin-0">Mon Calendrier</p>');

                    jQuery('#cs-mon-calendrier .dudp__cal-container .dudp__btn-cal-prev, #cs-mon-calendrier .dudp__cal-container .dudp__btn-cal-next')
                        .on('click', function() {
                            add_date_status();
                        });
                    jQuery('#cs-mon-calendrier .cs-mon-calendrier-header .cs_dudp__btn-cal-prev, #cs-mon-calendrier .cs-mon-calendrier-header .cs_dudp__btn-cal-next')
                        .on('click', function() {
                            add_date_status();
                            let $this = jQuery(this);
                            let status = $this.data('status');
                            let years = get_year();
                            let current = years.current;
                            if (status == 'prev') current = years.prev;
                            if (status == 'next') current = years.next;
                            jQuery('#cs-mon-calendrier .dudp__year[data-year="' + current + '"]').click();
                        });

                    function get_year() {
                        let current = jQuery('#cs-mon-calendrier .dudp__calendar-header .dudp__sel-year').html();
                        let prev = parseInt(current) - 1;
                        let next = parseInt(current) + 1;
                        return {
                            current: current,
                            prev: prev,
                            next: next
                        };
                    }

                    function add_date_status() {
                        setTimeout(function() {
                            scheduleData.forEach(function(e, i) {
                                jQuery('#cs-mon-calendrier span.dudp__date[data-date="' + e.date + '"][data-month="' + (parseInt(e.month) - 1) + '"][data-year="' + e.year + '"]').addClass(e.status);
                            });
                        }, 100);
                    }
                },
                dateChanged: function(data) {
                    document.querySelector('.cs-selecteur-horaires-header p.cs-date').innerHTML = formatDate(data.date, 'dd mmmm yyyy');
                    if (isModal) {
                        document.querySelector('#tabs_k3').scrollIntoView({
                            behavior: "smooth",
                            scrollTiming: 5000
                        });
                    } else {
                        scroll.animateScroll(document.getElementById('tabs_k3'), null, {
                            duration: 5000
                        });
                    }
                }
            },
            root: '#cs-mon-calendrier'
        })
        duDatepicker('#datepicker4', 'show')

        $('body').on('click', '.cs-selecteur-horaires-timepicker .cs-time-sg', function(e) {
            e.preventDefault();
            $(this).toggleClass('active');
        });

        $('body').on('click', '.cs-status-option', function(e) {
            e.preventDefault();
            let type = $(this).data('type');
            $('.cs-selecteur-horaires-timepicker .cs-time-sg.active').addClass('cs-' + type);
            $('.cs-selecteur-horaires-timepicker .cs-time-sg.active').removeClass('active');
        });

        $('body').on('click', '.cs-status-delete', function(e) {
            e.preventDefault();
            $('.cs-selecteur-horaires-timepicker .cs-time-sg.active').removeClass('active cs-disponible cs-indisponible cs-occupe');
        });
    });

    let arrow = document.querySelector(".fa-chevron-down.fa-angle-down")
    arrow.addEventListener("click", () => {
        arrow.classList.toggle("active")
    })


    function formatDate(date, format) {
        var d = new Date(date),
            day = d.getDate(),
            m = d.getMonth(),
            y = d.getFullYear(),
            i18n = duDatepicker.i18n.fr,
            mVal = m + 1;
        return format.replace(/(yyyy|yy|mmmm|mmm|mm|m|DD|D|dd|d)/g, function(e) {
            switch (e) {
                case 'd':
                    return day;

                case 'dd':
                    return ('00' + day).slice(-2);

                case 'D':
                    return i18n.shortDays[d.getDay()];

                case 'DD':
                    return i18n.days[d.getDay()];

                case 'm':
                    return mVal;

                case 'mm':
                    return ('00' + mVal).slice(-2);

                case 'mmm':
                    return i18n.shortMonths[m];

                case 'mmmm':
                    return i18n.months[m];

                case 'yy':
                    return y.toString().substring(2, 4);

                case 'yyyy':
                    return y;
            }
        });
    }
</script>