<div id="leftcolumn" class="col-md-3 col-sm-4 col-xs-12">
    <?php
    echo $this->Frontblock->getAccountSidebar();
    echo $this->element('reviewbox');
    ?>
</div>
<div id="content_with_leftcolumn" class="col-md-9 col-sm-8 col-xs-12">
    <?php
    echo $this->Session->flash();


    $html = isset($contenu['PageLang']['content'])?$contenu['PageLang']['content']:'';

    $html = str_replace("##CART_TOTAL##", isset($cart_datas['total_price'])?$this->Nooxtools->displayPrice($cart_datas['total_price']):'', $html);
    $html = str_replace("##CART_USER_MAIL##", isset($cart_datas['user']['email'])?$cart_datas['user']['email']:'', $html);

    echo $html;


    ?>
</div>