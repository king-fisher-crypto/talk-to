<?php

    $fiche_link = 'http://'.$customer['Domain']['domain'].$this->Html->url(
        array(
            'language'      => $customer['Lang']['language_code'],
            'controller'    => 'agents',
            'action'        => 'display',
            'link_rewrite'  => strtolower($agent['pseudo']),
            'agent_number'  => $agent['agent_number'],
            'admin'         => false
        ),
        array(
            'title'         => $agent['pseudo']
        )
    );
    $consult = '<table>';
    foreach($customer['Alert']['media'] as $media){
        $consult.= "<tr><td>".'- '.__($nameMedia[$media['name']]).'</td></tr>';
    }
    $consult.= '</table>';


    $tplVars = array(
        '##PARAM_AGENT_PSEUDO##'        =>      $agent['pseudo'],
        '##PARAM_AGENT_MEDIAS##'        =>      $consult,
        '##PARAM_LINK_AGENT##'          =>      $fiche_link,
        '##PARAM_LINK_STOP##'           =>      'http://'.
            $customer['Domain']['domain'].$this->Html->url(array(
                'controller' => 'alerts',
                'action' => 'stop_alert',
                'language'=> $customer['Lang']['language_code'],
                'id' => $customer['Alert']['media'][key($customer['Alert']['media'])]['id'],
                'admin' => false))
    );
    $mail = $this->Frontblock->getMailBlock(153);
    echo str_replace(array_keys($tplVars), array_values($tplVars), $mail);

/*
?><div>
    <?php
    echo '<h1>'.Configure::read('Site.name').'</h1>';
    echo '<p>'.__('Bonjour').', </p>';
    echo '<p>'.__('L\'agent ').' '.'&quot;'.$agent['pseudo'].'&quot; est disponible pour une consultation !</p>';

    $consult = '<p>'.__('Il est disponible pour une consultation par :');
    //Pour chaque media
    foreach($customer['Alert']['media'] as $media){
        $consult.= "<br>".'- '.__($nameMedia[$media['name']]);
    }
    $consult.= '</p>';

    echo $consult;

    echo '<p>'.__('Nous vous invitons à le consulter dès maintenant avant qu\'il ne soit occupé !').'</p>';

    echo '<p>'.__('Pour cela, connectez-vous dès maintenant sur notre site !').'</p>';

    echo '<a href="'.$fiche_link.'">'.__('Consulter ').$agent['pseudo'].' '.__('maintenant').'</a>';

    echo '<p>'. __('Pour ne plus recevoir d\'alertes, cliquez sur ').'<a href="http://'.$customer['Domain']['domain'].$this->Html->url(array('controller' => 'alerts', 'action' => 'stop_alert', 'language'=> $customer['Lang']['language_code'], 'id' => $customer['Alert']['media'][0]['id'])).'">'. __('STOP') .'</a></p>';

    ?>
</div>
*/