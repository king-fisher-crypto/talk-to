<?php 
echo $this->Html->script('/theme/black_blue/js/wissem_promo_agent_script', array('block' => 'script'));
?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/i18n.js'); ?>
<?= $this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?'); ?>
<?php //$this->Html->script('/theme/black_blue/js/calendar/duDatepicker.js?a='.rand()); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker.css'); ?>
<?php //$this->Html->css('/theme/black_blue/css/calendar/duDatepicker.min.css'); ?>
<?= $this->Html->css('/theme/black_blue/css/calendar/duDatepicker-theme.css'); ?>
<?php
$message = __("Confirmez-vous l'annulation de cette opération ?");
$this->set('message', $message);
echo $this->element('Utils/modal-confirmation');
?>

<section class="page agent_oeuvres_caritatives">
    <article class="marge">
        <div class="header">
            <h1><?= __('Reverser une partie de mes gains à des oeuvres caritatives') ?></h1>
            <p>
                <?= __("Vous avez la possibilité de reverser vos gains sur une période déterminée ou permanente concernant vos consultations par Téléphone, Chat, Webcam, Sms ou Email. Ce type d'opération peut avoir un impact bénéfique sur votre votre image auprès de vos clients ou followers. Une bannière sera affichée sur votre page pour en avertir ceux qui vous consulteront. ") ?>
            </p>
        </div>
        <div class="agent_oeuvres_caritatives_container">
            <div class="card">
                <div class="card_section">
                    <p class="text_blue">
                        <?= __("Je souhaite que les gains de mes prestations par Téléphone, Chat, Webcam, Sms et Email soient reversés à des oeuvres caritatives, en activant cette fonction je valide ce choix et LiviTalk sera dans l'obligation de reverser ces fonds à des oeuvres caritatives pour les dates indiquées par mes soins ci-dessous. Une attestation de reversion sera mise à disposition par LiviTalk.") ?>
                    </p>
                </div>
                <div class="card_section">
                    <p>
                        <?= __("Dates validité de cette opération: ") ?>
                    </p>
                    <div class="agenda_input" id="btn_datepicker">
                        <img src="/theme/black_blue/img/agenda.png" alt="">
                        <span>01/04/22</span>-<span>24/04/22</span>
                        
                    </div>
                </div>
                <div class="card_section">
                    <p>
                        <?= __("Sélectionnez la ou les associations auxquelles vous souhaitez reverser vos gains sur la période indiquée.") ?>
                    </p>
                </div>
                <div class="card_section">
                <div class="oeuvres_container">
                    <div class="oeuvre">
                        <div class="checkbox_ouevre">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  >
                                </div>
                        </div>
                        <img src="/theme/black_blue/img/oeuvres/stc.png" alt="Save The Children" style="--w:198px">
                    </div>
                    <div class="oeuvre">
                        <div class="checkbox_ouevre">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  onclick=" $(this).toggleClass('checked');">
                                </div>
                        </div>
                        <img src="/theme/black_blue/img/oeuvres/fcc.png" alt="Fondation Contre le Cancer" style="--w:251px">
                    </div>
                    <div class="oeuvre">
                        <div class="checkbox_ouevre">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  onclick=" $(this).toggleClass('checked');">
                                </div>
                        </div>
                        <img src="/theme/black_blue/img/oeuvres/msf.png" alt="Medicines Sans Frontieres" style="--w:241px">
                    </div>
                    <div class="oeuvre">
                        <div class="checkbox_ouevre">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  onclick=" $(this).toggleClass('checked');">
                                </div>
                        </div>
                        <img src="/theme/black_blue/img/oeuvres/unicef.png" alt="UNICEF" style="--w:236px">
                    </div>
                    <div class="oeuvre">
                        <div class="checkbox_ouevre">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  onclick=" $(this).toggleClass('checked');">
                                </div>
                        </div>
                        <img src="/theme/black_blue/img/oeuvres/wwf.png" alt="WWF" style="--w:160px">
                    </div>
                    <div class="oeuvre">
                        <div class="checkbox_ouevre">
                                <div class="checkbox-wrapper-19 displayFlex" >
                                    <input type="checkbox" id="cbtest-19" />
                                    <label for="cbtest-19" class="check-box"  onclick=" $(this).toggleClass('checked');">
                                </div>
                        </div>
                        <img src="/theme/black_blue/img/oeuvres/redcross.png" alt="Red Cross" style="--w:247px">
                    </div>
                </div>
                </div>
                <div class="card_section">
                    <button>Valider</button>
                </div>
            </div>
            <div class="cadre_table ">
                <div  class="overflow jswidth">
    	            <img class="arrow_right shake" src="/theme/black_blue/img/arrow_right.svg">

    	            <table class=" stries" > 

    		            <thead class=""> 
                            <tr>  
                                <th class="date"><?php echo __('Date').'<br>'.('Création'); ?></th> 
                                <th class="agent"><?php echo __('Date ').'<br>'.('Validitée'); ?></th> 
                                <th class="mode"><?php echo __('Organisme'); ?></th> 
                                <th class="cout"><?php echo __('Supprimer'); ?></th> 
                                <th class="cout"><?php echo __('Date ').'<br>'.('Supression') ?></th> 
                                
                            </tr> 
    		            </thead> 
    		            <tbody>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Save the Children</td>
                                <td><img src="/theme/black_blue/img/delete.png" class="btn-delete"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Medicines Sans Frontieres</td>
                                <td><img src="/theme/black_blue/img/delete.png" class="btn-delete"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Red Cross</td>
                                <td><img src="/theme/black_blue/img/delete.png" class="btn-delete"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Red Cross</td>
                                <td></td>
                                <td>24/04/22 15:11:25 </td>
                            </tr>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Red Cross</td>
                                <td><img src="/theme/black_blue/img/delete.png" class="btn-delete"></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Red Cross</td>
                                <td></td>
                                <td>24/04/22 15:11:25 </td>
                            </tr>
                            <tr>
                                <td>24/04/22</td>
                                <td>24/04/22 - 25/05/22</td>
                                <td>Red Cross</td>
                                <td></td>
                                <td>24/04/22 15:11:25 </td>
                            </tr>
                            
                            
                        </tbody>
                    </table>
</div>
</div>
            </div>
        </div>
    </article>
    <input type="hidden" id="daterange" class="form-control"  >
</section>
<style>
    .btn-delete{
        cursor: pointer;
    }


    #modal-confirmation .modal-content{
	width: calc(600px*var(--coef));
	height: 200px;
    }

    @media only screen   and (max-width : 767px)
    {

	#modal-confirmation .modal-content{
	    width: calc(347px*var(--coef));
	    height:  calc(291px*var(--coef));
	}


    }


</style>

<script>
    var btn_datepicker = document.getElementById('btn_datepicker');
    console.log(btn_datepicker);
btn_datepicker.addEventListener('click', function ()
{
    duDatepicker('#daterange', 'show')
}, false);

duDatepicker('#daterange', {
            range: true,
            events: {
                onRangeFormat: function (from, to)
                {
                    var fromFormat = 'mmmm d, yyyy', toFormat = 'mmmm d, yyyy';

                    console.log(from, to);

                    if (from.getMonth() === to.getMonth() && from.getFullYear()
                            === to.getFullYear())
                    {
                        fromFormat = 'mmmm d'
                        toFormat = 'd, yyyy'
                    } else if (from.getFullYear() === to.getFullYear())
                    {
                        fromFormat = 'mmmm d'
                        toFormat = 'mmmm d, yyyy'
                    }

                    return from.getTime() === to.getTime() ?
                            this.formatDate(from, 'mmmm d, yyyy') :
                            [this.formatDate(from, fromFormat),
                                this.formatDate(to, toFormat)].join('-');
                }
            }
        });
        $(".btn-delete").click(function ()
        {
            $("#modal-confirmation").modal();
        });
</script>