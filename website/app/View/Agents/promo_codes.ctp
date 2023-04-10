<?php
echo $this->Html->script('/theme/black_blue/js/wissem_promo_agent_script', array('block' => 'script'));
echo $this->Session->flash();
$pourcentages = ['5%','10%','15%','20%','25%','30%'];
$delai = ['24&nbsp;h','48&nbsp;h','72&nbsp;h','7&nbsp;j','15&nbsp;j','30&nbsp;j'];
$clients = [
    [
        "lorem Ipsuem",
        "12/08/22 21:50:22"
    ],
    [
        "lorem Ipsuem",
        ""
    ],
    [
        "lorem Ipsuem",
        "12/08/22 21:50:22"
    ],
    [
        "lorem Ipsuem",
        ""
    ],
    [
        "lorem Ipsuem",
        "12/08/22 21:50:22"
    ],
    [
        "lorem Ipsuem",
        ""
    ],
    [
        "lorem Ipsuem",
        "12/08/22 21:50:22"
    ],
    [
        "lorem Ipsuem",
        ""
    ],
    
   
]
?>

<section class="page promo_codes-page">
    <article class="marge">
        <div class="header">
            <h1 class="">  <?= __('Code promo clients') ?></h1>
            <p>
                <?= __('Configurez vous même les codes promo envoyés à vos clients en choisissant le pourcentage, les outils et le délai de validité de cette offre promotionnelle.') ?>
            </p>
            <p>
                <?= __('1 envoi par semaine et par client maximum. <br>Code promo envoyé valable 1 fois.') ?>
            </p>
        </div>
    <div class="promo_code_container">
        <div class="card">
            <div class="block">
                <p><?= __('Choisissez le pourcentage de remise') ?></p>
                <div class="options_groups pourcentage">
                    <?php foreach ($pourcentages as $p) : ?>
                        <button><?php echo $p ?></button>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="block">
                <p><?= __('Choisissez le délai de validité') ?></p>
                <div class="options_groups delai">
                    <?php foreach ($delai as $d) : ?>
                        <button><?php echo $d ?></button>
                    <?php endforeach ?>
                </div>
            </div>
            <div class="block">
                <p><?= __("Privilégiez les délais courts pour favoriser l'engagement de vos clients, 48h par exemple.") ?></p>
            </div>
        </div>
        <div class="card">
            <div class="block">
                <p><?= __('Choisissez le ou les modes sur lesquels appliquer cette réduction') ?></p>
                <div class="options_groups grid mode">
                    <div class="mode_block">
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/tel_bleu.svg' alt="">
                            </div>
                            <label>Tel</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/chat.png' alt="">
                            </div>
                            <label>Chat</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/webcam2.png' alt="">
                            </div>
                            <label>Webcam</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/sms.png' alt="">
                            </div>
                            <label>SMS</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/email.png' alt="">
                            </div>
                            <label>Email</label>
                        </div>
                    </div>
                    <div class="mode_block">
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/mc.png' alt="">
                            </div>
                            <label>MasterClass</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/video.png' alt="">
                            </div>
                            <label>Video à la demande</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/photo.png' alt="">
                            </div>
                            <label>Photo à la demande</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/dPDF.png' alt="">
                            </div>
                            <label>Document-Pdf</label>
                        </div>
                    </div>
                    <div class="mode_block">
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/vf.png' alt="">
                            </div>
                            <label>Vidéos formation</label>
                        </div>
                        <div class="option">
                            <div class="icon">
                                <img src='/theme/black_blue/img/promo_code_agent/cp.png' alt="">
                            </div>
                            <label style="width: min-content;">Contenus privés</label>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
        <div class="promo-code_search">
            <div class="search_container">
                <div class="search_modal">
                    <div class="search_input">
                        <img src="\theme\black_blue\img\promo_code_agent\icon_chercher_chercher_default.svg" alt="">
                        <input type="text" name="" id="search" placeholder="<?= __('Client') ?>">
                    </div>
                    <div class="search_suggestions hidden">
                        <div class="clients_suggestions">
                            <p>Wissem Chihaoui</p>
                            <p>Wissem Chihaoui</p>
                        </div>
                    </div>
                </div>
                <button><?= __('chercher') ?></button>
            </div>
            
        </div>
        <div class="card" style="margin-top: calc(50px*var(--coef));">
            <?php if($clients==[]):
                
            ?>
            <label for='no-clients_warning' class="warning"><?= __("Vous n'avez pas des clients!") ?></label>
            <?php else :
                
            ?>
                <table class="stries">
                    <thead>
                        <tr> 
                            <th>Client</th>
                            <th>Dernier envoi <br />depuis 7 jours</th>
                            <th>
                                <div class="checkbox-wrapper-19">
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box select-all"  onclick=" $(this).toggleClass('checked');">
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody> 
                        <?php foreach ($clients as $client): ?>
                            <tr class="
                                <?php 
                                if($client[1]!==""){
                                    echo("unavailable");
                                }else{
                                    echo("available");
                                }
                                ?>
                                "
                            >
                                <td><?php echo $client[0]; ?></td>
                                <td><?php echo $client[1]; ?></td>
                                <td>
                                <div class="checkbox-wrapper-19" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="<?php
                                    echo("check-box check ");
                                        if($client[1]!==""){
                                            echo("disabled");
                                        }else{
                                            echo("abled");
                                        }
                                        
                                        ?>"
                                    onclick=" $(this).toggleClass('checked');">
                                </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
          <?php endif; ?>
        </div>
        <div class="submitBtn">
            <button>
                Envoyer
            </button>
        </div>
        <p>
                <?= __("En validant cet envoi, les clients sélectionnés recevront un email automatique leur indiquant que vous en êtes l'émetteur et contenant un code promo, le pourcentage et le délai de validité de cette offre.") ?>
        </p>
    </div>
    </article>
</section>