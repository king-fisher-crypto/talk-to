<?php
    echo $this->element('leftcolumn');
?>
<div id="content_with_leftcolumn" class="col-md-9 col-sm-12">
    <?php
        $carousel = $this->FrontBlock->getCaroussel();
        if($carousel === false)
            echo $this->element('scene', array());
        else
            echo $carousel;
    ?>
    <div class="nav_categories clearfix"><?php echo $this->FrontBlock->getBandeauCat($cat['CategoryLang']['category_id']); ?></div>
    <div id="cms_container">
        <div class="cms_text" id="fullcontent">
            <?php echo $cat['CategoryLang']['description']; ?>

            <?php

            echo $this->Html->link('&lt;&lt; '.__('Revenir à la page précédente'), array(
                    'controller' => 'home',
                    'action'     => 'index'
                ),
                array('escape'=> false)
            );

            ?>
        </div>

    </div>
</div>