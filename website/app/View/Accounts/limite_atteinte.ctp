<div id="leftcolumn" class="col-md-3 col-sm-4 col-xs-12">
    <?php
    echo $this->Frontblock->getAccountSidebar();
    echo $this->element('reviewbox');
    ?>
</div>
<div id="content_with_leftcolumn" class="col-md-9 col-sm-8 col-xs-12">
    <?php
    $this->Session->flash();

    $cms = $this->FrontBlock->getPageBlocTexte(158, false, $title);

    echo $this->element('title', array(
        'title' => $title,
        'icon' => 'bell'
    ));

    echo str_replace(array_keys($parms), array_values($parms), $cms);






    ?>
    <?php
    echo $this->Html->script('/theme/default/js/select_product', array('block' => 'script'));
    //On affiche la page pour le tarif
    $page = $this->FrontBlock->getPageBlocTexte(90);
    if($page !== false)
        echo $page;

    $user = $this->Session->read('Auth.User');
    echo $this->element('cart_products', array('products' => $packs, 'user' => $user));
    ?>
</div>