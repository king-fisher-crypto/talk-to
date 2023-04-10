<div class="oeuvres_caritatives_bg">
    <section class="oeuvres_caritatives">
        <div class="container alignC">
            <div class="header">
                <h1>
                    <?= __('Nous contacter ?') ?>
                </h1>
                <p>
                    <?= __("Avant d'écrire au support, consultez la page ") ?>
                    <a href="#"><?= __(' "Questions fréquentes"') ?></a>
                </p>
            </div>

                <form action="">
                    <div class="form_container">
                        <div class="input_group">
                            <input type="text" placeholder="Titre">
                        </div>
                        <div class="input_group">
                            <input type="text" placeholder="Nom">
                        </div>
                        <div class="input_group">
                            <input type="text" placeholder="Prénom">
                        </div>
                        <div class="input_group">
                            <input type="mail" placeholder="Votre Mail">
                        </div>
                        <div class="input_group">
                            <textarea placeholder="Votre Message"></textarea>
                        </div>
                        <div class="attachFile">
                            <span>Joindre Un Fichier</span>
                            <button>Choisir</button>
                        </div>
                        <div class="robotCheckContainer">
                            <div class="robotCheck">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  onclick=" $(this).toggleClass('checked');">
                                </div>
                                <span><?= __('Je ne suis pas un robot', null, true) ?></span>
                                <img src="/theme/black_blue/img/robotCheck.png" alt="">
                            </div>
                        </div>
                    </div>
                    <button>ENVOYER</button>
                </form>

        </div>
    </section>
</div>