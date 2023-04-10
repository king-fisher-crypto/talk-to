<?php $user = $this->Session->read('Auth.User'); ?>
<div class="phoneboxnote" id="phoneboxnote_0">
    <div class="cb_pseudo">
        <div class="action"><i class="glyphicon glyphicon-remove-circle rfloat cb_close"></i></div>
        <p class="name"></p>
    </div>
        <?php
        echo $this->Form->create('Notes', array(
            'nobootstrap' => 1,
            'class' => 'form-horizontal',
            'default' => 1,
            'inputDefaults' => array(
                'label' => false,
                'div'   => false
            )
        )); ?>
	<div style="display:block;width:100%;text-align: left">&nbsp;&nbsp;Né le <select id="phone_note_birthday_day" style="height:25px;"><option value="">Jour</option><option value="01">1</option><option value="02">2</option><option value="03">3</option><option value="04">4</option><option value="05">5</option><option value="06">6</option><option value="07">7</option><option value="08">8</option><option value="09">9</option><option value="10">10</option><option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option><option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option><option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option><option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option><option value="31">31</option></select>&nbsp;<select id="phone_note_birthday_month" style="height:25px;"><option value="">Mois</option><option value="01">Janvier</option><option value="02">Février</option><option value="03">Mars</option><option value="04">Avril</option><option value="05">Mai</option><option value="06">Juin</option><option value="07">Juillet</option><option value="08">Aout</option><option value="09">Septembre</option><option value="10">Octobre</option><option value="11">Novembre</option><option value="12">Décembre</option></select>&nbsp;<select id="phone_note_birthday_year" style="height:25px;"><option value="">Année</option>
   <?php
		$list_year = array();
		for($x=(date('Y')-100); $x <= date('Y'); $x++){
			$list_year[] =  '<option value="'.$x.'">'.$x.'</option>';
		}
		krsort($list_year);
		foreach($list_year as $opt)echo $opt;
		?>
   </select>&nbsp;<select id="phone_note_sexe" style="height:25px;"><option value="">Sexe</option><option value="F">Femme</option><option value="H">Homme</option></select></div>
    <textarea class="content" placeholder="Ajoutez toutes les notes possibles concernant votre client(e) : prénom, âge, sujet etc etc…vos notes apparaîtront automatiquement lors de son prochain appel">
        
    </textarea>
    <input id="phone_note_call" value="" type="hidden"/>
    <input id="phone_note_tchat" value="" type="hidden"/>
    <input id="phonenoteagent" value="" type="hidden"/>
    <div class="submit">
    <?php			echo $this->Html->link('<div style="display:inline-block;float:left;">Dernières consultations&nbsp;<i class="glyphicon glyphicon-zoom-in"></i></div>',
                                                array('controller' => 'agents', 'action' => 'consult_history'),
                                                array('escape' => false, 'class' => 'mb0 nx_openlightbox_note', 'param' => 0)
                                            );
									?>
    <button type="button" class="btn btn-danger margin_top_5">Enregistrer</button>
    </div>
    </form>
</div>