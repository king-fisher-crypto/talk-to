<ul>
    <li><?php echo $this->Html->link(__('Accueil'),array('controller' => 'home', 'action' => 'index')) ?></li>
    <li class="<?php echo ($tab == 'profil'?'selected ':''); ?>">
        <?php echo ($tab == 'profil'?'<div class="arrow"></div>':''); ?>
        <?php echo $this->Html->link(
            __('Profil'),
            array('controller' => 'agents', 'action' => 'display', 'language' => $this->Session->read('Config.language'), 'link_rewrite' => strtolower($User['pseudo']), 'agent_number' => $User['agent_number'])
        ); ?>
    </li>
    <li class="hide-mobile">
        <?php echo $this->Html->link(
            __('Planning'),
            array('controller' => 'agents', 'action' => 'display',
                'language' => $this->Session->read('Config.language'), 'link_rewrite' => strtolower($User['pseudo']), 'agent_number' => $User['agent_number'], '#' => 'a_planning')
           // '#a_planning'
        ); ?>
    </li>
    <li  class="<?php echo ($tab == 'profil'?'selected ':''); ?>">
        <?php echo ($tab == 'reviews'?'<div class="arrow"></div>':''); ?>
        <?php echo $this->Html->link(
            __('Avis reçus'),
            array('controller' => 'agents', 'action' => 'display', 'language' => $this->Session->read('Config.language'), 'link_rewrite' => strtolower($User['pseudo']), 'agent_number' => $User['agent_number'], 'tab' => 'reviews')
        ); ?>
    </li>
    <li <?php echo ($tab == 'add_review'?'class="selected"':''); ?>>
        <?php echo ($tab == 'add_review'?'<div class="arrow"></div>':''); ?>
        <?php echo $this->Html->link(
            __('Déposer votre avis'),
            array('controller' => 'agents', 'action' => 'display', 'language' => $this->Session->read('Config.language'), 'link_rewrite' => strtolower($User['pseudo']), 'agent_number' => $User['agent_number'], 'tab' => 'add_review')
        ); ?>
    </li>
    <li  class="<?php echo ($tab == 'profil'?'selected ':''); ?>">
        <?php echo ($tab == 'private_msg'?'<div class="arrow"></div>':''); ?>
        <?php echo $this->Html->link(
            __('Envoyer un message privé'),
            array('controller' => 'agents', 'action' => 'display', 'language' => $this->Session->read('Config.language'), 'link_rewrite' => strtolower($User['pseudo']), 'agent_number' => $User['agent_number'], 'tab' => 'private_msg')
        ); ?>

    </li>
    <li  class="<?php echo ($tab == 'profil'?'selected ':''); ?>">
        <?php echo ($tab == 'abus'?'<div class="arrow"></div>':''); ?>
        <?php echo $this->Html->link(
            __('Dénoncer un abus'),
            array('controller' => 'agents', 'action' => 'display', 'language' => $this->Session->read('Config.language'), 'link_rewrite' => strtolower($User['pseudo']), 'agent_number' => $User['agent_number'], 'tab' => 'abus')
        ); ?>
    </li>
</ul>