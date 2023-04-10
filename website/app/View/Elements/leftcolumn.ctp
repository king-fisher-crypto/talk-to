<div id="leftcolumn" class="col-md-3 hidden-sm hidden-xs">
    <div class="box gray" id="box_subscribe">
    <?php $user = $this->Session->read('Auth.User'); ?>
    <?php if(empty($user) ||  !isset($user['role']) || $user['role'] === 'admin'): ?>
        <div class="btn_subscribe"><?php echo $this->Html->link(__('Inscription gratuite !'), array('controller' => 'users','action' => 'subscribe')); ?></div>
    <?php endif; ?>
        <ul>
            <li>
                
                <img width="18" height="13" alt="Tick icon spiriteo" src="/media/cms_photo/image/tick_icon.png" style="float:left">
                <p>
                    <span class="txt_black"><strong><?php echo __('Les meilleurs experts'); ?></strong></span>
                    <span class="txt_gray"><?php echo __('sélectionnés parmi des dizaines de candidatures'); ?></span>
                </p>
            </li>
            <li>
                <img width="18" height="13" alt="Tick icon spiriteo" src="/media/cms_photo/image/tick_icon.png" style="float:left">
                <p>
                    <span class="txt_black"><strong><?php echo __('Avis clients vérifiés'); ?></strong></span>
                    <span class="txt_gray"><?php echo __('Nous ne publions que de vrais avis'); ?></span>
                </p>
            </li>
            <li>
                <img width="18" height="13" alt="Tick icon spiriteo" src="/media/cms_photo/image/tick_icon.png" style="float:left">
                <p>
                    <span class="txt_black"><strong><?php echo __('Actuellement'); ?> <span class="txt_orange"><?php echo $this->FrontBlock->getAgentBusy(); ?></span> <?php echo __('experts en consultation'); ?></strong></span>
                </p>
            </li>
        </ul>
    </div>

    <?php
        if(!empty($user) && isset($user['role']) && $user['role'] === 'client')
            echo $this->Frontblock->getAccountSidebar();
        if(!empty($user) && isset($user['role']) && $user['role'] === 'agent')
            echo $this->Frontblock->getAgentSidebar();
    ?>
    <?php
        echo $this->element('reviewbox');
        echo $this->element('blockcolumn');
    ?>
</div>
