<?php

        $rdv = "\n";
        foreach($param['appointments'] as $row)
            $rdv.= "\t".('Le ').$row['date']."\n";


        $mail = $this->Frontblock->getMailBlock(152);
        $tplVars = array(
            '##SITE_NAME##'                 =>     Configure::read('Site.name'),
            '##PARAM_PSEUDO##'              =>     $param['pseudo'],
            '##PARAM_RENDEZVOUS##'          =>     $rdv,
            '##PARAM_URLSITE##'             =>     $urlSite
        );
        echo strip_tags(str_replace(array_keys($tplVars), array_values($tplVars), $mail));

/*
        echo '<h1>'.Configure::read('Site.name').'</h1>';
        echo '<p>'.__('Bonjour').' '.$param['pseudo'].',</p>';
        echo '<p>'.__('Voici vos prochains rendez-vous.').'</p>';
        echo '<ul>';

        endforeach;
        echo '</ul>';
        echo '<p>'.__('Vous pouvez les retrouver dans votre compte dans le menu "Mes RDV".').'</p>';

        echo __('A très vite sur').' <a href="'. $urlSite .'">'.Configure::read('Site.name').'</a>';
        */
