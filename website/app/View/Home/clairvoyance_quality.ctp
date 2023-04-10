<?php
    echo $this->element('leftcolumn');
?>
<div id="content_with_leftcolumn" class="col-md-9 col-sm-12">
    <?php echo '<h1>'.__('Le meilleur de la agents à votre service').'</h1>'; ?>
    <div class="clairagents">
        <?php foreach($categories as $category): ?>
            <?php echo $this->Html->link('<h3>'. $category['CategoryLang']['name'] .'</h3>',
                array(
                    'controller' => 'category',
                    'action' => 'display',
                    'language' => $this->Session->read('Config.language'),
                    'link_rewrite' => $category['CategoryLang']['link_rewrite'],
                    'id' => $category['CategoryLang']['category_id']
                ),
                array('escape' => false)
            ); ?>
            <?php if(!empty($category['CategoryLang']['description'])): ?>
                <div>
                    <?php echo nl2br(substr($category['CategoryLang']['description'],0,Configure::read('Site.lengthCatDescription')),true).(strlen($category['CategoryLang']['description']) <= Configure::read('Site.lengthCatDescription') ?'':'...'); ?>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>