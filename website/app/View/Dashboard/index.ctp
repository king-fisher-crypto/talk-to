<?php
$message =  "<div class='message-title'>" .__("Voulez vous supprimer ce LiviMaster de vos Favoris?") . "</div>"; // on peut injecter du HTML
$this->set('message', $message);
$this->set('class', 'custom-modal');

echo $this->element('Utils/modal-confirmation');
?>

<?php
echo $this->Html->css(
    '/theme/black_blue/css/home.css' . "?a=" . rand(),
    null,
    array('inline' => false)
);
echo $this->Html->css(
    '/theme/black_blue/css/owl.carousel/owl.carousel.css',
    null,
    array('inline' => false)
);
echo $this->Html->css(
    '/theme/black_blue/css/owl.carousel/owl.theme.black_blue.css' . "?a=" . rand(),
    null,
    array('inline' => false)
);
echo $this->Html->script('/theme/black_blue/js/owl.carousel/owl.carousel.js' . "?a=" . rand());
?>
<?php
$datas = [
    [
        'name' => 'Liam Williams',
        'img' => 'favourite1',
    ],
    [
        'name' => 'John Smith',
        'img' => 'favourite2',
    ],
    [
        'name' => 'Olivia Jones',
        'img' => 'favourite3',
    ],
    [
        'name' => 'Oliver Williams',
        'img' => 'favourite4',
    ],
    [
        'name' => 'John Smith',
        'img' => 'favourite2',
    ],
    [
        'name' => 'Liam Williams',
        'img' => 'favourite1',
    ],
];
?>

<?php
$datas_tablette = [
    [
        'name' => 'Liam Williams',
        'img' => 'favourite1',
    ],
    [
        'name' => 'John Smith',
        'img' => 'favourite2',
    ],
    [
        'name' => 'Olivia Jones',
        'img' => 'favourite3',
    ],
    [
        'name' => 'Liam Williams',
        'img' => 'favourite1',
    ],
    [
        'name' => 'John Smith',
        'img' => 'favourite2',
    ],
    [
        'name' => 'Olivia Jones',
        'img' => 'favourite3',
    ]

]
?>

<section class="dashboard-client-page">
    <h1 class="title">Dashboard</h1>
    <div class="dashboard-links-container">
        <a href="#">Créditer mon compte $</a>
        <a href="#" class="hidden-tablet-mobile">Trouver un LiviMaster</a>
        <a href="#">
            Programme d'Affiliation
            <span>gagner de l'argent en faisant connaitre Livitalk</span>
        </a>
        <a href="#">Questions Fréquentes</a>
    </div>
    <div class="dashboard-cards-container">
        <h2>Mes favoris</h2>
        <div class="dashboard-cards owl-carousel carousel_categories owl-theme">
            <?php foreach ($datas as $data) : ?>

                <div class="dashboard-card">
                    <img src="/theme/black_blue/img/dashboard-client/<?= $data['img'] ?>.avif" alt="Photo de profil" class="profile-img">
                    <btn class="btn-close">
                        <img src="/theme/black_blue/img/dashboard-client/icon-close.svg" alt="Close">
                    </btn>
                    <div class="card-text">
                        <p><strong><?= $data['name'] ?></strong></p>
                        <p>Catégorie Lorem Ipsum</p>
                        <div class="contact-icons">
                            <img src="/theme/black_blue/img/medias/tel2.svg" alt="Tel">
                            <img src="/theme/black_blue/img/medias/chat2.svg" alt="Chat">
                            <img src="/theme/black_blue/img/medias/webcam2.svg" alt="Webcam">
                            <img src="/theme/black_blue/img/medias/sms2.svg" alt="Message">
                            <img src="/theme/black_blue/img/medias/email2.svg" alt="Email">
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
        <div class="dashboard-cards tablette">
            <?php foreach ($datas_tablette as $data) : ?>

                <div class="dashboard-card">
                    <img src="/theme/black_blue/img/dashboard-client/<?= $data['img'] ?>.avif" alt="Photo de profil" class="profile-img">
                    <btn class="btn-close">
                        <img src="/theme/black_blue/img/dashboard-client/icon-close.svg" alt="Close">
                    </btn>
                    <div class="card-text">
                        <p><strong><?= $data['name'] ?></strong></p>
                        <p>Catégorie Lorem Ipsum</p>
                        <div class="contact-icons">
                            <img src="/theme/black_blue/img/medias/tel2.svg" alt="Tel">
                            <img src="/theme/black_blue/img/medias/chat2.svg" alt="Chat">
                            <img src="/theme/black_blue/img/medias/webcam2.svg" alt="Webcam">
                            <img src="/theme/black_blue/img/medias/sms2.svg" alt="Message">
                            <img src="/theme/black_blue/img/medias/email2.svg" alt="Email">
                        </div>
                    </div>
                </div>

            <?php endforeach; ?>
        </div>
        <div class="dashboard-cards-container">
            <h2>Vous aimerez aussi</h2>
            <div class="dashboard-cards owl-carousel carousel_categories owl-theme">
                <?php foreach ($datas as $data) : ?>

                    <div class="dashboard-card">
                        <img src="/theme/black_blue/img/dashboard-client/<?= $data['img'] ?>.avif" alt="Photo de profil" class="profile-img">
                        <btn class="btn-close">
                            <img src="/theme/black_blue/img/dashboard-client/icon-close.svg" alt="Close">
                        </btn>
                        <div class="card-text">
                            <p><strong><?= $data['name'] ?></strong></p>
                            <p>Catégorie Lorem Ipsum</p>
                            <div class="contact-icons">
                                <img src="/theme/black_blue/img/medias/tel2.svg" alt="Tel">
                                <img src="/theme/black_blue/img/medias/chat2.svg" alt="Chat">
                                <img src="/theme/black_blue/img/medias/webcam2.svg" alt="Webcam">
                                <img src="/theme/black_blue/img/medias/sms2.svg" alt="Message">
                                <img src="/theme/black_blue/img/medias/email2.svg" alt="Email">
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
            <div class="dashboard-cards tablette">
                <?php foreach ($datas_tablette as $data) : ?>

                    <div class="dashboard-card">
                        <img src="/theme/black_blue/img/dashboard-client/<?= $data['img'] ?>.avif" alt="Photo de profil" class="profile-img">
                        <btn class="btn-close">
                            <img src="/theme/black_blue/img/dashboard-client/icon-close.svg" alt="Close">
                        </btn>
                        <div class="card-text">
                            <p><strong><?= $data['name'] ?></strong></p>
                            <p>Catégorie Lorem Ipsum</p>
                            <div class="contact-icons">
                                <img src="/theme/black_blue/img/medias/tel2.svg" alt="Tel">
                                <img src="/theme/black_blue/img/medias/chat2.svg" alt="Chat">
                                <img src="/theme/black_blue/img/medias/webcam2.svg" alt="Webcam">
                                <img src="/theme/black_blue/img/medias/sms2.svg" alt="Message">
                                <img src="/theme/black_blue/img/medias/email2.svg" alt="Email">
                            </div>
                        </div>
                    </div>

                <?php endforeach; ?>
            </div>
        </div>

</section>

<script type="text/javascript">
    const closeBtns = document.querySelectorAll('.dashboard-card .btn-close');
    closeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            $("#modal-confirmation").modal();
        });
    })

    window.onload = function() {

        function home_start() {
            console.log("home_start");
            let items_car1 = 6;
            let margin_car1 = 15

            let nav1 = true;
            let dots1 = false;

            let w = getDocWidth() // - 40;
            let support_type

            if (w > 1024) // PC
                support_type = "pc"
            else // MOBILE
                if (w < 768)
                    support_type = "mobile"
            else // TABLETTE
                support_type = "tablet"


            switch (support_type) {
                case "mobile":
                    items_car1 = 2;
                    nav1 = false;
                    dots1 = false;
                    margin_car1 = 4;
                    break;
                case "tablet":
                    items_car1 = 3;

                    break;
            }

            $('.carousel_categories').owlCarousel('destroy');
            $('.carousel_categories').owlCarousel({
                margin: margin_car1,
                center: false,
                loop: true,
                autoWidth: false,
                navText: ['<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">', '<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">'],
                mouseDrag: true,
                touchDrag: true,
                nav: nav1,
                dots: dots1,
                checkVisibility: false,
                items: items_car1
            })
        }

        $(window).resize(function() {
            console.log("resize");

            home_start()
        })

        home_start()

    }; // fin  DOMContentLoaded
</script>