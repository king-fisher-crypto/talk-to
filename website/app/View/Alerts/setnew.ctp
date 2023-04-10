<?php

if (!isset($isAjax)){
   echo ' <div class="container">
	<section class="page profile-page mt20 mb40">
		<div class="row">
			<div class="col-sm-12 col-md-9">
				<div class="content_box mobile-center wow mb0 fadeIn" data-wow-delay="0.4s">
					
                    <div class="page-header">
						<h1 class="uppercase wow fadeIn" data-wow-delay="0.3s">'.$title_page.'</h1>
					</div><!--page-header END-->
                    ';
}
$text_footer = '';
if(isset($auth) && !$auth)
    echo __('Veuillez vous connecter avec un compte client.');
else{
    $page = $this->FrontBlock->getPageBlocTexte(160);
    if($page !== false)
        $text_footer =  $page;

}

echo $this->Session->flash();

if (!isset($success_alert) && !isset($auth)){
    echo $this->Form->create('Alert', array('controller' => 'alert', 'action' => 'setnew', 'nobootstrap' => 1,'class' => 'form-horizontal', 'default' => 1,
        'inputDefaults' => array(
            'div' => 'form-group',
            'between' => '<div class="col-lg-7">',
            'after' => '</div>',
            'class' => 'form-control'
        )));

    /* Sommes-nous loggués ? */
        $user = $this->Session->read('Auth.User');


    function getValueFromMultiSources($field="", $customerAlerts=false, $controller=false)
    {
        if (empty($field) || !$controller || !$customerAlerts)return false;
        if (isset($controller->request->data['Alert'][$field]) && !empty($controller->request->data['Alert'][$field]))
            return $controller->request->data['Alert'][$field];
        elseif (isset($controller->request->data['Alert'][$field]) && !empty($controller->request->data['Alert'][$field]))
            return $controller->request->data['Alert'][$field];
        elseif (isset($customerAlerts['0']['Alert'][$field]) && !empty($customerAlerts['0']['Alert'][$field])){
            return $customerAlerts['0']['Alert'][$field];
        }else return '';
    }


    /* On parse l'indicatif pays pour retrouver le numero de tel... */
        $phone_detail = $this->FrontBlock->parseTelStringForIndicatif(getValueFromMultiSources('phone_number', $customerAlerts, $this));

    echo '<div class="text_alerte_present" style="color:#330066; font-size:16px; font-weight:bold; margin:-5px 0 15px 0;text-align:center">'.__('Je choisis de recevoir une alerte').'</div>';
    echo '<div class="alert_panel">';

   $user = $this->Session->read('Auth.User');

    $options = array(
        'email'  => array('label' => array(
                                    'text'      => __('Par E-mail'),
                                    'class'     => 'control-label col-lg-4'
                                    ),
                           // 'required'  => true,
                            'type'      => 'email',
                            'value'     => $user['email']//getValueFromMultiSources('email', $customerAlerts, $this)
        ),
        
        'agent_id' => array(
                        'type'  =>   'hidden',
                        'value' =>   isset($agent['User']['id'])?(int)$agent['User']['id']:0
        )
    );
/*
'email2' => array('label' => array(
                                    'text'      => __('Confirmez votre E-mail'),
                                    'class'     => 'control-label col-lg-4'
                                    ),
                          //  'required'  => true,
                            'type'      => 'email',
                            'value'     => getValueFromMultiSources('email', $customerAlerts, $this)
        ),
*/


    echo $this->Form->inputs($options);
	
	 $options = array(
        'legend' => false,
        'phone_number' => array(
            'label' => array(
                'text'  => __('Et/ou par SMS'),
                'class' => 'control-label col-lg-4'
            ),
            'type'          => 'tel',
		 'placeholder'          => 'mobile',
            'between'    => '<div class="col-lg-4 col-md-3">'.$this->FrontBlock->getIndicatifTelInputIns(false, ($phone_detail!=false)?$phone_detail['indicatif']:false).'</div><div class="col-lg-4 col-md-5">',
            'value' => ($phone_detail!=false)?$phone_detail['phone']:''
        )
    );


    //echo '<div style="color:#330066; font-size:16px; font-weight:bold; margin:15px 0 10px 0">'.__('Et/ou je souhaite recevoir une alerte par sms lorsque').' '.$agent_pseudo.' '.__('sera disponible.')
     //   .' <span style="font-size:12px; color:#666">'.__('(Un sms par jour)').'</span></div><div class="alert_panel">';
//echo '<div style="color:#330066; font-size:16px; font-weight:bold; margin:15px 0 10px 0;text-align:center">'.__('Et/ou');
    echo $this->Form->inputs($options);
//echo '</div>';
	
    echo '</div><div class="alert_txt_receive" style="color:#330066; font-size:15px; font-weight:bold; margin:-15px 0 10px 0;text-align:center">'.__('Recevoir cette alerte lorsque').' '.$agent_pseudo.' '.__('sera disponible par :').'</div><div class="alert_panel">';
    /*$options = array();

    $optionsAlert = array();
    $optionsAlert[0] = __('Aucune alerte');
    for ($i=1; $i<6; $i++){
        $optionsAlert[$i] = $i.' '.__('fois par jour');
    }

    $title = '';
    foreach ($consult_medias AS $media => $txt){
        if ($media == 'phone')
            $title = __('Disponible par téléphone');
        elseif ($media == 'chat')
            $title = __('Disponible par chat');
        elseif ($media == 'email')
            $title = __('Disponible par e-mail');

        $options['media_'.$media] = array(
                                        'label' => array(
                                                'text'      => $title,
                                                'class'     => 'control-label col-lg-4'),
                                                'required'  => true,
                                                'type'      => 'select',
                                                'selected'  => 1,
                                                'options'   => $optionsAlert
                                    );
    }



    echo $this->Form->inputs($options);
*/


echo '<div class="col-sm-12 col-md-12" style="margin-bottom:40px;">
<div class="row">
<div class="col-md-2 col-sm-2 hidden-xs">&nbsp;</div>
<div class="col-md-3 col-sm-3 col-xs-5 col-nopad">
<div class="checkbox" style="text-align:center">
<label for="AlertConsult0">
<input id="AlertConsult0" name="data[Alert][consult][]" value="0" type="checkbox" checked>
<span class=""></span>
Téléphone
</label>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3 col-nopad">
<div class="checkbox" style="text-align:center">
<label for="AlertConsult1">
<input id="AlertConsult1" name="data[Alert][consult][]" value="1" type="checkbox" checked>
<span class=""></span>
Chat
</label>
</div>
</div>
<div class="col-md-3 col-sm-3 col-xs-3 col-nopad">
<div class="checkbox" style="text-align:center">
<label for="AlertConsult2">
<input id="AlertConsult2" name="data[Alert][consult][]" value="2" type="checkbox">
<span class=""></span>
Email
</label>
</div>
</div>
</div>
</div>';





    echo '</div>';





   



    /*
    echo $this->Form->submit('Enregistrer', array(
        'label' => false,
        'div'   => false,
        'class' => 'btn btn-primary'
    ));
*/
    echo '<div style="margin:30px 0 20px 0;clear:both;text-align:center"><input type="submit" value="'.__('Enregistrer').'" class="btn btn-pink btn-pink-modified btn-small-modified" /></div>';
}

echo '<div style="margin-bottom:-20px;display:block">'.$text_footer.'</div>';

if (!isset($isAjax)){
    echo '</div><!--content_box END-->
				</div><!--col-9 END-->
                '.$this->Frontblock->getRightSidebar().'
			
		</div><!--row END-->
	</section><!--expert-list END-->
</div><!--container END-->';
}