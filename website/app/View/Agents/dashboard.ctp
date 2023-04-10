<?php
$message =  __("<div class='message-title'> Vous pourrez supprimer ce bloc/message une fois que vous aurez cliqué sur chaque lien d'information </div>"); // on peut injecter du HTML
$this->set('message', $message);
$this->set('valider', __('j’ai compris'));
$this->set('class', 'custom-modal');
$this->set('id', 'modal-warning');
echo $this->element('Utils/modal-confirmation');

$links = [
    [
        "question" => "Paramétrer mon profil LiviMaster",
        "link"  => "#",
    ],
    [
        "question" => "Choisir les tarifs de mes prestations",
        "link"  => "#",
    ],
    [
        "question" => "Quels outils optionnels me propose LiviTalk?",
        "link"  => "#",
    ],
    [
        "question" => "Comment développer mon activité sur Livitalk?",
        "link"  => "#",
    ],
    [
        "question" => "Comment être payé par Livitalk?",
        "link"  => "#",
    ],
    [
        "question" => "Comment certifier mon profil?",
        "link"  => "#",
    ]
];
?>

<style>
    @media screen and (max-width: 1024px) {

        #modal-warning.custom-modal,
        #modal-appointment-confirmation.custom-modal {
            width: 50%;
        }
    }
</style>


<section class="dashboard-page">
    <div class="title">
        <h1>Dashboard <span class="hidden" style="margin-left: 5px;"> LiviMaster</span>
        </h1>
    </div>
    <?php if (count($links) > 0) : ?>
        <div class="notification-container">
            <div class="notification-block__header">
                <h2>Nouveau sur <span style="color: #4CBBEC;">Livitalk</span>? <span> <?= count($links) ?> pages à lire avant de vous lancer.</span></h2>
                <div class="close-btn-container" id="close-btn">
                    <img src="/theme/black_blue/img/recruit/close.svg" class="close-btn-icon">
                </div>
            </div>
            <table class="notification-block__table">
                <?php foreach ($links as $link) : ?>
                    <tr>
                        <td>
                            <div class="checkbox-wrapper-19">
                                <input type="checkbox" />
                                <label for="cbtest-19" class="check-box check abled" id="header-checkbox" onclick=" $(this).toggleClass('checked');">
                            </div>
                        </td>
                        <td class="second-child">
                            <label for="lien1">
                                <a href="<?= $link["question"] ?>"><?= $link["question"] ?></a>
                            </label>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
    <?php endif; ?>
    <div class="dashboard-options">
        <a href="/account/profil" class="dashboard-options__item">
            <h2>Mon Profil</h2>
        </a>
        <a href="#" class="dashboard-options__item">
            <h2>Outils en options</h2>
            <p>pour developper<br>votre activité</p>
        </a>
        <a href="/sponsorship/parrainage" class="dashboard-options__item">
            <h2>Programme<br>d'Affiliation</h2>
            <p>gagner de l'argent en<br>faisant connaître LiviTalk</p>
        </a>
        <a href="/agents/comment_communication" class="dashboard-options__item">
            <h2>Aide & Support</h2>
            <p>comment mieux<br>développer mon activité</p>
        </a>
        <a href="/agents/rates" class="dashboard-options__item">
            <h2>Mes Tarifs</h2>
        </a>
        <a href="/agents/planning" class="dashboard-options__item">
            <h2>Mon Agenda</h2>
        </a>
        <a href="#" class="dashboard-options__item">
            <h2>Voir ma page <br>publique</h2>
        </a>
        <a href="/agents/history" class="dashboard-options__item">
            <h2>Mes modes de<br> communication</h2>
        </a>
        <div class="dashboard-options__item-empty">
            <h1></h1>
        </div>
    </div>
</section>


<script type="text/javascript">
    let isAllLinkChecked;
    var checkboxes = document.querySelectorAll('label.check-box');

    function checkedStatus() {
        isAllLinkChecked = true
        var checkboxes = document.querySelectorAll('label.check-box');
        for (var i = 0; i < checkboxes.length; i++) {
            if (!checkboxes[i].classList.contains('checked')) {
                isAllLinkChecked = false;
            }
        }
    }
    let doc = document.querySelector('body');

    $("#close-btn").click(function() {
        checkedStatus();
        if (!isAllLinkChecked) {
            $("#modal-warning").modal()
        } else {
            document.querySelector('.notification-container').style.display = 'none';
        }
    });

    function showConfirmationModal() {
        $("#modal-confirmation").modal();
    }
</script>