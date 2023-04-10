<?php

    if(!isset($consult) || empty($consult)) $consult = array();
    if(!isset($univers) || empty($univers)) $univers = array();
    if(!isset($langs) || empty($langs)) $langs = array();
    if(!isset($countries) || empty($countries)) $countries = array();

    $boucleFirst = floor(count($category_langs)/2);
    $boucleSecond = count($category_langs) - $boucleFirst;
?>
<div class="col-lg-12">
    <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
    	<label class="col-sm-12 col-md-4 control-label required" for=""><?php echo __('Langues parlées couramment'); ?> <span class="star-condition">*</span></label>
        <div class="col-sm-12 col-md-8">
            <?php
            foreach ($select_langs as $k => $lang){
				
				$readonly = '';
				$css_span = '';
				if($k == 9 && $agent_country != 5){
						$readonly = 'onclick="return false" readonly disabled';
						$css_span = ' style="opacity:0.4;" data-toggle="tooltip" data-original-title="Vous ne pouvez cliquer la lanque parlée \'Canada\' si vous n\'êtes pas Canadien."';
					}
				
				
				if($k != 8 && $k != 10 && $k != 11 && $k != 12)
                echo '<div class="checkbox checkbox-inline"><label for="'. $nomModel .'Langs'.$k.'"><input type="checkbox" name="data['. $nomModel .'][langs][]" '. (in_array($k, $langs) || $k == 1?'checked':'') .' '.$readonly.' value='.$k.' id="'. $nomModel .'Langs'.$k.'"/><span '.$css_span.'></span><i class="lang_flags lang_'.key($lang).' " data-toggle="tooltip" data-original-title="'. $lang[key($lang)] .'" title="'. $lang[key($lang)] .'"></i></label></div>';
            }
            ?>
        </div>
    </div>

    <div class="form-group mt20 wow fadeIn animated" data-wow-delay="0.2s" style="visibility: visible;-webkit-animation-delay: 0.2s; -moz-animation-delay: 0.2s; animation-delay: 0.2s;">
        <label class="col-sm-12 col-md-4 control-label required" for="<?php echo $nomModel; ?>Countries"><?php echo __('Être visible sur les sites'); ?> <span class="star-condition">*</span></label>
        <div class="<?php echo ($nomModel === 'Agent'
            ?'col-sm-12 col-md-8'
            :'col-sm-12 col-md-8'
        ); ?>">
            <?php
           	$tickedFlag = array(1,3,4,5,13);
			$tickedRead = array(1,3,4,5,13);
                foreach ($select_countries_sites as $id => $country){
					
					$checked = (in_array($id, $countries)?'checked':'');
					$readonly = '';
					if(in_array($id,$tickedFlag)) $checked = 'checked';
					if(in_array($id,$tickedRead)) $readonly = 'onclick="return false"';
					
					
                    echo '<div class="checkbox checkbox-inline"><label for="'. $nomModel .'Countries'.$id.'"><input type="checkbox" name="data['. $nomModel .'][countries][]" '. $checked .' '.$readonly.' value='.$id.' id="'. $nomModel .'Countries'.$id.'"/><span></span><i class="country_flags country_'.$id.' " data-original-title="'. __($country) .'" data-toggle="tooltip" title="'. __($country) .'"></i></label></div>';
                }
            ?>
            <span class="help"><?php echo __('(Clientèle Francophone uniquement)'); ?></span>
        </div>
    </div>

    <div class="form-group">
        <label for="<?php echo $nomModel; ?>Categories" class="col-sm-12 col-md-4 control-label required"><?php echo __('Univers'); ?> <span class="star-condition">*</span></label>
        <div class="col-sm-12 col-md-8">
<div class="row"><div class="col-md-6 col-sm-12">
            <?php
            $i = 0;
            foreach ($category_langs as $k => $val){
                //Si catégorie "Accueil";
                if($k == 1) continue;

                echo '<div class="checkbox">';
                echo '<input type="checkbox" name="data['. $nomModel .'][categories][]" '. (in_array($k, $univers)?'checked':'') .' value='.$k.' id="'. $nomModel .'Categories'.$k.'"/>';
                echo '<label class="norequired" for="'. $nomModel .'Categories'. $k .'">'. __($val) .'</label>';
                echo '</div>';
                unset($category_langs[$k]);
                if(++$i == $boucleFirst) break;
            }
            ?>
        </div>
        <div class="col-md-6 col-sm-12">
            <?php
            $i = 0;
            foreach ($category_langs as $k => $val){
                //Si catégorie "Accueil";
                if($k == 1) continue;

                echo '<div class="checkbox">';
                echo '<input type="checkbox" name="data['. $nomModel .'][categories][]" '. (in_array($k, $univers)?'checked':'') .' value='.$k.' id="'. $nomModel .'Categories'.$k.'"/>';
                echo '<label class="norequired" for="'. $nomModel .'Categories'. $k .'">'. __($val) .'</label>';
                echo '</div>';
                unset($category_langs[$k]);
                if(++$i == $boucleSecond) break;
            }
            ?>
        </div></div></div>
    </div>

    <div class="form-group">
        <label for="<?php echo $nomModel; ?>Consult" class="col-sm-12 col-md-4 control-label required"><?php echo __('Consultation par'); ?> <span class="star-condition">*</span></label>
        <div class="col-sm-12 col-md-8"><div class="row">
            <?php
            echo '<div class="col-md-4 col-sm-12"><div class="checkbox"><label for="'. $nomModel .'Consult0"><input type="checkbox" name="data['. $nomModel .'][consult][]" '. (in_array('0', $consult)?'checked':'') .' value="'. ((isset($user) && $user['User']['consult_email'] == -1 )?'3':'0') .'" id="'. $nomModel .'Consult0" '. ((isset($user) && $user['User']['consult_email'] == -1 )?'disabled="disabled" readonly="readonly"':'') .' /><span class=""></span>'. __('Email') .'</label></div></div>';
            echo '<div class="col-md-4 col-sm-12"><div class="checkbox"><label for="'. $nomModel .'Consult1"><input type="checkbox" name="data['. $nomModel .'][consult][]" '. (in_array('1', $consult)?'checked':'') .' value="'. ((isset($user) && $user['User']['consult_phone'] == -1 )?'4':'1') .'" id="'. $nomModel .'Consult1" '. ((isset($user) && $user['User']['consult_phone'] == -1)?'disabled="disabled" readonly="readonly':'') .' /><span class=""></span>'. __('Téléphone') .'</label></div></div>';
            echo '<div class="col-md-4 col-sm-12"><div class="checkbox"><label for="'. $nomModel .'Consult2"><input type="checkbox" name="data['. $nomModel .'][consult][]" '. (in_array('2', $consult)?'checked':'') .' value="'. ((isset($user) && $user['User']['consult_chat'] == -1 )?'5':'2') .'" id="'. $nomModel .'Consult2" '. ((isset($user) && $user['User']['consult_chat'] == -1)?'disabled="disabled" readonly="readonly':'') .' /><span class=""></span>'. __('Chat') .'</label></div></div>';
            ?>
        </div></div>
    </div>
   </div>