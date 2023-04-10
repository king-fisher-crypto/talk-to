<?php
$user = $this->Session->read('Auth.User');
$country_id = $this->Session->read('Config.id_country');
if(!$user){
    ?>
    <div class="consult-prepayed">
        <p class="title"><?=__('avec inscription') ?></p>
        <p class="stitle"><?=__('En créant mon compte') ?></p>
        <ul class="list-unstyled list-inline">
            <li class="filter-icons wow fadeIn" data-wow-delay="0.2s"><label for="sf_media_phone"><img src="/theme/default/img/icons/filter-phone.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par téléphone" data-original-title="agents par Téléphone"></label></li>
            <li class="filter-icons wow fadeIn" data-wow-delay="0.2s"><label for="sf_media_chat"><img src="/theme/default/img/icons/filter-chat.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par tchat" data-original-title="agents par Chat"></label></li>
            <li class="filter-icons wow fadeIn" data-wow-delay="0.2s"><label for="sf_media_email"><img src="/theme/default/img/icons/filter-mail.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par email" data-original-title="agents par E-mail"></label></li>
        </ul>
        <?php if (!empty($phones['CountryLangPhone']['prepayed_phone_number'])): ?>
            <p class=" modal-num"><?=$phones['CountryLangPhone']['prepayed_minute_cost'].$this->Session->read('Config.devise'); ?> /min</p>
        <?php endif; ?>

        <p class="modal_consult_subscribe">
            <a href="/users/subscribe" class="modal_consult_subscribe_btn">&nbsp;</a>
        </p>

        <p class="modal_consult_login"><?=__('Déjà inscrit ? ') ?><a href="/users/login"><?=__('Connectez-vous') ?></a></p>

        <div class="modal_consult_payment">
            <?php
            echo str_replace('cms_text2','', str_replace('cms_container','', $this->FrontBlock->getPageBlocTextebyLang(236,$this->Session->read('Config.id_lang'))));
            ?>
        </div>

    </div>
    <?php if (!empty($phones['CountryLangPhone']['surtaxed_phone_number'])): ?>
        <div class="consult-sep"><span><?=__('ou') ?></span></div>
        <div class="consult-audiotel">
            <p class="title"><?=__('sans inscription') ?></p>
            <p class="stitle"><?=__('En appel surtaxé') ?></p>
            <ul class="list-unstyled list-inline">
                <li class="filter-icons wow fadeIn" data-wow-delay="0.2s"><label for="sf_media_phone"><img src="/theme/default/img/icons/filter-phone.png" data-toggle="tooltip" data-placement="top" title="" alt="Spiriteo - agents par téléphone" data-original-title="agents par Téléphone"></label>
            </ul>
            <?php if (!empty($phones['CountryLangPhone']['surtaxed_phone_number'])): ?>
                <p class=" modal-num"><?=$phones['CountryLangPhone']['surtaxed_minute_cost'].$this->Session->read('Config.devise'); ?> /min</p>
                <div class="modal-consult-btn">
                    <div class="btn-call">
                        <span class="btn-data"><?php if (!empty($phones['CountryLangPhone']['third_phone_number'])) echo __('Depuis tél. résidentiel'); else echo   __('Appelez le'); ?></span><br />
                        <span class="btn-num"><a title="<?=$phones['CountryLangPhone']['surtaxed_phone_number'] ?>" href="tel:<?=$phones['CountryLangPhone']['surtaxed_phone_number'] ?>" class="popupphone2"><?=$phones['CountryLangPhone']['surtaxed_phone_number'] ?></a>
					</span><br />
                        <span class="btn-data"><?=__('Entrez le code expert : ') ?><i><?=$agent['User']['agent_number']; ?></i></span>
                    </div>
                </div>
            <?php endif;  ?>

            <?php if (!empty($phones['CountryLangPhone']['third_phone_number'])): ?>
                <p class=" modal-num">Ou</p>
                <div class="modal-consult-btn">
                    <div class="btn-call">
                        <span class="btn-data"><?=__('Depuis un mobile') ?></span><br />
                        <span class="btn-num"><a title="<?=$phones['CountryLangPhone']['third_phone_number'] ?>" href="tel:<?=$phones['CountryLangPhone']['third_phone_number'] ?>" class="popupphone3"><?=$phones['CountryLangPhone']['third_phone_number'] ?></a>
					</span><br />
                        <span class="btn-data"><?=__('Entrez le code expert : ') ?><i><?=$agent['User']['agent_number']; ?></i></span>
                    </div>
                </div>
            <?php endif;  ?>

            <?php
            if($country_id == 4){
                ?>
                <br /><div style="font-size:12px;padding-bottom: 10px;"><?php echo __('Interdit au - de 18 ans'); ?> </div>
                <?php
            }
            ?>
        </div>
    <?php endif; ?>
<?php }else{ ?>
    <div class="consult-connected">
        <div class="select_content">
            <div class="modal_consult_select">
				<span class="youare"><?php
                    if($country_id == 5 || $country_id == 13)
                        echo __('Vous êtes au :');
                    else
                        echo __('Vous êtes en :');
                    ?></span>
                <div class="dropdown mobile-flag">
                    <?php
                    switch ($country_id) {
                        case 1:
                            echo '<a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents"><img src="/theme/default/img/flag/france.png" alt="Drapeau">&nbsp;</a>';
                            break;
                        case 3:
                            echo '<a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents"><img src="/theme/default/img/flag/suisse.png" alt="Drapeau">&nbsp;</a>';
                            break;
                        case 4:
                            echo '<a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents"><img src="/theme/default/img/flag/belgique.png" alt="Drapeau">&nbsp;</a>';
                            break;
                        case 5:
                            echo '<a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents"><img src="/theme/default/img/flag/luxembourg.png" alt="Drapeau">&nbsp;</a>';
                            break;
                        case 13:
                            echo '<a href="#" class="dropdown-toggle main-drop" data-toggle="dropdown" title="agents"><img src="/theme/default/img/flag/canada.png" alt="Drapeau">&nbsp;</a>';
                            break;
                    }


                    foreach($all_phones as $pp){

                        if($pp['CountryLangPhone']['country_id'] == 1)
                            $phone_fr = $pp['CountryLangPhone']['prepayed_phone_number'];
                        if($pp['CountryLangPhone']['country_id'] == 3)
                            $phone_su = $pp['CountryLangPhone']['prepayed_phone_number'];
                        if($pp['CountryLangPhone']['country_id'] == 4)
                            $phone_be = $pp['CountryLangPhone']['prepayed_phone_number'];
                        if($pp['CountryLangPhone']['country_id'] == 5)
                            $phone_lu = $pp['CountryLangPhone']['prepayed_phone_number'];
                        if($pp['CountryLangPhone']['country_id'] == 13)
                            $phone_ca = $pp['CountryLangPhone']['prepayed_phone_number'];
                    }


                    ?>

                    <ul class="dropdown-menu">
                        <li class="li_flag france <?php if($country_id == 1) echo 'hide'; ?>"><a href="#" title="agents France" rel="1" phone="<?=$phone_fr  ?>" country="france" youare="<?=__('Vous êtes en :') ?>"><span class="desk-flag"><img src="/theme/default/img/flag/france.png" alt="Drapeau France"></span> Fr</a></li>
                        <li class="li_flag  suisse<?php if($country_id == 3) echo 'hide'; ?>"><a href="#" title="agents Suisse" rel="3" phone="<?=$phone_su  ?>" country="suisse"youare="<?=__('Vous êtes en :') ?>"><span class="desk-flag"><img src="/theme/default/img/flag/suisse.png" alt="Drapeau Suisse"></span> Su</a></li>
                        <li class="li_flag belgique<?php if($country_id == 4) echo 'hide'; ?>"><a href="#" title="agents Belgique" rel="4" phone="<?=$phone_be  ?>" country="belgique"youare="<?=__('Vous êtes en :') ?>"><span class="desk-flag"><img src="/theme/default/img/flag/belgique.png" alt="Drapeau Belgique"></span> Be</a></li>
                        <li class="li_flag luxembourg<?php if($country_id == 5) echo 'hide'; ?>"><a href="#" title="agents Luxembourg" rel="5" phone="<?=$phone_lu  ?>" country="luxembourg"youare="<?=__('Vous êtes au :') ?>"><span class="desk-flag"><img src="/theme/default/img/flag/luxembourg.png" alt="Drapeau Luxembourg"></span> Lu</a></li>
                        <li class="li_flag canada<?php if($country_id == 13) echo 'hide'; ?>"><a href="#" title="agents Canada" rel="13" phone="<?=$phone_ca  ?>" country="canada" youare="<?=__('Vous êtes au :') ?>"><span class="desk-flag"><img src="/theme/default/img/flag/canada.png" alt="Drapeau Canada"></span> Ca</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <p class="stitle"><?=__('En appel prépayé') ?></p>
        <ul class="modal_consult_step">
            <li><span class="li_num">1</span><?=__('Appelez le (appel local) :') ?><br /><span class="num_dynamic"><?=$phones['CountryLangPhone']['prepayed_phone_number'] ?></span></li>
            <li><span class="li_num">2</span><?=__('Saisissez votre code personnel :') ?><br /><span><?=$user['personal_code'] ?></span></li>
            <li><span class="li_num li_big">3</span><?=__('Entrez le code expert de ') ?><br /><span class="bg"><?=$agent['User']['pseudo'] ?> : </span><span><?=$agent['User']['agent_number'] ?></span></li>
        </ul>
        <input type="hidden" name="to"  value="<?= $agent['User']['phone_api_use'] ?>">
        <input type="hidden" name="name"  value="<?= $user['pseudo'] ?: $user['firstname'] ?>">
        <button class="btn btn-pink btn-2 modal_consult_btn_phone_call num_link_dynamic popupphone1" type="button" id="call-voip"><?=__('Call VOIP') ?></button>
<!--        <button class="btn btn-call btn-2 modal_consult_btn_phone_call num_link_dynamic popupphone1" type="button" id="call-phone">--><?//=__('Call Phone') ?><!--</button>-->
        <button class="btn btn-pink btn-2 modal_consult_btn_phone_call num_link_dynamic popupphone1" type="button" style="display: none" id="hangup" ><?=__('Hang Up') ?></button>
        <div class="status-voip"></div>
    </div>

<?php } ?>