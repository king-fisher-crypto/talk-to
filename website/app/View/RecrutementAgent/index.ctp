<?php
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
<script src="https://cdn.jsdelivr.net/npm/smooth-scroll/dist/smooth-scroll.polyfills.min.js"></script>

<style>
    footer {
        border-top: 2px solid var(--blue);
        box-shadow: 0px 5px calc(35px * var(--coef)) rgba(255, 255, 255, 0.15);
        border-radius: calc(65px * var(--coef)) calc(65px * var(--coef)) 0px 0px;
    }

    @media screen and (max-width: 500px) {
        body {
            overflow-x: hidden;
        }
    }
</style>
<section class="recrutement-page utilities">
    <div class="mobile-header">
        <h1>
            <span> LIVITALK</span> : La 1ère MarketPlace de communication
            en étant rémunéré
        </h1>
        <p class="client-livitalk">
            Vos clients ou Followers achètent du temps et vous contactent via Livitalk sans avoir vos coordonnées.
        </p>
    </div>
    <div class="recrutement-header">

        <div class="text-container">
            <h1>
                <span> LIVITALK</span> : La 1ère MarketPlace de communication
                en étant rémunéré
            </h1>
            <p class="client-livitalk">
                Vos clients ou Followers achètent du temps et vous contactent via Livitalk sans avoir vos coordonnées.
            </p>
            <div class="options">
                <div class="option option1">
                    <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">
                    <p>Choisissez vos tarifs de communication.</p>
                </div>
                <div class="option option2">
                    <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">
                    <p>Diffusez le lien de votre page personnelle LiviTalk auprès de vos clients ou sur vos réseaux
                        sociaux.</p>
                </div>
                <div class="option option2">
                    <img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">
                    <p>Activez des outils facultatifs : Videos-formation, MasterClass/Visio-conférence, Contenus à la
                        demande et Сontenus privés.</p>
                </div>
            </div>
            <div class="links-container">
                <button>Devenir LiviMaster</button>
                <a href="#">Comment ça marche</a>
            </div>
        </div>

        <div class="img-container">
            <img class="group-img" src="/theme/black_blue/img/recruit/Group.png">
            <div class="bleu-icon-container">
                <div class="icon scroller" data-target="block-communication">
                    <img src="/theme/black_blue/img/medias/tel.svg" class="sm:hidden" alt="">
                    <img src="/theme/black_blue/img/medias/tel2.svg" class="md:hidden" alt="">
                    <p class="md:hidden">Tel</p>
                </div>
                <div class="icon scroller" data-target="block-communication">
                    <img src="/theme/black_blue/img/medias/chat.svg" class="sm:hidden" alt="">
                    <img src="/theme/black_blue/img/medias/chat2.svg" class="md:hidden" alt="">
                    <p class="md:hidden">Chat</p>
                </div>
                <div class="icon scroller" data-target="block-communication">
                    <img src="/theme/black_blue/img/medias/webcam.svg" class="sm:hidden" alt="">
                    <img src="/theme/black_blue/img/medias/webcam2.svg" class="md:hidden" alt="">
                    <p class="md:hidden">Webcam</p>

                </div>
                <div class="icon scroller" data-target="block-communication">
                    <img src="/theme/black_blue/img/medias/sms.svg" class="sm:hidden" alt="">
                    <img src="/theme/black_blue/img/medias/sms2.svg" class="md:hidden" alt="">
                    <p class="md:hidden">Sms</p>

                </div>
                <div class="icon scroller" data-target="block-communication">
                    <img src="/theme/black_blue/img/medias/email.svg" class="sm:hidden" alt="">
                    <img src="/theme/black_blue/img/medias/email2.svg" class="md:hidden" alt="">
                    <p class="md:hidden">Email</p>

                </div>
            </div>
            <div class="icon-option-container">
                <div class="option-text">
                    <p>Activez en option</p>
                </div>
                <div class="last-icon">
                    <div class="icon scroller" data-target="block-communication">
                        <img src="/theme/black_blue/img/medias/video_clap.svg" alt="">
                        <p>Formations Vidéos</p>
                    </div>
                    <div class="icon scroller" data-target="block-communication">
                        <img src="/theme/black_blue/img/medias/masterclass.svg" alt="">
                        <p>Masterclass</p>
                    </div>
                    <div class="icon scroller" data-target="block-communication">
                        <img src="/theme/black_blue/img/medias/picture.svg" alt="">
                        <p class="contenu">Contenus à la demande</p>
                    </div>
                    <div class="icon scroller" data-target="block-communication">
                        <img src="/theme/black_blue/img/medias/prives.png" alt="">
                        <p>Contenus privés</p>
                    </div>
                </div>

            </div>
        </div>

    </div>


    <div class="chevron-white first-chevron scroller" data-target="block-communication">
        <img src="/theme/black_blue/img/recruit/white-icon_arrow.svg" alt="">
    </div>
    <div class="livitalk-info mobile">
        <img class="visible-img" src="/theme/black_blue/img/recruit/Group2.png" alt="">
        <div class="description">
            <h2>Peu importe ton sujet d’expertise, ton univers, tes réseaux sociaux de prédilection ou la taille de ta
                communauté,
                tous les profils sont invités à s’inscrire sur <span>LiviTalk</span>.</h2>

            <img class="hidden-img" src="/theme/black_blue/img/recruit/1.png" alt="">

            <p>
                Tu es Influenceur, Micro-influenceur, Instagrameur, Tiktokeur ou simplement professionnel et
                expert dans un domaine et souhaite partager ton expérience ?<span> LIVITALK</span> te permet d’être mis
                en relation avec tes clients ou ta communauté en étant rémunéré pour ton temps de présence, tes
                échanges, tes formations,
                ou simplement en discutant et en partageant ta vie et ton expérience !
            </p>
        </div>
    </div>
    <div class="communications" id="block-communication">
        <h2>Modes de Communication</h2>
        <div class="bleu-icon-container">
            <div class="icon">
                <img src="/theme/black_blue/img/medias/tel.svg" class="communication_img sm:hidden" alt="">
                <img src="/theme/black_blue/img/medias/tel2.svg" class="communication_img md:hidden" alt="">
                <p class="md:hidden">Tel</p>
            </div>
            <div class="icon">
                <img src="/theme/black_blue/img/medias/chat.svg" class="communication_img sm:hidden" alt="">
                <img src="/theme/black_blue/img/medias/chat2.svg" class="communication_img md:hidden" alt="">
                <p class="md:hidden">Chat</p>
            </div>
            <div class="icon">
                <img src="/theme/black_blue/img/medias/webcam.svg" class="communication_img sm:hidden" alt="">
                <img src="/theme/black_blue/img/medias/webcam2.svg" class="communication_img md:hidden" alt="">
                <p class="md:hidden">Webcam</p>

            </div>
            <div class="icon">
                <img src="/theme/black_blue/img/medias/sms.svg" class="communication_img sm:hidden" alt="">
                <img src="/theme/black_blue/img/medias/sms2.svg" class="communication_img md:hidden" alt="">
                <p class="md:hidden">Sms</p>

            </div>
            <div class="icon">
                <img src="/theme/black_blue/img/medias/email.svg" class="communication_img sm:hidden" alt="">
                <img src="/theme/black_blue/img/medias/email2.svg" class="communication_img md:hidden" alt="">
                <p class="md:hidden">Email</p>

            </div>

        </div>
        <p class="options mt-10">5 Options disponibles pour être contacté en étant rémunéré, Téléphone, Chat, Webcam,
            SMS et Email.<a class="popup voir-plus" onclick="myFunction()"> Voir +</a>
        </p>
    </div>
    <div class="popup-div">
        <div class="container-communication">
            <img src="/theme/black_blue/img/recruit/pop-img.png" class="hidden desktop-img"alt="">
            <img src="/theme/black_blue/img/recruit/2.png" class="hidden mobile-img" alt="">
            <div class="communication-mode">
                <h2>Modes de Communication</h2>
                <p>Quelle que soit votre activité, discuter avec vous à une valeur.
                    Choisissez vos tarifs et vos Clients et Followers achètent du temps de consultation pour vous contacter
                    via LiviTalk par Téléphone, Chat, Webcam, SMS ou Email. Ces derniers n’auront jamais vos coordonnées
                    personnelles. Activez vos modes de consultation quand vous le souhaitez, un seul mode, deux modes, Trois
                    modes ou tous les modes en même temps, vous êtes totalement libre de choisir quand vous vous connectez.
                    Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
                </p>
            </div>
        </div>

        <div class="close-btn">
            <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
        </div>
    </div>
    <div class="optional-function">
        <div class="card-text">
            <p>+ 5 fonctionnalités Optionnelles</p>
        </div>
        <div class="cards">
            <div class="card">
                <div class="card-icon">
                    <img src="/theme/black_blue/img/medias/video_clap.svg" alt="">
                    <h3>Formations Videos</h3>
                </div>

                <div class="card-description">
                    <p>Créez vos formations <br> vidéos et monétisez vos <br> connaissances</p>
                    <a class="voir-plus video" onclick="video_popup()">Voir +</a>
                </div>

            </div>

            <div class="mobile-popup video">
                <div class="container-communication">
                    <img src="/theme/black_blue/img/recruit/youtube.png" alt="">
                    <div class="communication-mode">
                        <h2>Formations Vidéos</h2>
                        <p> Vous avez une expertise dans un domaine et souhaitez vendre vos connaissances ?
                            Réalisez vos vidéos Formation, professionnelles ou amateurs, choisissez votre prix de vente,
                            vous aurez la possibilité de faire une vidéo de présentation accessible à tous pour
                            expliquer le contenu de votre formation. Une fois acheté vos clients accèderont à vos Vidéos
                            Formation dans leur compte personnel LiviTalk. Vous pouvez réaliser autant d’heures que vous
                            le souhaitez et faire évoluer vos Vidéos Formation dans le temps.
                            Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
                        </p>
                    </div>
                </div>

                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>

            <div class="card">
                <div class="card-icon">
                    <img src="/theme/black_blue/img/medias/masterclass.svg" alt="">
                    <h3>MasterClass </h3>
                </div>
                <div class="card-description">
                    <p>Décuplez votre activité en <br> organisant des cessions <br class='tb:hidden'> groupées par <br class='tb:hidden'> Visioconférence</p>
                    <a class="voir-plus master" onclick="masterClass_popup()">Voir +</a>
                </div>
            </div>
            <div class="mobile-popup masterClass">
                <div class="container-communication">
                    <img src="/theme/black_blue/img/recruit/masterClass.png" alt="">
                    <div class="communication-mode">
                        <h2>MASTERCLASS / VISIOCONFERENCES</h2>
                        <p>
                            Quelle que soit votre activité, vous avez la possibilité de décupler
                            vos revenus en réunissant vos clients ou Followers en une seule cession.
                            LiviTalk vous permet de programmer les dates de vos futures MasterClass et
                            Visionconférences, vos clients paient le montant que vous avez déterminé et reçoivent
                            automatiquement un lien de connexion à l’heure et date convenue. Pour en savoir plus
                            inscrivez-vous et devenez LiviMaster dès maintenant !
                        </p>
                    </div>
                </div>

                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <img src="/theme/black_blue/img/medias/picture.svg" alt="">
                    <h3>Contenus à la demande</h3>
                </div>
                <div class="card-description">
                    <p>Développez vos revenus <br> grâce aux Vidéos et <br>Photos à la demande</p>
                    <a class="voir-plus contenu" onclick="contenu_popup()">Voir +</a>
                </div>
            </div>

            <div class="mobile-popup contenus">
                <div class="container-communication">
                    <img src="/theme/black_blue/img/recruit/contenu.png" class="hidden mobile-img"   alt="">

                    <div class="communication-mode">
                        <h2>Contenus à la demande</h2>
                        <p>
                            Vos Fans et Followers ont parfois envie d’une vidéo ou d’une photo qui leur seraient
                            spécialement dédiées. LiviTalk vous donne la possibilité de choisir un tarif ou de laisser
                            vos Followers détailler leur demande vous permettant ainsi de leur proposer un tarif adapté.
                            Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
                        </p>
                    </div>
                </div>

                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>

            <div class="tablette-popup video">
                <img src="/theme/black_blue/img/recruit/youtube.png" alt="">
                <div class="communication-mode">
                    <h2>Formations Vidéos</h2>
                    <p> Vous avez une expertise dans un domaine et souhaitez vendre vos connaissances ?
                        Réalisez vos vidéos Formation, professionnelles ou amateurs, choisissez votre prix de vente,
                        vous aurez la possibilité de faire une vidéo de présentation accessible à tous pour expliquer le
                        contenu de votre formation. Une fois acheté vos clients accèderont à vos Vidéos Formation dans
                        leur compte personnel LiviTalk. Vous pouvez réaliser autant d’heures que vous le souhaitez et
                        faire évoluer vos Vidéos Formation dans le temps.
                        Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
                    </p>
                </div>
                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>

            </div>

            <div class="tablette-popup masterClass">
                <img src="/theme/black_blue/img/recruit/masterClass.png" alt="">
                <div class="communication-mode">
                    <h2>MASTERCLASS / VISIOCONFERENCES</h2>
                    <p>
                        Quelle que soit votre activité, vous avez la possibilité de décupler
                        vos revenus en réunissant vos clients ou Followers en une seule cession.
                        LiviTalk vous permet de programmer les dates de vos futures MasterClass et Visionconférences,
                        vos clients paient le montant que vous avez déterminé et reçoivent automatiquement un lien de
                        connexion à l’heure et date convenue. Pour en savoir plus inscrivez-vous et devenez LiviMaster
                        dès maintenant !
                    </p>
                </div>
                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>

            <div class="tablette-popup contenus">
                <img src="/theme/black_blue/img/recruit/contents.png"  alt="">
                <div class="communication-mode">
                    <h2>Contenus à la demande</h2>
                    <p>
                        Vos Fans et Followers ont parfois envie d’une vidéo ou d’une photo qui leur seraient
                        spécialement dédiées. LiviTalk vous donne la possibilité de choisir un tarif ou de laisser vos
                        Followers détailler leur demande vous permettant ainsi de leur proposer un tarif adapté. Pour en
                        savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
                    </p>
                </div>
                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>
            <div class="card">
                <div class="card-icon">
                    <img src="/theme/black_blue/img/medias/pdf.svg" alt="">
                    <h3>Documents PDF</h3>
                </div>
                <div class="card-description">
                    <p>Vendez des documents ou <br> formations aux format <br> lecture</p>
                    <a class="voir-plus document" onclick="document_popup()">Voir +</a>
                </div>
            </div>




            <div class="mobile-popup documents ">
                <div class="container-communication">
                    <img src="/theme/black_blue/img/recruit/pdf.png" alt="">
                    <div class="communication-mode">
                        <h2>Documents PDF</h2>
                        <p>
                            Vous souhaitez vendre vos connaissances au format « Document » ? LiviTalk vous permet de
                            choisir votre tarif. Une fois acheté, vos clients accèderont à ce document dans leur compte
                            personnel. Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
                        </p>
                    </div>
                </div>

                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>


            <div class="card">
                <div class="card-icon">
                    <img src="/theme/black_blue/img/medias/prives.png" alt="">
                    <h3>Contenus privés</h3>
                </div>
                <div class="card-description">
                    <p>Partagez des Photos et <br> Videos privées par <br> abonnement avec votre <br> communauté</p>
                    <a class="voir-plus prive" onclick="prives_popup()">Voir +</a>
                </div>
            </div>
            <div class="mobile-popup prives">
                <div class="container-communication">
                    <div class="group-img">
                        <img class="prive-first-img hidden desktop-img" src="/theme/black_blue/img/recruit/Group4.png" alt="">
                        <img class="prive-second-img hidden desktop-img" src="/theme/black_blue/img/recruit/Group5.png"  alt="">
                        <img src="/theme/black_blue/img/recruit/mobile-group.png"  class="hidden mobile-img" alt="">

                    </div>
                    <div class="communication-mode">
                        <h2>Contenus privés</h2>
                        <p>
                            Sur LiviTalk, vous avez la possibilité de proposer des contenus Vidéos et Photos privés,
                            seuls les Clients et Followers qui optent pour un abonnement mensuel dont vous déterminez le
                            tarif, pourront y accéder . Pour en savoir plus inscrivez-vous et devenez LiviMaster dès
                            maintenant !
                        </p>
                    </div>
                </div>

                <div class="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>
        </div>

    </div>




    <div class="popup-div formation-video">
        <img src="/theme/black_blue/img/recruit/youtube.png" alt="">
        <div class="communication-mode">
            <h2>Formations Vidéos</h2>
            <p> Vous avez une expertise dans un domaine et souhaitez vendre vos connaissances ?
                Réalisez vos vidéos Formation, professionnelles ou amateurs, choisissez votre prix de vente, vous aurez
                la possibilité de faire une vidéo de présentation accessible à tous pour expliquer le contenu de votre
                formation. Une fois acheté vos clients accèderont à vos Vidéos Formation dans leur compte personnel
                LiviTalk. Vous pouvez réaliser autant d’heures que vous le souhaitez et faire évoluer vos Vidéos
                Formation dans le temps.
                Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
            </p>
        </div>
        <div class="close-btn">
            <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
        </div>
    </div>

    <div class="popup-div master-class">
        <img src="/theme/black_blue/img/recruit/masterClass.png" alt="">
        <div class="communication-mode">
            <h2>MASTERCLASS / VISIOCONFERENCES</h2>
            <p>
                Quelle que soit votre activité, vous avez la possibilité de décupler
                vos revenus en réunissant vos clients ou Followers en une seule cession.
                LiviTalk vous permet de programmer les dates de vos futures MasterClass et Visionconférences, vos
                clients paient le montant que vous avez déterminé et reçoivent automatiquement un lien de connexion à
                l’heure et date convenue. Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
            </p>
        </div>
        <div class="close-btn">
            <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
        </div>
    </div>
    <div class="popup-div contenus">
        <img src="/theme/black_blue/img/recruit/contents.png" alt="">
        <div class="communication-mode">
            <h2>Contenus à la demande</h2>
            <p>
                Vos Fans et Followers ont parfois envie d’une vidéo ou d’une photo qui leur seraient spécialement
                dédiées. LiviTalk vous donne la possibilité de choisir un tarif ou de laisser vos Followers détailler
                leur demande vous permettant ainsi de leur proposer un tarif adapté. Pour en savoir plus inscrivez-vous
                et devenez LiviMaster dès maintenant !
            </p>
        </div>
        <div class="close-btn">
            <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
        </div>
    </div>


    <div class="popup-div documents">
        <img src="/theme/black_blue/img/recruit/pdf.png" alt="">
        <div class="communication-mode">
            <h2>Documents PDF</h2>
            <p>
                Vous souhaitez vendre vos connaissances au format « Document » ? LiviTalk vous permet de choisir votre
                tarif. Une fois acheté, vos clients accèderont à ce document dans leur compte personnel. Pour en savoir
                plus inscrivez-vous et devenez LiviMaster dès maintenant !
            </p>
        </div>
        <div class="close-btn">
            <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
        </div>
    </div>



    <div class="popup-div prives">
        <div class="group-img">
            <img class="prive-first-img" src="/theme/black_blue/img/recruit/Group4.png" alt="">
            <img class="prive-second-img" src="/theme/black_blue/img/recruit/Group5.png" alt="">
        </div>
        <div class="communication-mode">
            <h2>Contenus privés</h2>
            <p>
                Sur LiviTalk, vous avez la possibilité de proposer des contenus Vidéos et Photos privés, seuls les
                Clients et Followers qui optent pour un abonnement mensuel dont vous déterminez le tarif, pourront y
                accéder . Pour en savoir plus inscrivez-vous et devenez LiviMaster dès maintenant !
            </p>
        </div>
        <div class="close-btn">
            <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
        </div>
    </div>

    <div class="chevron-white functional scroller" data-target="categories_livi">
        <img src="/theme/black_blue/img/recruit/white-icon_arrow.svg" alt="">
    </div>

    <div class="livitalk-info socials">
        <div class="share-text">
            <h3>
                PARTAGEZ LE LIEN DE VOTRE PAGE PERSONNELLE <span> LIVITALK</span> EN
                EN-TETE DE VOS RESEAUX SOCIAUX !
            </h3>
        </div>
        <div class="group-img-container">
            <img src="/theme/black_blue/img/recruit/Group3.png" alt="">
        </div>
        <div class="all-networks">
            <img src="/theme/black_blue/img/recruit/all_social_networks.png" alt="">
        </div>


    </div>
    <div class="categories" id="categories_livi">

        <div class="category-title">
            Toutes Les Catégories Peuvent S'incrire Sur LiviTalk, Ajoutez La Votre !
        </div>
        <div class="all-categories">

        </div>
    </div>
    <?php
    $categories_ar = [
        "Influenceurs",
        "Professeur de langue",
        "Instagrameur",
        "Micros-influenceurs",
        "Designer",
        "Professeur de Musique",
        "Youtubeurs",
        "Sportifs",
        "Nutritionniste",
        "Crypto-influenceurs",
        "Coach Sportif",
        "Trader",
        "Psychologues",
        "Coach de vie",
        "TikTokeur",
        "Influenceurs",
        "Professeur de langue",
        "Instagrameur",
        "Micros-influenceurs",
        "Designer",
        "Professeur de Musique",
        "Youtubeurs",
        "Sportifs",
        "Nutritionniste",
        "Crypto-influenceurs",
        "Coach Sportif",
        "Trader",
        "Psychologues",
        "Coach de vie",
        "TikTokeur"
    ];
    ?>

    <div class="carousel_categories_container">
        <div class="owl-carousel carousel_categories owl-theme">
            <?php
            $i = 0;
            foreach ($categories_ar as $categ) {
                $i++;
                if ($i == 1) {
                    echo "<div class='categories owl_col'>";
                }

                echo "<div class='category black '>$categ</div>";

                if ($i == 3) {
                    echo "</div>";
                    $i = 0;
                }
            }
            ?>
        </div>
    </div>
    <div class="salary-container">
        <div class="salary-img">
            <h1>Estimez vos revenus</h1>
            <p class="first-paragraph">Sélectionner votre nombre de Clients ou Followers </p>
            <div class="range-slider">
                <div class="container-slider">
                    <form class="range">
                        <div class="range-wrapper">
                            <div class="form-group range__slider">
                                <input type="range" step="500">
                            </div><!--/form-group-->
                            <div class="marker marker-0 quarters">1000</div>
                            <div class="marker marker-5"></div>
                            <div class="marker marker-10"></div>
                            <div class="marker marker-15"></div>
                            <div class="marker marker-20"></div>
                            <div class="marker marker-25 quarters">250 000</div>
                            <div class="marker marker-30"></div>
                            <div class="marker marker-35"></div>
                            <div class="marker marker-40"></div>
                            <div class="marker marker-45"></div>
                            <div class="marker marker-50 quarters">500 000</div>
                            <div class="marker marker-55"></div>
                            <div class="marker marker-60"></div>
                            <div class="marker marker-65"></div>
                            <div class="marker marker-70"></div>
                            <div class="marker marker-75 quarters">750 000</div>
                            <div class="marker marker-80"></div>
                            <div class="marker marker-85"></div>
                            <div class="marker marker-90"></div>
                            <div class="marker marker-95"></div>
                            <div class="marker marker-100 quarters">1000 000</div>
                        </div>

                        <div class="form-group range__value">
                            <label>Loan Amount</label>
                            <span></span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="salary-estimation">
                <div class="salary-range">
                    <p>Modes de communication</p>
                    <p>Revenu estimé = entre <span> 0$</span> et <span>0$</span> par mois*</p>
                </div>
                <div class="salary-range">
                    <p>+Outils optionnels</p>
                    <p>Revenu estimé = entre <span>0$</span> et <span>0$</span> par mois*</p>
                </div>
            </div>
        </div>
        <p class="last-paragraph">*Estimation moyenne des revenus mensuels, variables
            en fonction de votre activité et options proposées : Hors commission site</p>
    </div>
    <div class="chevron-white functional scroller" data-target="profil_block"> <img src="/theme/black_blue/img/recruit/white-icon_arrow.svg" alt=""></div>
    <div class="affiliation">
        <h1 class="title">
            4 Programmes d'affiliation pour tous
        </h1>

        <div class="generate-salary-container">
            <div class="generate-salary first-child">
                <div class="badge">1</div>
                <h2>GAGNEZ<span> 10% </span> <br class="mobile-device">DES REVENUS GÉNÉRÉS </h2>
                <p>En faisant connaître LiviTalk à de futurs LiviMasters qui s'inscriront grâce à vous,
                    vous gagnez 10% de ce qu'ils gagnent.</p>
            </div>
            <div class="generate-salary">
                <div class="badge">2</div>
                <h2>GAGNEZ<span> 50% </span> <br class="mobile-device">DES REVENUS GÉNÉRÉS </h2>
                <p>En faisant connaître LiviTalk à de futurs Ambassadeurs, vous gagnez 50% de ce qu'ils
                    gagnent lorsqu'ils recrutent de futurs LiviMasters.</p>
            </div>

        </div>
        <div class="generate-salary-container">
            <div class="generate-salary first-child">
                <div class="badge">3</div>
                <h2>GAGNEZ<span> 300$ </span> <br class="mobile-device">DES REVENUS GÉNÉRÉS </h2>
                <p>En faisant la promotion des vidéos formation d'autres LiviMasters
                    auprès de votre communauté, de vos proches ou de vos clients.
                </p>
            </div>
            <div class="generate-salary mb-10">
                <div class="badge">4</div>
                <h2>GAGNEZ<span> 90$</span> <br class="mobile-device">DES REVENUS GÉNÉRÉS </h2>
                <p>En faisant la promotion des MasterClass/Visioconférence d'autres LiviMasters auprès de votre
                    communauté, de vos proches ou de vos clients.
                </p>
            </div>

        </div>

        <p class="affiliation-link"><a href="#">Voir les programmes d'affiliation en détail</a><img src="/theme/black_blue/img/recruit/icon_arrow.svg"></p>
    </div>
    <div class="user-profil">
        <h1 id="profil_block">EXEMPLE PROFILS LIVIMASTERS</h1>
        <p> Nous avons fait une sélection de profils présents sur <span> LiviTalk </span> vous permettant de comprendre
            les
            différentes options proposées par d'autres LiviMasters.</p>

        <div class="owl-carousel carousel_fiche owl-theme">

            <div class=" profil profil-card1">
                <div class="info">
                    <h1>
                        Liam williams
                    </h1>
                    <h4>Micros-influenceurs</h4>
                </div>
            </div>
            <div class="profil profil-card2">
                <div class="info">
                    <h1>
                        John Smith
                    </h1>
                    <h4>Youtubeurs</h4>
                </div>
            </div>
            <div class="profil profil-card3">
                <div class="info">
                    <h1>
                        Olivia Jones
                    </h1>
                    <h4>Designer</h4>
                </div>
            </div>
            <div class="profil profil-card4">
                <div class="info">
                    <h1>
                        Oliver Williams
                    </h1>
                    <h4>Crypto-influenceurs</h4>
                </div>
            </div>

            <div class=" profil profil-card1">
                <div class="info">
                    <h1>
                        Liam williams
                    </h1>
                    <h4>Micros-influenceurs</h4>
                </div>
            </div>
            <div class="profil profil-card2">
                <div class="info">
                    <h1>
                        John Smith
                    </h1>
                    <h4>Youtubeurs</h4>
                </div>
            </div>
            <div class="profil profil-card3">
                <div class="info">
                    <h1>
                        Olivia Jones
                    </h1>
                    <h4>Designer</h4>
                </div>
            </div>
            <div class="profil profil-card4">
                <div class="info">
                    <h1>
                        Oliver Williams
                    </h1>
                    <h4>Crypto-influenceurs</h4>
                </div>
            </div>


            <div class=" profil profil-card1">
                <div class="info">
                    <h1>
                        Liam williams
                    </h1>
                    <h4>Micros-influenceurs</h4>
                </div>
            </div>
            <div class="profil profil-card2">
                <div class="info">
                    <h1>
                        John Smith
                    </h1>
                    <h4>Youtubeurs</h4>
                </div>
            </div>
            <div class="profil profil-card3 ">
                <div class="info">
                    <h1>
                        Olivia Jones
                    </h1>
                    <h4>Designer</h4>
                </div>
            </div>
            <div class="profil profil-card4">
                <div class="info">
                    <h1>
                        Oliver Williams
                    </h1>
                    <h4>Crypto-influenceurs</h4>
                </div>
            </div>


            <div class=" profil profil-card1 ">
                <div class="info">
                    <h1>
                        Liam williams
                    </h1>
                    <h4>Micros-influenceurs</h4>
                </div>
            </div>
            <div class="profil profil-card2 ">
                <div class="info">
                    <h1>
                        John Smith
                    </h1>
                    <h4>Youtubeurs</h4>
                </div>
            </div>
            <div class="profil profil-card3 ">
                <div class="info">
                    <h1>
                        Olivia Jones
                    </h1>
                    <h4>Designer</h4>
                </div>
            </div>
            <div class="profil profil-card4">
                <div class="info">
                    <h1>
                        Oliver Williams
                    </h1>
                    <h4>Crypto-influenceurs</h4>
                </div>
            </div>

            <div class=" profil profil-card1">
                <div class="info">
                    <h1>
                        Liam williams
                    </h1>
                    <h4>Micros-influenceurs</h4>
                </div>
            </div>
            <div class="profil profil-card2">
                <div class="info">
                    <h1>
                        John Smith
                    </h1>
                    <h4>Youtubeurs</h4>
                </div>
            </div>
            <div class="profil profil-card3">
                <div class="info">
                    <h1>
                        Olivia Jones
                    </h1>
                    <h4>Designer</h4>
                </div>
            </div>
            <div class="profil profil-card4">
                <div class="info">
                    <h1>
                        Oliver Williams
                    </h1>
                    <h4>Crypto-influenceurs</h4>
                </div>
            </div>
        </div>

    </div>

    <div class="sign-in">
        <button> S’inscrire et Devenir LiviMaster</button>
    </div>

    <div class="footer-chevron chevron-white scroller" data-target="body">
        <img src="/theme/black_blue/img/recruit/chevron_up_arrow.svg" alt="">
    </div>
</section>
<script>
    $('.recrutement-header').parents('section').parents('main').parents('body').eq(0).addClass('bgblack');

    $('.mobile-header').parents('section').parents('main').parents('body').eq(0).addClass('bgblack');
    var slideIndex = 1;

    class Slider {
        constructor(rangeElement, valueElement, options) {
            this.rangeElement = rangeElement
            this.valueElement = valueElement
            this.options = options

            // Attach a listener to "change" event
            this.rangeElement.addEventListener('input', this.updateSlider.bind(this))
        }

        // Initialize the slider
        init() {
            this.rangeElement.setAttribute('min', options.min)
            this.rangeElement.setAttribute('max', options.max)
            this.rangeElement.value = options.cur

            this.updateSlider()
        }

        // Format the money
        asMoney(value) {
            return '$' + parseFloat(value)
                .toLocaleString('en-US', {
                    maximumFractionDigits: 2
                })
        }

        generateBackground(rangeElement) {
            if (this.rangeElement.value === this.options.min) {
                return
            }

            let percentage = (this.rangeElement.value - this.options.min) / (this.options.max - this.options.min) * 100
            return 'background: linear-gradient(to right, var(--blue) ' + (percentage < 30 ? percentage + 1 : percentage) + '%, #d9d9d9 ' + percentage + '%)'
        }

        updateSlider(newValue) {
            this.valueElement.innerHTML = this.asMoney(this.rangeElement.value)
            this.rangeElement.style = this.generateBackground(this.rangeElement.value)
        }
    }

    let rangeElement = document.querySelector('.range [type="range"]')
    let valueElement = document.querySelector('.range .range__value span')

    let options = {
        min: 0,
        max: 1000000,
        cur: 0
    }

    if (rangeElement) {
        let slider = new Slider(rangeElement, valueElement, options)

        slider.init()
    }





    window.onload = function() {

        function initialize() {
            let items_car1 = 5;
            let items_car2 = 4;
            let margin_car1 = 1
            let margin_car2 = 10

            let nav1 = true;
            let dots1 = false;


            //let support_type = device_type();

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
                    items_car2 = 2;
                    nav1 = true;
                    dots1 = true;
                    margin_car1 = 4;
                    margin_car2 = 10
                    break;
                case "tablet":
                    items_car1 = 4;
                    items_car2 = 4;
                    margin_car2 = 13
                    break;
            }
            $('.carousel_categories').owlCarousel('destroy');

            switch (support_type) {
                case "mobile":
                    items_car1 = 2;
                    items_car2 = 2;
                    nav1 = true;
                    dots1 = false;
                    margin_car1 = 4;
                    margin_car2 = 10
                    break;
                case "tablet":
                    items_car1 = 4;
                    items_car2 = 4;
                    margin_car2 = 13
                    break;
            }

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
                responsive: {

                    1000: {
                        items: items_car1,
                        nav: true,
                        loop: false
                    }
                },

                checkVisibility: false,
                items: items_car1
            })

            $('.carousel_fiche').owlCarousel('destroy');
            $('.carousel_fiche').owlCarousel({
                margin: margin_car2,
                center: false,
                loop: true,
                autoWidth: false,
                navText: ['<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">', '<img class="chevron" src="/theme/black_blue/img/menu/chevron.svg">'],
                mouseDrag: true,
                touchDrag: true,
                nav: false,
                dots: true,
                responsive: {
                    0: {

                        nav: false,
                        dotsEach: 4,
                    },

                },
                checkVisibility: false,
                items: items_car2
            })

        }

        $(window).resize(function() {
            initialize()
        })

        initialize()

    }

    window.addEventListener('resize', () => {
        let popups = document.querySelectorAll(".popup-div")
        popups.forEach(el => el.classList.remove("show"))
        let tablette_popups = document.querySelectorAll(".tablette-popup")
        tablette_popups.forEach(el => el.classList.remove("show"))
        let mobile_popups = document.querySelectorAll(".mobile-popup")
        mobile_popups.forEach(el => el.classList.remove("show"))
        let voir = document.querySelectorAll(".voir-plus")
        voir.forEach(el => el.style.visibility = "visible")
    })

    function myFunction() {
        let popups = document.querySelectorAll(".popup-div")
        popups.forEach(el => el.classList.remove("show"))
        let popups_tablette = document.querySelectorAll(".tablette-popup")
        popups_tablette.forEach(el => el.classList.remove("show"))
        let popups_mobile = document.querySelectorAll(".mobile-popup")
        popups_mobile.forEach(el => el.classList.remove("show"))
        let voir = document.querySelectorAll(".voir-plus")
        voir.forEach(el => el.style.visibility = "visible")
        let link = document.querySelector(".popup")
        let popupDiv = document.querySelector(".popup-div");
        link.style.visibility = "hidden"
        popupDiv.classList.toggle("show")


    }


    let closeBtns = document.querySelectorAll(".close-btn-icon");
    closeBtns.forEach(el => el.addEventListener("click", (e) => {
        e.target.parentElement.parentElement.classList.toggle("show")
        let voir = document.querySelectorAll(".voir-plus")
        voir.forEach(el => el.style.visibility = "visible")
    }))


    let prives = document.querySelector(".popup-div.formation-video")

    function video_popup() {
        let width = window.innerWidth;
        if (width > 1024) {
            let popups = document.querySelectorAll(".popup-div")
            popups.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".popup-div.formation-video")
            let link = document.querySelector(".voir-plus.video")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"

        } else if (width > 728) {
            let popups = document.querySelectorAll(".tablette-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let tab_pop_video = document.querySelector(".tablette-popup.video");
            tab_pop_video.classList.toggle("show")
            let link = document.querySelector(".voir-plus.video")
            link.style.visibility = "hidden"
        } else {
            let popups = document.querySelectorAll(".mobile-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".mobile-popup.video")
            let link = document.querySelector(".voir-plus.video")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        }

    }


    function masterClass_popup() {
        let width = window.innerWidth ;
        if (width > 1024) {
            let popups = document.querySelectorAll(".popup-div")
            popups.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let masterDiv = document.querySelector(".popup-div.master-class")
            let link = document.querySelector(".voir-plus.master")
            masterDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        } else if (width > 728) {
            let popups = document.querySelectorAll(".tablette-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let tab_pop_masterClass = document.querySelector(".tablette-popup.masterClass");
            tab_pop_masterClass.classList.toggle("show")
            let link = document.querySelector(".voir-plus.master")
            link.style.visibility = "hidden"
        } else {
            let popups = document.querySelectorAll(".mobile-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".mobile-popup.masterClass")
            let link = document.querySelector(".voir-plus.master")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        }

    }

    function contenu_popup() {
        let width = window.innerWidth;
        if (width > 1024) {
            let popups = document.querySelectorAll(".popup-div")
            popups.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let contenuDiv = document.querySelector(".popup-div.contenus")
            let link = document.querySelector(".voir-plus.contenu")
            contenuDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        } else if (width > 728) {

            let popups = document.querySelectorAll(".tablette-popup")
            popups.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            voir.forEach(el => el.style.visibility = "visible")
            let tab_pop_masterClass = document.querySelector(".tablette-popup.contenus");
            tab_pop_masterClass.classList.toggle("show")
            let link = document.querySelector(".voir-plus.contenu")
            link.style.visibility = "hidden"
        } else {
            let popups = document.querySelectorAll(".mobile-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".mobile-popup.contenus")
            let link = document.querySelector(".voir-plus.contenu")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        }
    }


    function document_popup() {
        let width = screen.width;
        if (width > 700) {
            let popups = document.querySelectorAll(".popup-div")
            popups.forEach(el => el.classList.remove("show"))
            let popups_tablette = document.querySelectorAll(".tablette-popup")
            popups_tablette.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let masterDiv = document.querySelector(".popup-div.documents")
            let link = document.querySelector(".voir-plus.document")
            masterDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        } else {
            let popups = document.querySelectorAll(".mobile-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".mobile-popup.documents")
            let link = document.querySelector(".voir-plus.document")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        }

    }

    function prives_popup() {
        let width = screen.width;
        if (width > 700) {
            let popups = document.querySelectorAll(".popup-div")
            popups.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            let popups_tablette = document.querySelectorAll(".tablette-popup")
            popups_tablette.forEach(el => el.classList.remove("show"))
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".popup-div.prives")
            let link = document.querySelector(".voir-plus.prive")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        } else {
            let popups = document.querySelectorAll(".mobile-popup")
            popups.forEach(el => el.classList.remove("show"))
            let popups_desktop = document.querySelectorAll(".popup-div")
            popups_desktop.forEach(el => el.classList.remove("show"))
            let voir = document.querySelectorAll(".voir-plus")
            voir.forEach(el => el.style.visibility = "visible")
            let videoDiv = document.querySelector(".mobile-popup.prives")
            let link = document.querySelector(".voir-plus.prive")
            videoDiv.classList.toggle("show")
            link.style.visibility = "hidden"
        }

    }
    /*
        function closePopup() {
         
         let closePop = document.querySelector(".close-btn-icon");
         console.log("mdd")
         closePop.parentElement.parentElement.classList.toggle("show")
      

     } */

     function setupScroller(chevronClass) {
        // Get all the chevron icons
        const chevronIcons = document.querySelectorAll("." + chevronClass);

        // Create a new instance of Smooth Scroll
        const scroll = new SmoothScroll();

        // Loop through each chevron icon
        for (var i = 0; i < chevronIcons.length; i++) {
            const chevronIcon = chevronIcons[i];

            // Get the ID of the target element from the data-target attribute
            const targetId = chevronIcon.getAttribute("data-target");

            // Get the target element
            const targetElement = targetId === 'body' ? document.body : document.getElementById(targetId);

            // Add a click event listener to the chevron icon
            chevronIcon.addEventListener("click", function() {
                // Scroll to the element using Smooth Scroll
                scroll.animateScroll(targetElement, null, {duration: 2000});
            });
        }
    }

    setupScroller('scroller')
</script>